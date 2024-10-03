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
        Schema::create('students', function (Blueprint $table) {
            $table->increments("student_id");
            $table->string("serial_no");
            $table->string('language')->nullable();
            $table->string("district");
            $table->string('age');
            $table->string('school');
            $table->string('studentName');
            $table->string('image')->nullable();
            $table->string('student_detail')->nullable();
            $table->string('stream');
            $table->integer('marking_status')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
