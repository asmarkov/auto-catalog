<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('car_offers', function (Blueprint $table) {
            $table->bigInteger('id')->primary();
            $table->foreignId('mark_id')->constrained('marks')->restrictOnDelete();
            $table->foreignId('model_id')->constrained('models');
            $table->foreignId('generation_id')->nullable()->constrained('generations');
            $table->foreignId('color_id')->nullable()->constrained('colors')->restrictOnDelete();
            $table->foreignId('body_type_id')->nullable()->constrained('body_types');
            $table->foreignId('engine_type_id')->nullable()->constrained('engine_types');
            $table->foreignId('transmission_id')->nullable()->constrained('transmissions');
            $table->foreignId('gear_type_id')->nullable()->constrained('gear_types');
            $table->year('manufacture_year')->nullable()->index();
            $table->unsignedMediumInteger('mileage')->nullable()->index();
            $table->boolean('actualize')->default(true);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('car_offers');
    }
};
