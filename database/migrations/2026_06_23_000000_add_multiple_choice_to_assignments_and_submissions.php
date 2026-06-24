<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('assignments', function (Blueprint $table) {
            $table->string('assignment_type')->default('essay')->after('classroom_id');
            $table->json('questions')->nullable()->after('description');
        });

        Schema::table('submissions', function (Blueprint $table) {
            $table->json('answers')->nullable()->after('content');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assignments', function (Blueprint $table) {
            $table->dropColumn(['assignment_type', 'questions']);
        });

        Schema::table('submissions', function (Blueprint $table) {
            $table->dropColumn(['answers']);
        });
    }
};
