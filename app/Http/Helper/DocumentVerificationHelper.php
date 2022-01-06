<?php

namespace App\Http\Helper;
use DB;


class DocumentVerificationHelper
{
	public function nid($nid)
	{
		$cof_data = Config('verification.nid');
		
		$PostData = array(
						'national_id' => $nid, 
						'team_tx_id' => '',
						'english_output' => true,
						'person_dob' => '',
					);

		$url = curl_init($cof_data['url']);
        $postToken = json_encode($PostData);
        $header = array(
            'Content-Type:application/json',
            'x-api-key:'.$cof_data['key']
        );
        
        curl_setopt($url, CURLOPT_HTTPHEADER, $header);
        curl_setopt($url, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($url, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($url, CURLOPT_POSTFIELDS, $postToken);
        curl_setopt($url, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($url, CURLOPT_SSL_VERIFYPEER, false); 
        
        $resultData = curl_exec($url);
        $ResultArray = json_decode($resultData, true);
        curl_close($url);
        return $ResultArray;
	}
}
