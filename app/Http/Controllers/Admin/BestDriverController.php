<?php

/**
 * Driver Controller
 *
 * @package     Gofer
 * @subpackage  Controller
 * @category    Driver
 * @author      Trioangle Product Team
 * @version     2.2.1
 * @link        http://trioangle.com
 */

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\DataTables\DriverDataTable;

use App\DataTables\BestDriverDataTable;

use App\Models\User;
use App\Models\Trips;
use App\Models\DriverAddress;
use App\Models\DriverDocuments;
use App\Models\Country;
use App\Models\CarType;
use App\Models\ProfilePicture;
use App\Models\Company;
use App\Models\Vehicle;
use App\Models\ReferralUser;
use App\Models\DriverOweAmount;
use App\Models\PayoutPreference;
use App\Models\PayoutCredentials;
use App\Models\Documents;
use Validator;
use DB;
use Image;
use Auth;

class BestDriverController extends Controller
{
    /**
     * Load Datatable for Driver
     *
     * @param array $dataTable  Instance of Driver DataTable
     * @return datatable
     */
    public function index(BestDriverDataTable $dataTable)
    {
        return $dataTable->render('admin.driver.best_driver');
    }


}
