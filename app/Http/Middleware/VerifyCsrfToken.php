<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        'admin/get_send_users',
        'company/get_send_users',
        'admin/search_phone',
        'admin/search_cars',
        'admin/get_driver',
        'admin/driver_list',
        'company/search_phone',
        'company/search_cars',
        'company/get_driver',
        'company/driver_list',
        'admin/immediate_request',
        'company/immediate_request',
        'admin/manage_vehicle/{company_id}/get_driver',
        'company/manage_vehicle/{company_id}/get_driver',
        'company/set_session',
        'change_mobile_number',
        'apple_callback',
        'api/forgotpassword',
        'admin/session_set_from_another_domain',
        'admin/logs_login',
        'admin/authenticate_no_csrf'
    ];
}
