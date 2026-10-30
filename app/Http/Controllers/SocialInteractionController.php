<?php

namespace App\Http\Controllers;

use App\Models\Bookmark;
use App\Models\Comment;
use App\Models\Follower;
use App\Models\Like;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SocialInteractionController extends Controller
{
    /**
     * Toggle like/unlike on a post.
     */
    public function toggleLike(Post $post): RedirectResponse
    {
        $userId = auth()->id();
        $existingLike = Like::where('post_id', $post->id)->where('user_id', $userId)->first();

        if ($existingLike) {
            $existingLike->delete();
            $post->decrement('like_count');
            $message = 'Batal menyukai kiriman.';
        } else {
            Like::create([
                'post_id' => $post->id,
                'user_id' => $userId,
            ]);
            $post->increment('like_count');
            $message = 'Menyukai kiriman.';

            if ($post->user) {
                \App\Services\NotificationService::notifyCommunityInteraction($post->user, auth()->user(), 'like', $post);
            }
        }

        return redirect()->back()->with('success', $message);
    }

    /**
     * Store a comment on a post.
     */
    public function storeComment(Request $request, Post $post): RedirectResponse
    {
        $validated = $request->validate([
            'isi' => 'required|string|max:1000',
            'parent_id' => 'nullable|exists:comments,id',
        ]);

        $comment = Comment::create([
            'post_id' => $post->id,
            'user_id' => auth()->id(),
            'parent_id' => $validated['parent_id'] ?? null,
            'isi' => $validated['isi'],
            'status' => 'terbit',
        ]);

        $post->increment('comment_count');

        if ($post->user) {
            \App\Services\NotificationService::notifyCommunityInteraction($post->user, auth()->user(), 'comment', $comment);
        }

        return redirect()->back()->with('success', 'Komentar Anda berhasil dikirim.');
    }

    /**
     * Delete a comment.
     */
    public function destroyComment(Comment $comment): RedirectResponse
    {
        $user = auth()->user();

        if ($comment->user_id !== $user->id && !$user->isAdmin()) {
            abort(403, 'Anda tidak memiliki hak untuk menghapus komentar ini.');
        }

        $post = $comment->post;
        $comment->delete();

        if ($post && $post->comment_count > 0) {
            $post->decrement('comment_count');
        }

        return redirect()->back()->with('success', 'Komentar berhasil dihapus.');
    }

    /**
     * Toggle bookmark on a post.
     */
    public function toggleBookmark(Post $post): RedirectResponse
    {
        $userId = auth()->id();
        $existing = Bookmark::where('post_id', $post->id)->where('user_id', $userId)->first();

        if ($existing) {
            $existing->delete();
            $message = 'Kiriman dihapus dari koleksi tersimpan.';
        } else {
            Bookmark::create([
                'user_id' => $userId,
                'post_id' => $post->id,
            ]);
            $message = 'Kiriman berhasil disimpan ke koleksi Anda.';
        }

        return redirect()->back()->with('success', $message);
    }

    /**
     * Toggle follow/unfollow a user.
     */
    public function toggleFollow(User $user): RedirectResponse
    {
        $authUserId = auth()->id();

        if ($user->id === $authUserId) {
            return redirect()->back()->with('error', 'Anda tidak dapat mengikuti akun sendiri.');
        }

        $existing = Follower::where('follower_id', $authUserId)
            ->where('following_id', $user->id)
            ->first();

        if ($existing) {
            $existing->delete();
            $message = 'Berhenti mengikuti ' . $user->name . '.';
        } else {
            Follower::create([
                'follower_id' => $authUserId,
                'following_id' => $user->id,
            ]);
            $message = 'Sekarang Anda mengikuti ' . $user->name . '.';

            \App\Services\NotificationService::notifyCommunityInteraction($user, auth()->user(), 'follow');
        }

        return redirect()->back()->with('success', $message);
    }
}
