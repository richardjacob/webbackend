<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\RiderGroup;
use DB;
use App\Models\User;
class RiderGroupController extends Controller
{
    public function rider_group()
    {
        $list = RiderGroup::orderby('id','desc')->paginate();
        // $list =  DB::Table('rider_groups ')->first();
        // $list = $list->paginate($per_page);
        $data['list'] = $list;
        // return dd($list);
        return view('admin.rider_group.view', $data);
    }
    public function add_rider_group_submit(Request $r)
    {
       

        $exist = RiderGroup::where('name',$r->name)->pluck('id')->first();
        if ($exist) echo $r->name.' alrady Exist.';
        else{
            $group = new RiderGroup();
            $group->name =  $r->name;
            $group->save();
            if($group->id != '') echo '1';
        }
    }
    public function add_rider_in_group($group_id)
    {
        // $list = RiderGroup::orderby('id','desc')->paginate();
        // $data['list'] = $list;
        // return view('admin.rider_assign_in_group.view', $data);
        // echo $group_id;
        $rider_group_name =  RiderGroup::where('id', '=', $group_id)->firstOrFail();
        $data['rider_group_name'] = $rider_group_name;
        $data['rider_group_id'] = $group_id;
        return view('admin.rider_assign_in_group.view', $data);
        
    }
    
    public function search_rider($group_id , Request $r)
    {
        // dd($request);
        $keyword = $r->keyword ;
        $keyword = preg_replace("/((\r?\n)|(\r\n?))/", ',', $keyword);
        $keyword = str_replace(' ', ',', $keyword);
        $keyword = trim($keyword, ',');
        $keyword_array = explode(',', $keyword);
        // print_r($keyword_array);
        // exit;
        
        $list = DB::table('users')
                    ->leftJoin('profile_picture', function($join) {
                        $join->on('users.id', '=', 'profile_picture.user_id');
                    })
                    ->where('user_type', 'Rider')
                    ->whereNull('rider_group_id')
                    ->where(function($query) use ($keyword_array){
                         foreach($keyword_array as $k){
                            $query->orWhere('mobile_number',  $k);
                            $query->orWhere('id',  $k);
                         }
                    })
                     ->get();
                     $rider_group_name =  RiderGroup::where('id', '=', $group_id)->firstOrFail();
                     $data['rider_group_name'] = $rider_group_name;
                     $data['rider_group_id'] = $group_id;
                     $data['list'] = $list;
                     return view('admin.rider_assign_in_group.view', $data);
    }
    
    public function assign_rider_group(Request $r)
    {
        
        $rider_group_id = $r->rider_group_id;
        $rider_id = $r->rider_id;
        if($rider_group_id != ''){
            foreach($rider_id as $rider){
                $user =  User::find($rider);
                $user->rider_group_id = $rider_group_id;
                $user->save();
                // echo "done";
            }
            return redirect('admin/view_rider_group_list/'. $rider_group_id);
        }
    }
    
    public function view_rider_group_list($id)
    {
            $list = DB::table('rider_groups')
            ->leftJoin('users', function($join) {
                $join->on('rider_groups.id', '=', 'users.rider_group_id');
            })
            ->leftJoin('profile_picture', function($join) {
                $join->on('users.id', '=', 'profile_picture.user_id');
            })
            ->where('rider_group_id', $id)
            ->get();
        
            $rider_group_name =  RiderGroup::where('id', '=', $id)->firstOrFail();
            $data['rider_group_name'] = $rider_group_name;
            $data['rider_group_id'] = $id;
            $data['list'] = $list;
            
            return view('admin.rider_group_list.view', $data);
    }

    public function remove_rider_from_group(Request $r)
    {
        $id = $r->id;
        $user = USER::find($id);
        $user->rider_group_id = NULL;
        if($user->save()) echo "1";
    }
    
    

}
