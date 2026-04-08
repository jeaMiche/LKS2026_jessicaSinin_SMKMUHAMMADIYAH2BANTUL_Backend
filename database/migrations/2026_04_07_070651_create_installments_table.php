<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('installments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('financing_application_id')
                  ->constrained('financing_applications')
                  ->cascadeOnDelete();

            $table->integer('installment_number');         
            $table->date('jatuh_tempo');                   
            $table->bigInteger('nominal_pokok');             
            $table->bigInteger('nominal_bunga');
            $table->bigInteger('total_cicilan');

            $table->enum('status', ['unpaid', 'paid'])->default('unpaid');                        
            $table->timestamp('paid_at')->nullable();       

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('installments');  
    }
};
