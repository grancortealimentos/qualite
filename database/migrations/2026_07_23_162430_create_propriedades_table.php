<?php

use App\Models\Pessoa;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('propriedades', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Pessoa::class, 'produtor_id');
            $table->boolean('eh_ativo')->default(true);
            $table->string('razao_social');
            $table->string('nome_fantasia')->nullable();
            $table->string('cnpj', 14)->unique();
            $table->string('ie', 20)->nullable();
            $table->string('nrif', 50)->nullable();
            $table->string('car', 10)->nullable();
            $table->decimal('area_total', 10, 2)->nullable();
            $table->decimal('area_consolidada', 10, 2)->nullable();
            $table->decimal('area_reservada_legal', 10, 2)->nullable();
            $table->decimal('area_app', 10, 2)->nullable();
            $table->integer('capacidade_armazenamento')->nullable();
            $table->string('cep', 8)->nullable();
            $table->string('logradouro')->nullable();
            $table->string('numero')->nullable();
            $table->string('bairro')->nullable();
            $table->string('cidade')->nullable();
            $table->string('estado')->nullable();
            $table->string('pais')->nullable()->default('Brasil');
            $table->string('complemento')->nullable();
            $table->string('latitude')->nullable();
            $table->string('longitude')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('propriedades');
    }
};
