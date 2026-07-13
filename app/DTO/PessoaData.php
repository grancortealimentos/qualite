<?php

namespace App\DTO;

class PessoaData
{
    public function __construct(
        //campos obrigatorios
        public readonly string $tipoCadastro,
        public readonly string $nomeCompleto,

        //campos com valor padrão
        public readonly bool $ehAtivo = true,
        public readonly string $pais = 'Brasil',

        //campos opcionais
        public readonly ?string $urlFotoPerfil = null,
        public readonly ?string $tipoDocumento = null,
        public readonly ?string $documento = null,
        public readonly ?string $docProfissional = null,
        public readonly ?string $telefone = null,
        public readonly ?string $email = null,
        public readonly ?string $cep = null,
        public readonly ?string $logradouro = null,
        public readonly ?string $numero = null,
        public readonly ?string $bairro = null,
        public readonly ?string $cidade = null,
        public readonly ?string $estado = null,
        
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            $data['tipo_documento'],
            $data['nome_completo'],

            $data['eh_ativo'] ?? true,
            $data['pais'] ?? 'Brasil',

            $data['url_foto_perfil'] ?? null,
            $data['tipo_cadastro'] ?? null,
            $data['documento'] ?? null,
            $data['doc_profissional'] ?? null,
            $data['telefone'] ?? null,
            $data['email'] ?? null,
            $data['cep'] ?? null,
            $data['logradouro'] ?? null,
            $data['numero'] ?? null,
            $data['bairro'] ?? null,
            $data['cidade'] ?? null,
            $data['estado'] ?? null,
        );
    }

    public function comFoto(?string $url): self
    {
        return new self(
            $this->tipoCadastro,
            $this->nomeCompleto,
            $this->ehAtivo,
            $this->pais,
            $url,
            $this->tipoDocumento,
            $this->documento,
            $this->docProfissional,
            $this->telefone,
            $this->email,
            $this->cep,
            $this->logradouro,
            $this->numero,
            $this->bairro,
            $this->cidade,
            $this->estado,
        );
    }

    public function toArray(): array
    {
        return [
            'eh_ativo' => $this->ehAtivo,
            'url_foto_perfil' => $this->urlFotoPerfil,
            'tipo_cadastro' => $this->tipoCadastro,
            'nome_completo' => $this->nomeCompleto,
            'tipo_documento' => $this->tipoDocumento, 
            'documento' => $this->documento,
            'doc_profissional' => $this->docProfissional, 
            'telefone' => $this->telefone,
            'email' => $this->email, 
            'cep' => $this->cep,
            'logradouro' => $this->logradouro,
            'numero' => $this->numero,
            'bairro' => $this->bairro,
            'cidade' => $this->cidade,
            'estado' => $this->estado,
            'pais' => $this->pais, 
        ];
    }

    public function toArrayParaUpdate(): array
    {
        $dados = $this->toArray();
        if($this->urlFotoPerfil === null) {
            unset($dados['url_foto_perfil']);
        }

        return $dados;
    }
}