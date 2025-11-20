<?php

namespace App\Jobs\Imports;

use App\Models\Book;
use App\Models\Import;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessCsvImportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        protected string $path,
        protected int $importId
    ) {}

    public function handle(): void
    {
        /** @var Import $import */
        $import = Import::find($this->importId);
        if (!$import) {
            return;
        }

        $import->update(['status' => 'processing']);

        $fullPath = storage_path("app/{$this->path}");

        $csv = Reader::createFromPath($fullPath, 'r');
        $csv->setHeaderOffset(0);

        foreach ($csv as $record) {
            $isbn = $record['isbn'] ?? $record['ISBN'] ?? null;
            $title = $record['title'] ?? $record['Title'] ?? null;
            $authors = $record['authors'] ?? $record['Author'] ?? null;

            if (!$isbn && !$title) {
                continue;
            }

            Book::firstOrCreate(
                ['isbn_13' => $isbn],
                [
                    'title'   => $title ?: $isbn,
                    'authors' => $authors ? explode(',', $authors) : [],
                ]
            );
        }

        $import->update(['status' => 'done']);
    }
}
