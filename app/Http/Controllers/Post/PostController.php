<?php

namespace App\Http\Controllers\Post;

use App\Http\Controllers\Controller;
use App\Http\Requests\Post\StorePostRequest;
use App\Http\Requests\Post\UpdatePostRequest;
use App\Http\Resources\Comment\CommentResource;
use App\Http\Resources\Post\PostResource;
use App\Models\Post;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;

class PostController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of posts.
     * @throws AuthorizationException
     */
    public function index(): AnonymousResourceCollection|Response
    {
        $this->authorize('viewAny', Post::class);

        $posts = Post::with(['user', 'comments'])
            ->latest()
            ->paginate(10);

        if (request()->wantsJson()) {
            return PostResource::collection($posts);
        }

        return Inertia::render('posts/Index', [
            'posts' => PostResource::collection($posts),
        ]);
    }

    /**
     * Show the form for creating a new post.
     * @throws AuthorizationException
     */
    public function create(): Response
    {
        $this->authorize('create', Post::class);

        return Inertia::render('posts/Create');
    }

    /**
     * Store a newly created post.
     */
    public function store(StorePostRequest $request): PostResource|RedirectResponse
    {
        $validated = $request->validated();

        DB::beginTransaction();
        try {
            $post = new Post();
            $post->title = $validated['title'];
            $post->content = $validated['content'];
            $post->user_id = Auth::user()->id;

            if (!$post->save()) {
                throw new Exception('Failed to save Post.');
            }

            DB::commit();

            if ($request->wantsJson()) {
                return new PostResource($post->load('user'));
            }

            return redirect()
                ->route('posts.index')
                ->with('success', 'Post created successfully!');
        } catch (Exception $exception) {
            DB::rollBack();
            Log::error("Failed to create post. {$exception->getMessage()}", [__METHOD__]);
        }

        return redirect()
            ->route('posts.index')
            ->with('error', 'An internal error occurred, please try again later.');
    }

    /**
     * @throws AuthorizationException
     */
    public function show(Post $post): JsonResponse|Response
    {
        $this->authorize('view', $post);

        $post->load(['user']);

        $comments = $post->comments()
            ->with('user')
            ->latest()
            ->paginate(10);

        if (request()->wantsJson()) {
            return response()->json([
                'post' => new PostResource($post),
                'comments' => CommentResource::collection($comments),
            ]);
        }

        return Inertia::render('posts/Show', [
            'post' => new PostResource($post),
            'comments' => CommentResource::collection($comments),
        ]);
    }

    /**
     * Show the form for editing the specified post.
     * @throws AuthorizationException
     */
    public function edit(Post $post): Response
    {
        $this->authorize('update', $post);

        return Inertia::render('posts/Edit', [
            'post' => new PostResource($post->load('user')),
        ]);
    }

    /**
     * Update the specified post.
     */
    public function update(UpdatePostRequest $request, Post $post): PostResource|RedirectResponse
    {
        $validated = $request->validated();

        DB::beginTransaction();
        try {
            $post->title = $validated['title'];
            $post->content = $validated['content'];
            $post->user_id = Auth::user()->id;

            if (!$post->save()) {
                throw new Exception("Failed to save Post #$post->id.");
            }

            DB::commit();

            if ($request->wantsJson()) {
                return new PostResource($post->load('user'));
            }

            return redirect()
                ->route('posts.show', $post)
                ->with('success', 'Post updated successfully!');
        } catch (Exception $exception) {
            DB::rollBack();
            Log::error("Failed to update post. {$exception->getMessage()}", [__METHOD__]);
        }

        return redirect()
            ->route('posts.show', $post)
            ->with('error', 'An internal error occurred, please try again later.');
    }

    /**
     * @throws AuthorizationException
     */
    public function destroy(Request $request, Post $post): JsonResponse|RedirectResponse
    {
        $this->authorize('delete', $post);

        DB::beginTransaction();
        try {
            if (!$post->delete()) {
                throw new Exception("Failed to delete Post #$post->id.");
            }

            DB::commit();

            if ($request->wantsJson()) {
                return response()->json(['message' => 'Post deleted successfully!']);
            }

            return redirect()
                ->route('posts.index')
                ->with('success', 'Post deleted successfully!');
        } catch (Exception $exception) {
            DB::rollBack();
            Log::error("Failed to destroy post. {$exception->getMessage()}", [__METHOD__]);
        }

        return redirect()
            ->route('posts.index')
            ->with('error', 'An internal error occurred, please try again later.');
    }
}
