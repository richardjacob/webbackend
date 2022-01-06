<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Http\Helper\InvoiceHelper;
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\Activitylog\Traits\LogsActivity;

class PoolTrip extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use  LogsActivity;


    protected static $logAttributes = [
        'driver_id', 'car_id', 'seats', 'pickup_latitude', 'pickup_longitude', 'drop_latitude', 'drop_longitude', 'pickup_location', 'drop_location', 'trip_path',
        'map_image', 'total_time', 'total_km', 'time_fare', 'distance_fare', 'base_fare', 'additional_rider_amount', 'peak_fare', 'peak_amount', 'driver_peak_amount',
        'schedule_fare', 'access_fee', 'tips', 'waiting_charge', 'toll_reason_id', 'toll_fee', 'wallet_amount', 'promo_amount', 'subtotal_fare', 'total_fare', 'driver_payout',
        'driver_or_company_commission', 'owe_amount', 'remaining_owe_amount', 'applied_owe_amount', 'arrive_time', 'begin_trip', 'end_trip', 'currency_code', 'status'
    ];
    protected static $logOnlyDirty = true;


    // Join with Pool Trip table
    public function trips()
    {
        return $this->hasMany('App\Models\Trips', 'pool_id', 'id');
    }
}
