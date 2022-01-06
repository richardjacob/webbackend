<?php

namespace App\Http\Controllers\Admin;

use App\Services\PushNotificationService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\User;
use App\Models\Company;
use App\Models\ScheduleMessage;
use App\Http\Start\Helpers;
use App\Http\Helper\RequestHelper;
use Auth;
use Validator;
use DB;
use Illuminate\Validation\ValidationException;

//Added By Nishat
use Stichoza\GoogleTranslate\GoogleTranslate;
use SoapClient;
//Added By Nishat End

class SendmessageController extends Controller
{
    /**
     * @var PushNotificationService
     */
    private $pushNotificationService;

    /**
     * @param RequestHelper $request
     * @param PushNotificationService $pushNotificationService
     */
    public function __construct(RequestHelper $request, PushNotificationService $pushNotificationService)
    {
        $this->request_helper = $request;
        $this->helper = new Helpers;
        $this->sms_helper = resolve('App\Http\Helper\SmsHelper');
        $this->pushNotificationService = $pushNotificationService;
    }

    /**
     * Load Index View for Dashboard
     *
     * @return view index
     */
    public function index(Request $request)
    {
        if (!$_POST) {
            return view('admin.send_message');
        } elseif ($request->submit) {
            // Send Email Validation Rules
            $rules = array(
                'txtEditor' => 'required',
                'user_type' => 'required',
            );

            if ($request->to != 'to_all')
                $rules['users'] = 'required';

            // Send Email Validation Custom Names
            $attributes = array(
                'txtEditor' => 'Message',
                'users'     => (LOGIN_USER_TYPE == 'company') ? 'Drivers' : 'Users',
            );

            $validator = Validator::make($request->all(), $rules, [], $attributes);

            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }

            $to = $request->to;
            $user_type = $request->user_type;
            $message_type = $request->message_type;
            $users_id = [];
            $companies_id = [];

            if ($to == "to_specific") {
                $explode_users = explode(',', $request->users);
                foreach ($explode_users as $explode_user) {
                    $email = explode('-', $explode_user);
                    if ($email[0] == 'Company') {
                        $companies_id[] = $email[1];
                    } else {
                        $users_id[] = $email[1];
                    }
                }
            }


            if ($to == "to_specific" && $user_type != "Company") {
                $users_result = User::wherein('id', $users_id)->where('status', "Active");
            } else if ($to == "to_all" && $user_type != "Company") {
                $users_result = User::where('status', "Active");
                if ($user_type != "all") {
                    $users_result = $users_result
                        ->where(function ($query) {
                            //For company user login, get only that company's drivers
                            if (LOGIN_USER_TYPE == 'company') {
                                $query->where('company_id', Auth::guard('company')->user()->id);
                            }
                        })
                        ->where('user_type', $user_type);
                }
            }

            if (isset($users_result)) {
                $users_result = $users_result->select('id', 'country_code', 'mobile_number', 'device_id', 'device_type', 'user_type')->get();
            }

            if (LOGIN_USER_TYPE != 'company') {
                if (($user_type == "all" || $user_type == "Company") && $to == "to_all") {
                    $companies = Company::select('id', 'name', 'country_code', 'mobile_number', 'device_id', DB::raw('"Company" as user_type'))->where('status', 'Active')->where('id', '!=', 1)->get();
                } elseif ($to == "to_specific") {
                    $companies = Company::select('id', 'name', 'country_code', 'mobile_number', 'device_id', DB::raw('"Company" as user_type'))->where('status', 'Active')->where('id', '!=', 1)->wherein('id', $companies_id)->get();
                }

                if ($user_type == "all") {
                    $collection = collect([$users_result, $companies]);
                    $users_result = $collection->collapse();
                    $users_result->all();
                } elseif ($user_type == "Company") {
                    $users_result = $companies;
                }
            }

            $sms_gateway = resolve("App\Contracts\SMSInterface");

            if ($users_result->count()) {

                if ($request->message_priority == 'now') {
                    foreach ($users_result as $row_user) {
                        if ($message_type == "sms") {
                            $to = str_replace('+88', '', $row_user->phone_number);


                            // $Onnorokom_api_key_get = DB::table('api_credentials')
                            //     ->where('name', 'token')
                            //     ->where('site', 'Onnorokom')
                            //     ->first()->value;

                            try {
                                // $soapClient = new SoapClient("https://api2.onnorokomSMS.com/sendSMS.asmx?wsdl");
                                // $paramArray = array(
                                //     'apiKey' =>  $Onnorokom_api_key_get,
                                //     'messageText' => GoogleTranslate::trans($request->txtEditor, 'bn'),
                                //     'numberList' => $to,
                                //     'smsType' => "TEXT",
                                //     'maskName' => '',
                                //     'campaignName' => '',
                                // );
                                // $value = $soapClient->__call("NumberSms", array($paramArray));

                                // if (explode('||', $value->NumberSmsResult)[0] == 1900) {
                                //     flashMessage('success', 'Send Successfully');
                                // } else {
                                //     flashMessage('error', 'Cant Send Message');
                                // }

                                $sms_result = $this->sms_helper->send($to, $request->txtEditor);
                                $sms_result =  json_decode($sms_result, true);
                                if ($sms_result['Status'] == 0) {
                                    $sms_check = $this->sms_helper->send_message_check($sms_result['Message_ID']);
                                    $message_result =  json_decode($sms_check, true);
                                    // if ($message_result['Status'] == 0) {
                                    if ($message_result['Status'] == 0 || $message_result['Status'] == 2 || $message_result['Status'] == 4) {
                                        flashMessage('success', 'Send Successfully');
                                    } else {
                                        flashMessage('error', 'Cant Send Message');
                                    }
                                } else {
                                    flashMessage('error', 'Cant Send Message');
                                }
                            } catch (\Exception $e) {
                                // flashMessage('error', $e->getMessage());
                                flashMessage('error', 'Cant Send Message');
                            }

                            /*Commented By Nishat*/
                            // $to = $row_user->phone_number;
                            // $sms_responce = $sms_gateway->send($to,$request->txtEditor);
                            /*Comment End By Nishat*/
                        } else {
                            if ($row_user->device_id != "") {
                                $this->send_custom_pushnotification($row_user->device_id, $row_user->device_type, $row_user->user_type, $request->txtEditor);
                                flashMessage('success', 'Send Successfully');
                            }
                        }
                    }
                } else {
                    if ($request->schedule_time != '') {
                        $schedule_message = new ScheduleMessage;
                        $schedule_message->schedule_time = str_replace('/', '-', $request->schedule_time);
                        $schedule_message->to = $request->to;
                        $schedule_message->specific_user = $request->specific_user;
                        $schedule_message->user_type = $request->user_type;
                        $schedule_message->users = $request->users;
                        $schedule_message->message_type = $request->message_type;
                        $schedule_message->txtEditor = $request->txtEditor;
                        $schedule_message->status = '0';
                        $schedule_message->save();
                        flashMessage('success', 'Schedule Message Saved Successfully');
                    }
                }
            }
            // flashMessage('success', 'Send Successfully');
            return redirect(LOGIN_USER_TYPE . '/send_message');
        }
    }

    /**
     * Get user function by type -rider or driver or all
     *
     * @return users list
     */
    public function get_send_users(Request $request)
    {
        $type = $request->type;
        if ($type == "Company") {
            $company_details = Company::select('id', 'name', 'mobile_number', DB::raw('"Company" as user_type'))->where('status', 'Active')->where('id', '!=', 1)->get();
            return $company_details->toJson();
        }

        $user_details = User::select('id', 'first_name', 'mobile_number', 'user_type', 'gender')->where('status', 'Active');

        if ($type != "all") {
            $user_details = $user_details->where('user_type', $type);
        }

        if (LOGIN_USER_TYPE == 'company') {
            $user_details = $user_details->where('company_id', Auth::guard('company')->user()->id);
        }

        if ($type == 'all' && LOGIN_USER_TYPE != 'company') {
            $user_details = $user_details->get();
            $company_details = Company::select('id', 'name', 'mobile_number', DB::raw('"Company" as user_type'))->where('status', 'Active')->where('id', '!=', 1)->get();
            $collection = collect([$user_details, $company_details]);
            $user_details = $collection->collapse();
            $user_details->all();
            return $user_details;
        }
        return $user_details->get()->toJson();
    }


    /**
     * custom push notification android
     *
     * @return success or fail
     */
    public function send_custom_pushnotification($device_id, $device_type, $user_type, $message)
    {
        if (LOGIN_USER_TYPE == 'company') {
            $push_title = "Message from " . Auth::guard('company')->user()->name;
        } else {
            $push_title = "Message from " . SITE_NAME;
        }

        try {
            if ($device_type == 1) {
                $data       = array('custom_message' => array('title' => $message, 'push_title' => $push_title));
                $this->request_helper->push_notification_ios($message, $data, $user_type, $device_id, $admin_msg = 1);
            } else {
                $data = array('custom_message' => array('message_data' => $message, 'title' => $push_title));
                $this->request_helper->push_notification_android($push_title, $data, $user_type, $device_id, $admin_msg = 1);
            }
        } catch (\Exception $e) {
            logger('Could not send push notification');
        }
    }

    public function cron_jobs()
    {
        $now = date('Y-m-d H:i:00');
        //$now = '2021-05-20 11:07:00'; // to be off
        $schedule_messages = ScheduleMessage::where('schedule_time', $now)
            ->where('status', '0')
            ->get();

        foreach ($schedule_messages as $request) {
            $to = $request->to;
            $user_type = $request->user_type;
            $message_type = $request->message_type;
            $users_id = [];
            $companies_id = [];

            if ($to == "to_specific") {
                $explode_users = explode(',', $request->users);
                foreach ($explode_users as $explode_user) {
                    $email = explode('-', $explode_user);
                    if ($email[0] == 'Company') {
                        $companies_id[] = $email[1];
                    } else {
                        $users_id[] = $email[1];
                    }
                }
            }


            if ($to == "to_specific" && $user_type != "Company") {
                $users_result = User::wherein('id', $users_id)->where('status', "Active");
            } else if ($to == "to_all" && $user_type != "Company") {
                $users_result = User::where('status', "Active");
                if ($user_type != "all") {
                    $users_result = $users_result
                        ->where(function ($query) {
                            //For company user login, get only that company's drivers
                            if (LOGIN_USER_TYPE == 'company') {
                                $query->where('company_id', Auth::guard('company')->user()->id);
                            }
                        })
                        ->where('user_type', $user_type);
                }
            }

            if (isset($users_result)) {
                $users_result = $users_result->select('id', 'country_code', 'mobile_number', 'device_id', 'device_type', 'user_type')->get();
            }

            if (LOGIN_USER_TYPE != 'company') {
                if (($user_type == "all" || $user_type == "Company") && $to == "to_all") {
                    $companies = Company::select('id', 'name', 'country_code', 'mobile_number', 'device_id', DB::raw('"Company" as user_type'))->where('status', 'Active')->where('id', '!=', 1)->get();
                } elseif ($to == "to_specific") {
                    $companies = Company::select('id', 'name', 'country_code', 'mobile_number', 'device_id', DB::raw('"Company" as user_type'))->where('status', 'Active')->where('id', '!=', 1)->wherein('id', $companies_id)->get();
                }

                if ($user_type == "all") {
                    $collection = collect([$users_result, $companies]);
                    $users_result = $collection->collapse();
                    $users_result->all();
                } elseif ($user_type == "Company") {
                    $users_result = $companies;
                }
            }

            $sms_gateway = resolve("App\Contracts\SMSInterface");

            if ($users_result->count()) {
                foreach ($users_result as $row_user) {
                    if ($message_type == "sms") {
                        $to = str_replace('+88', '', $row_user->phone_number);


                        // $Onnorokom_api_key_get = DB::table('api_credentials')
                        //     ->where('name', 'token')
                        //     ->where('site', 'Onnorokom')
                        //     ->first()->value;

                        try {
                            // $soapClient = new SoapClient("https://api2.onnorokomSMS.com/sendSMS.asmx?wsdl");
                            // $paramArray = array(
                            //     'apiKey' =>  $Onnorokom_api_key_get,
                            //     'messageText' => GoogleTranslate::trans($request->txtEditor, 'bn'),
                            //     'numberList' => $to,
                            //     'smsType' => "TEXT",
                            //     'maskName' => '',
                            //     'campaignName' => '',
                            // );
                            // $value = $soapClient->__call("NumberSms", array($paramArray));

                            // if (explode('||', $value->NumberSmsResult)[0] == 1900) {
                            //     //flashMessage('success', 'Send Successfully'); 
                            //     $schedule_messages_update = Holiday::find($schedule_messages->id);
                            //     $schedule_messages_update->status = '1';
                            //     $schedule_messages_update->save();
                            // } else {
                            //     //flashMessage('error', 'Cant Send Message');               
                            // }

                            // $sms_result = $this->sms_helper->send($to, $request->txtEditor);
                            // if ($sms_result['0'] == 0) {
                            //     flashMessage('success', 'Send Successfully');
                            // } else {
                            //     flashMessage('error', 'Cant Send Message');
                            // }

                            $sms_result = $this->sms_helper->send($to, $request->txtEditor);
                            $sms_result =  json_decode($sms_result, true);
                            if ($sms_result['Status'] == 0) {
                                $sms_check = $this->sms_helper->send_message_check($sms_result['Message_ID']);
                                $message_result =  json_decode($sms_check, true);
                                //if ($message_result['Status'] == 0) {
                                if ($message_result['Status'] == 0 || $message_result['Status'] == 2 || $message_result['Status'] == 4) {
                                    flashMessage('success', 'Send Successfully');
                                } else {
                                    flashMessage('error', 'Cant Send Message');
                                }
                            } else {
                                flashMessage('error', 'Cant Send Message');
                            }
                        } catch (\Exception $e) {
                            //flashMessage('error', $e->getMessage());
                            //flashMessage('error', 'Cant Send Message');
                        }
                    } else {
                        if ($row_user->device_id != "") {
                            $this->send_custom_pushnotification($row_user->device_id, $row_user->device_type, $row_user->user_type, $request->txtEditor);
                            //flashMessage('success', 'Send Successfully');
                        }
                    }
                }
            }
        } //end foreach
    }

    /**
     * @param $input
     */
    public function bulkSMSSendForDriverRider($input)
    {
        //\DB::statement('SET GLOBAL group_concat_max_len = 999999999');
        //\DB::statement("SET SESSION sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''))");
        $user_details = User::select(\DB::raw('GROUP_CONCAT(CONCAT(IFNULL(country_code,"880"),mobile_number)) AS mobile_number'), \DB::raw('GROUP_CONCAT(DISTINCT device_id) AS device_id'))
            ->where(function ($sql) use (
                $input
            ) {
                //$sql->where('status', 'Active');
                $sql->where(\DB::raw('LENGTH(mobile_number)'),10);
                if(in_array($input['user_type'],array('Rider', 'Driver'))):
                    $sql->where('user_type',$input['user_type']);
                endif;
                if (isset($input['to']) && $input['to'] == "to_specific"):
                    $sql->wherein('id', explode(',', str_replace(array('Rider-', 'Driver-'), '', $input['users'])));
                endif;
            })->first();
        $data['mobile_number'] = $user_details['mobile_number'];
        $data['device_id'] = $user_details['device_id'];
        $data['sms_content'] = $input['txtEditor'];
        if ($input['message_type'] == 'push'):
            if (LOGIN_USER_TYPE == 'company') {
                $push_title = "Message from " . Auth::guard('company')->user()->name;
            } else {
                $push_title = "Message from " . SITE_NAME;
            }
            $deviceIDArray = explode(',',$data['device_id']);
            for($i=0; $i<= ceil(count($deviceIDArray)/1000); $i++):
                $filterDevice = array_slice($deviceIDArray, ($i*1000), (($i+1)*1000), true);
                if(!empty($filterDevice)):
                    $deviceIDString = implode(',',$filterDevice);
                    $this->pushNotificationService->sendSpecificNotification($deviceIDString,$push_title, $data['sms_content']);
                    $this->pushNotificationService->sendSpecificNotificationForIOS($deviceIDString, $push_title, $data['sms_content']);
                endif;
            endfor;
        else:
            $this->sms_helper->sendBulk($data);
        endif;
    }

    /**
     * @param $input
     */
    public function bulkSMSSendForCompany($input)
    {
        //\DB::statement('SET GLOBAL group_concat_max_len = 999999999');
        //\DB::statement("SET SESSION sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''))");
        $companies = Company::select(\DB::raw('GROUP_CONCAT(CONCAT(IFNULL(country_code,"880"),mobile_number)) AS mobile_number'))
            ->where(function ($sql) use (
                $input
            ) {
                //$sql->where('status', 'Active');
                $sql->where(\DB::raw('LENGTH(mobile_number)'),10);
                $sql->where('id', '!=', 1);
                if ($input['to'] == "to_specific"):
                    $sql->wherein('id', explode(',', str_replace(array('Company-'), '', $input['users'])));
                endif;
            })->first();
        if(!empty($companies['mobile_number'])):
            $data['mobile_number'] = $companies['mobile_number'];
            $data['sms_content'] = $input['txtEditor'];
            $this->sms_helper->sendBulk($data);
        endif;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|void
     */
    public function sendMessage(Request $request)
    {
        if($request['message_priority'] == 'now'):
            if($request['message_type'] == 'sms' || ($request['message_type'] == 'push' && $request['to'] == 'to_specific')):
                if(in_array($request['user_type'],array('Rider', 'Driver', 'all'))):
                    $this->bulkSMSSendForDriverRider($request->all());
                endif;
                if(in_array($request['user_type'],array('Company', 'all')) && $request['message_type'] != 'push'):
                    $this->bulkSMSSendForCompany($request->all());
                endif;
            else:
                $this->sendPushNotificationForAllRiderDriver($request->all());
            endif;
        else:
            if ($request->schedule_time != '') {
                $schedule_message = new ScheduleMessage;
                $schedule_message->schedule_time = str_replace('/', '-', $request->schedule_time);
                $schedule_message->to = $request->to;
                $schedule_message->specific_user = $request->specific_user;
                $schedule_message->user_type = $request->user_type;
                $schedule_message->users = $request->users;
                $schedule_message->message_type = $request->message_type;
                $schedule_message->txtEditor = $request->txtEditor;
                $schedule_message->status = '0';
                $schedule_message->save();
                flashMessage('success', 'Schedule Message Saved Successfully');
            }
        endif;
        //dd($request->all());
        return redirect(LOGIN_USER_TYPE . '/send_message');
    }

    /**
     * @param $input
     */
    public function sendPushNotificationForAllRiderDriver($input)
    {
        if (LOGIN_USER_TYPE == 'company') {
            $push_title = "Message from " . Auth::guard('company')->user()->name;
        } else {
            $push_title = "Message from " . SITE_NAME;
        }
        if($input['user_type'] == 'all'):
            $this->pushNotificationService->sendGeneralNotification($push_title, $input['txtEditor']);
            $this->pushNotificationService->sendGeneralNotificationForIOS($push_title, $input['txtEditor']);
        endif;
        if($input['user_type'] == 'Rider'):
            $this->pushNotificationService->sendGeneralNotificationForRider($push_title, $input['txtEditor']);
            $this->pushNotificationService->sendGeneralNotificationForIOSForRider($push_title, $input['txtEditor']);
        endif;
        if($input['user_type'] == 'Driver'):
            $this->pushNotificationService->sendGeneralNotificationForDriver($push_title, $input['txtEditor']);
            $this->pushNotificationService->sendGeneralNotificationForIOSForDriver($push_title, $input['txtEditor']);
        endif;
    }

    /**
     *
     */
    public function scheduleMessage()
    {
        $now = date('Y-m-d H:i:00');
        //$now = '2021-05-20 11:07:00'; // to be off
        $schedule_messages = ScheduleMessage::where('schedule_time', $now)
            ->where('status', '0')
            ->get();

        foreach ($schedule_messages as $schedule_message) {
            if($schedule_message['message_type'] == 'sms' || ($schedule_message['message_type'] == 'push' && $schedule_message['to'] == 'to_specific')):
                if(in_array($schedule_message['user_type'],array('Rider', 'Driver', 'all'))):
                    $this->bulkSMSSendForDriverRider($schedule_message);
                endif;
                if(in_array($schedule_message['user_type'],array('Company', 'all')) && $schedule_message['message_type'] != 'push'):
                    $this->bulkSMSSendForCompany($schedule_message);
                endif;
            else:
                $this->sendPushNotificationForAllRiderDriver($schedule_message);
            endif;
        }
    }

    /**
     * @param Request $request
     */
    public function sendSpecificSMSPUSH(Request $request)
    {
        // Send sms, push Validation Rules
        $rules = array(
            'txtEditor' => 'required',
            'user_type' => 'required',
            'message_type' => 'required',
        );

        // Send sms, push Validation Custom Names
        $attributes = array(
            'txtEditor'     => 'Message',
            'users'         => 'User Type (Driver/Rider)',
            'message_type'  =>  'Message Type (SMS/PUSH)',
        );

        $validator = Validator::make($request->all(), $rules, [], $attributes);

        if ($validator->fails()) {
            $errors = (new ValidationException($validator))->errors();
            dd($errors);
        }
        return $this->bulkSMSSendForDriverRider($request->all());
    }
}
