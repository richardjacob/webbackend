<?php

namespace App\Http\Helper;
use DB;
use App\Models\Nid;
use App\Models\User;

class NidHelper
{
    public function __construct()
    {
        $this->doc_helper = resolve('App\Http\Helper\DocumentVerificationHelper');
    }

	public function verify_nid($nid, $user_id) 
    {       
        if($nid =='' OR $user_id ==''){
            return trans('messages.api.nid_required'); 
        }
        else{
            $user_nid_exist = DB::table('users')->where('nid_number', $nid)->first();

            if(is_object($user_nid_exist) AND $user_id != '-'){
                if($user_id == $user_nid_exist->id){
                    return trans('messages.api.you_used_nid');
                }else{
                    return trans('messages.api.driver_used_nid');
                }                
            }else{
                $destinationPath = public_path() . "/images/nid_photo/";
                if (!is_dir($destinationPath)) mkdir($destinationPath, 0777, true);
                $exists_nid = Nid::where('nid', $nid)->first();
                
                if (!is_object($exists_nid)) {
                    $nid_response = $this->doc_helper->nid($nid);

                    if (array_key_exists('message', $nid_response)) {
                        return $nid_response['message'];
                        //return trans('messages.api.nid_digit');
                    }
                    else if(array_key_exists('title', $nid_response) AND $nid_response['title'] == 'Unauthorized') {                
                        $err_table = new TechnicalError;
                        $err_table->type = 'nid_verification';
                        $err_table->error_details = $nid_response['title'];
                        $err_table->save();

                        return trans('messages.api.nid_unauthorized');
                    }
                    else if (array_key_exists('voter', $nid_response)) { 
                        $voter = $nid_response['voter'];

                        $nameEn = $voter['nameEn'] ?? '';
                        if($nameEn !=''){
                            $fatherEn = $voter['fatherEn'];
                            $motherEn = $voter['motherEn'];
                            $spouseEn = $voter['spouseEn'];
                            $presentAddressEn = $voter['presentAddressEn'];
                            $permanentAddressEn = $voter['permanentAddressEn'];

                            $name = $voter['name'];
                            $father = $voter['father'];
                            $mother = $voter['mother'];
                            $spouse = $voter['spouse'];
                            $presentAddress = $voter['presentAddress'];
                            $permanentAddress = $voter['permanentAddress'];

                            $gender = $voter['gender'];
                            $dob = $voter['dob'];
                            $photo = $voter['photo'];

                            if($gender == 'male') $gender ='1';
                            else if($gender == 'female') $gender ='1';
                            else $gender ='3';

                            list($m,$d,$y) = explode("/", $dob);
                            $dob = $y.'-'.$m.'-'.$d;

                            $table = new Nid;
                            $table->nid = $nid;
                            if($user_id !='-'){
                                $table->user_id = $user_id;
                            }                            
                            $table->name_en = $nameEn;
                            $table->father_en = $fatherEn;
                            $table->mother_en = $motherEn;
                            $table->spouse_en = $spouseEn;
                            $table->present_address_en = $presentAddressEn;
                            $table->permanent_address_en = $permanentAddressEn;

                            $table->name = $name;
                            $table->father = $father;
                            $table->mother = $mother;
                            $table->spouse = $spouse;
                            $table->present_address = $presentAddress;
                            $table->permanent_address = $permanentAddress;

                            $table->gender = $gender;
                            $table->dob = $dob;
                            $table->photo = $photo;

                            if($table->save()){    
                                if($user_id !='-'){                            
                                    $user_table = User::find($user_id);
                                    $user_table->nid_number = $nid;
                                    $user_table->save();
                                }

                                $link = self::base64_to_jpeg($photo, $destinationPath, $nid.'.png');

                                return trans('messages.success');
                            }else{
                                $err_table = new TechnicalError;
                                $err_table->type = 'db_save';
                                $err_table->error_details = 'nid table is not saving record.';
                                $err_table->save();
                                
                                return trans('messages.api.db_not_save');
                            } // if($nameEn !='')
                        }
                        else{
                            return trans('messages.api.db_not_save');
                        }
                    }   
                }else{
                    $photo_name = $nid.".png";
                    $profile_photo = "public/images/nid_photo/".$photo_name;

                    if(!file_exists($profile_photo)){   
                        if($user_id !='-'){             
                            $user_table = User::find($user_id);
                            $user_table->nid_number = $nid;
                            $user_table->save();
                        }

                        $base64_string = Nid::where('nid', $nid)->first()->photo;
                        $destinationPath = public_path() . "/images/nid_photo/";                
                        self::base64_to_jpeg($base64_string, $destinationPath, $photo_name);
                    }
                    if($exists_nid->user_id == '' OR $exists_nid->user_id == NULL) return 'user_id_null';
                    else return trans('messages.api.driver_used_nid'); 
                }
            }
        }
    }

    public function base64_to_jpeg($base64_string, $output_file, $photo_name)
    {
        // open the output file for writing
        $ifp = fopen($output_file.$photo_name, 'wb');

        // split the string on commas
        // $data[ 0 ] == "data:image/png;base64"
        // $data[ 1 ] == <actual base64 string>
        $data = explode(',', $base64_string);

        // we could add validation here with ensuring count( $data ) > 1
        fwrite($ifp, base64_decode($data[0]));

        // clean up the file resource
        fclose($ifp);

        return "//".env('ADMIN_PANEL_SUB_DOMAIN').".".env('DOMAIN')."/images/nid_photo/".$photo_name;
    }
}
