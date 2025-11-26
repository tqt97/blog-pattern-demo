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
            'search' => ['nullable', 'string', 'max:255'],
            'category_id' => ['nullable', 'integer', 'exists:categories,id'],
            'status' => ['nullable', 'in:draft,pending,published'],
            'user_id' => ['nullable', 'integer', 'exists:users,id'],
            'only_published' => ['nullable', 'boolean'],
            'order_by' => ['nullable', 'in:published_at,created_at,view_count'],
            'direction' => ['nullable', 'in:asc,desc'],
        ];
    }

    public function toFilter(): PostFilter
    {
        // validated() đảm bảo dữ liệu đúng kiểu/format
        $data = $this->validated();

        // Nếu là frontend, có thể default only_published = true
        if (! $this->has('only_published')) {
            $data['only_published'] = true;
        }

        return PostFilter::fromArray($data);
    }
}
