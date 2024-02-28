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
        Schema::create(config('layout.tables.layout_extension'), function (Blueprint $table) {
            $table->foreignId('layout_id')->index()
                ->references('id')->on((new Layout)->getTable())->cascadeOnDelete()->cascadeOnUpdate();

            $table->string('extension')->index();
            /**
             * The extension field is used to store the extension of the layout.
             * For example, the extension of the layout is blade.php.
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
                'extension',
                'position'
            ], 'LAYOUT_EXTENSION_UNIQUE_KEY');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists(config('layout.tables.layout_path'));
    }
};
