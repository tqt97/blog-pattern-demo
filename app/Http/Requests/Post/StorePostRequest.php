<?php

namespace App\Http\Requests\Post;

use App\DTOs\Post\PostDTO;
use Illuminate\Foundation\Http\FormRequest;

class StorePostRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->hasAnyRole(['admin', 'editor']) ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'user_id' => ['integer', 'exists:users,id'],
            'category_id' => ['nullable', 'integer', 'exists:categories,id'],
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255'], // có thể để null, trait HasSlug lo
            'excerpt' => ['required', 'string'],
            'content' => ['required', 'string'],
            'status' => ['required', 'string', 'in:draft,pending,published'],
            'published_at' => ['nullable', 'datetime'],
            'thumbnail' => ['nullable', 'image', 'mimes:jpeg,png', 'max:2048', 'dimensions:min_width=100,min_height=100'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:255'],
            'tag_ids' => ['array'],
            'tag_ids.*' => ['integer', 'exists:tags,id'],
        ];
    }

    public function prepareForValidation(): void
    {
        $this->merge([
            'user_id' => $this->user()?->id,
        ]);
    }

    public function toDto(): PostDTO
    {
        return PostDTO::fromArray($this->validated());
    }
}
