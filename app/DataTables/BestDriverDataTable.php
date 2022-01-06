<?php

/**
 * Driver DataTable
 *
 * @package     Gofer
 * @subpackage  DataTable
 * @category    Driver
 * @author      Trioangle Product Team
 * @version     2.2.1
 * @link        http://trioangle.com
 */

namespace App\DataTables;

use App\Models\User;
use App\Models\Rating;
use Yajra\DataTables\Services\DataTable;
use DB;

class BestDriverDataTable extends DataTable
{
    /**
     * Build DataTable class.
     *
     * @param mixed $query Results from query() method.
     * @return \Yajra\DataTables\DataTableAbstract
     */
    public function dataTable($query)
    {
        return datatables()
            ->of($query)
            ->addColumn('email', function ($users) {
                return protectedString($users->email);
            })
            ->addColumn('rating', function ($users) {
                $total_rating = DB::table('rating')
                                ->select(DB::raw('sum(rider_rating) as rating'))
                                ->where('driver_id', $users->id)
                                ->where('rider_rating', '>', 0)
                                ->first()
                                ->rating;

                $total_rating_count = Rating::where('driver_id', $users->id)
                                            ->where('rider_rating','>', 0)
                                            ->get()
                                            ->count();

                $driver_rating = '0.00';
                if ($total_rating_count != 0) {
                    $driver_rating = (string) round(($total_rating / $total_rating_count), 2);
                }
                
                return $driver_rating;
            })
            ->addColumn('trips', function ($users) {
                return $users->count;

            })            
            ->addColumn('action', function ($users) {
                $edit = (LOGIN_USER_TYPE=='company' || auth('admin')->user()->can('update_driver')) ? '<a href="'.url(LOGIN_USER_TYPE.'/edit_driver/'.$users->id).'" class="btn btn-xs btn-primary"><i class="glyphicon glyphicon-edit"></i></a>&nbsp;' : '';
                $delete = (auth()->guard('company')->user()!=null || auth('admin')->user()->can('delete_driver')) ? '<a data-href="'.url(LOGIN_USER_TYPE.'/delete_driver/'.$users->id).'" class="btn btn-xs btn-primary" data-toggle="modal" data-target="#confirm-delete"><i class="glyphicon glyphicon-trash"></i></a>&nbsp;':'';
                return $edit.$delete;
            })
            ->addIndexColumn();
    }

    /**
     * Get query source of dataTable.
     *
     * @param User $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(User $model)
    {
        /* only for Package */
        /*$users = $model->select('users.id as id', 'users.first_name', 'users.last_name', 'users.email', 'users.country_code', 'users.mobile_number', 'users.status', 'users.user_type', 'users.company_id', 'users.created_at', DB::raw('CONCAT("+",users.country_code," ",users.mobile_number) AS mobile'))
        ->where('user_type','Driver')->groupBy('id');*/


        $users = DB::Table('users')->select(
            'users.id as id',
            'users.first_name',
            'users.last_name',
            'users.email',
            'users.country_code',
            'users.mobile_number',
            'users.status',
            'companies.name as company_name',
            'users.created_at',
            DB::raw("count(trips.id) as count")
        )
        ->leftJoin('companies', function($join) {
            $join->on('users.company_id', '=', 'companies.id');
        })
        ->leftJoin('trips', function($join) {
            $join->on('trips.driver_id', '=', 'users.id');
        })
        ->where('trips.created_at','>=', date('Y-m-d', strtotime('-7 days')))
        ->where('users.user_type','Driver')
        ->groupBy('id')
        ->orderBy('count', 'DESC')
        ->skip(0)
        ->take(10)
        ->get();


        //If login user is company then get that company drivers only
        if (LOGIN_USER_TYPE=='company') {
            $users = $users->where('company_id',auth()->guard('company')->user()->id);
        }
        return $users;
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html()
    {
        return $this->builder()
                    ->columns($this->getColumns())
                    ->minifiedAjax()
                    ->dom('lBfr<"table-responsive"t>ip')
                    ->orderBy(0)
                    ->buttons(
                        ['csv', 'excel', 'print', 'reset']
                    );
    }

    /**
     * Get columns.
     *
     * @return array
     */
    protected function getColumns()
    {
        $mobile_number_column = (isLiveEnv())?'hidden_mobile':'mobile_number';
        $columns = [
            ['data' => 'DT_RowIndex', 'orderable' => false, 'title' => 'Serial', 'searchable' => false],
            ['data' => 'id', 'name' => 'users.id', 'title' => 'Id'],
            ['data' => 'first_name', 'name' => 'users.first_name', 'title' => 'First Name'],
            ['data' => 'last_name', 'name' => 'users.last_name', 'title' => 'Last Name'],
        ];
        if (LOGIN_USER_TYPE!='company') {
            $columns[] = ['data' => 'company_name', 'name' => 'companies.name', 'title' => 'Company Name'];
        }
        $more_columns = [
            ['data' => 'email', 'name' => 'users.email', 'title' => 'Email'],
            ['data' => 'mobile_number', 'name' => 'users.mobile_number', 'title' => 'Mobile'],
            ['data' => 'rating', 'name' => 'rating', 'title' => 'Rating'],
            ['data' => 'trips', 'name' => 'trips', 'title' => 'Trips'],
        ];

        return array_merge($columns,$more_columns);
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'drivers_' . date('YmdHis');
    }
    

}
