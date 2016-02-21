<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

function generate_site_info($site_id, $data, $site, $keys)
{
    $html = '<div class="info_view">';
    if ( ! empty($site) ) 
    {

        $options = <<<EOT
        <option value = 'Residential'>Residential</option>
        <option value = 'Business'>Business</option>
EOT;

        $edit_form = <<<EOT
    <div class='row'>
        <div class='form-horizontal'>
            <div class='form-group'>
                <h3 class='col-xs-12'>Site Info</h3>
            </div>

            <div class='form-group'>
                <label for='site_address' class='control-label col-xs-3'>Address</label>
                <input type='hidden' id='site_id' name='site_id' value='{$site_id}'> test
                <div class='col-xs-9'>
                    <input class='form-control input-sm' id='site_address' name='site_address' value='{$site['address']}' placeholder='{$site['address']}'>
                </div>
            </div>

            <div class='form-group'>
                <label for='address_ext' class='control-label col-xs-3'>Ext</label>
                <input type='hidden' id='address_ext' name='address_ext' value='{$site['address_ext']}'>
                <div class='col-xs-4'>
                    <input class='form-control input-sm' id='site_address' name='site_address' value='{$site['address']}' placeholder='{$site['address']}'>
                </div>
                <div class='col-xs-5 text-right'>
                    <select id='address_type' name='address_type' class='form-control input-sm'>
                        {$options}
                    </select>
                </div>
            </div>

            <div class='form-group'>
                <div class='col-xs-12 text-right'>
                    <button id='site_info_update_button' type='submit' name='submit' value='Save' class='btn btn-blue'>Save</button>
                </div>
            </div>


        </div>
    </div>
EOT;

        if ( isset($data['edit_link']) )
        {
            $html .= "<div>";
            // $html .= '<form id="site_info_form">';
            $html .= '<h4 class="inline info_header">' . $data['title'] . '</h4>';
            $html .= '<a href="' . $data['edit_link'] . '" class="btn btn-blue btn-sm pull-right" data-toggle="popover" data-placement="right" data-content="' . $edit_form . '">Edit</a>';
            $html .= '<script>$("a[data-toggle=popover]").popover({
                    html: true,
                    title: function () {
                        return $(this).parent().find(".head").html();
                    },
                    content: function () {
                        return $(this).parent().find(".content").html();
                    }
            });</script>';
            // $html .= '</form>';
            $html .= '</div>';
        }

        $html  .= <<<EOT
        <div class='show-grid'>
        <div class='site_set'>
EOT;

        if ( !empty($site['address'] ))
        {
            $html .= <<<EOT
            <div id='site_address' class='site_item'>{$site['address']}</div>
EOT;
        }

        if ( !empty($site['address_ext'] ))
        {
            $html .= <<<EOT
            <div id='site_address_ext' class='site_item'>{$site['address_ext']}</div>
EOT;
        }

        if ( !empty($site['city_state_zipcode'] ))
        {
            $html .= <<<EOT
            <div id='site_city_state_zipcode' class='site_item'>{$site['city_state_zipcode']}</div>
EOT;
        }

        if ( !empty($site['address_type'] ))
        {
            $html .= <<<EOT
            <div id='site_address_type' class='site_item'>{$site['address_type']}</div>
EOT;
        }

        $html .= <<<EOT
        </div>
        </div>
EOT;

    } else {

        $html .= $data['empty_message'];
    }

    $html .= '<br/>';
    $html .= '</div>';

    return $html;
}   

