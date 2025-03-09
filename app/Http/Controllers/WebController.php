<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Project;
use App\Models\Projectdetail;
use App\Models\Projectupdate;
use App\Models\User;
use Auth;
use Hash;
use Illuminate\Http\Request;

class WebController extends Controller
{
    //
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
        return view('dashboard');
    }

    public function userLogout(Request $request)
    {
        Auth::logout(); // Log out the user

        $request->session()->invalidate(); // Invalidate the session

        $request->session()->regenerateToken(); // Regenerate CSRF token

        return redirect(''); // Redirect to login page
    }

    public function projects()
    {
        $projects = Project::with('details')->orderBy('id', 'desc')->get();

        return view('projectList', compact('projects'));
    }

    public function projectUpdates($id)
    {
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
        return view('projectUpdates', compact('projectUpdates'));
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

    public function storeProject(Request $request)
    {
        //dd($request->all());
        $user = Auth::user();


        // Create the project
        $project = Project::create([
            'created_by' => $user->id,
        ]);

        $fileName = null;
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/projects'), $fileName);
        }

        // Store project details
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

        return redirect()->back()->with('success', 'Project created successfully.');

    }

    public function projectEdit ($id)
    {
        $project = Project::findOrFail($id);

        return view('projectEdit', compact('project'));
    }

    public function updateProject(Request $request, $id)
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
            'location' => $request->location,
            'description' => $request->description,
            'duration' => $request->duration,
            'return_amount' => $request->return_amount,
        ]);

        if ($request->hasFile('image')) {
            $projectDetail->save();
        }

        return redirect()->route('projects')->with('success', 'Project updated successfully');
    }




}
