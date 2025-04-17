<?php

namespace App\Http\Controllers;

use App\Events\OfficePaymentConfirm;
use App\Events\ProjectClosed;
use App\Models\Bank;
use App\Models\Booking;
use App\Models\Comment;
use App\Models\Investor;
use App\Models\Project;
use App\Models\Projectagent;
use App\Models\Projectcost;
use App\Models\Projectdetail;
use App\Models\Projectupdate;
use App\Models\User;
use App\Repositories\UserRepository;
use Auth;
use Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Mpdf\Mpdf;
use Session;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Cache;
use App\Repositories\ProjectRepository;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;


class WebController extends Controller
{

    public function userLogin(Request $request)
    {

        $user = User::where('email', $request->email)->first();

        if ($user && Hash::check($request->password, $user->password)) {
            // Authenticate the user
            Auth::login($user);

            return redirect()->route('dashboard'); // Redirect to the dashboard
        } else {
            return redirect()->back()->withErrors(['email' => 'Invalid credentials.']);
        }
    }

    public function dashboard()
    {
        $users = User::all();
        $totalUsers = $users->count();
        $admin = $users->where('level', 100)->count();
        $investors = $users->where('level', 200)->count();
        $agents = $users->where('level', 300)->count();
        $projects = Project::all();
        $totalProject = $projects->count();
        $runningProjects = $projects->where('status', 1)->count();
        $completedProjects = $projects->where('status', 5)->count();
        $draftProjects = $projects->where('status', 0)->count();
        $bookings = Booking::all();
        $totalBooking = $bookings->count();
        $todayBooking = $bookings->where('created_at', '>=', now()->startOfDay())->count();
        $thisMonthBooking = $bookings->where('created_at', '>=', now()->startOfMonth())->count();
        $thisYearBooking = $bookings->where('created_at', '>=', now()->startOfYear())->count();
        $totalUnits = $bookings->sum('total_unit');
        $totalAmount = $bookings->sum(fn($booking) => $booking->total_unit * $booking->project->details->unit_price);
        return view('dashboard', compact([
            'agents',
            'investors',
            'totalProject',
            'runningProjects',
            'completedProjects',
            'draftProjects',
            'totalBooking',
            'totalUsers',
            'admin',
            'todayBooking',
            'thisMonthBooking',
            'thisYearBooking',
            'totalUnits',
            'totalAmount'
        ]));
    }

    public function userLogout(Request $request)
    {
        Auth::logout(); // Log out the user

        $request->session()->invalidate(); // Invalidate the session

        $request->session()->regenerateToken(); // Regenerate CSRF token

        return redirect(''); // Redirect to login page
    }

    public function projects()
    {
        //$this->projectRepository->debugProjectCache();
        $status = Session::get('status');

        $projects = Project::with('details')->get();

        if ($status) {
            $projects = $projects->where('status', $status);
        }

        // Sort by ID in descending order
        $projects = $projects->sortByDesc('id');

        // Paginate the sorted data
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $perPage = 9;
        $currentItems = $projects->slice(($currentPage - 1) * $perPage, $perPage)->all();

        // Create LengthAwarePaginator instance
        $projects = new LengthAwarePaginator(
            $currentItems,
            $projects->count(),
            $perPage,
            $currentPage,
            ['path' => Paginator::resolveCurrentPath()]
        );

        return view('projectList', compact('projects'));
    }


    public function projectFilter(Request $request)
    {
        $status = $request->status;
        Session::put('status', $status);

        return redirect()->route('projects');
    }

    public function projectUpdates($id)
    {
        $project = Project::with('details')->findOrFail($id);
        // Fetch project updates for the given project ID, including comments and replies
        $projectUpdates = Projectupdate::with('user', 'comment.reply')->where('project_id', $id)->get();
        //dd($projectUpdates);
        // Transform the project updates to format images correctly
        $projectUpdates->transform(function ($update) {
            // Decode JSON images
            $images = json_decode($update->image, true) ?? [];

            // Generate full URLs for images
            $update->image_urls = array_map(fn($path) => url($path), $images);

            return $update;
        });

        // dd($projectUpdates);

        // Return the view with project updates data
        return view('projectUpdates', compact('projectUpdates', 'project'));
    }

