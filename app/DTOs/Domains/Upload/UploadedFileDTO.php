<?php

namespace App\DTOs\Domains\Upload;

use App\DTOs\BaseDTO;

class UploadedFileDTO extends BaseDTO
{
    public function __construct(
        public readonly string $path,          // URL for front-end: /storage/...
        public readonly string $relative,      // uploads/posts/xxx.jpg
        public readonly string $filename,      // Filename: abc.jpg
        public readonly string $disk,          // Disk: public
        public readonly string $original,      // original filename from client
        public readonly string $type,          // posts / banners / ...
    ) {}
}
