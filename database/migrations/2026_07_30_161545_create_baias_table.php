<?php

use App\Models\Galpao;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('baias', function (Blueprint $table) {
            $table->id();
            $table->boolean('eh_ativo')->default(true);
            $table->foreignIdFor(Galpao::class, 'galpao_id');
            $table->string('lote');
            $table->string('descricao')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('baias');
    }
};
