<div class="modal fade" id="confirm-delete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                    <h4 class="modal-title" id="myModalLabel">Confirm Delete</h4>
                </div>
                <div class="modal-body">
                    <!-- <p>You are about to delete one track, this procedure is irreversible.</p>
                    <p>Do you want to proceed?</p> -->
                    <p>Are you sure want to delete?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default confirm-delete_cancel" data-dismiss="modal">Cancel</button>
                    <a class="btn btn-danger btn-ok confirm-delete">Delete</a>
                </div>
            </div>
        </div>
</div>

<div class="modal fade" id="payout-details" tabindex="-1" role="dialog" aria-labelledby="payout-details" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header text-center">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                    <h3 class="modal-title" id="payout-details"> Payout Details </h3>
                </div>
                <div class="modal-body">
                    <table class="table" id="payout_details">
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-dismiss="modal"> Close </button>
                </div>
            </div>
        </div>
</div>


<div class="modal fade" id="payout_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <h4 class="modal-title" id="myModalLabel">Account Number: <span id="label_account_nember"></span></h4>
            </div>
            <form action="{{url(LOGIN_USER_TYPE.'/make_payout')}}" method="post" name="payout_form" id="payout_form">
                <input type="hidden" name="_token" value="{{csrf_token()}}">
                <input type="hidden" name="type" id="type" value="">
                <input type="hidden" name="driver_id" id="driver_id" value="">
                <input type="hidden" name="redirect_url" id="redirect_url" value="">
                <input type="hidden" name="start_date" id="start_date" value="">
                <input type="hidden" name="end_date" id="end_date" value="">
                <input type="hidden" name="day" id="day" value="">
                <input type="hidden" name="trip_id" id="trip_id" value="">

                <div class="modal-body">
                    <div class="row">
                        <label for="PayoutMethod" class="col-sm-6" style="padding:5px 0px 0px 50px;">Payout Method : </label>
                        <div class="col-sm-6" id="payout_method"></div>
                    </div>

                    <div class="row">
                        <label for="tr_no" class="col-sm-6" style="padding:5px 0px 0px 50px;">Transaction No. (Reference No.) : </label>
                        <div class="col-sm-6">
                            <input class="form-control" type="text" id="tr_no" name="tr_no" placeholder="Transaction No. (Reference No.)" >
                        </div>
                    </div>

                    <div class="row">
                        <label for="amount" class="col-sm-6" style="padding:5px 0px 0px 50px;">Amount : </label>
                        <div class="col-sm-6">
                            <input class="form-control" type="text" name="amount" id="amount" placeholder="Amount" value="" readonly="">
                        </div>
                    </div>
                </div>
                <div class="modal-footer text-right">
                    <button type="button" class="btn btn-default confirm-delete_cancel" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="payout_text"></button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="payout_modal_company" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <h4 class="modal-title" id="myModalLabel">Account Number: <span id="label_account_nember"></span></h4>
            </div>
            <form action="{{url(LOGIN_USER_TYPE.'/make_payout/company')}}" method="post" name="payout_form" id="payout_form_company">
                <input type="hidden" name="_token" value="{{csrf_token()}}">
                <input type="hidden" name="type" id="type" value="">
                <input type="hidden" name="company_id" id="company_id" value="">
                <input type="hidden" name="redirect_url" id="redirect_url" value="">
                <input type="hidden" name="start_date" id="start_date" value="">
                <input type="hidden" name="end_date" id="end_date" value="">
                <input type="hidden" name="day" id="day" value="">
                <input type="hidden" name="trip_id" id="trip_id" value="">

                <div class="modal-body">
                    <div class="row">
                        <label for="PayoutMethod" class="col-sm-6" style="padding:5px 0px 0px 50px;">Payout Method : </label>
                        <div class="col-sm-6" id="payout_method"></div>
                    </div>

                    <div class="row">
                        <label for="tr_no" class="col-sm-6" style="padding:5px 0px 0px 50px;">Transaction No. (Reference No.) : </label>
                        <div class="col-sm-6">
                            <input class="form-control" type="text" id="tr_no" name="tr_no" placeholder="Transaction No. (Reference No.)" >
                        </div>
                    </div>

                    <div class="row">
                        <label for="amount" class="col-sm-6" style="padding:5px 0px 0px 50px;">Amount : </label>
                        <div class="col-sm-6">
                            <input class="form-control" type="text" name="amount" id="amount" placeholder="Amount" value="" readonly="">
                        </div>
                    </div>
                </div>
                <div class="modal-footer text-right">
                    <button type="button" class="btn btn-default confirm-delete_cancel" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="payout_text"></button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="bonus_payout_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <h4 class="modal-title" id="myModalLabel">Account Number: <span id="label_account_nember"></span></h4>
            </div>
            <form action="{{url(LOGIN_USER_TYPE.'/driver_balance_paout')}}" method="post" name="payout_form">
                <input type="hidden" name="_token" value="{{csrf_token()}}">
                <input type="hidden" name="balance_id" id="balance_id" value="">
                <input type="hidden" name="bonus_id" id="bonus_id" value="">
                <input type="hidden" name="user_id" id="user_id" value="">
                <input type="hidden" name="redirect_url" id="redirect_url" value="">

                <div class="modal-body">
                    <div class="row">
                        <label for="tr_no" class="col-sm-6" style="padding:5px 0px 0px 50px;">Payout Method : </label>
                        <div class="col-sm-6" id="payout_method"></div>
                    </div>

                    <div class="row">
                        <label for="tr_no" class="col-sm-6" style="padding:5px 0px 0px 50px;">Transaction No. (Reference No.) : </label>
                        <div class="col-sm-6">
                            <input class="form-control" type="text" name="tr_no" placeholder="Transaction No. (Reference No.)" >
                        </div>
                    </div>

                    <div class="row">
                        <label for="amount" class="col-sm-6" style="padding:5px 0px 0px 50px;">Amount : </label>
                        <div class="col-sm-6">
                            <input class="form-control" type="text" name="amount" id="amount" placeholder="Amount" value="" readonly="">
                        </div>
                    </div>
                </div>
                <div class="modal-footer text-right">
                    <button type="button" class="btn btn-default confirm-delete_cancel" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="payout_text"></button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="owe_trip_list_modal" tabindex="-1" role="dialog" aria-labelledby="payout-details" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                    <h3 class="modal-title" id="company_driver_name"></h3>
                </div>
                <div class="modal-body">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>SL#</th>
                                <th>Trip ID</th>
                                <th>Driver ID</th>
                                <th>Rider ID</th>
                                <th>Pickup Location</th>
                                <th>Drop Location</th>
                                <th>Total Fare</th>
                                <th>Owe Amount</th>
                                <th>RemainingOwe Amount</th>
                                <th>Start Time</th>
                                <th>End Time</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-dismiss="modal"> Close </button>
                </div>
            </div>
        </div>
</div>


<footer class="main-footer">
    <div class="pull-right hidden-xs">
    </div>
    <strong>Copyright &copy; 2021 <a href="http://alesharide.com">ALESHA RIDE</a>.</strong> All rights
    reserved.

</footer>
