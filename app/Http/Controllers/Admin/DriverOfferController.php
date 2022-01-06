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
use App\DataTables\DriverOffer\SigningBonusDataTable;
use App\DataTables\DriverOffer\TripBonusDataTable;
use App\DataTables\DriverOffer\OnlineBonusDataTable;
use App\DataTables\DriverOffer\DriverReferralBonusDataTable;
use App\Http\Start\Helpers;
use Validator;

class DriverOfferController extends Controller
{
    protected $helper;  // Global variable for instance of Helpers

    public function __construct()
    {
        $this->helper = new Helpers;
    }

    public function signing_bonus(SigningBonusDataTable $dataTable)
    {
        return $dataTable->render('admin.driver_offer.signing_bonus');
    }

    public function trip_bonus(TripBonusDataTable $dataTable)
    {
        return $dataTable->render('admin.driver_offer.trip_bonus');
    }
    
    public function online_bonus(OnlineBonusDataTable $dataTable)
    {
        return $dataTable->render('admin.driver_offer.online_bonus');
    }

    public function referral_bonus(DriverReferralBonusDataTable $dataTable)
    {
        return $dataTable->render('admin.driver_offer.referral_bonus');
    }
}
