<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class CPFRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $cpf = preg_replace('/[^0-9]/', '', $value);
        
        if (strlen($cpf) != 11) {
            $fail('validation.size.string')->translate(['attribute' => $attribute, 'size' => 11]);
            return;
        }

        if (preg_match('/^(\d)\1+$/', $cpf)) {
            $fail('validation.cpf')->translate(['attribute' => $attribute]);
            return;
        }
        
        for ($t = 9; $t < 11; $t++) {
             
            for ($d = 0, $c = 0; $c < $t; $c++) {
                $d += $cpf[$c] * (($t + 1) - $c);
            }
            $d = ((10 * $d) % 11) % 10;
            if ($cpf[$c] != $d) {
                $fail('validation.cpf')->translate(['attribute' => $attribute]);
            }
        }
    }
}
