<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Bank;
use App\Models\Booking;
use App\Models\Investor;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class InvestorController extends Controller
{
    //
    public function index()
    {
        $status = Session::get('status');
        $investorQuery = User::with('booking.project.details','investor')->where('level', 200);

        if ($status) {
            $investorQuery->where('status', $status);
        }else {
            $investorQuery->where('status', '!=', 2);
        }
        $investors = $investorQuery->orderBy('id', 'desc')->get();
        $banks = Bank::all();

        return view('investor.index', compact('investors', 'banks'));
    }

    public function filter(Request $request)
    {
        $status = $request->status;

        Session::put('status', $status);
        return redirect()->route('investors.index');
    }

    public function create()
    {
        $banks = Bank::all();
        return view('investor.create', compact('banks'));
    }

    public function store(Request $request)
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

        return redirect()->route('investors.index')->with('success', 'Investor added successfully.');
    }

    public function suspend($id)
    {

        //dd($id);
        $investor = User::find($id);
        //dd($investor);

        if ($investor) {
            $investor->update(['status' => 2]);
            return redirect()->back()->with('success', 'Investor suspended successfully.');
        }

        return redirect()->back()->with('error', 'Investor not found.');
    }

    public function activate($id)
    {

        //dd($id);
        $investor = User::find($id);
        //dd($investor);

        if ($investor) {
            $investor->update(['status' => 1]);
            return redirect()->back()->with('success', 'Investor activated successfully.');
        }

        return redirect()->back()->with('error', 'Investor not found.');
    }

    public function nid($id)
    {
        //dd(1);
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

    public function cheque($id)
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

    public function history($id)
    {
        $user = User::find($id);
        $bookings = Booking::with('project.details', 'investor')->where('investor_id', $id)->get();

        return view('investor.history', compact('bookings', 'user'));
    }


}
