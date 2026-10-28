<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Comment;
use App\Models\Hashtag;
use App\Models\Like;
use App\Models\Post;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class CommunityFeedTest extends TestCase
{
    private function getUser(string $email, string $username, string $name): User
    {
        $user = User::where('email', $email)->first();
        if (!$user) {
            $role = Role::firstOrCreate(['nama' => 'siswa'], ['label' => 'Siswa']);
            $user = User::create([
                'role_id' => $role->id,
                'name' => $name,
                'username' => $username,
                'email' => $email,
                'password' => Hash::make('password'),
                'is_active' => true,
            ]);
        }
        return $user;
    }

    public function test_guest_cannot_access_community_feed(): void
    {
        $response = $this->get(route('feed.index'));
        $response->assertRedirect('/login');
    }

    public function test_authenticated_user_can_view_feed(): void
    {
        $user = $this->getUser('user_feed_test@demo.com', 'user_feed_test', 'User Feed Test');

        $response = $this->actingAs($user)->get(route('feed.index'));

        $response->assertStatus(200);
        $response->assertSee('Semua Kiriman');
        $response->assertSee('Tagar Populer');
        $response->assertSee('Kategori Komunitas');
    }

    public function test_user_can_publish_post_with_hashtags(): void
    {
        $user = $this->getUser('user_feed_test@demo.com', 'user_feed_test', 'User Feed Test');
        $category = Category::firstOrCreate(['slug' => 'karya-dan-portofolio'], [
            'nama' => 'Karya & Portofolio',
            'jenis' => 'umum',
        ]);

        $response = $this->actingAs($user)->post(route('feed.store'), [
            'isi' => 'Mengerjakan tugas akhir sistem informasi sekolah dengan #laravel dan #pplg hari ini!',
            'category_id' => $category->id,
            'jenis' => 'post',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('posts', [
            'user_id' => $user->id,
            'category_id' => $category->id,
        ]);

        $this->assertDatabaseHas('hashtags', ['tag' => 'laravel']);
        $this->assertDatabaseHas('hashtags', ['tag' => 'pplg']);
    }

    public function test_user_can_filter_feed_by_hashtag_and_category(): void
    {
        $user = $this->getUser('user_feed_test@demo.com', 'user_feed_test', 'User Feed Test');

        $responseTag = $this->actingAs($user)->get(route('feed.index', ['tag' => 'laravel']));
        $responseTag->assertStatus(200);

        $responseCat = $this->actingAs($user)->get(route('feed.index', ['kategori' => 'karya-dan-portofolio']));
        $responseCat->assertStatus(200);
    }

    public function test_user_can_delete_own_post(): void
    {
        $user = $this->getUser('user_feed_test@demo.com', 'user_feed_test', 'User Feed Test');

        $post = Post::create([
            'user_id' => $user->id,
            'isi' => 'Kiriman sementara untuk dihapus',
            'status' => 'terbit',
        ]);

        $response = $this->actingAs($user)->delete(route('feed.destroy', $post));

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertSoftDeleted('posts', [
            'id' => $post->id,
        ]);
    }

    public function test_user_cannot_delete_other_user_post(): void
    {
        $userA = $this->getUser('user_a@demo.com', 'user_a', 'User A');
        $userB = $this->getUser('user_b@demo.com', 'user_b', 'User B');

        $postB = Post::create([
            'user_id' => $userB->id,
            'isi' => 'Kiriman milik User B',
            'status' => 'terbit',
        ]);

        $response = $this->actingAs($userA)->delete(route('feed.destroy', $postB));
        $response->assertStatus(403);
    }

    public function test_user_can_toggle_like_on_post(): void
    {
        $user = $this->getUser('user_feed_test@demo.com', 'user_feed_test', 'User Feed Test');

        $post = Post::create([
            'user_id' => $user->id,
            'isi' => 'Kiriman untuk pengujian like',
            'status' => 'terbit',
            'like_count' => 0,
        ]);

        // 1. Like
        $responseLike = $this->actingAs($user)->post(route('posts.like', $post));
        $responseLike->assertRedirect();
        $this->assertDatabaseHas('likes', ['post_id' => $post->id, 'user_id' => $user->id]);
        $post->refresh();
        $this->assertEquals(1, $post->like_count);

        // 2. Unlike
        $responseUnlike = $this->actingAs($user)->post(route('posts.like', $post));
        $responseUnlike->assertRedirect();
        $this->assertDatabaseMissing('likes', ['post_id' => $post->id, 'user_id' => $user->id]);
        $post->refresh();
        $this->assertEquals(0, $post->like_count);
    }

    public function test_user_can_store_and_delete_comment(): void
    {
        $user = $this->getUser('user_feed_test@demo.com', 'user_feed_test', 'User Feed Test');

        $post = Post::create([
            'user_id' => $user->id,
            'isi' => 'Kiriman untuk uji komentar',
            'status' => 'terbit',
            'comment_count' => 0,
        ]);

        // Store comment
        $response = $this->actingAs($user)->post(route('posts.comments.store', $post), [
            'isi' => 'Komentar apresiasi karya kejuruan!',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('comments', [
            'post_id' => $post->id,
            'user_id' => $user->id,
            'isi' => 'Komentar apresiasi karya kejuruan!',
        ]);

        $post->refresh();
        $this->assertEquals(1, $post->comment_count);

        $comment = Comment::where('post_id', $post->id)->first();

        // Delete comment
        $responseDelete = $this->actingAs($user)->delete(route('comments.destroy', $comment));
        $responseDelete->assertRedirect();

        $this->assertDatabaseMissing('comments', ['id' => $comment->id]);
        $post->refresh();
        $this->assertEquals(0, $post->comment_count);
    }

    public function test_user_can_toggle_bookmark_on_post(): void
    {
        $user = $this->getUser('user_feed_test@demo.com', 'user_feed_test', 'User Feed Test');

        $post = Post::create([
            'user_id' => $user->id,
            'isi' => 'Kiriman materi untuk dibookmark',
            'status' => 'terbit',
        ]);

        // Bookmark
        $response = $this->actingAs($user)->post(route('posts.bookmark', $post));
        $response->assertRedirect();
        $this->assertDatabaseHas('bookmarks', ['post_id' => $post->id, 'user_id' => $user->id]);

        // Un-bookmark
        $responseRemove = $this->actingAs($user)->post(route('posts.bookmark', $post));
        $responseRemove->assertRedirect();
        $this->assertDatabaseMissing('bookmarks', ['post_id' => $post->id, 'user_id' => $user->id]);
    }

    public function test_user_can_toggle_follow_another_user(): void
    {
        $userA = $this->getUser('user_a@demo.com', 'user_a', 'User A');
        $userB = $this->getUser('user_b@demo.com', 'user_b', 'User B');

        // Follow User B
        $response = $this->actingAs($userA)->post(route('users.follow', $userB));
        $response->assertRedirect();
        $this->assertDatabaseHas('followers', ['follower_id' => $userA->id, 'following_id' => $userB->id]);

        // Unfollow User B
        $responseUnfollow = $this->actingAs($userA)->post(route('users.follow', $userB));
        $responseUnfollow->assertRedirect();
        $this->assertDatabaseMissing('followers', ['follower_id' => $userA->id, 'following_id' => $userB->id]);
    }

    public function test_user_cannot_follow_self(): void
    {
        $user = $this->getUser('user_feed_test@demo.com', 'user_feed_test', 'User Feed Test');

        $response = $this->actingAs($user)->post(route('users.follow', $user));
        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    public function test_user_can_view_public_community_profile(): void
    {
        $user = $this->getUser('user_feed_test@demo.com', 'user_feed_test', 'User Feed Test');

        $response = $this->actingAs($user)->get(route('community.profile', $user->username));

        $response->assertStatus(200);
        $response->assertSee($user->name);
        $response->assertSee('@' . $user->username);
        $response->assertSee('Kiriman dari ' . $user->name);
    }

    public function test_user_can_view_bookmarks_page(): void
    {
        $user = $this->getUser('user_feed_test@demo.com', 'user_feed_test', 'User Feed Test');

        $response = $this->actingAs($user)->get(route('bookmarks.index'));

        $response->assertStatus(200);
        $response->assertSee('Koleksi Kiriman Tersimpan');
    }
}
