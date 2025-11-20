<?php

namespace App\Services\Import\Adapters;

use App\Jobs\Imports\ProcessImportJob;
use App\Models\Import;

class GoodreadsImportAdapter
{
    public function createImport(string $url, ?int $userId = null): Import
    {
        // For now, assume we downloaded the CSV from Goodreads and stored it
        // In reality, you'd: fetch the page/API, transform to CSV/JSON, save file, dispatch ProcessImportJob

        $fakePath = 'imports/goodreads_' . uniqid() . '.csv';

        $import = Import::create([
            'user_id'   => $userId,
            'source'    => 'goodreads',
            'status'    => 'queued',
            'file_path' => $fakePath,
            'meta'      => ['url' => $url],
        ]);

        ProcessImportJob::dispatch($fakePath, $import->id);

        return $import;
    }
}
