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
        Schema::create('marks', function (Blueprint $table) {
            $table->increments("mark_id");
            $table->integer('mark_01');
            $table->integer('mark_02');
            $table->integer('mark_03');
            $table->integer('mark_04');
            $table->integer('mark_05')->nullable();
            $table->double('total');
            $table->unsignedInteger('teacher_id');
            $table->unsignedInteger('student_id');
            $table->timestamps();
            $table->foreign('teacher_id')->references('teacher_id')->on('teachers')->onDelete('cascade');
            $table->foreign('student_id')->references('student_id')->on('students')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('table_marks');
    }
};