/*Don't rename this function again*/
function generate_contact_info_rename($customer_id, $data, $customer, $keys, $mode='default')
{
    $html = '';
    ob_start();
    $phone_options = array('Home', 'Work', 'Mobile');
    $email_options = array('Home', 'Work');
    ?>
    <div id="contact_info_view" class="info_view">
    <?
    $html = ob_get_clean();
    ob_start();
    ?>
        <div class='row'>
            <form id="editcustomerform" class='form-horizontal' method="post" action='/customers/edit/<?= $customer_id ?>' onsubmit="return contactinfo_submit(this);">
                <? if (isset($data['module'])) { ?>
                    <input type='hidden' name='module' value='<?= $data['module'] ?>'>
                <? } ?>
                <input type='hidden' id='customer_id' name='customer_id' value='<?= $customer_id ?>'>
                <div class='form-group'>
                    <h3 class='col-xs-12'>Contact Info</h3>
                </div>

                <div class='form-group'>

                    <label for='first' class='col-xs-3 control-label'>First Name</label>
                    <div class='col-xs-9'>
                        <input id='first' name='first' class='form-control input-sm' value='<?= $customer['first_name'] ?>'>
                    </div>
                </div>

                <div class='form-group'>
                    <label for='last' class='col-xs-3 control-label'>Last Name</label>
                    <div class='col-xs-9'>
                        <input class='form-control input-sm' id='last' name='last_name' value='<?= $customer['last_name'] ?>'>
                    </div>
                </div>

                <div class='form-group'>
                    <label for='company' class='control-label col-xs-3'>Company</label>
                    <div class='col-xs-9'>
                        <input class='form-control input-sm' id='company' name='organization_name' value='<?= $customer['organization_name'] ?>'>
                    </div>
                </div>

                <div class='form-group'>
                    <label for='email_1' class='control-label col-xs-3'>Email</label>
                    <div class='col-xs-6'>
                        <input type='email' class='form-control input-sm' id='email_1' name='email_1'  value='<?= $customer['email_1'] ?>'>
                    </div>
                    <div class='col-xs-3 text-right'>
                        <?= form_dropdown('email_type_1',$email_options,$customer['email_type_1_int'], "class='form-control input-sm'");?>
                    </div>
                </div>

                <div class='form-group'>
                    <label for='email_2' class='control-label col-xs-3'>Email</label>
                    <div class='col-xs-6'>
                        <input type='email' class='form-control input-sm' id='email2' name='email_2' value='<?= $customer['email_2'] ?>'>
                    </div>
                    <div class='col-xs-3'>
                        <?= form_dropdown('email_type_2',$email_options,$customer['email_type_2_int'], "class='form-control input-sm'");?>
                    </div>
                </div>

                <div class='form-group'>
                    <label for='phone1' class='control-label col-xs-3'>Phone</label>
                    <div class='col-xs-6'>
                        <input class='form-control input-sm' id='phone1' name='phone_1' value='<?= $customer['phone_1'] ?>'>
                    </div>
                    <div class='col-xs-3'>
                        <?= form_dropdown('phone_type_1',$phone_options, $customer['phone_type_1_int'], "class='form-control input-sm'");?>
                    </div>
                </div>

                <div class='form-group'>
                    <label for='phone_2' class='control-label col-xs-3'>Phone</label>
                    <div class='col-xs-6'>
                        <input class='form-control input-sm' id='phone_2' name='phone_2' value='<?= $customer['phone_2'] ?>'>
                    </div>
                    <div class='col-xs-3'>
                        <?= form_dropdown('phone_type_2',$phone_options, $customer['phone_type_2_int'], "class='form-control input-sm'");?>
                    </div>
                </div>

                <div class='form-group'>
                    <label for='phone_3' class='control-label col-xs-3'>Phone</label>
                    <div class='col-xs-6'>
                        <input class='form-control input-sm' id='phone_3' name='phone_3' value='<?= $customer['phone_3'] ?>'>
                    </div>
                    <div class='col-xs-3'>
                        <?= form_dropdown('phone_type_3',$phone_options, $customer['phone_type_3_int'], "class='form-control input-sm'");?>
                    </div>
                </div>

                <div class='form-group'>
                    <div class='col-xs-12 text-right'>
                        <input id="editcustomersave" type='submit' name='submit' value='Save' class='btn btn-blue'>
                    </div>
                </div>
            </form>
        </div>
    <?
    $edit_form = str_replace('"', "'", ob_get_clean()); // why not to store huge html sections in tag attributes... edit: also because its really hard to bind stuff to that html using jquery :\ so much nicer to clone from hidden containers
    if ( isset($data['edit_link']))
    {
        ob_start();
        ?>
         
     <a href="javascript:void(0);" class="inline btn btn-default btn-content" id="remove-tab" data-placement="right" data-content="">Manage</a>
        <script>
            $("a[data-toggle=popover]").popover({
                html: true,
                title: function () {
                    return $(this).parent().find(".head").html();
                },
                content: function () {
                    return $(this).parent().find(".content").html();
                }
            });
        </script>
        <?
        $edit_form = ob_get_clean();
    } else {
        $edit_form = '';
    }

    ob_start();
    ?>
    <div class='contact_group'>
        <h4 class="inline info_header"><?= $data['title'] ?></h4>
        <?
            if ($mode === 'primary') {
                echo "<button class='btn btn-blue btn-sm' id='manage_customers'>Manage</button>";
            } else {
                echo $edit_form;
            }
        ?>
    </div>
    <div class='show-grid'>
        <span class="contact_name" id='contact_first_name'><?= $customer['first_name'] ?></span>
        <span class="contact_name" id='contact_last_name'><?= $customer['last_name'] ?></span><br />
        <div id='contact_organization_name' class='contact_item'><?= $customer['company_name'] ?></div>
    </div>
    <div class='show-grid'>
        <? if (!empty($customer['phone_1'])) { ?>
            <div class='contact_set'>
                <div id='contact_phone_type_1' class='contact_type'><?= $customer['phone_type_1'] ?></div>
                <div id='contact_phone_1' class='contact_item'><?= $customer['phone_1'] ?></div>
            </div>
        <? } ?>
        <? if (!empty($customer['phone_2'])) { ?>
            <div class='contact_set'>
                <div id='contact_phone_type_1' class='contact_type'><?= $customer['phone_type_2'] ?></div>
                <div id='contact_phone_1' class='contact_item'><?= $customer['phone_2'] ?></div>
            </div>
        <? } ?>
        <? if (!empty($customer['phone_3'])) { ?>
            <div class='contact_set'>
                <div id='contact_phone_type_1' class='contact_type'><?= $customer['phone_type_3'] ?></div>
                <div id='contact_phone_1' class='contact_item'><?= $customer['phone_3'] ?></div>
            </div>
        <? } ?>
    </div>
    <div class='show-grid'>
        <? if (!empty($customer['email_1'])) { ?>
            <div class='contact_set'>
                <div id='contact_email_type_1' class='contact_type'><?= $customer['email_type_1'] ?></div>
                <div id='contact_email_1' class='contact_item'><?= $customer['email_1'] ?></div>
            </div>
        <? } ?>
        <? if (!empty($customer['email_2'])) { ?>
            <div class='contact_set'>
                <div id='contact_email_type_2' class='contact_type'><?= $customer['email_type_2'] ?></div>
                <div id='contact_email_2' class='contact_item'><?= $customer['email_2'] ?></div>
            </div>
        <? } ?>
    </div>
</div>
<?
    $html .= ob_get_clean();
    return $html;
}

