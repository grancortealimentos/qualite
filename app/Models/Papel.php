<?php

namespace App\Models;

use App\Models\Concerns\Auditavel;
use Spatie\Permission\Models\Role as SpatieRole;

/**
 * Papel do sistema (tabela 'roles', do Spatie Permission).
 * 
 * Estende o model do pacote apenas para plugar o trait Auditavel - 
 * toda criação/alteração/exclusão de papel entra na trilha de auditoria,
 * o que o model original do Spatie não faz.
 * 
 * Para o Spatie usar esta classeno lugar dele, 
 * é obrigatório apontar config/permission.php => 'models.role' => App\Models\Papel::class.
 * Sem isso, métodos como assignRole() continuam devolvendo o model do pacote
 * e a auditoria simplemente não dispara - falha silenciosa.
*/
class Papel extends SpatieRole
{
    use Auditavel;

    /**
     * Verifica se este é o papel de sistema (Admin), protegido contra
     * edição e exclusão no UI. Centraliza a comparação para não espalhar 
     * config('permissoes.papel_administrador') pelas telas.
    */
    public function ehPapelSistema(): bool
    {
        return $this->name === config('permissoes.papel_administrador');
    }
}