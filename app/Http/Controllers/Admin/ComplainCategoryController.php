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
use App\DataTables\ComplainCategoryDataTable;
use Validator;
use App\Models\ComplainCategory;
use App\Http\Start\Helpers;
use DB;


class ComplainCategoryController extends Controller
{
    protected $helper; 
    public function __construct()
    {
        $this->helper = new Helpers;
    }

    /**
     * Load Datatable for Category
     *
     * @param array $dataTable  Instance of ComplainCategoryDataTable
     * @return datatable
     */
    public function index(ComplainCategoryDataTable $dataTable)
    {
        return $dataTable->render('admin.complain.category.view');
    }

    /**
     * Add a New Category
     *
     * @param array $request  Input values
     * @return redirect     to Category view
     */
    public function add(Request $request)
    {
    	if($request->isMethod("GET")) {       	
            return view('admin.complain.category.add');
        } elseif ($request->submit) {

            $rules = array(
                'category' => 'required',
                'category_bn' => 'required',                
                'status' => 'required'           
            );
       
            $validator = Validator::make($request->all(), $rules);
            if($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }

            $table = new ComplainCategory;
            $table->category    = $request->category;
            $table->category_bn = $request->category_bn;
            $table->status      = $request->status;
            $table->save();

            flashMessage('success', __('messages.user.add_success'));
        }
        return redirect('admin/complain_category');
    }

    /**
     * Edit Category
     *
     * @param array $request Input values
     * @return redirect to Category view
     */
    public function edit(Request $request)
    {
        if($request->isMethod("GET")) {       	
            $data['result'] = ComplainCategory::find($request->id);      	
            return view('admin.complain.category.edit',$data);
        } 
        elseif ($request->submit) {
            $rules = array(
                'category' => 'required',
                'category_bn' => 'required',                
                'status' => 'required'     
            );
       
            $validator = Validator::make($request->all(), $rules);
            if($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }

            $table              = ComplainCategory::find($request->id);
            $table->category    = $request->category;
            $table->category_bn = $request->category_bn;
            $table->status      = $request->status;
            $table->save(); 
            flashMessage('success', __('messages.user.update_success'));
            return redirect('admin/complain_category');
        }
    }

    /**
     * Delete Category
     *
     * @param array $request Input values
     * @return redirect to Category View
     */
    public function delete(Request $request)
    {
        if(auth('admin')->user()->can('delete_complain_category')){
            $data = ComplainCategory::find($request->id);             
            if(is_object($data)){
                $data->delete();
                $this->helper->flash_message('success',  __('messages.user.delete_success'));
                return redirect('admin/complain_category'); 
            } else {
                $this->helper->flash_message('danger', __('messages.user.invalid_info'));
                return redirect('admin/complain_category');
            }            
        }
    }
}
