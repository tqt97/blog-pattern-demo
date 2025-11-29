<?php

namespace App\Http\Requests\Tag;

use App\DTOs\Domains\Tag\TagFilterDTO;
use Illuminate\Foundation\Http\FormRequest;

class FilterTagRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    // public function authorize(): bool
    // {
    //     return false;
    // }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'q' => ['nullable', 'string', 'max:255'],
            'sort' => ['nullable', 'string', 'max:255', 'in:created_at,updated_at,name'],
            'direction' => ['nullable', 'string', 'max:255', 'in:asc,desc'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ];
    }

    public function toFilter(): TagFilterDTO
    {
        return TagFilterDTO::fromArray($this->validated());
    }
}
