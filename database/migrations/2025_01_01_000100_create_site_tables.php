<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('users', 'is_admin')) {
            Schema::table('users', function (Blueprint $table) {
                $table->boolean('is_admin')->default(false)->after('password');
            });
        }

        Schema::create('videos', function (Blueprint $table) {
            $table->id();
            $table->string('youtube_id', 32)->unique();
            $table->string('slug')->unique();
            $table->string('title');
            $table->text('description')->nullable();
            $table->timestamp('published_at')->nullable()->index();
            $table->unsignedInteger('duration')->nullable();
            $table->unsignedBigInteger('view_count')->default(0);
            $table->unsignedBigInteger('like_count')->default(0);
            $table->unsignedInteger('site_likes')->default(0);
            $table->string('thumbnail', 1024)->nullable();
            $table->string('type', 16)->default('video')->index();
            $table->json('tags')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_published')->default(true);
            $table->timestamps();
        });

        Schema::create('video_likes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('video_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('visitor_key', 64);
            $table->timestamps();
            $table->unique(['video_id', 'visitor_key']);
        });

        Schema::create('video_comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('video_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('guest_name', 60)->nullable();
            $table->text('body');
            $table->string('ip', 64)->nullable();
            $table->timestamps();
        });

        Schema::create('chat_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('nickname', 40);
            $table->string('body', 500);
            $table->string('ip', 64)->nullable();
            $table->timestamps();
        });

        Schema::create('collab_requests', function (Blueprint $table) {
            $table->id();
            $table->string('name', 120);
            $table->string('contact', 190);
            $table->string('company', 190)->nullable();
            $table->string('type', 32);
            $table->string('budget', 120)->nullable();
            $table->text('message');
            $table->string('status', 16)->default('new')->index();
            $table->text('admin_note')->nullable();
            $table->string('ip', 64)->nullable();
            $table->timestamps();
        });

        Schema::create('forum_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name', 120);
            $table->string('slug', 140)->unique();
            $table->string('description', 500)->nullable();
            $table->string('emoji', 16)->nullable();
            $table->unsignedInteger('sort')->default(0);
            $table->timestamps();
        });

        Schema::create('forum_topics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('forum_category_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title', 200);
            $table->boolean('is_pinned')->default(false);
            $table->boolean('is_locked')->default(false);
            $table->unsignedInteger('replies_count')->default(0);
            $table->unsignedInteger('views')->default(0);
            $table->timestamp('last_post_at')->nullable()->index();
            $table->timestamps();
        });

        Schema::create('forum_posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('forum_topic_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->text('body');
            $table->timestamps();
        });

        Schema::create('instagram_posts', function (Blueprint $table) {
            $table->id();
            $table->string('shortcode', 64)->unique();
            $table->text('caption')->nullable();
            $table->timestamp('posted_at')->nullable()->index();
            $table->string('type', 32)->nullable();
            $table->json('media_urls')->nullable();
            $table->string('local_image', 512)->nullable();
            $table->unsignedBigInteger('likes')->default(0);
            $table->timestamps();
        });

        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key', 100)->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        foreach (['settings', 'instagram_posts', 'forum_posts', 'forum_topics', 'forum_categories',
            'collab_requests', 'chat_messages', 'video_comments', 'video_likes', 'videos'] as $t) {
            Schema::dropIfExists($t);
        }
        if (Schema::hasColumn('users', 'is_admin')) {
            Schema::table('users', fn (Blueprint $table) => $table->dropColumn('is_admin'));
        }
    }
};
