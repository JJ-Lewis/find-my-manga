<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Import;
use App\Services\Import\ImportService;
use App\Services\Import\GoodreadsImportService;
use Illuminate\Http\Request;

class ImportController extends Controller
{
    public function importFile(Request $request, ImportService $service)
    {
        $request->validate([
            'file'   => 'required|file|mimes:csv,txt,json',
            'source' => 'nullable|string',
        ]);

        $path = $request->file('file')->store('imports');

        $import = $service->createFileImport(
            $request->user()?->id,
            $path,
            $request->input('source', 'csv')
        );

        return response()->json([
            'import_id' => $import->id,
            'status'    => $import->status,
        ], 202);
    }

    public function importGoodreads(Request $request, GoodreadsImportService $service)
    {
        $data = $request->validate([
            'url' => 'required|url',
        ]);

        $import = $service->importFromUrl($data['url'], $request->user()?->id);

        return response()->json([
            'import_id' => $import->id,
            'status'    => $import->status,
        ], 202);
    }

    public function show(Import $import)
    {
        return response()->json($import);
    }
}
