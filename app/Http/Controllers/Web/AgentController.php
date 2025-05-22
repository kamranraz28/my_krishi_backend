<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class AgentController extends Controller
{
    //
    public function index()
    {
        $status = Session::get('status');
        $agentQuery = User::where('level', 300);

        if ($status) {
            $agentQuery->where('status', $status);
        }else {
            $agentQuery->where('status', '!=', 2);
        }
        $agents = $agentQuery->orderBy('id', 'desc')->get();

        return view('agent.index', compact('agents'));
    }

    public function filter(Request $request)
    {
        $status = $request->status;

        Session::put('status', $status);
        return redirect()->route('agents.index');
    }

    public function create()
    {
        return view('agent.create');
    }

    public function store(Request $request)
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

        return redirect()->route('agents.index')->with('success', 'Agent added successfully.');
    }

    public function suspend($id)
    {

        //dd($id);
        $agent = User::find($id);
        //dd($agent);

        if ($agent) {
            $agent->update(['status' => 2]);
            return redirect()->back()->with('success', 'Agent suspended successfully.');
        }

        return redirect()->back()->with('error', 'Agent not found.');
    }

    public function activate($id)
    {

        //dd($id);
        $agent = User::find($id);
        //dd($agent);

        if ($agent) {
            $agent->update(['status' => 1]);
            return redirect()->back()->with('success', 'Agent activated successfully.');
        }

        return redirect()->back()->with('error', 'Agent not found.');
    }
}
