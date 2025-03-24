<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Comment;
use App\Models\Project;
use App\Models\Projectagent;
use App\Models\Projectcost;
use App\Models\Projectdetail;
use App\Models\Projectupdate;
use App\Models\User;
use App\Repositories\UserRepository;
use Auth;
use Hash;
use Illuminate\Http\Request;
use Mpdf\Mpdf;
use Session;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Cache;
use App\Repositories\ProjectRepository;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;


class WebController extends Controller
{
    //
    protected $projectRepository;
    protected $userRepository;
    public function __construct(ProjectRepository $projectRepository, UserRepository $userRepository)
    {
        $this->projectRepository = $projectRepository;
        $this->userRepository = $userRepository;
    }

    public function refreshProjectCache()
    {
        // Clear existing project cache
        $this->projectRepository->forgetAllProjectsCache();
        $this->userRepository->forgetAllAgentsCache();

        // Re-cache all projects
        $this->projectRepository->cacheAllProjects();
        $this->userRepository->cacheAgent();

        return response()->json([
            'message' => 'Project cache refreshed successfully!',
            'projects' => $this->projectRepository->getAllCachedProjects(),
            'agents' => $this->userRepository->cacheAgent(),
        ]);
    }
    public function userLogin(Request $request)
    {
        if (!Cache::has('agents')) {
            $this->userRepository->cacheAgent();
        }

        //$this->userRepository->debugAgentCache();

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

    public function projects()
    {
        // Cache all projects if not already cached

        if (!Cache::has('all_projects')) {
            $this->projectRepository->cacheAllProjects();
        }

        //$this->projectRepository->debugProjectCache();
        $status = Session::get('status');

        // Fetch cached data
        $projects = $this->projectRepository->getAllCachedProjects();


        // Filter and sort the cached data manually
        $projects = collect($projects);

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

        return view('projectList', compact('projects'));
    }


    public function projectFilter(Request $request)
    {
        $status = $request->status;
        Session::put('status', $status);

        return redirect()->route('projects');
    }

    public function projectUpdates($id)
    {
        $project = Project::with('details')->findOrFail($id);
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
        return view('projectUpdates', compact('projectUpdates', 'project'));
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
            'description' => $request->description,
            'image' => $fileName,
            'duration' => $request->duration,
            'return_amount' => $request->return_amount
        ]);

        // ✅ Attach details to the project object
        $project->setRelation('details', collect([$projectDetail]));

        // ✅ Add the new project to the existing cache
        $this->projectRepository->newProjectCache($project);

