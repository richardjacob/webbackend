<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function transferFile($from_api, $to_api, $file_name, $user_id){
    	$file_info = explode('.', $file_name);
        $ext = $file_info[count($file_info) - 1];
        $fileName = str_replace(".".$ext, "", $file_name);        

        $domain ="";
        if (env('ROOT_DOMAIN_SUB_DOMAIN') != '') $domain.= env('ROOT_DOMAIN_SUB_DOMAIN').".";
        $domain.= env('DOMAIN');
        
        $this->url_get_contents("https://".$domain."/getImage/".$from_api."/".$to_api."/".$user_id."/".$fileName.'/'.$ext); 
    }

    public function transferVehicle($from_api, $to_api, $file_name, $vehicle_id){
    	$file_info = explode('.', $file_name);
        $ext = $file_info[count($file_info) - 1];
        $fileName = str_replace(".".$ext, "", $file_name);        

        $domain ="";
        if (env('ROOT_DOMAIN_SUB_DOMAIN') != '') $domain.= env('ROOT_DOMAIN_SUB_DOMAIN').".";
        $domain.= env('DOMAIN');
        
        $this->url_get_contents("https://".$domain."/getVehicleImage/".$from_api."/".$to_api."/".$vehicle_id."/".$fileName.'/'.$ext); 
    }


    public function url_get_contents ($Url) {
        if (!function_exists('curl_init')){ 
            die('CURL is not installed!');
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $Url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $output = curl_exec($ch);
        curl_close($ch);
        return $output;
    }  

    

   
}
