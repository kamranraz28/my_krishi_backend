<?php

namespace App\Http\Controllers\Web;

use App\Events\ProjectClosed;
use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Investor;
use App\Models\Project;
use App\Models\Projectagent;
use App\Models\Projectcost;
use App\Models\Projectdetail;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Pagination\Paginator;
use App\Models\Term;
use Mpdf\Mpdf;

class ProjectController extends Controller
{
    //
    public function index ()
    {
        //$this->projectRepository->debugProjectCache();
        $status = Session::get('status');

        $projectQuery = Project::with('details');

        if ($status) {
            $projectQuery->where('status', $status);
        }
        $projects = $projectQuery->orderBy('id', 'desc')->get();

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

        $terms = Term::all();

        return view('project.index', compact('projects','terms'));
    }

    public function filter(Request $request)
    {
        $status = $request->status;
        Session::put('status', $status);

        return redirect()->route('projects.index');
    }

    public function create()
    {
        return view('project.create');
    }

    public function store(Request $request)
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


    public function edit($id)
    {
        $project = Project::with('details')->findOrFail($id);
        $remainingUnit = $project->details->unit - $project->details->booked_unit;
        $bookings = Booking::with('investor.project.details')->where('project_id', $id)->get();
        $uniqueTotalInvestors = $bookings->unique('investor_id')->count();
        $agents = Projectagent::with('user')->where('project_id', $id)->get();
        $uniqeAgents = $agents->unique('user_id')->count();

        return view('project.edit', compact('remainingUnit','project','uniqeAgents','uniqueTotalInvestors','bookings', 'agents'));
    }

    public function update(Request $request, $id)
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

    public function people($id)
    {
        $project = Project::with('details')->findOrFail($id);
        $remainingUnit = $project->details->unit - $project->details->booked_unit;
        $bookings = Booking::with('investor.project.details')->where('project_id', $id)->get();
        $uniqueTotalInvestors = $bookings->unique('investor_id')->count();
        $agents = Projectagent::with('user')->where('project_id', $id)->get();
        $uniqeAgents = $agents->unique('user_id')->count();
        $agentList = User::where('level', 300)->get();
        $investorList = Investor::with('user')->get();
        return view('project.people', compact('uniqeAgents','uniqueTotalInvestors','investorList', 'bookings', 'agents', 'agentList', 'project', 'remainingUnit'));
    }


    public function finance($id)
    {
        $project = Project::with('details')->findOrFail($id);
        $costs = Projectcost::where('project_id', $id)->get();
        // dd($costs);
        $totalCost = $costs->sum('cost');

        $remainingUnit = $project->details->unit - $project->details->booked_unit;
        $bookings = Booking::with('investor.project.details')->where('project_id', $id)->get();
        $uniqueTotalInvestors = $bookings->unique('investor_id')->count();
        $agents = Projectagent::with('user')->where('project_id', $id)->get();
        $uniqeAgents = $agents->unique('user_id')->count();

        return view('project.cost', compact('project', 'costs', 'totalCost', 'remainingUnit', 'uniqeAgents', 'uniqueTotalInvestors', 'bookings', 'agents'));
    }

    public function financeStore(Request $request)
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

    public function start($id)
    {
        $project = Project::findOrFail($id);

        // Update the project status to "started"
        $project->update(['status' => 2, 'start_date' => now()]);

        return redirect()->back()->with('success', 'Project started successfully.');
    }

    public function close(Request $request)
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

        $maturityDate = now()->format('Y-m-d H:i:s');

        // Update the project details
        Projectdetail::where('project_id', $request->project_id)->update([
            'closing_amount' => $request->closing_amount,
            'service_charge' => $request->service_charge,
            'remarks' => $request->remarks,
            'voucher_file' => $imageFileName,
            'maturity_date' => $maturityDate,
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

    public function printFinanceDetails ($id)
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

}
