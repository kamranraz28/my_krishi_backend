<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Cart;
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

        $projects = Project::with('details','cart')->get();

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
        $details = Project::with('details','cart')->find($id);

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

    public function addToCart(Request $request, $id)
    {
        $user = Auth::user();

        if ($user->level !== 200) {
            return response()->json([
                'status' => 'error',
                'message' => 'You are not eligible to do this.'
            ], 401);
        }

        try {
            Cart::create([
                'project_id' => $id,
                'investor_id' => $user->id,
                'unit' => $request->total_unit
            ]);
        } catch (\Exception $e) {
            \Log::error('Add To Cart failed: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while adding the project to the cart.'
            ], 500);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Add to cart Successfull.'
        ], 200);

    }

    public function cartList()
    {
        $user = Auth::user();

        if ($user->level !== 200) {
            return response()->json([
                'status' => 'error',
                'message' => 'You are not eligible to do this.'
            ], 401);
        }

        $carts = Cart::with('project.details')->where('investor_id', $user->id)->get();

        return response()->json([
            'status' => 'success',
            'message' => 'Showing all carts',
            'carts' => $carts
        ],200);
    }

    public function cartConfirm(Request $request)
    {
        $user = Auth::user();

        if ($user->level !== 200) {
            return response()->json([
                'status' => 'error',
                'message' => 'You are not eligible to do this.'
            ], 401);
        }

        $projects = $request->project_id;
        $units = $request->unit;

        foreach ($projects as $key => $project) {
            Booking::create([
                'project_id' => $project,
                'investor_id' => $user->id,
                'total_unit' => $units[$key]
            ]);
        }
        // Delete the user's cart after booking
        Cart::where('investor_id', $user->id)->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Booking successfull'
        ],200);
    }

    public function removeFromCart(Request $request)
    {
        $user = Auth::user();

        if ($user->level !== 200) {
            return response()->json([
                'status' => 'error',
                'message' => 'You are not eligible to do this.'
            ], 401);
        }

        $ids = $request->cart_id;

        // Ensure $ids is an array
        if (!is_array($ids) || empty($ids)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid cart IDs.'
            ], 400);
        }

        // Delete the retrieved carts
        Cart::whereIn('id', $ids)->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Carts removed successfully.'
        ], 200);
    }


    public function cartEdit($id)
    {
        $user = Auth::user();

        if ($user->level !== 200) {
            return response()->json([
                'status' => 'error',
                'message' => 'You are not eligible to do this.'
            ], 401);
        }

        $cart = Cart::with('project.details')->find($id);

        if (!$cart) {
            return response()->json([
                'status' => 'error',
                'message' => 'Cart not found.'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Showing cart',
            'cart' => $cart
        ],200);
    }

    public function cartUpdate(Request $request, $id)
    {
        $user = Auth::user();

        if ($user->level !== 200) {
            return response()->json([
                'status' => 'error',
                'message' => 'You are not eligible to do this.'
            ], 401);
        }

        $cart = Cart::find($id);

        if (!$cart) {
            return response()->json([
                'status' => 'error',
                'message' => 'Cart not found.'
            ], 404);
        }

        $cart->update([
            'unit' => $request->unit
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Cart updated successfully.',
            'cart' => $cart
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

        $bookings = Booking::with('investor', 'project.details')->where('investor_id', $user->id)->get();

        return response()->json([
            'status' => 'success',
            'message' => 'Showing all bookings',
            'bookings' => $bookings
        ],200);

    }

    public function projectUpdate($id)
    {
        // Fetch project updates with comments, replies, users, and reactions
        $projectUpdates = Projectupdate::with([
            'user',
            'comment.user',
            'comment.reply.user',
            'reactions',  // Fetch reactions for updates
            'comment.reactions', // Fetch reactions for comments
            'comment.reply.reactions' // Fetch reactions for replies
        ])->where('project_id', $id)->get();

        // Transform the project updates
        $projectUpdates->transform(function ($update) {
            // Decode JSON images
            $images = json_decode($update->image, true) ?? [];
            $update->image_urls = array_map(fn($path) => url($path), $images);

            // Attach reaction summary for update
            $update->reaction_summary = $update->reactions()
                ->selectRaw('type, COUNT(*) as count')
                ->groupBy('type')
                ->pluck('count', 'type');

            // Transform comments
            $update->comment->transform(function ($comment) {
                // Attach reaction summary for comments
                $comment->reaction_summary = $comment->reactions()
                    ->selectRaw('type, COUNT(*) as count')
                    ->groupBy('type')
                    ->pluck('count', 'type');

                // Transform replies
                $comment->reply->transform(function ($reply) {
                    // Attach reaction summary for replies
                    $reply->reaction_summary = $reply->reactions()
                        ->selectRaw('type, COUNT(*) as count')
                        ->groupBy('type')
                        ->pluck('count', 'type');

                    return $reply;
                });

                return $comment;
            });

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
