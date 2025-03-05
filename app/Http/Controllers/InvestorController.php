<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Project;
use Auth;
use Illuminate\Http\Request;

class InvestorController extends Controller
{
    //
    public function projectList()
    {
        $user = Auth::user();

        if ($user->level !== 200) {
            return response()->json([
                'status' => 'error',
                'message' => 'You are not eligible to do this.'
            ], 401);
        }

        $projects = Project::with('details')->get();

        return response()->json([
            'status' => 'success',
            'message' => 'projects showing successfull.',
            'project' => $projects
        ], 200);
    }
    public function projectDetails($id)
    {
        $user = Auth::user();

        if ($user->level !== 200) {
            return response()->json([
                'status' => 'error',
                'message' => 'You are not eligible to do this.'
            ], 401);
        }
        // Fetch the project with the 'details' relationship using eager loading
        $details = Project::with('details')->find($id);

        // Check if the project exists
        if (!$details) {
            return response()->json([
                'status' => 'error',
                'message' => 'Project not found.',
            ], 404);
        }

        // Return the response with the project details
        return response()->json([
            'status' => 'success',
            'message' => 'Project details retrieved successfully.',
            'project' => $details
        ], 200);
    }

    public function projectBooking($id)
    {
        $user = Auth::user();

        if ($user->level !== 200) {
            return response()->json([
                'status' => 'error',
                'message' => 'You are not eligible to do this.'
            ], 401);
        }
        // Fetch the project with the 'details' relationship using eager loading
        $details = Project::with('details')->find($id);

        // Check if the project exists
        if (!$details) {
            return response()->json([
                'status' => 'error',
                'message' => 'Project not found.',
            ], 404);
        }

        // Return the response with the project details
        return response()->json([
            'status' => 'success',
            'message' => 'Project details retrieved successfully.',
            'project' => $details
        ], 200);
    }

    public function confirmBooking(Request $request, $id)
    {
        $user = Auth::user();

        if ($user->level !== 200) {
            return response()->json([
                'status' => 'error',
                'message' => 'You are not eligible to do this.'
            ], 401);
        }

        try{
            Booking::create([
                'project_id' => $id,
                'investor_id' => $user->id,
                'total_unit' => $request->total_unit
            ]);
        }catch (\Exception $e) {
            \Log::error('Project booking failed: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while booking the project.'
            ], 500);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Project Booking Successfull.'
        ], 200);

    }

    public function myBookings()
    {
        $user = Auth::user();

        if ($user->level !== 200) {
            return response()->json([
                'status' => 'error',
                'message' => 'You are not eligible to do this.'
            ], 401);
        }

        $bookings = Booking::with('investor','project')->where('investor_id',$user->id)->get();

        return response()->json([
            'status' => 'success',
            'message' => 'Showing all bookings',
            'bookings' => $bookings
        ]);

    }

    public function projectUpdate($id)
    {
        $user = Auth::user();


    }


}
