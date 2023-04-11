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
        
        for ($i = 9, $j = 0, $sum = 0; $i > 0; $i--, $j++) {
            $sum += $cpf[$j] * $i;
        }
        $mod = $sum % 11;
        $dv1 = ($mod < 2) ? 0 : 11 - $mod;
        
        for ($i = 10, $j = 0, $sum = 0; $i > 0; $i--, $j++) {
            $sum += $cpf[$j] * $i;
        }
        $mod = $sum % 11;
        $dv2 = ($mod < 2) ? 0 : 11 - $mod;
        
        if ($cpf[9] != $dv1 || $cpf[10] != $dv2) {
            $fail('validation.cpf')->translate(['attribute' => $attribute]);
        }
    }
}
