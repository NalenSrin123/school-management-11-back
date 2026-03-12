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
        Schema::create('events', function (Blueprint $table) {
              $table->id('EventID');
            $table->string('Name');
            $table->date('Date');
            $table->text('Description')->nullable();
            $table->unsignedBigInteger('CreatedBy');
            $table->dateTime('CreatedDate')->nullable();
            $table->timestamps();

            $table->foreign('CreatedBy')->references('UserID')->on('user_accounts')->onDelete('cascade');

            $table->index('Date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
