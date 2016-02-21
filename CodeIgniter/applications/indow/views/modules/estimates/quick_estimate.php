<?//= form_dropdown('product_types_id_test',$product_type_options,'', "class='input-sm form-control product_type_option'"); ?>
<table style='display: none;'>
    <tr id='clone' class='qe_row'>
        <td id='action'>
            <a class='icon' href='Javascript://return false;' onClick='delete_row(this);'> <i class='fa fa-times'></i></a>
        </td>
        <td id='width'>
            <input type='text' name='width' class='input_tiny input-sm form-control width_input' data-width='0' onChange='update_price(this);' />
        </td>
        <td id='height'>
            <input type='text' name='height' class='input_tiny input-sm form-control height_input' data-height='0' onChange='update_price(this);' />
        </td>
        <td id='product'>
            <?=htmlspecialchars(str_replace("\n", '', form_dropdown('',$product_options,'', "class='input-sm form-control product_option'")))?>
        </td>
        <td id='product_type'>
            <?= htmlspecialchars(str_replace("\n", '', form_dropdown('product_types_id',$product_type_options,'', "class='input-sm form-control product_type_option'"))) ?>
        </td>
        <td id='edging'>
            <?=htmlspecialchars(str_replace("\n", '', form_dropdown('edging_id',$edging_options,'', "class='input-sm form-control edging_option'")))?>
        </td>
        <td id='qty'>
            <input  type='text' class='input_tiny input-sm form-control qty' onChange='update_price(this);' value='1'>
        </td>
        <td id='cost'>
            $<span class='cost'>0.00</span>
        </td>
    </tr>
</table>
<form action='/estimates/add' id='estimateform' method='post'>
    <input id='estimatedata' name='estimatedata' type='hidden'>
