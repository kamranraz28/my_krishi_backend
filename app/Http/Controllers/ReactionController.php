<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Projectupdate;
use App\Models\Reply;
use Illuminate\Http\Request;

class ReactionController extends Controller
{
    //
    public function react(Request $request)
    {
        $request->validate([
            'reactable_type' => 'required|in:updates,comments,replies',
            'reactable_id'   => 'required|integer',
            'type'           => 'required|in:like,love,haha,wow,sad,angry',
        ]);

        $modelClass = match ($request->reactable_type) {
            'updates'  => Projectupdate::class,
            'comments' => Comment::class,
            'replies'  => Reply::class,
            default    => null,
        };

        if (!$modelClass) {
            return response()->json(['error' => 'Invalid reactable type'], 400);
        }

        $reactable = $modelClass::findOrFail($request->reactable_id);

        $reaction = $reactable->reactions()->updateOrCreate(
            ['user_id' => auth()->id()],
            ['type' => $request->type]
        );

        return response()->json([
            'status' => 'success',
            'message' => 'Reaction saved successfully.',
            'reaction' => $reaction,
        ]);
    }



}
