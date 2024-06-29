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
        Schema::create(config('layout.tables.layout_plugin'), function (Blueprint $table) {
            $table->foreignId('layout_id')->index()
                ->references('id')->on(config('layout.tables.layout'))->cascadeOnDelete()->cascadeOnUpdate();

            $table->foreignId('plugin_id')->index()
                ->references('id')->on(config('extension.tables.plugin'))->cascadeOnDelete()->cascadeOnUpdate();
            /**
             * The plugin_id field is used to store the plugin of the layout.
             */

            $table->string('position')->index();
            /**
             * The position field is used to store the position of the layout.
             * For example, the position of the layout is header.
             */

            $table->integer('ordering')->default(0)->index();
            /**
             * The ordering field is used to store the ordering of the layout.
             * For example, the ordering of the layout is 1.
             */

            $table->unique([
                'layout_id',
                'plugin_id',
                'position'
            ], 'LAYOUT_PLUGIN_UNIQUE_KEY');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists(config('layout.tables.layout_plugin'));
    }
};
