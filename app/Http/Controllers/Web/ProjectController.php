<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Investor;
use App\Models\Project;
use App\Models\Projectagent;
use App\Models\Projectdetail;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Pagination\Paginator;
use App\Models\Term;

class ProjectController extends Controller
{
    //
    public function index ()
    {
        //$this->projectRepository->debugProjectCache();
        $status = Session::get('status');

        $projects = Project::with('details')->get();

        if ($status) {
            $projects = $projects->where('status', $status);
        }

        // Sort by ID in descending order
        $projects = $projects->sortByDesc('id');

        // Paginate the sorted data
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $perPage = 9;
        $currentItems = $projects->slice(($currentPage - 1) * $perPage, $perPage)->all();

        // Create LengthAwarePaginator instance
        $projects = new LengthAwarePaginator(
            $currentItems,
            $projects->count(),
            $perPage,
            $currentPage,
            ['path' => Paginator::resolveCurrentPath()]
        );

        $terms = Term::all();

        return view('project.index', compact('projects','terms'));
    }

    public function store(Request $request)
    {
        //dd($request->all());
        $user = Auth::user();


        // Create the project
        $project = Project::create([
            'created_by' => $user->id,
        ]);

        // Generate unique_id using user ID (e.g., AG01, AG02)
        $project->update([
            'unique_id' => 'MKPR' . str_pad($project->id, 2, '0', STR_PAD_LEFT),
        ]);


        $fileName = null;
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/projects'), $fileName);
        }

        // Store project details
        $projectDetail = Projectdetail::create([
            'project_id' => $project->id,
            'title' => $request->title,
            'total_price' => $request->total_price,
            'unit_price' => $request->unit_price,
            'unit' => $request->unit,
            'location' => $request->location,
            'location_map' => $request->location_map,
            'description' => $request->description,
            'image' => $fileName,
            'youtube_video' => $request->youtube_video,
            'duration' => $request->duration,
            'return_amount' => $request->return_amount
        ]);

        return redirect()->back()->with('success', 'Project created successfully.');

    }


    public function edit($id)
    {
        $project = Project::with('details')->findOrFail($id);
        $remainingUnit = $project->details->unit - $project->details->booked_unit;
        $bookings = Booking::with('investor.project.details')->where('project_id', $id)->get();
        $uniqueTotalInvestors = $bookings->unique('investor_id')->count();
        $agents = Projectagent::with('user')->where('project_id', $id)->get();
        $uniqeAgents = $agents->unique('user_id')->count();

        return view('project.edit', compact('remainingUnit','project','uniqeAgents','uniqueTotalInvestors','bookings', 'agents'));
    }

    public function update(Request $request, $id)
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
            'youtube_video' => $request->youtube_video,
            'location' => $request->location,
            'description' => $request->description,
            'duration' => $request->duration,
            'return_amount' => $request->return_amount,
            'location_map' => $request->location_map,
        ]);

        if ($request->hasFile('image')) {
            $projectDetail->save();
        }


        return redirect()->route('projects')->with('success', 'Project updated successfully');
    }

    public function people($id)
    {
        $project = Project::with('details')->findOrFail($id);
        $remainingUnit = $project->details->unit - $project->details->booked_unit;
        $bookings = Booking::with('investor.project.details')->where('project_id', $id)->get();
        $uniqueTotalInvestors = $bookings->unique('investor_id')->count();
        $agents = Projectagent::with('user')->where('project_id', $id)->get();
        $uniqeAgents = $agents->unique('user_id')->count();
        $agentList = User::where('level', 300)->get();
        $investorList = Investor::with('user')->get();
        return view('projectPeople', compact('uniqeAgents','uniqueTotalInvestors','investorList', 'bookings', 'agents', 'agentList', 'project', 'remainingUnit'));
    }

}
