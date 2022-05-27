<?php

namespace App\Service\Interfaces;

use App\DTO\UploadedVideoDTO;
use Symfony\Component\HttpFoundation\File\UploadedFile;

interface VideoUploaderInterface
{
    public function upload(UploadedFile $file): ?UploadedVideoDTO;
    public function delete(string $path): bool;
}