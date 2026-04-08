<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('financing_applications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('business_verification_id')
                ->constrained('business_verifications')
                ->cascadeOnDelete();
            $table->bigInteger('omzet')->default(0);
            $table->bigInteger('jumlah_pembiayaan');
            $table->integer('tenor_bulan');
            $table->text('tujuan_pembiayaan');
            $table->integer('skor_kelayakan')->nullable();
            $table->bigInteger('rekomendasi_limit')->nullable();
            $table->text('catatan_analisis')->nullable();

            $table->enum('status', [
                'submitted',
                'under_review',
                'recommended',
                'rejected_by_analyst',
                'approved',
                'rejected_by_manager',
            ])->default('submitted');

            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->text('rejected_reason')->nullable();


            $table->timestamps();
            $table->softDeletes();
        });

    }

    public function down(): void
    {
        Schema::dropIfExists('financing_applications');
    }
};