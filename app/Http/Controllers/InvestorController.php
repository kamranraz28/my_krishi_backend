<?php

namespace App\Http\Controllers;

use App\Http\Resources\BookingResource;
use App\Http\Resources\CartResource;
use App\Http\Resources\CommentResource;
use App\Http\Resources\ProjectResource;
use App\Http\Resources\ProjectUpdateResource;
use App\Http\Resources\ReplyResource;
use App\Http\Resources\UserResource;
use App\Models\Booking;
use App\Models\Cart;
use App\Models\Comment;
use App\Models\Project;
use App\Models\Projectupdate;
use App\Models\Reply;
use App\Models\User;
use Auth;
use Illuminate\Http\Request;

class InvestorController extends Controller
{
    // Fetch the list of all projects for the authenticated investor
    public function projectList()
    {
        $user = Auth::user();

        // Ensure the user has the required access level
        if ($user->level !== 200) {
            return response()->json([
                'status' => 'error',
                'message' => 'You are not eligible to do this.'
            ], 401);
        }

        // Fetch all projects with their details
        $projects = Project::with('details')->get();

        // Return the list of projects as a JSON response
        return response()->json([
            'status' => 'success',
            'message' => 'Projects retrieved successfully.',
            'project' => ProjectResource::collection($projects)
        ], 200);
    }

    // Fetch the details of a specific project
    public function projectDetails($id)
    {
        $user = Auth::user();

        // Ensure the user has the required access level
        if ($user->level !== 200) {
            return response()->json([
                'status' => 'error',
                'message' => 'You are not eligible to do this.'
            ], 401);
        }

        // Fetch the project with its details
        $details = Project::with('details')->find($id);

        // Check if the project exists
        if (!$details) {
            return response()->json([
                'status' => 'error',
                'message' => 'Project not found.',
            ], 404);
        }

        // Return the project details as a JSON response
        return response()->json([
            'status' => 'success',
            'message' => 'Project details retrieved successfully.',
            'project' => new ProjectResource($details)
        ], 200);
    }

    // Add a project to the investor's cart
    public function addToCart(Request $request, $id)
    {
        $user = Auth::user();

        // Ensure the user has the required access level
        if ($user->level !== 200) {
            return response()->json([
                'status' => 'error',
                'message' => 'You are not eligible to do this.'
            ], 401);
        }

        try {
            // Add the project to the cart
            Cart::create([
                'project_id' => $id,
                'investor_id' => $user->id,
                'unit' => $request->total_unit
            ]);
        } catch (\Exception $e) {
            // Log the error and return a failure response
            \Log::error('Add To Cart failed: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while adding the project to the cart.'
            ], 500);
        }

        // Return a success response
        return response()->json([
            'status' => 'success',
            'message' => 'Added to cart successfully.'
        ], 200);
    }

    // Fetch the list of all items in the investor's cart
    public function cartList()
    {
        $user = Auth::user();

        // Ensure the user has the required access level
        if ($user->level !== 200) {
            return response()->json([
                'status' => 'error',
                'message' => 'You are not eligible to do this.'
            ], 401);
        }

        // Fetch all cart items with project details
        $carts = Cart::with('project.details')->where('investor_id', $user->id)->get();

        // Return the cart items as a JSON response
        return response()->json([
            'status' => 'success',
            'message' => 'Showing all carts.',
            'carts' => CartResource::collection($carts)
        ], 200);
    }

    // Confirm the cart and create bookings for the projects
    public function cartConfirm(Request $request)
    {
        $user = Auth::user();

        // Ensure the user has the required access level
        if ($user->level !== 200) {
            return response()->json([
                'status' => 'error',
                'message' => 'You are not eligible to do this.'
            ], 401);
        }

        $projects = $request->project_id; // Array of project IDs
        $units = $request->unit; // Array of units for each project

        // Create bookings for each project in the cart
        foreach ($projects as $key => $project) {
            Booking::create([
                'project_id' => $project,
                'investor_id' => $user->id,
                'total_unit' => $units[$key]
            ]);
        }

        // Clear the cart after confirming the bookings
        Cart::where('investor_id', $user->id)->delete();

        // Return a success response
        return response()->json([
            'status' => 'success',
            'message' => 'Booking successful.'
        ], 200);
    }

    // Remove specific items from the cart
    public function removeFromCart(Request $request)
    {
        $user = Auth::user();

        // Ensure the user has the required access level
        if ($user->level !== 200) {
            return response()->json([
                'status' => 'error',
                'message' => 'You are not eligible to do this.'
            ], 401);
        }

        $ids = $request->cart_id; // Array of cart item IDs to remove

        // Validate the input
        if (!is_array($ids) || empty($ids)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid cart IDs.'
            ], 400);
        }

        // Remove the specified cart items
        Cart::whereIn('id', $ids)->delete();

        // Return a success response
        return response()->json([
            'status' => 'success',
            'message' => 'Carts removed successfully.'
        ], 200);
    }

    // Fetch the list of all bookings for the investor
    public function myBookings()
    {
        $user = Auth::user();

        // Ensure the user has the required access level
        if ($user->level !== 200) {
            return response()->json([
                'status' => 'error',
                'message' => 'You are not eligible to do this.'
            ], 401);
        }

        // Fetch all bookings with project details
        $bookings = Booking::with('project.details')->where('investor_id', $user->id)->get();

        // Return the bookings as a JSON response
        return response()->json([
            'status' => 'success',
            'message' => 'Showing all bookings.',
            'bookings' => BookingResource::collection($bookings)
        ], 200);
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

        // Create a new comment
        $comment = Comment::create([
            'projectupdate_id' => $id,
            'comment_by' => $user->id,
            'comment' => $request->comment
        ]);

        // Return the created comment as a JSON response
        return response()->json([
            'status' => 'success',
            'message' => 'Comment added successfully.',
            'comment' => new CommentResource($comment)
        ], 200);
    }

    // Add a reply to a specific comment
    public function reply(Request $request, $id)
    {
        $user = Auth::user();

        // Create a new reply
        $reply = Reply::create([
            'comment_id' => $id,
            'replied_by' => $user->id,
            'reply' => $request->reply
        ]);

        // Return the created reply as a JSON response
        return response()->json([
            'status' => 'success',
            'message' => 'Reply added successfully.',
            'reply' => new ReplyResource($reply)
        ], 200);
    }

    // Update the profile of a specific user
    public function profileUpdate(Request $request, $id)
    {
        $user = User::find($id);

        // Check if the user exists
        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'User not found.'
            ], 404);
        }

        // Update the user's profile with the provided data
        $user->update($request->all());

        // Return the updated user profile as a JSON response
        return response()->json([
            'status' => 'success',
            'message' => 'Profile updated successfully.',
            'user' => new UserResource($user)
        ], 200);
    }
}
