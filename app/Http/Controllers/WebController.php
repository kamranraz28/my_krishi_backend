<?php

namespace App\Http\Controllers;

use App\Events\BankPaymentCancel;
use App\Events\BankPaymentConfirm;
use App\Events\OfficePaymentCancel;
use App\Events\OfficePaymentConfirm;
use App\Events\ProjectClosed;
use App\Models\Bank;
use App\Models\Booking;
use App\Models\Comment;
use App\Models\Faq;
use App\Models\Investor;
use App\Models\Project;
use App\Models\Projectagent;
use App\Models\Projectcost;
use App\Models\Projectdetail;
use App\Models\Projectupdate;
use App\Models\Term;
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








    public function investorHistory($id)
    {
        $user = User::find($id);
        $bookings = Booking::with('project.details', 'investor')->where('investor_id', $id)->get();

        return view('investor.history', compact('bookings', 'user'));
    }






    public function pendingPayment()
    {
        $bookings = Booking::with('investor','project.details')->where('status',2)->where('payment_method',2)->get();

        return view('pendingPayment.office',compact('bookings'));
    }

    public function confirmOfficePayment($id)
    {
        $booking = Booking::with('investor', 'project.details')->findOrFail($id);

        // Update the booking status
        $booking->update([
            'status' => 5
        ]);

        event(new OfficePaymentConfirm($booking));

        return redirect()->back()->with('success','Booking confirmed successfully.');
    }

    public function cancelOfficePayment($id)
    {
        $booking = Booking::with('investor', 'project.details')->findOrFail($id);

        // Update the booking status
        $booking->update([
            'status' => 7
        ]);

        // Fire the event
        event(new OfficePaymentCancel($booking));

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

    public function confirmBankPayment($id)
    {
        $booking = Booking::with('investor', 'project.details')->findOrFail($id);

        // Update the booking status
        $booking->update([
            'status' => 5
        ]);

        event(new BankPaymentConfirm($booking));

        return redirect()->back()->with('success','Booking confirmed successfully.');
    }

    public function cancelBankPayment($id)
    {
        $booking = Booking::with('investor', 'project.details')->findOrFail($id);

        // Update the booking status
        $booking->update([
            'status' => 7
        ]);

        // Fire the event
        event(new BankPaymentCancel($booking));

        // Decrement booked units
        Projectdetail::where('project_id', $booking->project_id)
            ->decrement('booked_unit', $booking->total_unit);

        return redirect()->back()->with('success', 'Booking canceled successfully.');
    }






    public function addFAQ($id)
    {
        $project = Project::with('details')->findOrFail($id);
        return view('faq.index',compact('project'));
    }

    public function storeFAQ(Request $request)
    {
        $request->validate([
            'question' => 'required|array',
            'answer' => 'required|array',
            'question.*' => 'string|max:255',
            'answer.*' => 'string|max:1000',
        ]);
        //dd($request->all());
        $project_id = $request->project_id;
        $questions = $request->question;
        $answers = $request->answer;

        foreach ($questions as $index => $question) {
            Faq::create([
                'project_id' => $project_id,
                'question' => $question,
                'answer' => $answers[$index] ?? null,
            ]);
        }
        return redirect()->redirect()->back()->with('success', 'FAQ added successfully.');
    }

    public function editFAQ($id)
    {
        $faq = Faq::with('project.details')->findOrFail($id);
        return view('faq.edit',compact('faq'));
    }

    public function updateFAQ(Request $request, $id)
    {
        $request->validate([
            'question' => 'required|string|max:255',
            'answer' => 'required|string',
        ]);

        $faq = Faq::findOrFail($id);

        $faq->update([
            'question' => $request->question,
            'answer' => $request->answer,
        ]);

        return redirect()->route('addFAQ', $faq->project_id)
            ->with('success', 'FAQ updated successfully.');
    }

    public function deleteFAQ($id)
    {
        $faq = Faq::findOrFail($id);

        $faq->delete();

        return redirect()->back()->with('success', 'FAQ deleted successfully.');


    }






}
