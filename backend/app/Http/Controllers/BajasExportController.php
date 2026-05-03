<?php

namespace App\Http\Controllers;

use App\Exports\BajasRegistradasExport;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Maatwebsite\Excel\Facades\Excel;

class BajasExportController
{
    public function download()
    {
        return Excel::download(new BajasRegistradasExport, 'bajas_registradas.xlsx');
    }
}
