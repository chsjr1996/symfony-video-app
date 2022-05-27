<?php

namespace App\Service\Implementations;

use App\DTO\UploadedVideoDTO;
use App\Service\Interfaces\VideoUploaderInterface;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class LocalVideoUploader implements VideoUploaderInterface
{
    /**
     * @param string $targetDirectory related with config/services.yaml injector
     */
    public function __construct(
        private string $targetDirectory
    ) {
    }

    public function upload(UploadedFile $file): ?UploadedVideoDTO
    {
        try {
            $videoNumber = random_int(1, 1000000);
            $fileName = uniqid('local_video_') . $videoNumber . '.' . $file->guessExtension();
            $file->move($this->getTargetDirectory(), $fileName);
            $originalFileName = $this->clear(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));

            return (new UploadedVideoDTO)
                ->setFileName($fileName)
                ->setOriginalFileName($originalFileName);
        } catch (FileException $ex) {
            // TODO: Log
            return null;
        }
    }

    public function delete(string $path): bool
    {
        try {
            $fileSystem = new Filesystem();
            $fileSystem->remove('.' . $path);

            return true;
        } catch (IOExceptionInterface $ex) {
            // TODO: Log
            return false;
        } catch (\Exception) {
            // TODO: Log
            return false;
        }
    }

    private function getTargetDirectory(): string
    {
        return $this->targetDirectory;
    }

    private function clear(string $filePath): string
    {
        return preg_replace('/[^A-Za-z0-9- ]+/', '', $filePath);
    }
}
