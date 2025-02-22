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
        Schema::create('message_files', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('message_id');
            $table->text('file_name');
            $table->string('file_path');
            $table->text('file_type');
            
            $table->text('file_size');
            $table->timestamps();

          
            $table->foreign('message_id')->references('id')->on('messages')->onDelete('cascade');
            $table->index('message_id'); 
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('message_files');
    }
};
