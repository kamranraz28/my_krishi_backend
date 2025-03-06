<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Projectagent;
use App\Models\Projectupdate;
use App\Models\Reply;
use Auth;
use Illuminate\Http\Request;

class AgentController extends Controller
{
    //
    public function projectList()
    {
        $user = Auth::user();

        if ($user->level !== 300) {
            return response()->json([
                'status' => 'error',
                'message' => 'You are not eligible to do this.'
            ], 401);
        }

        $projects = Projectagent::with('project.details')->where('agent_id', $user->id)->get();

        return response()->json([
            'status' => 'success',
            'message' => 'projects are showing',
            'projects' => $projects
        ]);
    }

    public function projectUpdateStore(Request $request, $id)
    {
        $user = Auth::user();
        $fileNames = []; // Array to store multiple image paths

        // Check if any file is received
        if (!$request->hasFile('image')) {
            \Log::error('No files received in request.');
            return response()->json([
                'status' => 'error',
                'message' => 'No files uploaded.'
            ], 400);
        }

        try {
            foreach ($request->file('image') as $file) {
                if ($file->isValid()) {
                    $fileName = $id . '_' . time() . '_' . $file->getClientOriginalName();
                    $file->move(public_path('uploads/projectUpdates'), $fileName);
                    $fileNames[] = 'uploads/projectUpdates/' . $fileName;
                } else {
                    \Log::error('Invalid file detected.');
                }
            }
        } catch (\Exception $e) {
            \Log::error('File upload failed: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to upload images.'
            ], 500);
        }

        // If no images were processed successfully
        if (empty($fileNames)) {
            \Log::error('No valid images were saved.');
            return response()->json([
                'status' => 'error',
                'message' => 'No valid images were uploaded.'
            ], 400);
        }

        // Store multiple image paths as JSON
        Projectupdate::create([
            'project_id' => $id,
            'update_by' => $user->id,
            'image' => json_encode($fileNames),
            'description' => $request->description,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Project update stored successfully.',
            'uploaded_images' => $fileNames
        ]);
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