    public function comment(Request $request, $id)
    {
        // Validate the request
        $request->validate([
            'comment' => 'required|string',
        ]);

        // Create a new comment
        Comment::create([
            'projectupdate_id' => $id,
            'comment_by' => Auth::id(),
            'comment' => $request->comment,
        ]);

        // Redirect back to the project updates page
        return redirect()->back();
    }

    public function storeProject(Request $request)
    {
        //dd($request->all());
        $user = Auth::user();


        // Create the project
        $project = Project::create([
            'created_by' => $user->id,
        ]);

        // Generate unique_id using user ID (e.g., AG01, AG02)
        $project->update([
            'unique_id' => 'MKPR' . str_pad($project->id, 2, '0', STR_PAD_LEFT),
        ]);


        $fileName = null;
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/projects'), $fileName);
        }

        // Store project details
        $projectDetail = Projectdetail::create([
            'project_id' => $project->id,
            'title' => $request->title,
            'total_price' => $request->total_price,
            'unit_price' => $request->unit_price,
            'unit' => $request->unit,
            'location' => $request->location,
            'location_map' => $request->location_map,
            'description' => $request->description,
            'image' => $fileName,
            'youtube_video' => $request->youtube_video,
            'duration' => $request->duration,
            'return_amount' => $request->return_amount
        ]);

