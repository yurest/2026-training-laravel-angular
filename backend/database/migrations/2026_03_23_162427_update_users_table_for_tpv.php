<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('restaurant_id')->after('uuid')->constrained('restaurants');
            $table->string('role')->default('operator')->after('restaurant_id');
            $table->string('image_src')->nullable()->after('role');
            $table->string('pin')->nullable()->after('password');
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('restaurant_id');
            $table->dropColumn(['role', 'image_src', 'pin', 'deleted_at']);
        });
    }
};