        return redirect()->back()->with('success', 'Project created successfully.');

    }

    public function projectEdit($id)
    {
        $project = Project::with('details')->findOrFail($id);

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
        $this->projectRepository->refreshProjectCache($id);

        return redirect()->route('projects')->with('success', 'Project updated successfully');
    }


    // public function projectPeople($id)
    // {
    //     $project = Project::with('details')->findOrFail($id);
    //     $bookings = Booking::with('investor.project.details')->where('project_id', $id)->get();
    //     $agents = Projectagent::with('user')->where('project_id', $id)->get();
    //     $agentList = User::where('level', 300)->get();
    //     $investorList = User::where('level', 200)->get();
    //     return view('projectPeople', compact('investorList', 'bookings', 'agents', 'agentList', 'project'));
    // }

    public function projectPeople($id)
    {
        // Fetch cached data
        $project = $this->projectRepository->cacheProject($id);
        $bookings = Booking::with('investor.project.details')->where('project_id', $id)->get();
        $agents = Projectagent::with('user')->where('project_id', $id)->get();
        $agentList = $this->userRepository->getAllCachedAgents();
        $investorList = User::where('level', 200)->get();


        return view('projectPeople', compact('project', 'bookings', 'agents', 'agentList', 'investorList'));
    }

    // public function projectPeople($id)
    // {
    //     // Cache the project details
    //     $project = Cache::remember("project_{$id}", 60, function () use ($id) {
    //         return Project::with('details')->findOrFail($id);
    //     });

    //     // Cache the bookings related to the project
    //     $bookings = Cache::remember("bookings_project_{$id}", 60, function () use ($id) {
    //         return Booking::with('investor.project.details')->where('project_id', $id)->get();
    //     });

    //     // Cache the agents related to the project
    //     $agents = Cache::remember("agents_project_{$id}", 60, function () use ($id) {
    //         return Projectagent::with('user')->where('project_id', $id)->get();
    //     });

    //     // Cache the agent list
    //     $agentList = Cache::remember("agent_list_project_{$id}", 60, function () {
    //         return User::where('level', 300)->get();
    //     });

    //     // Cache the investor list
    //     $investorList = Cache::remember("investor_list_project_{$id}", 60, function () {
    //         return User::where('level', 200)->get();
    //     });

    //     // Debugging cached data using Cache::get()
    //     $cachedProject = Cache::get("project_{$id}");
    //     $cachedBookings = Cache::get("bookings_project_{$id}");
    //     $cachedAgents = Cache::get("agents_project_{$id}");
    //     $cachedAgentList = Cache::get("agent_list_project_{$id}");
    //     $cachedInvestorList = Cache::get("investor_list_project_{$id}");

    //     // You can dump the cache data to check
    //     dd($cachedProject, $cachedBookings, $cachedAgents, $cachedAgentList, $cachedInvestorList);

    //     return view('projectPeople', compact('investorList', 'bookings', 'agents', 'agentList', 'project'));
    // }


    public function assignAgent()
    {
        $data = request()->validate([
            'project_id' => 'required',
            'agent_id' => 'required'
        ]);
        //dd($data);

        Projectagent::create($data);

        return redirect()->back()->with('success', 'Agent assigned successfully.');
    }

    public function deleteAgent($id)
    {
        $agent = ProjectAgent::find($id);

        if ($agent) {
            $agent->delete();
            return redirect()->back()->with('success', 'Agent removed successfully.');
        }

        return redirect()->back()->with('error', 'Agent not found.');
    }

    public function assignInvestor(Request $request)
    {

        Booking::create([
            'project_id' => $request->project_id,
            'investor_id' => $request->investor_id,
            'total_unit' => $request->unit,
        ]);

        return redirect()->back()->with('success', 'Investor added successfully.');
    }

    public function agents()
    {
        $agents = $this->userRepository->getAllCachedAgents();

        return view('agent.index', compact('agents'));
    }

    public function agentDelete($id)
    {
        $agent = User::find($id);

        if ($agent) {
            $agent->delete();
            return redirect()->back()->with('success', 'Agent deleted successfully.');
        }

        return redirect()->back()->with('error', 'Agent not found.');
    }

    public function agentStore(Request $request)
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
        // ✅ Add the new project to the existing cache
        $this->userRepository->newAgentCache($user);

        return redirect()->back()->with('success', 'Agent added successfully.');
    }


    public function investors()
    {
        $investors = User::with('booking.project.details')->where('level', 200)->get();

        return view('investor.index', compact('investors'));
    }

    public function investorDelete($id)
    {
        $investor = User::find($id);

        if ($investor) {
            $investor->delete();
            return redirect()->back()->with('success', 'Investor deleted successfully.');
        }

        return redirect()->back()->with('error', 'Investor not found.');
    }

    public function investorStore(Request $request)
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'level' => 200,
            'phone' => $request->phone,
            'password' => Hash::make(12345678),
        ]);

        // Generate unique_id using user ID (e.g., AG01, AG02)
        $user->update([
            'unique_id' => 'MKIN' . str_pad($user->id, 2, '0', STR_PAD_LEFT),
        ]);

        return redirect()->back()->with('success', 'Investor added successfully.');
    }

    public function investorHistory($id)
    {
        $user = User::find($id);
        $bookings = Booking::with('project.details', 'investor')->where('investor_id', $id)->get();

        return view('investor.history', compact('bookings', 'user'));
    }

    public function projectCosts($id)
    {
        $project = Project::with('details')->findOrFail($id);
        $costs = Projectcost::where('project_id', $id)->get();
        // dd($costs);
        $totalCost = $costs->sum('cost');

        return view('project.cost', compact('project', 'costs', 'totalCost'));
    }

    public function projectCostsStore(Request $request)
    {
        //dd($request->all());
        // Validate request
        $request->validate([
            'reason' => 'required|array',
            'cost' => 'required|array',
            'reason.*' => 'string|max:255',
            'cost.*' => 'numeric|min:0',
        ]);

        $costFields = $request->reason;
        $costValues = $request->cost;
        //dd($costFields, $costValues);

        foreach ($costFields as $index => $field) {
            Projectcost::create([
                'project_id' => $request->project_id,
                'field' => $field,
                'cost' => $costValues[$index] ?? 0,
            ]);
        }

        return redirect()->back()->with('success', 'Costs added successfully.');

    }


    public function projectClose(Request $request)
    {
        $project = Project::findOrFail($request->project_id);

        $project->update([
            'status' => 5,
        ]);

        Projectdetail::where('project_id', $request->project_id)->update([
            'closing_amount' => $request->closing_amount,
            'service_charge' => $request->service_charge,
        ]);

        return redirect()->back()->with('success', 'Project closed successfully.');
    }


    public function financeDetails($id)
    {
        $project = Project::with('details')->findOrFail($id);
        $costs = Projectcost::where('project_id', $id)->get();
        // dd($costs);

        // Calculate the total cost of the project
        $totalCost = $costs->sum('cost') ?? 0;

        // Get the revenue from project details
        $revenue = $project->details->closing_amount ?? 0;

        // Calculate profit (Revenue - Total Cost)
        $profit = $revenue - $totalCost;

        // Get the service charge as a percentage
        $serviceChargePercent = $project->details->service_charge ?? 0;

        // Calculate service charge in value
        $serviceChargeValue = ($profit * $serviceChargePercent) / 100;

        // Calculate net profit (Profit - Service Charge)
        $netProfit = $profit - $serviceChargeValue;

        // Get the total unit
        $unit = $project->details->unit ?? 0;

        $profitPerUnit = $netProfit / $unit;

        // Pass values to the view
        return view('project.finance', compact('unit', 'profitPerUnit', 'profit', 'revenue', 'serviceChargePercent', 'serviceChargeValue', 'netProfit', 'project', 'costs', 'totalCost'));
    }

    public function printFinanceDetails($id)
    {
        $project = Project::with('details')->findOrFail($id);
        $costs = Projectcost::where('project_id', $id)->get();
        // dd($costs);

        // Calculate the total cost of the project
        $totalCost = $costs->sum('cost') ?? 0;

        // Get the revenue from project details
        $revenue = $project->details->closing_amount ?? 0;

        // Calculate profit (Revenue - Total Cost)
        $profit = $revenue - $totalCost;

        // Get the service charge as a percentage
        $serviceChargePercent = $project->details->service_charge ?? 0;

        // Calculate service charge in value
        $serviceChargeValue = ($profit * $serviceChargePercent) / 100;

        // Calculate net profit (Profit - Service Charge)
        $netProfit = $profit - $serviceChargeValue;

        // Get the total unit
        $unit = $project->details->unit ?? 0;

        $profitPerUnit = $netProfit / $unit;

        // Render the HTML content from the view
        $html = view('project.pdf-finance-details', compact('costs','profit', 'revenue', 'serviceChargePercent', 'netProfit', 'unit', 'profitPerUnit', 'project', 'totalCost'))->render();

        // Initialize mPDF
        $mpdf = new Mpdf();

        // Write the HTML content to the PDF
        $mpdf->WriteHTML($html);

        // Stream the PDF in the browser (inline view)
        $mpdf->Output('finance_details_' . $project->details->title . '.pdf', 'I');  // This streams the PDF in the browser

        // If you want to download the PDF afterward, you can use the following code
        // $mpdf->Output('finance_details_' . $project->id . '.pdf', 'D'); // This would trigger the download
    }



    public function getCacheSize()
    {
        // Get all cached keys
        $cacheKeys = ['all_projects','agents']; // Add more keys if needed

        $totalSize = 0;
        $cacheData = [];

        foreach ($cacheKeys as $key) {
            if (Cache::has($key)) {
                $cachedItem = Cache::get($key);
                $size = mb_strlen(serialize($cachedItem), '8bit'); // Get size in bytes
                $totalSize += $size;
                $cacheData[$key] = $size . ' bytes';
            }
        }

        return response()->json([
            'total_size_bytes' => $totalSize,
            'total_size_kb' => round($totalSize / 1024, 2) . ' KB',
            'cached_items' => $cacheData,
        ]);
    }



}
