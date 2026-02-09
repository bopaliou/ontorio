<?php

namespace App\Http\Requests;

use App\Traits\HandlesApiValidation;
use Illuminate\Foundation\Http\FormRequest;

abstract class BaseApiFormRequest extends FormRequest
{
    use HandlesApiValidation;

    public function authorize(): bool
    {
        return true;
    }
}
