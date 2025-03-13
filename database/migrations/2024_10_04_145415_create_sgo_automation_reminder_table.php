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
        Schema::create('sgo_automation_reminder', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('status'); // 0 là không kích hoạt, 1 là kích hoạt
            $table->time('sent_time'); // Thời gian gửi dưới dạng time
            $table->unsignedBigInteger('template_id'); // Khóa ngoại liên kết đến bảng template
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sgo_automation_reminder');
    }
};
