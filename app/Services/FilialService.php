<?php

namespace App\Services;

use App\DTO\FilialData;
use App\Models\Filial;
use App\Repositories\FilialRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class FilialService
{
    public function __construct(
        private readonly FilialRepository $filialRepository,
    ) {}

    public function create(FilialData $data): Filial
    {
        return DB::transaction(function () use ($data) {
            $this->validarCnpjUnico($data->cnpj);
            return $this->filialRepository->create($data->toArray());
        });
    }

    public function update(int $id, FilialData $data): Filial
    {
        return DB::transaction(function () use ($id, $data) {
            $filial = $this->filialRepository->buscarPorIdOuFalhar($id);
            $this->validarCnpjUnico($data->cnpj, $id);

            return $this->filialRepository->update(
                $filial,
                $data->toArray()
            );
        }); 
    }

    public function delete(int $id): void
    {
        DB::transaction(function () use ($id) {
            $filial = $this->filialRepository->buscarPorIdOuFalhar($id);
            $this->filialRepository->delete($filial);
        });
    }

    public function alterarStatus(int $id): Filial
    {
        return DB::transaction(function () use ($id) {
            $filial = $this->filialRepository->buscarPorIdOuFalhar($id);
            $this->filialRepository->alterarStatus($filial);

            return $filial->refresh();
        }); 
    }

    private function validarCnpjUnico(?string $cnpj, ?int $ignorarId = null): void
    {
        if(empty($cnpj)) {
            return;
        }

        if($this->filialRepository->existeCnpj($cnpj, $ignorarId)) {
            throw ValidationException::withMessages([
                'cnpj' => 'Já existe uma filial cadastrada com este CNPJ.',
            ]);
        }
    }
}