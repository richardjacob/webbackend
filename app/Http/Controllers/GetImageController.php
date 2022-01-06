<?php
namespace App\Http\Controllers;
use File;


class GetImageController extends Controller
{
    public function driver_doc($from, $to, $user_id, $image, $extension){
        $domain = $from.".".env('DOMAIN').'/';
        $file = 'images/users/'.$user_id.'/'.$image.'.'.$extension; 

        try {
            $destinationPath = public_path() . '/images/users/'.$user_id; 

			if (!is_dir($destinationPath)) {
				mkdir($destinationPath, 0777, true);
			}

            $content = $this->url_get_contents("https://".$domain.$file); 
            $fp = fopen($file, "w");
            fwrite($fp, $content);

        } catch(\Exception $e) {
            //echo "err: ".$e;
        }               
    } 

    public function vehicle_doc($from, $to, $vehicle_id, $image, $extension){
        $domain = $from.".".env('DOMAIN').'/';
        $file = 'images/vehicle/'.$vehicle_id.'/'.$image.'.'.$extension; 

        try {
            $destinationPath = public_path() . '/images/vehicle/'.$vehicle_id; 

			if (!is_dir($destinationPath)) {
				mkdir($destinationPath, 0777, true);
			}

            $content = $this->url_get_contents("https://".$domain.$file); 
            $fp = fopen($file, "w");
            fwrite($fp, $content);

        } catch(\Exception $e) {
            //echo "err: ".$e;
        }               
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