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
        Schema::create(config('layout.tables.layout_relation'), function (Blueprint $table) {
            $table->string('application', 100)->nullable();
            /**
             * for another app file
             *
             * null value for base app
             */

            $table->morphs('relatable');
            /**
             * relatable to:
             *
             * Product
             * Post
             * ...
             */

            $table->foreignId('layout_id')->index()
                ->references('id')->on(config('layout.tables.layout'))->cascadeOnDelete()->cascadeOnUpdate();

            $table->string('collection', 100)->nullable();
            /**
             * for another collection file
             *
             * null value for base collection
             */

            $table->unique([
                'application',
                'relatable_type',
                'relatable_id',
                'layout_id',
                'collection'
            ], 'LAYOUT_RELATION_UNIQUE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists(config('layout.tables.layout_relation'));
    }
};
