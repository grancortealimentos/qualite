<?php

use App\Models\User;
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
            $table->string('tipo_cadastro')->nullable();
            $table->string('nome_completo');
            $table->string('tipo_pessoa', 5)->nullable();
            $table->string('documento', 14)->nullable();
            $table->string('doc_profissional')->nullable();
            $table->string('contato', 15)->nullable();
            $table->string('cep', 8)->nullable();
            $table->string('logradouro')->nullable();
            $table->string('numero', 10)->nullable();
            $table->string('bairro')->nullable();
            $table->string('cidade')->nullable();
            $table->string('estado')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pessoas');
    }
};
