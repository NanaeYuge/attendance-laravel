<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->date('work_date');
            $table->timestamp('clock_in')->nullable();
            $table->timestamp('clock_out')->nullable();
            $table->enum('status', ['off','working','break','done','approved'])->default('off');
            $table->text('note')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'work_date']);
        });
    }
    public function down(): void {
        Schema::dropIfExists('attendances');
    }
};
