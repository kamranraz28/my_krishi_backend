<?php

namespace App\Http\Controllers;

use App\Events\BankPayment;
use App\Events\OfficePayment;
use App\Http\Resources\BankResource;
use App\Http\Resources\BookingResource;
use App\Http\Resources\CartResource;
use App\Http\Resources\CommentResource;
use App\Http\Resources\NotificationResource;
use App\Http\Resources\ProjectResource;
use App\Http\Resources\ProjectUpdateResource;
use App\Http\Resources\ReplyResource;
use App\Http\Resources\UserResource;
use App\Models\Bank;
use App\Models\Booking;
use App\Models\Cart;
use App\Models\Comment;
use App\Models\Investor;
use App\Models\Project;
use App\Models\Projectdetail;
use App\Models\Projectupdate;
use App\Models\Reply;
use App\Models\User;
use Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

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
        $details = Project::with('details', 'faq')->find($id);

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

            $existingCart = Cart::where('project_id', $id)->where('investor_id', $user->id)->count();

            if ($existingCart > 0) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'This project is already to your cart'
                ], 400);
            }
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
    // public function cartConfirm(Request $request)
    // {
    //     $user = Auth::user();

    //     // Ensure the user has the required access level
    //     if ($user->level !== 200) {
    //         return response()->json([
    //             'status' => 'error',
    //             'message' => 'You are not eligible to do this.'
    //         ], 401);
    //     }

    //     $projects = $request->project_id; // Array of project IDs
    //     $units = $request->unit; // Array of units for each project

    //     // Create bookings for each project in the cart
    //     foreach ($projects as $key => $project) {
    //         Booking::create([
    //             'project_id' => $project,
    //             'investor_id' => $user->id,
    //             'total_unit' => $units[$key]
    //         ]);
    //     }

    //     // Clear the cart after confirming the bookings
    //     Cart::where('investor_id', $user->id)->delete();

    //     // Return a success response
    //     return response()->json([
    //         'status' => 'success',
    //         'message' => 'Booking successful.'
    //     ], 200);
    // }


    // public function cartConfirm(Request $request)
// {
//     $user = Auth::user();

    //     // Ensure the user has the required access level
//     if ($user->level !== 200) {
//         return response()->json([
//             'status' => 'error',
//             'message' => 'You are not eligible to do this.'
//         ], 401);
//     }

    //      // Step 1: Authenticate Shurjopay and get the token
//      $apiUrl = 'https://sandbox.shurjopayment.com/api/get_token';  // Shurjopay API URL

    //      // Send authentication request to Shurjopay API with JSON data
//      $response = Http::post($apiUrl, [
//          'username' => 'sp_sandbox',  // API Username
//          'password' => 'pyyk97hu&6u6' // API Password
//      ]);

    //     // Check if the authentication was successful
//     if ($response->successful()) {
//         // Check if the token exists in the response
//         $data = $response->json();

    //         if (isset($data['token'])) {
//             $token = $data['token'];  // Extract the token from the response

    //             // Log the token for debugging
//             Log::info('Shurjopay Token: ' . $token);



    //             // Return a success response
//             return response()->json([
//                 'status' => 'success',
//                 'message' => 'Booking successful.',
//                 'token' => $data // Return token for reference
//             ], 200);
//         } else {
//             // Log error if token is not found in the response
//             Log::error('Shurjopay Authentication response does not contain token: ' . $response->body());

    //             return response()->json([
//                 'status' => 'error',
//                 'message' => 'Shurjopay Authentication failed. Token not found.'
//             ], 400);
//         }
//     } else {
//         // Log error if authentication failed
//         Log::error('Shurjopay Authentication failed: ' . $response->body());

    //         return response()->json([
