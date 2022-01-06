<?php

/**
 * Token Auth Controller
 *
 * @package     Gofer
 * @subpackage  Controller
 * @category    Token Auth
 * @author      Trioangle Product Team
 * @version     2.2.1
 * @link        http://trioangle.com
 */

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Mail;


class EmailTestController extends Controller
{
    public function index()
    {
        $data = array('name' => 'Taslim');
        Mail::send('emails.test', $data, function($message) {
            $message->to('mtaslim@gmail.com', 'Taslim')->subject
            ('Test Email');
        });
    }
}
