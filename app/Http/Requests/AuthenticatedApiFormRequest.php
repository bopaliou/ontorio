<?php

namespace App\Http\Requests;

abstract class AuthenticatedApiFormRequest extends BaseApiFormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }
}
