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
        Schema::create(config('layout.tables.layout'), function (Blueprint $table) {
            $table->id();

            $table->string('name')->index();
            /**
             * Display name of the layout.
             */

            $table->boolean('status')->default(true)->index();
            /**
             * Active flag; when false the layout is excluded from layout listing.
             */

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(config('layout.tables.layout'));
    }
};
