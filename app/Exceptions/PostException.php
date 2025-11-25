<?php

namespace App\Exceptions;

use Exception;

class PostException extends Exception
{
    public static function notFound(string $identifier): self
    {
        return new self("Post not found: {$identifier}");
    }

    public static function slugExists(string $slug): self
    {
        return new self("Slug '{$slug}' already exists.");
    }
}
