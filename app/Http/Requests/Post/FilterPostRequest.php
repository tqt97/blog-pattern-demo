<?php

namespace App\Http\Requests\Post;

use App\DTOs\Post\PostFilter;
use Illuminate\Foundation\Http\FormRequest;

class FilterPostRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'search' => ['nullable', 'string'],
            'category_id' => ['nullable', 'integer', 'exists:categories,id'],
            'status' => ['nullable', 'in:draft,published'],
            'order_by' => ['nullable', 'in:created_at,published_at,title'],
            'direction' => ['nullable', 'in:asc,desc'],
        ];
    }

    public function toFilter(): PostFilter
    {
        return PostFilter::fromArray($this->validated());
    }
}
