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
        Schema::create(config('layout.tables.layout_page'), function (Blueprint $table) {
            $table->foreignId('layout_id')
                ->index()
                ->constrained(config('layout.tables.layout'))
                ->cascadeOnDelete()
                ->cascadeOnUpdate();

            $table->string('application', 100)->nullable()->index();
            /**
             * Application/context identifier; null for base app.
             */

            $table->string('page', 100)->nullable()->index();
            /**
             * Page/collection identifier; null for base collection.
             */

            $table->unique([
                'layout_id',
                'application',
                'page',
            ], 'LAYOUT_APP_PAGE_UNIQUE_KEY');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(config('layout.tables.layout_page'));
    }
};
