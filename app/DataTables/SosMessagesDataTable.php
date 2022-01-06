<?php

namespace App\DataTables;

use App\Models\User;
use App\Models\Vehicle;
use Yajra\DataTables\Services\DataTable;
use DB;


class SosMessagesDataTable extends DataTable
{

    public function dataTable($query)
    {
        return datatables()->of($query)
            ->addColumn('vehicle', function ($users) {
                $vehicles = Vehicle::where('user_id', $users->driverid)->select('id', 'vehicle_number')->get();
                $output = "";
                foreach ($vehicles as $vehicle) {
                    $output .= '<a href="' . url(LOGIN_USER_TYPE . '/edit_vehicle/' . $vehicle->id) . '">' . $vehicle->vehicle_number . '</a><br>';
                }
                return $output;
            })
            ->addColumn('driver_id', function ($users) {
                $driver_data = User::where('id', $users->driverid)->select('id')->get();
                $output2 = "";
                foreach ($driver_data as $s_driver_data) {
                    $output2 .= '<a href="' . url(LOGIN_USER_TYPE . '/edit_driver/' . $s_driver_data->id) . '">' . $s_driver_data->id . '</a><br>';
                }
                return $output2;
            })
            ->rawcolumns(['vehicle', 'driver_id']);
    }

    public function query(User $model)
    {
        DB::statement(DB::raw('set @serial=0'));
        $data =  DB::Table('sos_message')->select(
            'sos_message.id as id',
            'sos_message.driver_id as driverid',
            'sos_message.rider_id',
            'sos_message.rider_location',
            'sos_message.trip_id',
            DB::raw('DATE_FORMAT(sos_message.created_at, "%d-%b-%Y") as created_at'),
            DB::raw('@serial  := @serial  + 1 AS serial'),
            DB::raw("CONCAT(driver_user.first_name,' ',driver_user.last_name) AS driver_name"),
            DB::raw("CONCAT(rider_user.first_name,' ',rider_user.last_name) AS rider_name"),
            DB::raw("CONCAT('0',driver_user.mobile_number) AS driver_phone"),
            DB::raw("CONCAT('0',rider_user.mobile_number) AS rider_phone")
        )->leftJoin('users as driver_user', function ($join) {
            $join->on('sos_message.driver_id', '=', 'driver_user.id');
        })->leftJoin('users as rider_user', function ($join) {
            $join->on('sos_message.rider_id', '=', 'rider_user.id');
        });
        return $data;
    }

    public function html()
    {
        return $this->builder()
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->dom('lBfr<"table-responsive"t>ip')
            ->orderBy(0, 'DESC')
            ->buttons(['excel', 'print']);
    }


    protected function getColumns()
    {
        return [
            ['data' => 'serial', 'name' => 'serial', 'title' => 'Serial'],
            // ['data' => 'id', 'name' => 'id', 'title' => 'Id'],
            ['data' => 'driver_name', 'name' => 'driver_user.driver_name', 'title' => 'Driver Name'],
            ['data' => 'driver_phone', 'name' => 'driver_user.mobile_number', 'title' => 'Driver Phone'],
            ['data' => 'driver_id', 'name' => 'driver_id', 'title' => 'Driver Id'],
            ['data' => 'vehicle', 'name' => 'vehicle', 'title' => 'Vehicle'],
            ['data' => 'rider_name', 'name' => 'rider_user.rider_name', 'title' => 'Rider Name'],
            ['data' => 'rider_phone', 'name' => 'rider_user.mobile_number', 'title' => 'Rider Phone'],
            ['data' => 'trip_id', 'name' => 'sos_message.trip_id', 'title' => 'Trip Id'],
            ['data' => 'rider_location', 'name' => 'sos_message.rider_location', 'title' => 'Riders Current Location'],
            ['data' => 'created_at', 'name' => 'created_at', 'title' => 'Created At'],
        ];
    }
    protected function filename()
    {
        return 'sos_messages_' . date('YmdHis');
    }
}
