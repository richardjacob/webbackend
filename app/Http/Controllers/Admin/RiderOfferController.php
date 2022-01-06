<?php

/**
 * Country Controller
 *
 * @package     Gofer
 * @subpackage  Controller
 * @category    Country
 * @author      Trioangle Product Team
 * @version     2.2.1
 * @link        http://trioangle.com
 */

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\DataTables\RiderOffer\CashBackDataTable;
use App\DataTables\RiderOffer\RiderReferralDataTable;
use App\Http\Start\Helpers;
use Validator;

class RiderOfferController extends Controller
{
    protected $helper;  // Global variable for instance of Helpers

    public function __construct()
    {
        $this->helper = new Helpers;
    }

    public function cash_back(CashBackDataTable $dataTable)
    {
        return $dataTable->render('admin.rider_offer.cash_back');
    }
    public function referral_bonus(RiderReferralDataTable $dataTable)
    {
        return $dataTable->render('admin.rider_offer.referral_bonus');
    }

    
}
