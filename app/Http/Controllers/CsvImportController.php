<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\DataImport;

class CsvImportController extends Controller
{
    public function showForm()
    {
        return view('form'); // Blade view with a file upload form
    }

    public function import(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|mimes:csv,txt|max:10240', // 10MB limit
        ]);

        $import = new DataImport;
        Excel::import($import, $request->file('csv_file'));

        $invalidRows = $import->getInvalidRows();
        $duplicateRows = $import->getDuplicateRows();

        $totalRows = $import->getTotalRows();
        $newRows = $import->getNewRows();
        $updatedRows = $import->getUpdatedRows();

        return redirect()->back()
            ->with('success', 'CSV import started!')
            ->with('invalidRows', $invalidRows)
            ->with('invalidRowsCount', count($invalidRows))
            ->with('duplicateRows', $duplicateRows)
            ->with('duplicateRowsCount', count($duplicateRows))
            ->with('totalRows', $totalRows)
            ->with('newRows', $newRows)
            ->with('updatedRows', $updatedRows);
    }
}