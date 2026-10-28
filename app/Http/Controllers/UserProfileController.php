<?php

namespace App\Http\Controllers;

use App\Models\Bookmark;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserProfileController extends Controller
{
    /**
     * Display public community profile and personal posts of a user.
     */
    public function show(string $username): View
    {
        $profileUser = User::where('username', $username)
            ->with(['role', 'profile', 'student.schoolClass', 'teacher', 'company'])
            ->firstOrFail();

        $posts = Post::with([
            'user.role',
            'user.profile',
            'category',
            'hashtags',
            'comments' => function ($q) {
                $q->whereNull('parent_id')->with('user.role')->latest('created_at');
            },
        ])->where('user_id', $profileUser->id)
          ->where('status', 'terbit')
          ->latest()
          ->paginate(10);

        $stats = [
            'posts_count' => $profileUser->posts()->where('status', 'terbit')->count(),
            'followers_count' => $profileUser->followers()->count(),
            'following_count' => $profileUser->following()->count(),
        ];

        $isFollowing = auth()->check() ? auth()->user()->isFollowing($profileUser) : false;

        return view('community.profile', compact(
            'profileUser',
            'posts',
            'stats',
            'isFollowing'
        ));
    }

    /**
     * Display the authenticated user's bookmarked posts collection.
     */
    public function bookmarks(Request $request): View
    {
        $user = auth()->user();

        $bookmarks = Bookmark::with([
            'post.user.role',
            'post.category',
            'post.hashtags',
        ])->where('user_id', $user->id)
          ->whereNotNull('post_id')
          ->whereHas('post', fn($p) => $p->where('status', 'terbit'))
          ->latest('created_at')
          ->paginate(12);

        return view('community.bookmarks', compact('bookmarks', 'user'));
    }
}
