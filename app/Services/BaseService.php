<?php

namespace App\Services;

class BaseService
{
    protected function uploadImages(array $files, string $folder): array
    {
        $paths = [];
        foreach ($files as $file) {
            $paths[] = $file->store($folder, 'public');
        }
        return $paths;
    }
}
