<?php

/**
 * Trips Controller
 *
 * @package     Gofer
 * @subpackage  Controller
 * @category    Trips
 * @author      Trioangle Product Team
 * @version     2.2.1
 * @link        http://trioangle.com
 */

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\DataTables\CompanyDuesDatatable;
use App\Models\Bonus;
use App\Models\DriverBalance;
use App\Models\PayoutCredentials;
use App\Models\BonusTransaction;

class CompanyDuesController extends Controller
{
    public function view(CompanyDuesDatatable $dataTable)
    {
        return $dataTable->render('admin.company_dues.view');
    }

}