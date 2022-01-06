<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\DataTables\BonuseDataTable;

class BonuseController extends Controller
{
    
    public function index(BonuseDataTable $dataTable)
    {
        return $dataTable->render('admin.bonuse.view');
    }
}
