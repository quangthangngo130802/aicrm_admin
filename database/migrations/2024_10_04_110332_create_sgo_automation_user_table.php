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
        Schema::create('sgo_automation_user', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Cột 'name' kiểu string (VARCHAR)
            $table->unsignedBigInteger('template_id'); // Cột 'template_id' kiểu khóa ngoại
            $table->boolean('status')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sgo_automation_user');
    }
};
