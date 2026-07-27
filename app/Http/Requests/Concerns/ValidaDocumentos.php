<?php

namespace App\Http\Requests\Concerns;

trait ValidaDocumentos
{
    /**
     * Valida o dígito verificador de um CNPJ (espera 14 dígitos, já sem máscara).
     * O Laravel não tem regra nativa 'cnpj'; a checagem real vive aqui.
     */
    protected function cnpjValido(string $cnpj): bool
    {
        if (strlen($cnpj) !== 14 || preg_match('/^(\d)\1{13}$/', $cnpj)) {
            return false;
        }

        $calcular = function (int $tamanho) use ($cnpj): int {
            $pesos = $tamanho === 12
                ? [5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2]
                : [6, 5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];

            $soma = 0;
            for ($i = 0; $i < $tamanho; $i++) {
                $soma += (int) $cnpj[$i] * $pesos[$i];
            }

            $resto = $soma % 11;

            return $resto < 2 ? 0 : 11 - $resto;
        };

        return $calcular(12) === (int) $cnpj[12]
            && $calcular(13) === (int) $cnpj[13];
    }

    /**
     * Valida o dígito verificador de um CPF (espera 11 dígitos, já sem máscara).
     */
    protected function cpfValido(string $cpf): bool
    {
        if (strlen($cpf) !== 11 || preg_match('/^(\d)\1{10}$/', $cpf)) {
            return false;
        }

        for ($t = 9; $t < 11; $t++) {
            $soma = 0;
            for ($i = 0; $i < $t; $i++) {
                $soma += (int) $cpf[$i] * (($t + 1) - $i);
            }

            $digito = ((10 * $soma) % 11) % 10;
            if ((int) $cpf[$t] !== $digito) {
                return false;
            }
        }

        return true;
    }
}