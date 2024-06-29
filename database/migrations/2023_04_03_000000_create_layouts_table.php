<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create(config('layout.tables.layout'), function (Blueprint $table) {
            $table->id();

            $table->string('name')->index();

            $table->boolean('status')->default(true)->index();
            /**
             * If the layout is not active, it will not be displayed in the layout list.
             */

            $table->softDeletes();

            $table->timestamps();
        });

        cache()->forget('layout');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists(config('layout.tables.layout'));

        cache()->forget('layout');
    }
};
