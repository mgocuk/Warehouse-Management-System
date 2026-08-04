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
        Schema::create('product_code_histories', function (Blueprint $table) {
            
            $table->id();
        
            $table->foreignId('product_id')
                  ->constrained();
        
            $table->string('old_code');
        
            $table->string('new_code');
        
            $table->foreignId('changed_by')
                  ->constrained('users');
        
            $table->text('reason');
        
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_code_histories');
    }
};
