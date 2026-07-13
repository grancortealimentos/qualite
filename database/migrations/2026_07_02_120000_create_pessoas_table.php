<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pessoas', function (Blueprint $table) {
            $table->id();
            $table->boolean('eh_ativo')->default(true);
            $table->string('url_foto_perfil')->nullable();
            $table->string('tipo_cadastro', 50);
            $table->string('nome_completo');
            $table->string('tipo_documento', 20)->nullable();
            $table->string('documento', 14)->nullable();
            $table->string('doc_profissional')->nullable();
            $table->string('telefone', 11)->nullable();
            $table->string('email')->nullable();
            $table->string('cep', 8)->nullable();
            $table->string('logradouro')->nullable();
            $table->string('numero', 10)->nullable();
            $table->string('bairro')->nullable();
            $table->string('cidade')->nullable();
            $table->string('estado')->nullable();
            $table->string('pais')->default('Brasil');
            $table->timestamps();
            $table->softDeletes();

            $table->index([
                'documento',
                'nome_completo',
                'email',
                'eh_ativo',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pessoas');
    }
};
