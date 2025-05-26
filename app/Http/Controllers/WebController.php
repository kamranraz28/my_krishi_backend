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







}
