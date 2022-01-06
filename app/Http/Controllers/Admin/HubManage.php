<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\DataTables\HubDataTable;
use App\Models\Hub;
use App\Models\Language;
use Validator;
use DB;
class HubManage extends Controller
{
     public function index(HubDataTable $dataTable)
    {
        return $dataTable->render('admin.hub_manage.view');
    }

    public function add(Request $request)
    {
        if($request->isMethod('GET')) {
            $data['languages'] = Language::where('status', '=', 'Active')->pluck('name', 'value');
            
            // $data['category'] = HelpCategory::active_all();
            // $data['subcategory'] = HelpSubCategory::active_all();
            return view('admin.hub_manage.add', $data);
        }

        if($request->submit) {

            $this->validate($request,[
            'hub_name'=> 'required',
            'address'=> 'required',
            'status'=> 'required'
             ]);

        $hub = new Hub();
        $hub->name = $request->hub_name;
        $hub->address = $request->address;
        $hub->status = $request->status;
        $hub->save();

        flashMessage('success', 'Added Successfully');
        }
        
        return redirect('admin/manage_hub');
    }

     public function update(Request $request)
    {
        if($request->isMethod("GET")) {
            $data['languages'] = Language::where('status', '=', 'Active')->pluck('name', 'value');
            $data['result'] = Hub::findOrFail($request->id);

            return view('admin.hub_manage.edit', $data);
        }
        else if($request->submit) {
            // Edit Help Validation Rules
            $rules = array(
                'name'    => 'required',
                'address' => 'required',
                'status'      => 'required'
            );

            // Edit Help Validation Custom Fields Name
            $attributes = array(
                'name'    => 'Name',
                'address' => 'Address',
                'status'      => 'Status'
            );

            $hub = Hub::findOrFail($request->id);
            $hub->name    = $request->name;
            $hub->address = $request->address;
            $hub->status         = $request->status;
            $hub->save();
            
            flashMessage('success', 'Updated Successfully');
        }
        return redirect('admin/manage_hub');
    }


    public function delete(Request $request)
    {
        $hub = Hub::findOrFail($request->id);
        $hubset= DB::table('hub_employees')->where('hub_id',$request->id)->count();
        if($hubset > 0) flashMessage('danger', 'Hub Have Employee You First Need To Delete Employee');

        else {
            $hub->delete();
            flashMessage('success', 'Deleted Successfully');
        }
        return redirect('admin/manage_hub');
    }
}
