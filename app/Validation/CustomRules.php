<?php

namespace App\Validation;

/**
 * CustomRules class for data validations.
 */
class CustomRules
{
    /**
     * Checks if a password meets security requirements.
     *
     * @param string $str The password to be checked.
     * @param string $error (optional) Error message, if the password is not valid.
     *
     * @return bool TRUE if the password is valid, FALSE otherwise.
     */
    public function strongPassword(string $str, string &$error = null): bool
    {
        if (strlen($str) < 8) {
            $error = 'A senha deve ter pelo menos 8 caracteres.';
            return false;
        }

        if (!preg_match('/[A-Z]/', $str)) {
            $error = 'A senha deve conter pelo menos uma letra maiúscula.';
            return false;
        }

        if (!preg_match('/[a-z]/', $str)) {
            $error = 'A senha deve conter pelo menos uma letra minúscula.';
            return false;
        }

        if (!preg_match('/[0-9]/', $str)) {
            $error = 'A senha deve conter pelo menos um número.';
            return false;
        }

        if (!preg_match('/[\W]/', $str)) {
            $error = 'A senha deve conter pelo menos um caractere especial.';
            return false;
        }

        return true;
    }

    /**
     * Validates a CNPJ (Cadastro Nacional da Pessoa Jurídica).
     *
     * @param string $cnpj The CNPJ to be validated.
     * @param string $error (optional) Error message, if the CNPJ is not valid.
     *
     * @return bool TRUE if the CNPJ is valid, FALSE otherwise.
     */
    public function validateCNPJ(string $cnpj, string &$error = null): bool
    {
        // Remove any non-numeric characters
        $cnpj = preg_replace('/\D/', '', $cnpj);

        // Check if the CNPJ has exactly 14 digits
        if (strlen($cnpj) !== 14) {
            $error = 'O CNPJ deve ter 14 caracteres numéricos. ERROR-CNPJ-RULES-1';
            return false;
        }

        // Eliminate known invalid CNPJs
        if (preg_match('/^(\d)\1{13}$/', $cnpj)) {
            $error = 'O CNPJ informado é inválido. ERROR-CNPJ-RULES-2';
            return false;
        }

        // Validate verification digits
        $sum1 = 0;
        $sum2 = 0;
        $weights1 = [5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];
        $weights2 = [6, 5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];

        for ($i = 0; $i < 12; $i++) {
            $sum1 += $cnpj[$i] * $weights1[$i];
        }

        $remainder = $sum1 % 11;
        $firstDigit = ($remainder < 2) ? 0 : 11 - $remainder;

        if ($cnpj[12] != $firstDigit) {
            $error = 'O CNPJ informado é inválido. ERROR-CNPJ-RULES-3';
            return false;
        }

        for ($i = 0; $i < 13; $i++) {
            $sum2 += $cnpj[$i] * $weights2[$i];
        }

        $remainder = $sum2 % 11;
        $secondDigit = ($remainder < 2) ? 0 : 11 - $remainder;

        if ($cnpj[13] != $secondDigit) {
            $error = 'O CNPJ informado é inválido. ERROR-CNPJ-RULES-4';
            return false;
        }

        return true;
    }

    /**
     * Validates a Brazilian CEP (ZIP code).
     *
     * @param string $cep The CEP to be validated.
     * @param string $error (optional) Error message, if the CEP is not valid.
     *
     * @return bool TRUE if the CEP is valid, FALSE otherwise.
     */
    public function validateCEP(string $cep, string &$error = null): bool
    {
        // Removes any non-numeric character
        $cep = preg_replace('/\D/', '', $cep);

        // Verifica se o CEP tem exatamente 8 dígitos
        if (strlen($cep) !== 8) {
            $error = 'O CEP deve ter exatamente 8 dígitos numéricos.';
            return false;
        }

        // Simple ZIP code verification (can be customized to include more validations)
        if (!preg_match('/^[0-9]{8}$/', $cep)) {
            $error = 'O CEP informado é inválido.';
            return false;
        }

        return true;
    }
}