function show_site_info($site_id, $site, $site_options, &$mode ='default') {
    $ci =& get_instance();
    $ci->load->helper('functions_helper', 'info_helper');

    $options = array();
    $site_info = array();
    foreach ($site_options as $o) {
        $options[$o->id] = $o->address . ' ' . $o->address_ext . ', ' . $o->city . ', ' . $o->state . ' ' . $o->zipcode;
        $site_info[$o->id] = $o;
    }
    //prd($site);
    ob_start() ?>
    <div id="site_info_hidden" style="display: none;">
        <div class="site_info_popover text-center">
            <div class="form-group">
                <?= form_dropdown('', $options, $site_id, "id='select-jobsite' class='site_id form-control input-sm'") ?>
            </div>
            <div id="create-site-form" class="show-grid-sm">
                <hr />
                <div class="form-group text-left">
                    <h4><span id="site_popup_text">Create</span> Job Site</h4>
                </div>
                <form id="site_info_form" class="form-horizontal row" method="post">
                    <input type="hidden" data-name="id" name="id">
                    <div class="form-group">
                        <label for='site_address' class="col-sm-3 control-label">Address</label>
                        <div class="col-sm-9">
                            <input class='form-control input-sm' data-name="address" name='site_address' id='site_address' />
                        </div>
                    </div>
                    <div class="form-group">
                        <label for='site_address_ext' class="col-sm-3 control-label">Address ext</label>
                        <div class="col-sm-9">
                            <input class='form-control input-sm' data-name="address_ext" name='site_address_ext' id='site_address_ext' />
                        </div>
                    </div>
                    <div class="form-group">
                        <label for='site_city' class="col-sm-3 control-label">City</label>
                        <div class="col-sm-9">
                            <input class='form-control input-sm' data-name="city" name='site_city' id='city' class='large_input' />
                        </div>
                    </div>
                    <div class="form-group">
                        <label for='site_state' class="col-sm-3 control-label">State</label>
                        <div class="col-sm-9">
                            <?= state_select('site_state',false, true, array('data-name' => 'state', 'class' => 'form-control input-sm')); ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for='site_zipcode' class="col-sm-3 control-label">Zip-Code</label>
                        <div class="col-sm-9">
                            <input data-name="zipcode" class='form-control input-sm' name='site_zipcode' id='site_zipcode' />
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-offset-3 col-sm-9">
                            <select class='input-sm form-control' data-name="address_type" name='site_address_type'>
                                <option value='0'>Residential</option>
                                <option value='1'>Business</option>
                            </select>
                        </div>
                    </div>
                </form>
            </div>
            <div class="form-group">
                <button id='save_site_info' type='submit' name='submit' value='Save' class='btn btn-default btn-content'>Save</button>
            </div>
        </div>
    </div>
    <div id="job_site_cont" class="contact_group">
        <h4 class="inline info_header">Job Site</h4>
        <?php if($mode != 'view'): ?>
        <button class="btn btn-blue btn-sm" id="manage_site">Manage</button>
        <?php endif; ?>
    </div>
    <div id="site_select_address"><a href="/sites/edit/<?= $site_id ?>"><?= $site['address'] . ' ' . $site['address_ext'] . '</a><br>' . $site['city_state_zipcode'] . '<br>' . $site['address_type'] ?></div>
    <script>
        var indow_site_id_saved = <?= json_encode($site_id) ?>;
        var indow_site_id = <?= json_encode($site_id) ?>;
        function indow_set_site(site, site_id) {
            indow_site_id = site_id;
            var type = {'0':'Residential', '1':'Business'}[site.address_type]
            $('#site_select_address').html('<a href="/sites/edit/' + site_id + '">' + site.address + ' ' + site.address_ext + '</a><br>' + site.city + ', ' + site.state + ' ' + site.zipcode + '<br>' + (type ? 'Type: ' + type : ''));
            var module = window.indow_module ? window.indow_module : null;
            if (module === 'estimates') {
                indow_ajax_save.call(indow_module_obj, {site: site_id});
            }
        }
        $(function () {
            var site_info = <?= json_encode($site_info) ?>;
            $('#job_site_cont').on('click', '#save_site_info', function () {
                var site_id = $('#job_site_cont .site_id').val();
                var new_site = true;
                var address_data = $("#site_info_form").serialize();
                if (site_id > 0) {
                    new_site = false;
                    var site = site_info[site_id];
                    indow_set_site(site, site_id);
                } else {
                    address_data += "&customers=" + encodeURIComponent($.toJSON(customer_manager_get_customers()));
                }
                $.ajax({
                    url: '/sites/site_ajax',
                    type: 'POST',
                    dataType: 'json',
                    data: address_data,
                    success: function(response) {
                        if (response.success) {
                            indow_set_site(response.site, response.id);
                        }
                        alert(response.message);
                    }
                });
                $(this).closest('.popover').prev().click(); //close popover
                
            });

            $('#job_site_cont').on('change', '.site_id', function () {
                var site_id = $(this).val();
                var inputs = $('#create-site-form').find('input, select');
                var site = site_info[site_id];
                inputs.val('');
                if (site) {
                    inputs.each(function () {
                        var field = $(this).attr('data-name');
                        if (site[field] !== undefined) {
                            $(this).val(site[field]);
                        }
                    });
                    $('#site_popup_text').text('Edit')
                } else {
                    $('#site_popup_text').text('Create');
                }
            });

            $('#manage_site').popover({
                content: $('#site_info_hidden .site_info_popover'),
                html: true,
                placement: 'right',
                callback: function () {
                    var select = $('#manage_site').next().find('#select-jobsite');
                    var customer_ids = customer_manager_get_ids();
                    if (!customer_ids.length) {
                        $('#manage_site').popover('hide');
                        alert('A customer must be added first');
                        return;
                    }
                    select.prop('disabled', true).html('');
                    if (customer_ids.length) {
                        $.get('/sites/site_list_json/' + customer_ids.join('_'), function (sites) {
                            select.append('<option value="">--- Select Jobsite ---</option>');
                            select.append('<option value="0">Create Jobsite</option>');
                            $.each(sites, function (i, site) {
                                site_info[site.id] = site;
                                var option = $('<option value="' + site.id + '">').text(site.address + ' ' + site.address_ext + ', ' + site.city + ' ' + site.state);
                                select.append(option);
                            });
                            select.val(indow_site_id).prop('disabled', false).change();
                        });
                    }
                }
            });

        });
    </script>
    <?
    $html = ob_get_clean();
    return $html;
}
function user_assoc($users, $show_bar=true) {
    ob_start();
    ?>
        <script src="<?php echo base_url('assets/theme/default/js/users_search.js'); ?>"></script>
        <script>
            var indow_user_assoc_init = <?= json_encode($users) ?>;
        </script>
        <style>
            #user_search_results {
                left: 0;
                bottom: 30px;
                position: absolute;
                width: 200px;
                background-color: #ffffff;
                border: 1px solid #999999;
                -webkit-border-radius: 4px;
                -moz-border-radius: 4px;
                border-radius: 4px;
                display: none;
                z-index: 4;
            }
            .search_user {
                padding: 4px;
                border-bottom: 1px solid #cccccc;
            }
            .search_user:last-child {
                border-bottom: 0px;
            }
            .search_user.active {
                background-color: #cecece;
            }
            #srcont {
                position: relative;
                top: 22px;
                left: 0;
            }
            #ajax_user_search {
                width: 50%;
            }
        </style>
        <? if ($show_bar) { ?>
        <div id="assoc_users_cont" class="assumption-acc">
            <a style="width:100%;" data-target="#assoc_users"  href="javascript:void(0);" data-toggle="collapse" class="add-user-opt"><h1><span id="associated_user_count">0</span> Associated Users <span id="extra_info"></span>  <i style="margin-top: 7px;float: right;margin-right: 10px;" class="fa fa-chevron-down"></i></h1> </a>

            <div id="assoc_users" class="collapse out">
        <? } ?>
                <div class="col-xs-12">
                    <br>
                    <?= form_input(array('name' => 'associated_customer',
                        'id' => 'ajax_user_search',
                        'class' => 'form-control input-sm',
                        'size' => '50',
                        'placeholder' => 'Search by User Email/Name to Add',
                        'autocomplete' => 'off',
                        'selector' => '#optional-user'
                    )); ?>
                    <span id="srcont">
                        <table class="display table dataTable no-footer" id="user_search_results"></table>
                    </span><br>
                </div>
                <table id="user_manager_table" class="display table table-hover">
                    <thead>
                        <tr>
                            <th>Actions</th>
                            <th>Name</th>
                        </tr>
                    </thead>
                </table>
        <? if ($show_bar) { ?>
            </div>
        </div>
        <? } ?>
        <?
    return ob_get_clean();
}

function customer_history() {
    ob_start();
    ?>
        <div id="orders-section" class="assumption-acc">
            <a style="width:100%;" data-target="#customerhistory"  href="javascript:void(0);" data-toggle="collapse" class="add-user-opt"><h1>Customer & Dealer History  <i style="margin-top: 7px;float: right;margin-right: 10px;" class="fa fa-chevron-down"></i></h1> </a>
            <div id="customerhistory" class="collapse out">
                <table id="customerhistorytable" class="display table table-hover" style="width: 100%">
                    <thead>
                        <tr >
                            <th>Actions</th>
                            <th>Group</th>
                            <th>Type</th>
                            <th>First</th>
                            <th>Last</th>
                            <th>Zip</th>
                            <th>Username</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    <?
}