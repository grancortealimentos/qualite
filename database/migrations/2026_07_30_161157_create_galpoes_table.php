<?php

use App\Models\Propriedade;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('galpoes', function (Blueprint $table) {
            $table->id();
            $table->boolean('eh_ativo')->default(true);
            $table->foreignIdFor(Propriedade::class, 'propriedade_id');
            $table->string('tipo');
            $table->string('nome');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('galpoes');
    }
};