        return redirect()->back()->with('success', 'Project created successfully.');

    }

    public function projectEdit($id)
    {
        $project = Project::with('details')->findOrFail($id);

        return view('projectEdit', compact('project'));
    }

    public function updateProject(Request $request, $id)
    {
        $projectDetail = Projectdetail::where('project_id', $id)->first();

        if ($request->hasFile('image')) {
            // Delete the previous image if it exists
            if ($projectDetail->image && file_exists(public_path('uploads/projects/' . $projectDetail->image))) {
                unlink(public_path('uploads/projects/' . $projectDetail->image));
            }

            // Upload new image
            $file = $request->file('image');
            $fileName = $id . time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/projects'), $fileName);

            // Update image field in database
            $projectDetail->image = $fileName;
        }

        // Update other fields
        $projectDetail->update([
            'title' => $request->title,
            'total_price' => $request->total_price,
            'unit_price' => $request->unit_price,
            'unit' => $request->unit,
            'youtube_video' => $request->youtube_video,
            'location' => $request->location,
            'description' => $request->description,
            'duration' => $request->duration,
            'return_amount' => $request->return_amount,
            'location_map' => $request->location_map,
        ]);

        if ($request->hasFile('image')) {
            $projectDetail->save();
        }


        return redirect()->route('projects')->with('success', 'Project updated successfully');
    }


    public function projectPeople($id)
    {
        $project = Project::with('details')->findOrFail($id);
        $bookings = Booking::with('investor.project.details')->where('project_id', $id)->get();
        $agents = Projectagent::with('user')->where('project_id', $id)->get();
        $agentList = User::where('level', 300)->get();
        $investorList = Investor::with('user')->get();
        return view('projectPeople', compact('investorList', 'bookings', 'agents', 'agentList', 'project'));
    }



    public function assignAgent()
    {
        $data = request()->validate([
            'project_id' => 'required',
            'agent_id' => 'required'
        ]);
        //dd($data);

        Projectagent::create($data);

        return redirect()->back()->with('success', 'Agent assigned successfully.');
    }

    public function deleteAgent($id)
    {
        $agent = ProjectAgent::find($id);

        if ($agent) {
            $agent->delete();
            return redirect()->back()->with('success', 'Agent removed successfully.');
        }

        return redirect()->back()->with('error', 'Agent not found.');
    }

    public function assignInvestor(Request $request)
    {
        $now = now();
        $futureDateTime = $now->addHours(24)->format('Y-m-d H:i:s');

        Booking::create([
            'project_id' => $request->project_id,
            'investor_id' => $request->investor_id,
            'total_unit' => $request->unit,
            'status' => 2,
            'payment_method' => 2,
            'time_to_pay' => $futureDateTime,
            'payment_note' => $request->payment_note,
        ]);

        // Increment projectdetail booked_unit
        Projectdetail::where('project_id', $request->project_id)
        ->increment('booked_unit', $request->unit);

        return redirect()->back()->with('success', 'Project Booking Successful.');
    }

    public function agents()
    {
        $agents = User::where('level', 300)->get();

        return view('agent.index', compact('agents'));
    }

    public function agentDelete($id)
    {
        $agent = User::find($id);

        if ($agent) {
            $agent->delete();
            return redirect()->back()->with('success', 'Agent deleted successfully.');
        }

        return redirect()->back()->with('error', 'Agent not found.');
    }

    public function agentStore(Request $request)
    {
        // Create the user first
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'level' => 300,
            'phone' => $request->phone,
            'password' => Hash::make(12345678),
        ]);

        // Generate unique_id using user ID (e.g., AG01, AG02)
        $user->update([
            'unique_id' => 'MKAG' . str_pad($user->id, 2, '0', STR_PAD_LEFT),
        ]);

        return redirect()->back()->with('success', 'Agent added successfully.');
    }


    public function investors()
    {
        $investors = User::with('booking.project.details','investor')->where('level', 200)->get();
        $banks = Bank::all();

        return view('investor.index', compact('investors', 'banks'));
    }

    public function investorDelete($id)
    {
        $investor = User::find($id);

        if ($investor) {
            $investor->delete();
            return redirect()->back()->with('success', 'Investor deleted successfully.');
        }

        return redirect()->back()->with('error', 'Investor not found.');
    }

    public function investorStore(Request $request)
    {
        $request->validate([
            'nid_upload' => 'nullable|file|mimes:jpg,png,pdf|max:2048', // 2MB max
            'check_upload' => 'nullable|file|mimes:jpg,png,pdf|max:2048', // 2MB max
            'image' => 'nullable|file|mimes:jpg,png|max:2048', // 2MB max
        ]);

        //dd($request->all());

        $imageFileName = null;
        if ($request->hasFile('image')) {
            $imageFile = $request->file('image');
            $imageFileName = time() . '_' . $imageFile->getClientOriginalName();
            $imageFile->move(public_path('uploads/investors'), $imageFileName);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'address' => $request->address,
            'level' => 200,
            'phone' => $request->phone,
            'password' => Hash::make(12345678),
            'image' => $imageFileName
        ]);

        // Generate unique_id using user ID (e.g., AG01, AG02)
        $user->update([
            'unique_id' => 'MKIN' . str_pad($user->id, 2, '0', STR_PAD_LEFT),
        ]);

        $nidFileName = null;
        if ($request->hasFile('nid_upload')) {
            $nidFile = $request->file('nid_upload');
            $nidFileName = time() . '_' . $nidFile->getClientOriginalName();
            $nidFile->move(public_path('uploads/investors/nid'), $nidFileName);
        }

        $checkFileName = null;
        if ($request->hasFile('check_upload')) {
            $checkFile = $request->file('check_upload');
            $checkFileName = time() . '_' . $checkFile->getClientOriginalName();
            $checkFile->move(public_path('uploads/investors/blank_check'), $checkFileName);
        }

        // Update or create investor record
        Investor::updateOrCreate(
            ['investor_id' => $user->id],
            [
                'nid' => $request->nid,
                'nid_upload' => $nidFileName,
                'bank_id' => $request->bank_id,
                'acc_name' => $request->acc_name,
                'acc_number' => $request->acc_number,
                'branch_name' => $request->branch_name,
                'routing_number' => $request->routing_number,
                'swift_code' => $request->swift_code,
                'check_upload' => $checkFileName,
            ]
        );

        return redirect()->back()->with('success', 'Investor added successfully.');
    }

    public function investorHistory($id)
    {
        $user = User::find($id);
        $bookings = Booking::with('project.details', 'investor')->where('investor_id', $id)->get();

        return view('investor.history', compact('bookings', 'user'));
    }

    public function projectCosts($id)
    {
        $project = Project::with('details')->findOrFail($id);
        $costs = Projectcost::where('project_id', $id)->get();
        // dd($costs);
        $totalCost = $costs->sum('cost');

        return view('project.cost', compact('project', 'costs', 'totalCost'));
    }

    public function projectCostsStore(Request $request)
    {
        //dd($request->all());
        // Validate request
        $request->validate([
            'reason' => 'required|array',
            'cost' => 'required|array',
            'voucher' => 'required|array',
            'reason.*' => 'string|max:255',
            'cost.*' => 'numeric|min:0',
            'voucher.*' => 'numeric',
        ]);

        $costFields = $request->reason;
        $costValues = $request->cost;
        $costVouchers = $request->voucher;
        //dd($costFields, $costValues, $costVouchers);

        foreach ($costFields as $index => $field) {
            Projectcost::create([
                'project_id' => $request->project_id,
                'field' => $field,
                'cost' => $costValues[$index] ?? 0,
                'voucher' => $costVouchers[$index] ?? null,
            ]);
        }

        return redirect()->back()->with('success', 'Costs added successfully.');

    }


    public function projectClose(Request $request)
    {
        // Validate input (optional but recommended)
        $request->validate([
            'project_id' => 'required|exists:projects,id',
            'closing_amount' => 'required|numeric',
            'service_charge' => 'required|numeric',
        ]);

        // Eager load the project with details correctly
        $project = Project::with('details','cost')->findOrFail($request->project_id);

        // Update the project status
        $project->update([
            'status' => 5,
        ]);

        $imageFileName = null;
        if ($request->hasFile('voucher_file')) {
            $imageFile = $request->file('voucher_file');
            $imageFileName = time() . '_' . $imageFile->getClientOriginalName();
            $imageFile->move(public_path('uploads/vouchers'), $imageFileName);
        }

        // Update the project details
        Projectdetail::where('project_id', $request->project_id)->update([
            'closing_amount' => $request->closing_amount,
            'service_charge' => $request->service_charge,
            'remarks' => $request->remarks,
            'voucher_file' => $imageFileName,
        ]);

        // Fire the event (if needed)
        event(new ProjectClosed($project));

        return redirect()->back()->with('success', 'Project closed successfully.');
    }



    public function financeDetails($id)
    {
        $project = Project::with('details')->findOrFail($id);
        $costs = Projectcost::where('project_id', $id)->get();
        // dd($costs);

        // Calculate the total cost of the project
        $totalCost = $costs->sum('cost') ?? 0;

        // Get the revenue from project details
        $revenue = $project->details->closing_amount ?? 0;

        // Calculate profit (Revenue - Total Cost)
        $profit = $revenue - $totalCost;

        // Get the service charge as a percentage
        $serviceChargePercent = $project->details->service_charge ?? 0;

        // Calculate service charge in value
        $serviceChargeValue = ($profit * $serviceChargePercent) / 100;

        // Calculate net profit (Profit - Service Charge)
        $netProfit = $profit - $serviceChargeValue;

        // Get the total unit
        $unit = $project->details->unit ?? 0;

        $profitPerUnit = $netProfit / $unit;

        // Pass values to the view
        return view('project.finance', compact('unit', 'profitPerUnit', 'profit', 'revenue', 'serviceChargePercent', 'serviceChargeValue', 'netProfit', 'project', 'costs', 'totalCost'));
    }

    public function printFinanceDetails($id)
    {
        $project = Project::with('details')->findOrFail($id);
        $costs = Projectcost::where('project_id', $id)->get();
        // dd($costs);

        // Calculate the total cost of the project
        $totalCost = $costs->sum('cost') ?? 0;

        // Get the revenue from project details
        $revenue = $project->details->closing_amount ?? 0;

        // Calculate profit (Revenue - Total Cost)
        $profit = $revenue - $totalCost;

        // Get the service charge as a percentage
        $serviceChargePercent = $project->details->service_charge ?? 0;

        // Calculate service charge in value
        $serviceChargeValue = ($profit * $serviceChargePercent) / 100;

        // Calculate net profit (Profit - Service Charge)
        $netProfit = $profit - $serviceChargeValue;

        // Get the total unit
        $unit = $project->details->unit ?? 0;

        $profitPerUnit = $netProfit / $unit;

        // Render the HTML content from the view
        $html = view('project.pdf-finance-details', compact('costs','profit', 'revenue', 'serviceChargePercent', 'netProfit', 'unit', 'profitPerUnit', 'project', 'totalCost'))->render();

        // Initialize mPDF
        $mpdf = new Mpdf();

        // Write the HTML content to the PDF
        $mpdf->WriteHTML($html);

        // Stream the PDF in the browser (inline view)
        $mpdf->Output('finance_details_' . $project->details->title . '.pdf', 'I');  // This streams the PDF in the browser

        // If you want to download the PDF afterward, you can use the following code
        // $mpdf->Output('finance_details_' . $project->id . '.pdf', 'D'); // This would trigger the download
    }



    public function pendingPayment()
    {
        $bookings = Booking::with('investor','project.details')->where('status',2)->where('payment_method',2)->get();

        return view('pendingPayment.office',compact('bookings'));
    }

    public function confirmOfficePayment($id)
    {
        $booking = Booking::find($id)->with('investor','project.details')->first();

        // Update the booking status
        $booking->update([
            'status' => 5
        ]);

        event(new OfficePaymentConfirm($booking));

        return redirect()->back()->with('success','Booking confirmed successfully.');
    }

    public function cancelOfficePayment($id)
    {
        $booking = Booking::find($id);

        // Update the booking status
        $booking->update([
            'status' => 7
        ]);

        // Decrement booked units
        Projectdetail::where('project_id', $booking->project_id)
            ->decrement('booked_unit', $booking->total_unit);

        return redirect()->back()->with('success', 'Booking canceled successfully.');
    }


    public function bankPendingPayment()
    {
        $bookings = Booking::with('investor','project.details')->where('status',2)->where('payment_method',3)->get();

        return view('pendingPayment.bank',compact('bookings'));
    }

    public function viewBankReceopt($id)
    {
        $booking = Booking::find($id);

        if (!$booking || !$booking->bank_receipt) {
            abort(404, 'Receipt not found.');
        }

        $path = public_path('uploads/bank_receipts/' . $booking->bank_receipt);

        if (!file_exists($path)) {
            abort(404, 'File not found.');
        }

        $mimeType = mime_content_type($path);

        return response()->file($path, [
            'Content-Type' => $mimeType,
        ]);
    }

    public function viewNid($id)
    {
        $investor = Investor::find($id);

        if (!$investor || !$investor->nid_upload) {
            abort(404, 'Receipt not found.');
        }

        $path = public_path('uploads/investors/nid/' . $investor->nid_upload);

        if (!file_exists($path)) {
            abort(404, 'File not found.');
        }

        $mimeType = mime_content_type($path);

        return response()->file($path, [
            'Content-Type' => $mimeType,
        ]);
    }

    public function viewCheck($id)
    {
        $investor = Investor::find($id);

        if (!$investor || !$investor->check_upload) {
            abort(404, 'Receipt not found.');
        }

        $path = public_path('uploads/investors/blank_check/' . $investor->check_upload);

        if (!file_exists($path)) {
            abort(404, 'File not found.');
        }

        $mimeType = mime_content_type($path);

        return response()->file($path, [
            'Content-Type' => $mimeType,
        ]);
    }



}
