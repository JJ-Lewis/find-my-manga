<?php

namespace App\Services\Import\Adapters;

use App\Jobs\Imports\ProcessImportJob;
use App\Models\Import;

class DefaultImportAdapter
{
    public function createImport(int $userId = null, string $path, string $source = 'csv'): Import
    {
        $import = Import::create([
            'user_id'   => $userId,
            'source'    => $source,
            'status'    => 'queued',
            'file_path' => $path,
            'meta'      => [],
        ]);

        ProcessImportJob::dispatch($path, $import->id);

        return $import;
    }
}
