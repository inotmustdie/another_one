<?php

namespace App\Http\Controllers;

use App\Jobs\RowsImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class FileImportController extends Controller
{
    public function importView( Request $request ) {
        return view( 'file-import' );
    }

    public function import( Request $request ) {
        $unique_file_name = $request->file( 'file' )->getClientOriginalName() . '_' . time();
        Excel::import( new RowsImport( $unique_file_name ),
            $request->file( 'file' ) );

        return redirect()->route( 'file_import.showData' );
    }

    public function showData( Request $request ) {
        return view( 'import-results' );
    }
}
