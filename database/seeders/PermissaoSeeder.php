<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class PermissaoSeeder extends Seeder
{
    /**
     * Sincroniza a tabela 'permissions' com o catálogo de config/permissoes.php
     * e garante a existência do papel administrador.
     * 
     * É idempotente: pode rodar quantas vezes for necessário sem duplicar nada.
    */
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        DB::transaction(function () {
            $this->criarPermissoes($this->nomesDoCatalogo());
            $this->garantirPapelAdministrador();
        });

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    /**
     * Achata o catálogo agrupado da config em uma lista simples com os nomes
     * técnicos das permissões (ex.: ['permissoes.visualizar'])
    */
    private function nomesDoCatalogo(): array
    {
        $nomes = [];
        foreach(config('permissoes.grupos', []) as $grupo) {
            $nomes = array_merge($nomes, array_keys($grupo['permissoes'] ?? []));
        }

        return $nomes;
    }

    /**
     * Cria no banco as permissões do catálogo que ainda não existem.
     * 
     * o guard_name vai explícito de propósito: permissão criada com guard
     * diferente do usado na autenticação nunca é encontrada na checagem,
     * e a falha é silenciosa - parece bug, mas é guard errado.
    */
    private function criarPermissoes(array $nomes): void
    {
        foreach($nomes as $nome) {
            Permission::firstOrCreate([
                'name' => $nome,
                'guard_name' => 'web'
            ]);
        }
    }

    /**
     * Garante que o papel Admin exista.
     * 
     * Não atribuímos permisões a ele de propósito: o bypass será feito
     * via Gate::before, então o papel não precisa carregar a lsita inteira - e
     * assim nunca fica desatualizado quando entra permissão nova no catalogo;
    */
    private function garantirPapelAdministrador(): void
    {
        Role::firstOrCreate([
            'name' => config('permissoes.papel_administrador'),
            'guard_name' => 'web'
        ]);
    }
}
