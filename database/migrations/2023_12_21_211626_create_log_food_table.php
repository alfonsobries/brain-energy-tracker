<?php

use App\Models\Log;
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
        Schema::create('log_food', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Log::class, 'log_id');
            $table->json('main_ingredients')->nullable();
            $table->float('calories')->nullable();
            $table->float('sugar')->nullable();
            $table->float('protein')->nullable();
            $table->float('fat')->nullable();
            $table->float('carbohydrates')->nullable();
            $table->float('fiber')->nullable();
            $table->string('gluten_level')->nullable();
            $table->string('lactose_level')->nullable();
            $table->text('common_allergens')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('log_food');
    }
};
