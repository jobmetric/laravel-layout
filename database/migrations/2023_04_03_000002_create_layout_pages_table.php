<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use JobMetric\Layout\Models\Layout;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create(config('layout.tables.layout_page'), function (Blueprint $table) {
            $table->foreignId('layout_id')->index()
                ->references('id')->on((new Layout)->getTable())->cascadeOnDelete()->cascadeOnUpdate();

            $table->string('application', 100)->nullable();
            /**
             * for another app file
             *
             * null value for base app
             */

            $table->string('page', 100)->nullable();
            /**
             * for another collection file
             *
             * null value for base collection
             */

            $table->unique([
                'layout_id',
                'application',
                'page'
            ], 'LAYOUT_PAGE_UNIQUE_KEY');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists(config('layout.tables.layout_page'));
    }
};
