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
        Schema::create(config('layout.tables.layout_relation'), function (Blueprint $table) {
            $table->string('application', 100)->nullable()->index();
            /**
             * Application/context identifier; null for base app.
             */

            $table->morphs('relatable');
            /**
             * Polymorphic relation to the entity (e.g. Product, Post) that uses this layout.
             */

            $table->foreignId('layout_id')
                ->index()
                ->constrained(config('layout.tables.layout'))
                ->cascadeOnDelete()
                ->cascadeOnUpdate();

            $table->string('collection', 100)->nullable()->index();
            /**
             * Collection identifier; null for base collection.
             */

            $table->unique([
                'application',
                'relatable_type',
                'relatable_id',
                'layout_id',
                'collection',
            ], 'LAYOUT_RELATION_UNIQUE');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(config('layout.tables.layout_relation'));
    }
};
