<?php

namespace Database\Seeders;

use App\Enums\PostStatus;
use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class PostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::first() ?? User::factory()->create();

        $categories = Category::all();
        if ($categories->count() === 0) {
            $categories = Category::factory()->count(5)->create();
        }

        $tags = Tag::all();
        if ($tags->count() === 0) {
            $tags = Tag::factory()->count(10)->create();
        }

        $totalPosts = 50;

        for ($i = 1; $i <= $totalPosts; $i++) {
            $title = fake()->sentence(6);

            $post = Post::create([
                'user_id' => $user->id,
                'category_id' => $categories->random()->id,
                'title' => $title,
                'excerpt' => fake()->sentence(12),
                'content' => fake()->paragraphs(5, true),
                'status' => Arr::random([
                    PostStatus::DRAFT,
                    PostStatus::PENDING,
                    PostStatus::PUBLISHED,
                ]),
                'published_at' => now()->subDays(rand(1, 60)),
                'thumbnail' => 'https://picsum.photos/seed/'.Str::slug($title).'/640/480',
                'meta_title' => $title,
                'meta_description' => fake()->sentence(20),
            ]);

            $post->tags()->sync(
                $tags->random(rand(1, 4))->pluck('id')->toArray()
            );
        }

        $this->command->info("✔ Seeded {$totalPosts} posts successfully!");
    }
}
