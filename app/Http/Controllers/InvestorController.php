<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Comment;
use App\Models\Project;
use App\Models\Projectupdate;
use App\Models\Reply;
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

        try {
            Booking::create([
                'project_id' => $id,
                'investor_id' => $user->id,
                'total_unit' => $request->total_unit
            ]);
        } catch (\Exception $e) {
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

        $bookings = Booking::with('investor', 'project')->where('investor_id', $user->id)->get();

        return response()->json([
            'status' => 'success',
            'message' => 'Showing all bookings',
            'bookings' => $bookings
        ],200);

    }

    public function projectUpdate($id)
    {
        // Fetch project updates for the given project ID
        $projectUpdates = Projectupdate::with('comment.reply')->where('project_id', $id)->get();

        // Transform the project updates to format images correctly
        $projectUpdates->transform(function ($update) {
            // Decode JSON images
            $images = json_decode($update->image, true) ?? [];

            // Generate full URLs for images
            $update->image_urls = array_map(fn($path) => url($path), $images);

            return $update;
        });

        // Return the response with the updated project updates
        return response()->json([
            'status' => 'success',
            'message' => 'Showing all updates',
            'updates' => $projectUpdates
        ], 200);
    }


    public function comment(Request $request, $id)
    {
        $user = Auth::user();

        $comment = Comment::create([
            'projectupdate_id' => $id,
            'comment_by' => $user->id,
            'comment' => $request->comment
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Comment successfull',
            'bookings' => $comment
        ],200);

    }

    public function reply(Request $request, $id)
    {
        $user = Auth::user();

        $reply= Reply::create([
            'comment_id' => $id,
            'replied_by' => $user->id,
            'reply' => $request->reply
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Reply successfull',
            'reply' => $reply
        ],200);
    }



}
