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
        Schema::create('user_accounts', function (Blueprint $table) {
            $table->id('UserID');
            $table->string('Username');
            $table->string('Email')->unique();
            $table->string('Password');

            $table->unsignedBigInteger('RoleID');

            $table->timestamps();

            $table->foreign('RoleID')
                ->references('id')
                ->on('roles')
                ->onDelete('cascade');

            $table->index('Username');
            $table->index('Email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_accounts');
    }
};
