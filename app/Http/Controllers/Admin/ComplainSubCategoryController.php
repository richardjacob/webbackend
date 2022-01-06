<?php

/**
 * Documents Controller
 *
 * @package     Makent
 * @subpackage  Controller
 * @category    Documents
 * @author      Trioangle Product Team
 * @version     2.2.1
 * @link        http://trioangle.com
 */

namespace App\Http\Controllers\Admin;
 
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\DataTables\ComplainSubCategoryDataTable;
use Validator;
use App\Models\ComplainCategory;
use App\Models\ComplainSubCategory;
use App\Http\Start\Helpers;
use DB;


class ComplainSubCategoryController extends Controller
{
    protected $helper; 
    protected $cat_list; 
    public function __construct()
    {
        $this->helper = new Helpers;
        $this->cat_list = ComplainCategory::pluck('category', 'id');
    }

    /**
     * Load Datatable for Sub Category
     *
     * @param array $dataTable  Instance of ComplainCategoryDataTable
     * @return datatable
     */
    public function index(ComplainSubCategoryDataTable $dataTable)
    {   
        return $dataTable->render('admin.complain.sub_category.view');
    }

    /**
     * Add a New Sub Category
     *
     * @param array $request  Input values
     * @return redirect     to Sub Category view
     */
    public function add(Request $request)
    {
    	if($request->isMethod("GET")) {       	
            return view('admin.complain.sub_category.add')->with('cat_list', $this->cat_list);
        } elseif ($request->submit) {

            $rules = array(
                'complain_cat_id' => 'required',
                'sub_category' => 'required',
                'sub_category_bn' => 'required',
                'status' => 'required'                
            );
       
            $validator = Validator::make($request->all(), $rules);
            if($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }

            $table                  = new ComplainSubCategory;
            $table->complain_cat_id = $request->complain_cat_id;
            $table->sub_category    = $request->sub_category;
            $table->sub_category_bn = $request->sub_category_bn;
            $table->status          = $request->status;
            $table->save();

            flashMessage('success', __('messages.user.add_success'));
        }
        return redirect('admin/complain_sub_category');
    }

    /**
     * Edit sub Category
     *
     * @param array $request Input values
     * @return redirect to sub Category view
     */
    public function edit(Request $request)
    {
        if($request->isMethod("GET")) {       	
            $data['result'] = ComplainSubCategory::find($request->id);  
            $data['cat_list'] = $this->cat_list; 	
            return view('admin.complain.sub_category.edit',$data);
        } 
        elseif ($request->submit) {
            $rules = array(
                'complain_cat_id' => 'required',
                'sub_category' => 'required',
                'sub_category_bn' => 'required',                
                'status' => 'required'      
            );
       
            $validator = Validator::make($request->all(), $rules);
            if($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }

            $table                  = ComplainSubCategory::find($request->id);
            $table->complain_cat_id = $request->complain_cat_id;
            $table->sub_category    = $request->sub_category;
            $table->sub_category_bn = $request->sub_category_bn;
            $table->status          = $request->status;
            $table->save(); 
            flashMessage('success', __('messages.user.update_success'));
            return redirect('admin/complain_sub_category');
        }
    }

    /**
     * Delete sub Category
     *
     * @param array $request Input values
     * @return redirect to Sub Category View
     */
    public function delete(Request $request)
    {
        if(auth('admin')->user()->can('delete_complain_sub_category')){
            $data = ComplainSubCategory::find($request->id);             
            if(is_object($data)){
                $data->delete();
                $this->helper->flash_message('success',  __('messages.user.delete_success'));
                return redirect('admin/complain_sub_category'); 
            } else {
                $this->helper->flash_message('danger', __('messages.user.invalid_info'));
                return redirect('admin/complain_sub_category');
            }            
        }
    }
}
