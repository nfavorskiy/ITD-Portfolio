<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\User;
use App\Models\Post;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            // Add the user_id column as nullable first
            $table->unsignedBigInteger('user_id')->nullable();
        });

        // Assign existing posts to the first user (or create a default user)
        $firstUser = User::first();
        if ($firstUser) {
            // Update all existing posts to belong to the first user
            Post::whereNull('user_id')->update(['user_id' => $firstUser->id]);
        } else {
            // If no users exist, create a default user
            $defaultUser = User::create([
                'name' => 'Default User',
                'email' => 'default@example.com',
                'password' => bcrypt('password')
            ]);
            Post::whereNull('user_id')->update(['user_id' => $defaultUser->id]);
        }

        // Now make the column required and add foreign key constraint
        Schema::table('posts', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable(false)->change();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });
    }
};