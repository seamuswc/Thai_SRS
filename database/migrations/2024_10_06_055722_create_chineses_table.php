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
        Schema::create('chinese', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('word');
            $table->string('meaning');
            $table->string('pronunciation');
            $table->integer('interval')->default(1);
            $table->integer('repetitions')->default(0);
            $table->float('easeFactor')->default(2.5);
            $table->boolean('mastered')->default(false);
            $table->date('nextReviewDate')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chinese');//i removed an s
    }
};
