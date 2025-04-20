<?php

namespace App\Http\Controllers;

use App\Http\Resources\AgentResource;
use App\Http\Resources\CommentResource;
use App\Http\Resources\ProjectUpdateResource;
use App\Http\Resources\ReplyResource;
use App\Models\Comment;
use App\Models\Notification;
use App\Models\Project;
use App\Models\Projectagent;
use App\Models\Projectupdate;
use App\Models\Reply;
use App\Models\User;
use Auth;
use Illuminate\Http\Request;

class AgentController extends Controller
{
    // Fetch the list of projects assigned to the authenticated agent
    public function projectList()
    {
        $user = Auth::user();

        // Check if the user has the required access level
        if ($user->level !== 300) {
            return response()->json([
                'status' => 'error',
                'message' => 'You are not eligible to do this.'
            ], 401);
        }

        // Fetch projects assigned to the agent with related details
        $projects = Projectagent::with('project.details')->where('agent_id', $user->id)->get();

        // Return the list of projects as a JSON response
        return response()->json([
            'status' => 'success',
            'message' => 'projects are showing',
            'projects' => AgentResource::collection($projects)
        ]);
    }

    // Store a project update with multiple image uploads
    public function projectUpdateStore(Request $request, $id)
    {
        $user = Auth::user();
        $fileNames = []; // Array to store multiple image paths

        // Check if any file is received in the request
        if (!$request->hasFile('image')) {
            \Log::error('No files received in request.');
            return response()->json([
                'status' => 'error',
                'message' => 'No files uploaded.'
            ], 400);
        }

        try {
            // Process each uploaded file
            foreach ($request->file('image') as $file) {
                if ($file->isValid()) {
                    // Generate a unique file name and move the file to the uploads directory
                    $fileName = $id . '_' . time() . '_' . $file->getClientOriginalName();
                    $file->move(public_path('uploads/projectUpdates'), $fileName);
                    $fileNames[] = 'uploads/projectUpdates/' . $fileName;
                } else {
                    \Log::error('Invalid file detected.');
                }
            }
        } catch (\Exception $e) {
            // Log and return an error response if file upload fails
            \Log::error('File upload failed: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to upload images.'
            ], 500);
        }

        // If no valid images were uploaded
        if (empty($fileNames)) {
            \Log::error('No valid images were saved.');
            return response()->json([
                'status' => 'error',
                'message' => 'No valid images were uploaded.'
            ], 400);
        }

        // Store the project update with image paths and description
        Projectupdate::create([
            'project_id' => $id,
            'update_by' => $user->id,
            'image' => json_encode($fileNames), // Store image paths as JSON
            'description' => $request->description,
        ]);

        // Get the project title
        $project = Project::with('details')->findOrFail($id);
        $projectTitle = $project->details->title;

        // Create meaningful notification message
        $message = "{$projectTitle}: An agent has created a new post.";

        $notification = Notification::create([
            'project_id' => $id,
            'created_by' => $user->id,
            'message' => $message,
        ]);

        // STEP: Assign notification to all investors who booked in this project
        $investors = User::whereHas('booking', function ($q) use ($id) {
            $q->where('project_id', $id);
        })->get();

        foreach ($investors as $investor) {
            $notification->users()->attach($investor->id);
        }

        // Return a success response
        return response()->json([
            'status' => 'success',
            'message' => 'Project update stored successfully.',
        ]);
    }

    // Fetch all updates for a specific project
    public function projectUpdate($id)
    {
        // Fetch project updates along with related comments, replies, and users
        $projectUpdates = Projectupdate::with([
            'user',
            'comment.user',
            'comment.reply.user',
        ])->where('project_id', $id)->get();

        // Transform the updates to include full image URLs
        $projectUpdates->transform(function ($update) {
            // Decode JSON images (stored as a JSON string in the database)
            $images = json_decode($update->image, true) ?? [];

            // Generate full URLs for the images
            $update->image_urls = array_map(fn($path) => url($path), $images);

            return $update;
        });

        // Return the transformed updates as a JSON response
        return response()->json([
            'status' => 'success',
            'message' => 'Showing all updates',
            'updates' => ProjectUpdateResource::collection($projectUpdates)
        ], 200);
    }

    // Add a comment to a specific project update
    public function comment(Request $request, $id)
    {
        $user = Auth::user();

        // Create a new comment for the project update
        $comment = Comment::create([
            'projectupdate_id' => $id,
            'comment_by' => $user->id,
            'comment' => $request->comment
        ]);

        // Return the created comment as a JSON response
        return response()->json([
            'status' => 'success',
            'message' => 'Comment successfull',
            'bookings' => new CommentResource($comment)
        ], 200);
    }

    // Add a reply to a specific comment
    public function reply(Request $request, $id)
    {
        $user = Auth::user();

        // Create a new reply for the comment
        $reply = Reply::create([
            'comment_id' => $id,
            'replied_by' => $user->id,
            'reply' => $request->reply
        ]);

        // Return the created reply as a JSON response
        return response()->json([
            'status' => 'success',
            'message' => 'Reply successfull',
            'reply' => new ReplyResource($reply)
        ], 200);
    }
}
