<?php

namespace App\Http\Requests\Category;

use App\DTOs\Domains\Category\CategoryFilterDTO;
use Illuminate\Foundation\Http\FormRequest;

class FilterCategoryRequest extends FormRequest
{
    // /**
    //  * Determine if the user is authorized to make this request.
    //  */
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
            'search' => ['nullable', 'string', 'max:255'],
            'sort' => ['nullable', 'string', 'max:255', 'in:created_at,updated_at,name'],
            'direction' => ['nullable', 'string', 'max:255', 'in:asc,desc'],
            // 'from_date' => ['nullable', 'date'],
            // 'to_date'   => ['nullable', 'date', 'after_or_equal:from_date'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ];
    }

    public function messages(): array
    {
        return [
            'sort.in' => 'Colum sort invalid.',
            'direction.in' => 'Direction invalid.',
        ];
    }

    public function toFilter(): CategoryFilterDTO
    {
        return CategoryFilterDTO::fromArray($this->validated());
    }
}