</form>
<form method='post' action=''>
    <div class='row'>
        <div class='col-xs-12'>
            <button id='add_row' type='button' class='btn btn-blue pull-right' onClick='clone_row();'>Add</button>
        </div>
    </div>
    <div class='row'>
        <div class='col-xs-12'>
            <table class='table table-hover' id='quick_estimate_table'>
                <thead>
                    <tr>
                        <th>Action</th>
                        <th>Width (in)</th>
                        <th>Height (in)</th>
                        <th>Product</th>
                        <th>Product Type</th>
                        <th>Tubing</th>
                        <th>Qty</th>
                        <th>Est. Cost</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
    <div class='row'>
        <div class='col-xs-12 text-right'>
            <h3 class='total-est'>Total Est. Cost <span>$<span id='total'>0.00</span></span></h3>
            <button type='button' class='btn btn-blue' onClick='show_customer(this);'>Continue to Full Estimate</button>
        </div>
    </div>
    <div class='row' id='customer_pane' style='display: none;'>
        <div id='customer_pane_inner'></div>
        <div class='col-xs-12'>
            <input type='button' class='closepopover btn btn-blue pull-left' value='Cancel'>
            <button type='button' class='btn btn-blue pull-right' id='quick_estimate_submit' disabled>Save</button>
        </div>
    </div>
    <?/*
    <div class='row' id='customer_pane' style='display: none;'>
        <div class='col-xs-12'>
            <h3>Add Customer</h3>
            <ul class='nav nav-tabs'>
                <li id='existing_customer_tab' class='active'><a href='#general' data-toggle='tab'>Existing Customer</a></li>
                <li id='new_customer_tab'><a href='#meta_data' data-toggle='tab' style='color: #fff;'>New Customer</a></li>
            </ul>
            <div class='tab-content'>
                <div class='row tab-pane fade in active new_customer_tab' id='general'>
                    <div class='col-xs-8'>
                        <input type='text' placeholder='Search By Customer Email/Name to add' class='form-control'>
                    </div>
                </div>
                <div class='row tab-pane fade in form-horizontal' id='meta_data'>
                    <div class='col-md-6'>
                        <h4>Contact Info</h4>

                        <div class='form-group'>
                            <label for='first_name' class='control-label col-xs-3' >First Name</label>
                            <div class='col-xs-9'>
                                <input type='text' class='form-control input-sm' id='first_name' name='first_name' value=''>
                            </div>
                        </div>

                        <div class='form-group'>
                            <label for='last_name' class='control-label col-xs-3'>Last Name</label>
                            <div class='col-xs-9'>
                                <input type='text' class='form-control input-sm' id='last_name' name='last_name' value=''>
                            </div>
                        </div>

                        <div class='form-group'>
                            <label for='company' class='control-label col-xs-3'>Company</label>
                            <div class='col-xs-9'>
                                <input type='text' class='form-control input-sm' id='company' name='company' value=''>
                            </div>
                        </div>

                        <div class='form-group'>
                            <label for='email_1' class='control-label col-xs-3'>Email</label>
                            <div class='col-xs-6'>
                                <input type='email' class='form-control input-sm' id='email_1' name='email_1' value='' placeholder='email@email.com'>
                            </div>
                            <div class='col-xs-3'>
                                <select id='emailOneType' name='emailOneType' class='form-control input-sm'>
                                    <option value='livingRoom'>Home</option>
                                    <option value='More Room'>Work</option>
                                </select>
                            </div>
                        </div>

                        <div class='form-group'>
                            <label for='email_2' class='control-label col-xs-3'>Email</label>
                            <div class='col-xs-6'>
                                <input type='email' class='form-control input-sm' id='email_2' name='email_2' value='' placeholder='email@email.com'>
                            </div>
                            <div class='col-xs-3'>
                                <select id='emailTwoType' name='emailTwoType' class='form-control input-sm'>
                                    <option value='livingRoom'>Home</option>
                                    <option value='More Room'>Work</option>
                                </select>
                            </div>
                        </div>

                        <div class='form-group'>
                            <label for='phone_1' class='control-label col-xs-3'>Phone</label>
                            <div class='col-xs-6'>
                                <input type='tel' class='form-control input-sm' id='phone_1' name='phone_1' value=''
                                       placeholder='503-123-1234'>
                            </div>
                            <div class='col-xs-3'>
                                <select id='phoneOneType' name='phoneOneType' class='form-control input-sm'>
                                    <option value='livingRoom'>Home</option>
                                    <option value='More Room'>Work</option>
                                </select>
                            </div>
                        </div>

                        <div class='form-group'>
                            <label for='phone_2' class='control-label col-xs-3'>Phone</label>
                            <div class='col-xs-6'>
                                <input type='tel' class='form-control input-sm' id='phone_2' name='phone_2' value=''
                                       placeholder='503-123-1234'>
                            </div>
                            <div class='col-xs-3'>
                                <select id='phoneTwoType' name='phoneTwoType' class='form-control input-sm'>
                                    <option value='livingRoom'>Home</option>
                                    <option value='More Room'>Work</option>
                                </select>
                            </div>
                        </div>

                        <div class='form-group'>
                            <label for='phone_3' class='control-label col-xs-3'>Phone</label>
                            <div class='col-xs-6'>
                                <input type='tel' class='form-control input-sm' id='phone_3' name='phone_3' value=''
                                       placeholder='503-123-1234'>
                            </div>
                            <div class='col-xs-3'>
                                <select id='phoneThreeType' name='phoneThreeType' class='form-control input-sm'>
                                    <option value='livingRoom'>Home</option>
                                    <option value='More Room'>Work</option>
                                </select>
                            </div>
                        </div>

                    </div>

                    <div class='col-md-5 col-xs-offset-1'>
                        <h4>Address</h4>
                        <div class='form-group'>
                            <label for='address_type' class='control-label col-xs-4'>Address Type</label>
                            <div class='col-xs-8'>
                                <select class='form-control input-sm'>
                                    <option>home</option>
                                    <option>business</option>
                                </select>
                            </div>
                        </div>

                        <div class='form-group'>
                            <label for='address' class='control-label col-xs-4'>Address</label>
                            <div class='col-xs-8'>
                                <input type='text' class='form-control input-sm' id='address' name='address' value=''>
                            </div>
                        </div>

                        <div class='form-group'>
                            <label for='address_ext' class='control-label col-xs-4'>Address Ext</label>
                            <div class='col-xs-8'>
                                <input type='text' class='form-control input-sm' id='address_ext' name='address_ext' value=''>
                            </div>
                        </div>

                        <div class='form-group'>
                            <label for='country' class='control-label col-xs-4'>Country</label>
                            <div class='col-xs-8'>
                                <select class='form-control input-sm' id='country' name='country'>
                                    <option>home</option>
                                    <option>business</option>
                                </select>
                            </div>
                        </div>

                        <div class='form-group'>
                            <label for='city' class='control-label col-xs-4'>City</label>
                            <div class='col-xs-8'>
                                <input type='text' class='form-control input-sm' id='city' name='city' value=''>
                            </div>
                        </div>

                        <div class='form-group'>
                            <label for='state' class='control-label col-xs-4'>State/Providence</label>
                            <div class='col-xs-8'>
                                <select class='form-control input-sm' id='state' name='state'>
                                    <option>home</option>
                                    <option>business</option>
                                </select>
                            </div>
                        </div>

                        <div class='form-group'>
                            <label for='zip' class='control-label col-xs-4'>Zip/Postal Code</label>
                            <div class='col-xs-8'>
                                <input type='text' class='form-control input-sm' id='zip' name='zip' value=''>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class='col-xs-12'>
            <input type='button' class='closepopover btn btn-blue pull-left' value='Cancel'>
            <button type='button' class='btn btn-blue pull-right' id='quick_estimate_submit'>Save</button>
        </div>
    </div>
    */ ?>
</form>