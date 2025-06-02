<?php

namespace App\Http\Controllers\Comment;

use App\Http\Controllers\Controller;
use App\Http\Requests\Comment\StoreCommentRequest;
use App\Http\Resources\Comment\CommentResource;
use App\Models\Comment;
use App\Models\Post;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CommentController extends Controller
{
    use AuthorizesRequests;

    public function store(StoreCommentRequest $request, Post $post): CommentResource|RedirectResponse
    {
        $validated = $request->validated();

        DB::beginTransaction();
        try {
            $comment = new Comment();
            $comment->comment = $validated['comment'];
            $comment->post_id = $post->id;
            $comment->user_id = Auth::user()->id;

            if (!$comment->save()) {
                throw new Exception('Failed to save Comment.');
            }

            DB::commit();

            if ($request->wantsJson()) {
                return new CommentResource($comment->load(['user', 'post']));
            }

            return redirect()
                ->route('posts.show', $post)
                ->with('success', 'Comment created successfully.');
        } catch (Exception $exception) {
            DB::rollBack();
            Log::error("Failed to store Comment. {$exception->getMessage()}", [__METHOD__]);
        }

        return redirect()
            ->route('posts.index')
            ->with('error', 'An internal error occurred, please try again later.');
    }

    /**
     * @throws AuthorizationException
     */
    public function destroy(Request $request, Comment $comment): JsonResponse|RedirectResponse
    {
        $this->authorize('delete', $comment);

        DB::beginTransaction();
        try {
            $post = $comment->post;

            if (!$comment->delete()) {
                throw new Exception("Failed to delete Comment #$comment->id.");
            }

            DB::commit();

            if ($request->wantsJson()) {
                return response()->json(['message' => 'Comment deleted successfully!']);
            }

            return redirect()
                ->route('posts.show', $post)
                ->with('success', 'Comment deleted successfully!');
        } catch (Exception $exception) {
            DB::rollBack();
            Log::error("Failed to destroy comment. {$exception->getMessage()}", [__METHOD__]);
        }

        return redirect()
            ->route('posts.show', $post)
            ->with('error', 'An internal error occurred, please try again later.');
    }
}
