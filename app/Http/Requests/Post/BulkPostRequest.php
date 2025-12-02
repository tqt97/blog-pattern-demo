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

        if (! $rawIds) {
            return;
        }

        $ids = collect(explode(',', (string) $rawIds))
            ->map(fn ($id) => trim($id))
            ->filter()               // bỏ rỗng
            ->map(fn ($id) => (int) $id)
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
