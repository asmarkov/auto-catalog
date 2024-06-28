<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('generations', function (Blueprint $table) {
            $table->bigInteger(column: 'id', unsigned: true)->primary();
            $table->string('name');
            $table->foreignId('model_id')->constrained('models');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('generations');
    }
};
