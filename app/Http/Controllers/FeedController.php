<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Hashtag;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class FeedController extends Controller
{
    /**
     * Display the Vocational Community Feed timeline.
     */
    public function index(Request $request): View
    {
        $user = auth()->user();

        $query = Post::with([
            'user.role',
            'user.profile',
            'user.student.schoolClass',
            'user.teacher',
            'user.company',
            'category',
            'hashtags',
            'comments' => function ($q) {
                $q->whereNull('parent_id')
                  ->with(['user.role', 'user.profile', 'replies.user.role', 'replies.user.profile'])
                  ->latest('created_at');
            },
        ])->where('status', 'terbit');

        // Tab Filter: All Posts vs Following
        $tab = $request->query('tab', 'all');
        if ($tab === 'following') {
            $followingIds = $user->following()->pluck('following_id')->toArray();
            // Include own posts in following feed as standard
            $followingIds[] = $user->id;
            $query->whereIn('user_id', $followingIds);
        }

        // Category Filter
        if ($categorySlug = $request->query('kategori')) {
            $category = Category::where('slug', $categorySlug)->first();
            if ($category) {
                $query->where('category_id', $category->id);
            }
        }

        // Hashtag Filter
        if ($tag = $request->query('tag')) {
            $cleanedTag = ltrim($tag, '#');
            $query->whereHas('hashtags', fn($h) => $h->where('tag', $cleanedTag));
        }

        // Search Query
        if ($search = $request->query('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('isi', 'like', "%{$search}%")
                  ->orWhereHas('user', fn($u) => $u->where('name', 'like', "%{$search}%"));
            });
        }

        $posts = $query->latest()->paginate(10)->withQueryString();

        // Sidebar Data
        $categories = Category::withCount(['posts' => fn($p) => $p->where('status', 'terbit')])->get();
        $popularHashtags = Hashtag::where('jumlah_pakai', '>', 0)
            ->orderByDesc('jumlah_pakai')
            ->take(8)
            ->get();

        // Suggested users to connect with
        $followedIds = $user->following()->pluck('following_id')->toArray();
        $followedIds[] = $user->id;

        $suggestedUsers = User::with(['role', 'profile'])
            ->whereNotIn('id', $followedIds)
            ->where('is_active', true)
            ->inRandomOrder()
            ->take(5)
            ->get();

        // User stats
        $userStats = [
            'posts_count' => $user->posts()->where('status', 'terbit')->count(),
            'followers_count' => $user->followers()->count(),
            'following_count' => $user->following()->count(),
            'bookmarks_count' => $user->bookmarks()->count(),
        ];

        return view('community.feed', compact(
            'posts',
            'categories',
            'popularHashtags',
            'suggestedUsers',
            'userStats',
            'user'
        ));
    }

    /**
     * Store a newly created post in community feed.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'isi' => 'required|string|max:5000',
            'category_id' => 'nullable|exists:categories,id',
            'jenis' => 'nullable|in:post,materi,pengumuman,karier',
            'media' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        $mediaPath = null;
        if ($request->hasFile('media')) {
            $mediaPath = $request->file('media')->store('posts', 'public');
        }

        $post = Post::create([
            'user_id' => auth()->id(),
            'category_id' => $validated['category_id'] ?? null,
            'jenis' => $validated['jenis'] ?? 'post',
            'isi' => $validated['isi'],
            'media' => $mediaPath,
            'status' => 'terbit',
        ]);

        // Extract and sync hashtags (#tag)
        preg_match_all('/#([a-zA-Z0-9_\x{0080}-\x{FFFF}]+)/u', $validated['isi'], $matches);
        if (!empty($matches[1])) {
            $uniqueTags = array_unique(array_map('strtolower', $matches[1]));
            foreach ($uniqueTags as $tag) {
                $hashtag = Hashtag::firstOrCreate(['tag' => $tag]);
                $hashtag->increment('jumlah_pakai');
                $post->hashtags()->syncWithoutDetaching([$hashtag->id]);
            }
        }

        return redirect()->back()->with('success', 'Kiriman Anda berhasil dipublikasikan di feed komunitas.');
    }

    /**
     * Delete user's own post or admin moderate.
     */
    public function destroy(Post $post): RedirectResponse
    {
        $user = auth()->user();

        if ($post->user_id !== $user->id && !$user->isAdmin()) {
            abort(403, 'Anda tidak memiliki hak untuk menghapus kiriman ini.');
        }

        if ($post->media && Storage::disk('public')->exists($post->media)) {
            Storage::disk('public')->delete($post->media);
        }

        $post->delete();

        return redirect()->back()->with('success', 'Kiriman berhasil dihapus.');
    }
}