//             'status' => 'error',
//             'message' => 'Shurjopay Authentication failed.'
//         ], 400);
//     }
// }

    public function onlinePayment(Request $request)
    {
        $user = Auth::user();

        if ($user->level !== 200) {
            return response()->json([
                'status' => 'error',
                'message' => 'You are not eligible to do this.'
            ], 401);
        }

        // Step 1: Get Token
        $authResponse = Http::post('https://sandbox.shurjopayment.com/api/get_token', [
            'username' => 'sp_sandbox',
            'password' => 'pyyk97hu&6u6'
        ]);

        if (!$authResponse->successful()) {
            Log::error('Shurjopay Authentication failed: ' . $authResponse->body());
            return response()->json([
                'status' => 'error',
                'message' => 'Authentication failed'
            ], 400);
        }

        $authData = $authResponse->json();
        $token = $authData['token'];
        $store_id = $authData['store_id'] ?? '1';

        // Step 2: Prepare Payment Request
        $order_id = 'sp' . time(); // Unique Order ID
        $paymentUrl = 'https://sandbox.shurjopayment.com/api/secret-pay';


        $paymentResponse = Http::withToken($token)
            ->asMultipart()
            ->post($paymentUrl, [
                ['name' => 'prefix', 'contents' => 'sp'],
                ['name' => 'token', 'contents' => $token],
                // ['name' => 'return_url', 'contents' => 'https://sandbox.shurjopayment.com/response'],
                ['name' => 'return_url', 'contents' => route('api.shurjopay.response')],
                ['name' => 'cancel_url', 'contents' => route('api.shurjopay.response')],
                ['name' => 'store_id', 'contents' => $store_id],
                ['name' => 'amount', 'contents' => $request->amount], // You can replace with dynamic cart total
                ['name' => 'order_id', 'contents' => $order_id],
                ['name' => 'currency', 'contents' => 'BDT'],
                ['name' => 'customer_name', 'contents' => $user->name],
                ['name' => 'customer_address', 'contents' => 'Dhaka'],
                ['name' => 'customer_phone', 'contents' => '01700000000'], // Should be dynamic
                ['name' => 'customer_city', 'contents' => 'Dhaka'],
                ['name' => 'customer_post_code', 'contents' => '1212'],
                ['name' => 'client_ip', 'contents' => $request->ip()],
                ['name' => 'discount_amount', 'contents' => '10'],
                ['name' => 'disc_percent', 'contents' => '0'],
                ['name' => 'customer_email', 'contents' => $user->email],
                ['name' => 'customer_state', 'contents' => 'Dhaka'],
                ['name' => 'customer_country', 'contents' => 'BD'],
                ['name' => 'shipping_address', 'contents' => 'Test Shipping Address'],
                ['name' => 'shipping_city', 'contents' => 'Test City'],
                ['name' => 'shipping_country', 'contents' => 'Test Country'],
                ['name' => 'received_person_name', 'contents' => 'Jon Doe'],
                ['name' => 'shipping_phone_number', 'contents' => '01700000000'],
                ['name' => 'value1', 'contents' => 'Order Payment'],
                ['name' => 'value2', 'contents' => ''],
                ['name' => 'value3', 'contents' => ''],
                ['name' => 'value4', 'contents' => ''],
            ]);

        if ($paymentResponse->successful()) {
            $paymentData = $paymentResponse->json();

            // Optionally store the order with `order_id`, `sp_order_id`, etc.
            $projects = $request->project_id; // Array of project IDs
            $units = $request->unit; // Array of units for each project

            // Create bookings for each project in the cart
            foreach ($projects as $key => $project) {
                Booking::create([
                    'project_id' => $project,
                    'investor_id' => $user->id,
                    'total_unit' => $units[$key],
                    'transaction_id' => $paymentData['sp_order_id'],
                    'status' => 1,
                    'payment_method' => 1,
                ]);
            }

            // Clear the cart after confirming the bookings
            // Cart::where('investor_id', $user->id)->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Redirect to payment.',
                // 'checkout_url' => $paymentData['checkout_url'],
                'shurjopay_response' => $paymentData
            ], 200);
        } else {
            Log::error('Shurjopay Payment Initiation Failed: ' . $paymentResponse->body());

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to initiate payment.'
            ], 400);
        }
    }


    public function officePayment(Request $request)
    {
        $user = Auth::user();

        // Check if the user is eligible
        if ($user->level !== 200) {
            return response()->json([
                'status' => 'error',
                'message' => 'You are not eligible to do this.'
            ], 401);
        }

        $projects = $request->project_id; // Array of project IDs
        $units = $request->unit;          // Array of units for each project

        $now = now();
        $futureDateTime = $now->addHours(24)->format('Y-m-d H:i:s');

        // Collect all bookings to trigger event later
        $allBookings = collect();

        // Create bookings for each project
        foreach ($projects as $key => $project) {
            $bookingInfo = Booking::create([
                'project_id' => $project,
                'investor_id' => $user->id,
                'total_unit' => $units[$key],
                'status' => 2,
                'time_to_pay' => $futureDateTime,
                'payment_method' => 2,
            ]);

            // Load relationships
            $bookingInfo->load([
                'project.details',
                'investor'
            ]);

            // Increment projectdetail booked_unit
            Projectdetail::where('project_id', $project)
                ->increment('booked_unit', $units[$key]);

            // Add to booking collection
            $allBookings->push($bookingInfo);
        }

        // Clear the cart after confirming the bookings
        Cart::where('investor_id', $user->id)->delete();

        // Send email notification (fire event with all bookings)
        event(new OfficePayment($allBookings));

        return response()->json([
            'status' => 'success',
            'message' => 'Booking successful with office payment.'
        ], 200);
    }


    public function bankPayment(Request $request)
    {
        $user = Auth::user();

        if ($user->level !== 200) {
            return response()->json([
                'status' => 'error',
                'message' => 'You are not eligible to do this.'
            ], 401);
        }

        $projects = $request->project_id; // Array of project IDs
        $units = $request->unit; // Array of units for each project

        //Handle file upload (PDF or image)
        if ($request->hasFile('receipt')) {
            $file = $request->file('receipt');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/bank_receipts'), $filename);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Receipt file is required.'
            ], 422);
        }

        // Collect all bookings to trigger event later
        $allBookings = collect();

        // Create bookings for each project in the cart
        foreach ($projects as $key => $project) {
            $bookingInfo = Booking::create([
                'project_id' => $project,
                'investor_id' => $user->id,
                'total_unit' => $units[$key],
                'status' => 2,
                'bank_receipt' => $filename,
                'payment_method' => 3,
            ]);

            // Load relationships
            $bookingInfo->load([
                'project.details',
                'investor'
            ]);

            // Increment projectdetail booked_unit
            Projectdetail::where('project_id', $project)
                ->increment('booked_unit', $units[$key]);

            // Add to booking collection
            $allBookings->push($bookingInfo);
        }

        // Clear the cart after confirming the bookings
        Cart::where('investor_id', $user->id)->delete();

        // Send email notification (fire event with all bookings)
        event(new BankPayment($allBookings));

        return response()->json([
            'status' => 'success',
            'message' => 'booking successsful with bank payment.'
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
            'cart' => new CartResource($cart)
        ], 200);
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
            'cart' => new CartResource($cart)
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

    public function profileEdit()
    {

        $banks = Bank::all();

        // Return the user profile as a JSON response
        return response()->json([
            'status' => 'success',
            'message' => 'Showing bank details.',
            'banks' => BankResource::collection($banks),
        ], 200);
    }

    // Update the profile of a specific user
    public function profileUpdate(Request $request, $id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'User not found.'
            ], 404);
        }

        // Upload profile image
        $imageFileName = $user->image; // keep old if no new
        if ($request->hasFile('image')) {
            $imageFile = $request->file('image');
            $imageFileName = time() . '_' . $imageFile->getClientOriginalName();
            $imageFile->move(public_path('uploads/investors'), $imageFileName);
        }

        // Update user
        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'address' => $request->address,
            'image' => $imageFileName
        ]);

        // Upload NID
        $nidFileName = null;
        if ($request->hasFile('nid_upload')) {
            $nidFile = $request->file('nid_upload');
            $nidFileName = time() . '_' . $nidFile->getClientOriginalName();
            $nidFile->move(public_path('uploads/investors/nid'), $nidFileName);
        }

        // Upload Check
        $checkFileName = null;
        if ($request->hasFile('check_upload')) {
            $checkFile = $request->file('check_upload');
            $checkFileName = time() . '_' . $checkFile->getClientOriginalName();
            $checkFile->move(public_path('uploads/investors/blank_check'), $checkFileName);
        }

        // Update or Create Investor
        Investor::updateOrCreate(
            ['investor_id' => $user->id], // condition
            [
                'nid' => $request->nid,
                'nid_upload' => $nidFileName,
                'bank_id' => $request->bank_id,
                'acc_name' => $request->acc_name,
                'acc_number' => $request->acc_number,
                'branch_name' => $request->branch_name,
                'routing_number' => $request->routing_number,
                'swift_code' => $request->swift_code,
                'check_upload' => $checkFileName,
            ]
        );

        return response()->json([
            'status' => 'success',
            'message' => 'Profile updated successfully.',
            'user' => new UserResource($user)
        ], 200);
    }

    public function getUnseenNotifications()
    {
        $user = Auth::user();

        $notifications = $user->notifications()
            ->wherePivot('is_seen', false)
            ->latest()
            ->get();

        return response()->json([
            'count' => $notifications->count(),
            'notifications' => NotificationResource::collection($notifications),
        ]);
    }


    public function markNotificationAsSeen($notificationId)
    {
        $user = Auth::user();

        $user->notifications()->updateExistingPivot($notificationId, ['is_seen' => true]);

        return response()->json(['message' => 'Notification marked as seen']);
    }

    public function finance()
    {
        $user = Auth::user();

        // Ensure the user has the required access level
        if ($user->level !== 200) {
            return response()->json([
                'status' => 'error',
                'message' => 'You are not eligible to do this.'
            ], 401);
        }

        $bookings = Booking::with('project')
            ->where('investor_id', $user->id)
            ->get();

        $projects = $bookings->pluck('project')->unique('id')->values();

        // Return the investor details as a JSON response
        return response()->json([
            'status' => 'success',
            'message' => 'Showing finance.',
            'details' => ProjectResource::collection($projects)
        ], 200);
    }

    public function maturedFinance()
    {
        $user = Auth::user();

        if ($user->level !== 200) {
            return response()->json([
                'status' => 'error',
                'message' => 'You are not eligible to do this.'
            ], 401);
        }

        $bookings = Booking::with('project.details')
        ->where('investor_id', $user->id)
        ->whereHas('project', function ($query) {
            $query->where('status', 5);
        })
        ->get()
        ->unique('project_id') // This filters only one booking per project
        ->values(); // optional: resets the keys
            // Return the investor details as a JSON response


        return response()->json([
            'status' => 'success',
            'message' => 'Showing matured finance projects.',
            'bookings' => BookingResource::collection($bookings)
        ], 200);
    }

    public function maturedFinanceDetails($id)
    {
        $user = Auth::user();

        if ($user->level !== 200) {
            return response()->json([
                'status' => 'error',
                'message' => 'You are not eligible to do this.'
            ], 401);
        }

        // Load bookings with project + project.details
        $bookings = Booking::with('project.details')->findOrFail($id);

        if (!$bookings) {
            return response()->json([
                'status' => 'error',
                'message' => 'Booking not found.'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Showing matured finance projects.',
            'booking' => new BookingResource($bookings)
        ], 200);
    }



}
