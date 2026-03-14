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
        Schema::create(config('layout.tables.layout_plugin'), function (Blueprint $table) {
            $table->foreignId('layout_id')
                ->index()
                ->constrained(config('layout.tables.layout'))
                ->cascadeOnDelete()
                ->cascadeOnUpdate();

            $table->foreignId('plugin_id')
                ->index()
                ->constrained(config('extension.tables.plugin'))
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            /**
             * Reference to the extension plugin bound to this layout.
             */

            $table->string('position')->index();
            /**
             * Placement key (e.g. header, footer) for the plugin within the layout.
             */

            $table->integer('ordering')->nullable()->default(0)->index();
            /**
             * Display/execution order of the plugin at this position; lower runs first.
             */

            $table->unique([
                'layout_id',
                'plugin_id',
                'position',
            ], 'LAYOUT_PLUGIN_UNIQUE_KEY');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(config('layout.tables.layout_plugin'));
    }
};
