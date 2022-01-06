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
use App\DataTables\ContactListDataTable;
use App\Models\Contact;
use App\Models\ContactMovement;
use App\Http\Start\Helpers;
use DB;
use Auth;
use Validator;


class ContactListController extends Controller
{
    protected $helper; 
    public function __construct()
    {
        $this->helper = new Helpers;
        $this->request_helper = resolve('App\Http\Helper\RequestHelper');
    }

    /**
     * Load Datatable for Complain
     *
     * @param array $dataTable  Instance of ComplainComplainListDataTable
     * @return datatable
     */
    public function index(ContactListDataTable $dataTable, Request $r)
    {
        $contact_for = $r->contact_for;
        $contact_id   = $r->contact_id;
        $status    = $r->status;
        $start_date  = $r->start_date;
        $end_date    = $r->end_date;

        $data['contact_for']  = $contact_for;
        $data['contact_id']    = $contact_id;
        $data['status']       = $status;
        $data['start_date']   = $start_date;
        $data['end_date']     = $end_date;        
        
        $array = array(
            'contact_for' => $contact_for,
            'contact_id'   => $contact_id,
            'status'      => $status,
            'start_date'  => $start_date,
            'end_date'    => $end_date,
        );

        return $dataTable->with($array)->render('admin.complain.contact.view', $data);
    }


    /**
     * Load Datatable for Contact
     *
     * @param array $dataTable  Instance of ComplainComplainListDataTable
     * @return datatable
     */
    public function movement_contact(Request $request)
    {   
        if($request->isMethod("GET")) {       	
            $data['result'] = Contact::find($request->id);  
            $data['last_record'] = ContactMovement::where('contact_id', $request->id)->orderBy('id', 'DESC')->first();  

            return view('admin.complain.contact.movement_contact',$data);
        } 
        elseif ($request->submit) {

            $rules = array(
                'process_by' => 'required',
                'process' => 'required',  
                'remarks' => 'required',                   
                'status' => 'required'           
            );
       
            $validator = Validator::make($request->all(), $rules);
            if($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }

            $table              = new ContactMovement;
            $table->contact_id = $request->id;
            $table->process_by  = $request->process_by;
            $table->process     = $request->process;
            $table->remarks     = $request->remarks;
            $table->status      = $request->status;
            $table->entry_by_id = Auth::guard('admin')->user()->id;
            $table->updated_by_id = Auth::guard('admin')->user()->id;
            $table->save();

            $table_ccontact  = Contact::find($request->id);
            if( $request->status == '1' ){
                $table_ccontact->status = '1';
            } else {
                $table_ccontact->status = '2';
            }
            $table_ccontact->save();
            
            flashMessage('success', __('messages.user.add_success'));
        }
        return redirect('admin/contact_list');

    }

    /**
     * Edit Movement of complain
     *
     * @param array $request Input values
     * @return redirect to complain view
     */
    public function edit_movement_contact(Request $request)
    {
        $rules = array(
            'process_by' => 'required',
            'process' => 'required',  
            'remarks' => 'required',                   
            'status' => 'required'           
        );
    
        $validator = Validator::make($request->all(), $rules);
        if($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $table                = ContactMovement::find($request->contact_movement_id);
        $table->process_by    = $request->process_by;
        $table->process       = $request->process;
        $table->remarks       = $request->remarks;
        $table->status        = $request->status;
        $table->updated_by_id = Auth::guard('admin')->user()->id;
        $table->save();

        $table_complain  = Contact::find($table->contact_id);
        if( $request->status == '1' ){
            $table_complain->status = '1';
        } else {
            $table_complain->status = '2';
        }
        $table_complain->save();

        flashMessage('success', __('messages.user.update_success'));
        return redirect('admin/contact_list');
    }

    public function tracking_movement_contact(Request $request){   
        $result = DB::table('contacts')
                        ->select(
                            'id',
                            'name',
                            'mobile',
                            'email',
                            'contact_for',
                            'msg',
                            DB::raw("(
                                CASE
                                WHEN status='0' THEN 'Pending'
                                WHEN status='1' THEN 'Completed'
                                END
                            ) AS status"),
                            DB::raw('DATE_FORMAT(created_at, "%d-%b-%Y, %h:%i %p") as created_at')
                        )
                        ->where('id', $request->id)
                        ->first();
       
        $data['result'] = $result;  
        $data['movement'] = ContactMovement::where('contact_id', $request->id)->orderBy('id', 'DESC')->get();  
        return view('admin.complain.contact.tracking',$data);
    }
    
    
}
