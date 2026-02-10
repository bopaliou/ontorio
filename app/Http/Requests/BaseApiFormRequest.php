<?php

namespace App\Http\Requests;

use App\Traits\HandlesApiValidation;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

abstract class BaseApiFormRequest extends FormRequest
{
    use HandlesApiValidation;

    protected bool $requiresAuthentication = false;

    protected ?int $maxContentLengthBytes = null;

    public function authorize(): bool
    {
        return ! $this->requiresAuthentication || auth()->check();
    }

    public function withValidator($validator)
    {
        if ($this->maxContentLengthBytes === null) {
            return;
        }

        $validator->after(function (Validator $validator): void {
            $contentLength = (int) ($this->server('CONTENT_LENGTH') ?? 0);

            if ($contentLength > $this->maxContentLengthBytes) {
                $validator->errors()->add(
                    'request',
                    "La taille de la requête dépasse la limite autorisée de {$this->maxContentLengthBytes} octets."
                );
            }
        });
    public function authorize(): bool
    {
        return true;
    }
}
