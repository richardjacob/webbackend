<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\DataTables\SosMessagesDataTable;

class SosMessagesList extends Controller
{
    public function index(SosMessagesDataTable $dataTable)
    {
        return $dataTable->render('admin.sos_messages');
    }
}
