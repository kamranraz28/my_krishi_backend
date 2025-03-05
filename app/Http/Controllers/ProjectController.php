<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\Project;
use App\Models\Projectagent;
use App\Models\Projectdetail;
use App\Models\User;
use Auth;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    //
    public function projectList()
    {
        $projects = Project::with('details')->get();

        return response()->json([
            'status' => 'success',
            'message' => 'projects showing successfull.',
            'project' => $projects
        ], 200);
    }

    public function projectDetails($id)
    {
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


    public function createProject()
    {
        $user = Auth::user();

        if ($user->level !== 100) {
            return response()->json([
                'status' => 'error',
                'message' => 'you are not eligible to do this,'
            ], 401);
        } else {
            return response()->json([
                'status' => 'success',
                'message' => 'project created page showing successfull.'
            ], 200);
        }

    }

    public function storeProject(Request $request)
    {
        $user = Auth::user();

        if ($user->level !== 100) {
            return response()->json([
                'status' => 'error',
                'message' => 'You are not eligible to do this.'
            ], 401);
        }


        try {
            // Create the project
            $project = Project::create([
                'created_by' => $user->id,
                'status' => $request->status
            ]);
        } catch (\Exception $e) {
            \Log::error('Project creation failed: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while creating the project.'
            ], 500);
        }

        // Handle image upload
        $fileName = null;
        if ($request->hasFile('image')) {
            try {
                $file = $request->file('image');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('uploads/projects'), $fileName);
            } catch (\Exception $e) {
                \Log::error('File upload failed: ' . $e->getMessage());
                return response()->json([
                    'status' => 'error',
                    'message' => 'Failed to upload image.'
                ], 500);
            }
        }

        try {
            // Ensure $project->id exists before inserting details
            Projectdetail::create([
                'project_id' => $project->id,
                'title' => $request->title,
                'total_price' => $request->total_price,
                'unit_price' => $request->unit_price,
                'unit' => $request->unit,
                'location' => $request->location,
                'description' => $request->description,
                'image' => $fileName,
                'duration' => $request->duration,
                'return_amount' => $request->return_amount
            ]);
        } catch (\Exception $e) {
            \Log::error('Project details creation failed: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while saving project details.'
            ], 500);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Project creation successful.'
        ], 200);
    }


    public function messageStore(Request $request)
    {
        Message::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'message' => $request->message,
            'project_id' => $request->project_id
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'message sent!'
        ], 200);
    }

    public function agentMapping()
    {
        $user = Auth::user();

        if ($user->level !== 100) {
            return response()->json([
                'status' => 'error',
                'message' => 'You are not eligible to do this.'
            ], 401);
        }
        $projects = Project::with('details')->get();
        $agents = User::where('level', 300)->get();

        return response()->json([
            'status' => 'success',
            'message' => 'Mapping page showing successfully.',
            'projects' => $projects,
            'agents' => $agents
        ], 200);
    }

    public function agentMappingConfirm(Request $request)
    {
        $user = Auth::user();

        if ($user->level !== 100) {
            return response()->json([
                'status' => 'error',
                'message' => 'You are not eligible to do this.'
            ], 401);
        }

        Projectagent::create([
            'project_id' => $request->project_id,
            'agent_id' => $request->agent_id
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Project-Agent mapping successfull.'
        ], 200);

    }


}
