<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Papel de sistema
    |--------------------------------------------------------------------------
    | Papel criado pelo seeder, protegido contra edição/exclusão na tela de
    | papéis e com bypass total via Gate::before. Centralizado aqui para não
    | espalhar a string "Administrador" pelo código.
    */
    'papel_administrador' => 'Admin',

    /*
    |--------------------------------------------------------------------------
    | Catálogo de permissões
    |--------------------------------------------------------------------------
    | Fonte única da verdade (RN-010). Quem adiciona permissão é o dev, aqui.
    | O seeder lê este array para popular a tabela `permissions`; a tela de
    | papéis lê o mesmo array para montar as colunas MÓDULO / PERMISSÕES.
    |
    | Estrutura de cada grupo:
    |   'codigo'     => código fixo do módulo, usado para exibição e ordenação.
    |                   NUNCA é gravado no banco e NUNCA é reciclado: módulo
    |                   removido queima o número, módulo novo entra no fim.
    |   'label'      => nome do módulo exibido na coluna MÓDULO.
    |   'permissoes' => [nome técnico gravado em `permissions` => rótulo legível].
    |
    | O nome técnico da permissão é colado na ENTIDADE (pessoas.*, usuarios.*),
    | não no módulo. Por isso um mesmo módulo pode conter prefixos diferentes:
    | a Policy protege a entidade, o módulo só agrupa para o admin.
    |
    | Códigos reservados (ainda sem permissões):
    |   01 - Dashboard
    |   03 - Papéis
    |   04 - Logs de erro
    |   05 - Propriedades
    */
    'grupos' => [
        'pessoas' => [
            'codigo' => '02',
            'label' => 'Pessoas',
            'permissoes' => [
                'pessoas.visualizar' => 'Listar e visualizar pessoas',
                'pessoas.criar' => 'Cadastrar pessoas',
                'pessoas.editar' => 'Editar pessoas',
                'pessoas.excluir' => 'Excluir pessoa',
                'pessoas.status' => 'Ativar ou inativar pessoa',

                'usuarios.visualizar' => 'Listar e visualizar usuários',
                'usuarios.criar' => 'Cadastrar usuários',
                'usuarios.editar' => 'Editar usuários',
                'usuarios.excluir' => 'Excluir usuários',
                'usuarios.gerenciar_permissoes' => 'Atribuir permissões diretas ao usuário',
                'usuarios.revogar' => 'Revogar acesso de usuário',
                'usuarios.reativar' => 'Reativar acesso de usuário',         
            ],
        ],
    ],
];