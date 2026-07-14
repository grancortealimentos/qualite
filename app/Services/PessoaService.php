<?php

namespace App\Services;

use App\DTO\PessoaData;
use App\Models\Pessoa;
use App\Repositories\PessoaRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PessoaService
{
    private const DIRETORIO_FOTOS = 'pessoas/fotos';

    public function __construct(
        private readonly PessoaRepository $pessoaRepository,
        private readonly UsuarioService $usuarioService,
    ) {}

    public function listar(array $filtros = [], int $porPagina = 10): LengthAwarePaginator
    {
        return $this->pessoaRepository->paginar($filtros, $porPagina);
    }

    public function create(PessoaData $data, ?UploadedFile $foto = null): Pessoa
    {
        return DB::transaction(function () use ($data, $foto) {
            if($foto) {
                $data = $data->comFoto($this->salvarFoto($foto));
            }

            return $this->pessoaRepository->create($data);
        });
    }

    public function update(Pessoa $pessoa, PessoaData $data, ?UploadedFile $foto = null): Pessoa
    {
        return DB::transaction(function () use ($pessoa, $data, $foto) {
            if($foto) {
                $fotoAntiga = $pessoa->url_foto_perfil;
                $data = $data->comFoto($this->salvarFoto($foto));

                $pessoa = $this->pessoaRepository->update($pessoa, $data);

                //Só remove a antiga depois que o update deu certo
                $this->removerFoto($fotoAntiga);

                return $pessoa;
            }

            return $this->pessoaRepository->update($pessoa, $data);
        });
    }

    public function delete(Pessoa $pessoa): bool
    {
        //Soft delete: a foto é preservada para eventual restauração
        return $this->pessoaRepository->delete($pessoa);
    }

    public function alternarStatus(Pessoa $pessoa): Pessoa
    {
        return $this->pessoaRepository->alterarStatus($pessoa);
    }

    private function salvarFoto(UploadedFile $foto): string
    {
        return $foto->store(self::DIRETORIO_FOTOS, 'public');
    }

    private function removerFoto(?string $caminho): void
    {
        if($caminho && Storage::disk('public')->exists($caminho)) {
            Storage::disk('public')->delete($caminho);
        }
    }
}