<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up(): void
    {
        Schema::create('application_logs', function (Blueprint $table) {
            $table->uuid('id')->primary();                           

            $table->foreignUuid('financing_application_id')           
                  ->constrained('financing_applications')              
                  ->cascadeOnDelete();

            $table->foreignUuid('user_id')->constrained()->cascadeOnDelete();

            $table->string('status_from');
            $table->string('status_to');
            $table->string('role');
            $table->text('notes')->nullable();

             $table->timestamp('created_at')->useCurrent();           
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('application_logs');
    }
};