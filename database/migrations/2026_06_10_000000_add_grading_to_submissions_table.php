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
        Schema::table('submissions', function (Blueprint $table) {
            $table->string('status')->default('submitted')->after('files');
            $table->float('grade')->nullable()->after('status');
            $table->text('teacher_comment')->nullable()->after('grade');
            $table->timestamp('graded_at')->nullable()->after('teacher_comment');
            $table->timestamp('submitted_at')->useCurrent()->after('graded_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('submissions', function (Blueprint $table) {
            $table->dropColumn(['status', 'grade', 'teacher_comment', 'graded_at', 'submitted_at']);
        });
    }
};
