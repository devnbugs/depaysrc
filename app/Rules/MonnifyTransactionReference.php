<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class MonnifyTransactionReference implements Rule
{
    public function passes($attribute, $value)
    {
        // Define a regular expression pattern for the expected format
        $pattern = '/^MNFY\|\d+\|\d{14}\|\d+$/';

        return preg_match($pattern, $value) === 1;
    }

    public function message()
    {
        return 'The :attribute must be in the format MNFY|87|20230629132018|049239.';
    }
}
