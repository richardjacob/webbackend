<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\DataTables\ActivityLogDataTable;
use App\DataTables\AuditLogDataTable;
use DirectoryIterator;
use Auth;
use Session;

class LogsController extends Controller
{
    public function activity_log(ActivityLogDataTable $dataTable)
    {
        return $dataTable->render('admin.logs.activity_log');
    }
    public function audit_log(AuditLogDataTable $dataTable, Request $r)
    {
        $array = array(
            'user_id' => $r->search_user,
            'vehicle_id' => $r->vehicle_id,
        );

        return $dataTable->with($array)->render('admin.logs.audit_log');
    }
    public function sys_log()
    {
        $dir_path = public_path() . '/logs';
        $dir = new DirectoryIterator($dir_path);        
           
        $Url = 'https://'.env('DOMAIN').'/sys_log_from_api';
        $apiLog = $this->url_get_contents ($Url);
        if($apiLog !='') {
            $api_log_data = explode(',', $apiLog);
            foreach($api_log_data as $apiLogdata){
                if($apiLogdata !=''){
                    list($name, $size) = explode(' ', $apiLogdata);
                    $api_log[] = array('name' => $name, 'size' => $size);
                }
            }
        }
        else $api_log = array('name' => '', 'size' => '');


        return view('admin.logs.sys_log')
                    ->with('dir', $dir)
                    ->with('api_log', $api_log);
    }

    public function sys_log_from_api()
    {
        $dir_path = public_path() . '/logs';
        $dir = new DirectoryIterator($dir_path);
        
        $files = array();
        foreach($dir as $key => $file){
            if (!$file->isDot()) {
                echo $file->getFilename().' '.number_format(($file->getSize()/(1024*1024)),2).',';
            }
        }       
    }




    public function delete_log_file($file_name)
    {
        unlink(public_path()."/logs/".$file_name);
        return redirect('admin/sys_log');
    }
    public function delete_api_log_file($file_name)
    {
        $Url = 'https://'.env('DOMAIN').'/delete_from_admin_log_file?file='.$file_name;
        $this->url_get_contents ($Url);
        return redirect('admin/sys_log');        
    }

    public function delete_from_admin_log_file(Request $r)
    {
        if(unlink(public_path()."/logs/".$r->file)) echo "1";
    }

    public function api_logs(Request $r)
    {
        if($auth = Auth::guard('admin')->user()){
            echo '
                <body onload="setTimeout(function() { document.frm1.submit() }, 0)">
                    <form action="//'.env('DOMAIN').'/admin/logs_login" name="frm1" method="post">
                        <input type="hidden" name="username" value="'.$auth->username.'" />
                        <input type="hidden" name="password" value="'.Session::get('admin_password').'" />
                    </form>
                </body>
            ';
        }
    }
    
    
    
}
