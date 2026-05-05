<?php

namespace App\Http\Controllers;

use App\Exports\CategoryExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ExportController extends Controller
{
    private $exportModels;
    public function __construct()
    {
        $this->exportModels = [
            'categories'       => CategoryExport::class,
        ];
    }
    public function export(Request $request,$page)
    {
        if (!array_key_exists($page, $this->exportModels)) {
            return abort(404, 'Not Found');
        }

        $file_name = $page.'_'.date('Y_m_d_h_i_s',time()).'.xlsx';
        return Excel::download(new $this->exportModels[$page], $file_name);
    }
}
