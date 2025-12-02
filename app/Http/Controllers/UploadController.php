<?php

namespace App\Http\Controllers;

use App\Services\UploadService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class UploadController extends Controller
{
    public function __construct(
        private UploadService $uploader,
    ) {}

    public function store(Request $request, string $type): JsonResponse
    {
        try {
            $request->validate([
                'file' => $this->uploader->rulesFor($type),
            ]);
        } catch (ValidationException $e) {
            Log::error($e->errors());

            // Trả JSON 422 với message rõ ràng
            return response()->json([
                'message' => 'Upload validation failed',
                'errors' => $e->errors(), // ['file' => ['The file must be an image.']]
            ], 422);
        }

        $file = $request->file('file');

        $data = $this->uploader->upload($file, $type);

        return response()->json($data->toArray());
    }

    public function destroy(Request $request, string $type): JsonResponse
    {
        $request->validate([
            'path' => ['required', 'string'],
        ]);

        $deleted = $this->uploader->delete($request->string('path'), $type);

        return response()->json([
            'deleted' => $deleted,
        ]);
    }
}
