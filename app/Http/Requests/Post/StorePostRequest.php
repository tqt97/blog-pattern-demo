<?php

namespace App\Http\Requests\Post;

use App\DTOs\Domains\Post\PostDTO;
use App\Enums\PostStatus;
use App\Rules\ValidPublishedAt;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Carbon;

class StorePostRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // return $this->user()?->hasAnyRole(['admin', 'editor']) ?? false;
        return true;
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
            'excerpt' => ['nullable', 'string'],
            'content' => ['required', 'string'],
            'status' => ['required', PostStatus::rule()],
            'published_at' => [new ValidPublishedAt($this->input('status'))],
            'thumbnail' => ['nullable', 'string', 'max:255'],
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
        $data = $this->validated(); // hoặc all()

        if (! empty($data['published_at'])) {
            // input type="datetime-local" trả dạng Y-m-d\TH:i
            $data['published_at'] = Carbon::parse($data['published_at']);
        } else {
            $data['published_at'] = null;
        }

        return PostDTO::fromArray($data);
    }
}
