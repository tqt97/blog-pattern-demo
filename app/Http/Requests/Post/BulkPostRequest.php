<?php

namespace App\Http\Requests\Post;

use Illuminate\Foundation\Http\FormRequest;

class BulkPostRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Ví dụ: chỉ admin mới được bulk
        // return auth()->user()?->can('manage-posts') ?? false;

        return true; // nếu đã authorize ở controller
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'ids' => ['required', 'array'],
            'ids.*' => ['integer', 'exists:posts,id'],
            'action' => ['required', 'in:delete,restore,force_delete,publish,unpublish'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $rawIds = $this->input('ids');

        if (is_string($rawIds)) {
            $ids = collect(explode(',', $rawIds));
        } elseif (is_array($rawIds)) {
            $ids = collect($rawIds);
        } else {
            $ids = collect();
        }

        $ids = $ids
            ->map(fn ($id) => (int) trim((string) $id))
            ->filter()
            ->unique()
            ->values()
            ->all();

        $this->merge([
            'ids' => $ids,
        ]);
    }

    public function ids(): array
    {
        return $this->validated('ids');
    }
}
