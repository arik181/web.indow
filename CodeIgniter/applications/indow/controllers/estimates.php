<?php if (!defined('BASEPATH')) exit('No direct script access allowed');


class Estimates extends MM_Controller
{
    protected $_model;
    protected $_user;
    protected $_feature = 2;


    function __construct()
    {
        parent::__construct();

        $this->load->model(array('user_model', 'customer_model', 'product_model', 'item_model', 'estimate_model', 'configuration_calculator_model', 'group_model', 'sales_modifiers_model'));
        $this->load->helper(array('notes', 'download', 'language', 'macrolist', 'totallist', 'info', 'contact_info_helper', 'site_info_helper', 'tabbar', 'ulist', 'fees_helper'));
        $this->load->factory("EstimateFactory");
        $this->load->factory("ItemFactory");
        $this->_user = $this->data['user'];
    }

    public function index_get($order_id='')
    {
        $this->permissionslibrary->require_view_perm($this->_feature);

        $data = array(
            'content' => 'modules/estimates/list',
        );

        //$data["estimates"] = $this->estimate_model->fetch_estimates(10, 0);

        $data['nav']            = "estimates";
        $data['title']          = 'Estimates';
        $data['subtitle']       = 'Estimate List';
        $data['edit_path']      = '/estimates/edit';
        $data['add_path']       = '/estimates/add';
        $data['form']           = '/estimates/list';
        $data['delete_path']    = '/estimates/delete';
        $data['add_button']     = 'Create New Estimate';
        $data['manager']        = 'Estimates';
        $data['section']        = 'Estimate List';
        $data['order_id']       = $order_id;
        

          ///////////////////////////////
         //   Start Quick Estimates   //
        ///////////////////////////////
        $data['edging_options'] = $this->estimate_model->id_name_array('edging');
        /* If option value changes be sure to check quick_estimates.js function filter_select */
        $data['product_type_options'] = $this->estimate_model->id_name_array('product_types', 'product_type'); /* Pulled from extended class */
        $data['product_options'] = $this->product_model->get_all();
        $dealer_id  = $this->user_model->get_group_id($this->ion_auth->get_user_id());
        $data['product_info'] = $this->product_model->get_product_info($dealer_id, date('Y-m-d'));

        // Show Quick-Estimates Button
        $data['qe_button']      = TRUE;
     
        // This is all data that should be connected to a model   
        $data['products'][] = (object) array(
            'id' => 1,
            'name' => 'IndowWindows'
        );

        $data['product_types'][] = (object) array(
            'id' => 1,
            'name' => 'Standard',
            'cost' => 40
        );

        $data['product_types'][] = (object) array(
            'id' => 2,
            'name' => 'Museum',
            'cost' => 90
        );

        $data['product_types'][] = (object) array(
            'id' => 3,
            'name' => 'Blackout',
            'cost' => 45
        ); 

        $data['edging'][] = (object) array(
            'id' => 1,
            'name' => 'White'
        );

        $data['edging'][] = (object) array(
            'id' => 2,
            'name' => 'Black'
        );     

        $data['edging'][] = (object) array(
            'id' => 3,
            'name' => 'Brown'
        );

        $data['quick_estimate'] = $this->load->view('modules/estimates/quick_estimate', $data, TRUE);                     

        ///////////////////////////////
        //    End Quick Estimates    //
        ///////////////////////////////

        if (!$this->session->flashdata('message') == FALSE) 
        {
            $data['message'] = $this->session->flashdata('message');
        }

        $this->load->view($this->config->item('theme_list'), $data);

    }

    public function delete_get($current_id)
    {
        $this->permissionslibrary->require_edit_perm($this->_feature, $current_id);
        $this->estimate_model->delete($current_id);
        $this->session->set_flashdata('message', 'Estimate Deleted');
        redirect('estimates');
    }

    public function ajax_save_post($estimate_id) {
        if (!$this->permissionslibrary->has_edit_permission($this->_feature, $estimate_id)) {
            $this->response(array('message' => 'You do not have permission to make changes to this estimate.'), 200);
            exit;
        }
        $data = json_decode($this->input->post('data'), true);
        if ($estimate_id) {
            $response = new stdClass;

            if (!empty($data['additem'])) {
                $bad_keys = array('id', 'product', 'product_id', 'square_feet');
                foreach ($bad_keys as $key) {
                    unset($data['additem'][$key]);
                }
                $this->db->insert('items', $data['additem']);
                $item_id = $this->db->insert_id();
                $this->db->insert('estimates_has_item', array(
                    'estimate_id' => $estimate_id,
                    'item_id' => $item_id
                ));
                $response->additem = $item_id;
            }

            if (!empty($data['new_subproducts'])) {
                $response->new_subproducts = array();
                foreach ($data['new_subproducts'] as $subp) {
                    $subp = json_decode(json_encode($subp));
                    if ($subp->id === 'new') {
                        unset($subp->id);
                        $subp->item_id = $data['parent_id'];
                    }
                    $this->db->insert('subitems', $subp);
                    $subp->id = $this->db->insert_id();
                    $response->new_subproducts[] = $subp;
                }
            }

            if (!empty($data['delete_items'])) {
                $this->estimate_model->delete_items($data['delete_items'], $estimate_id);
            }

            if (!empty($data['update_subproduct'])) {
                unset($data['update_subproduct']['checked']);
                $this->db->where('id', $data['update_subproduct']['id'])->update('subitems', $data['update_subproduct']);
            }

            if (!empty($data['update_item'])) {
                unset($data['update_item']['checked']);
                if (isset($data['update_item']['room']) && $data['update_item']['room'] === '') {
                    $data['update_item']['room'] = 'unknown';
                }
                $this->db->where('id', $data['update_item']['id'])->update('items', $data['update_item']);
            }

            if (!empty($data['allitems'])) {
                $this->estimate_model->saveitems($data['allitems'], $estimate_id);
            }

            if (isset($data['fees'])) {
                $this->sales_modifiers_model->set_estimate_modifiers($data['fees'], $estimate_id, $data['user_fees'], $data['delete_user_fees']);
                $response->user_fees = $this->sales_modifiers_model->get_estimate_fees($estimate_id, 1);
                if ($response->user_fees === array()) {
                    $response->user_fees = new stdClass;
                }
            }

            if (!empty($data['estimate_data'])) {
                $this->db->where('id', $estimate_id)->update('estimates', $data['estimate_data']);
            }

            if (isset($data['customers'])) {
                $this->estimate_model->set_estimate_customers($estimate_id, $data['customers']);
            }
            if (!empty($data['change_owner'])) {
                $this->estimate_model->change_owner($estimate_id, $data['change_owner']);
            }

            $this->response($response, 200);
        }
        $this->response('No item id', 500);
    }

    public function list_json_get($cid, $order_id=null)
    {
        $this->permissionslibrary->require_view_perm($this->_feature);
        $results = $this->EstimateFactory->getList($cid, $order_id);

        $dataTables = array("draw"=>1,
                            "recordsTotal"=>count($results),
                            "recordsFiltered"=>count($results),
                            "data"=>$results);

        $this->response($dataTables,200);

    }

    public function item_list_json_get($estimate_id)
    {
        $this->permissionslibrary->require_view_perm($this->_feature, $estimate_id);
        $results = $this->ItemFactory->getEstimateList($estimate_id);
        $dataTables = array("draw"            => 1,
                            "recordsTotal"    => count($results),
                            "recordsFiltered" => count($results),
                            "data"            => $results);
        $this->response($dataTables, 200);
    }

    public function item_list__wsubitems_json_get($estimate_id)
    {
        $this->permissionslibrary->require_view_perm($this->_feature, $estimate_id);
        $results = $this->ItemFactory->getEstimateList_wsubitems($estimate_id);
        $dataTables = array("draw"            => 1,
                            "recordsTotal"    => count($results),
                            "recordsFiltered" => count($results),
                            "data"            => $results);
        $this->response($dataTables, 200);
    }

    public function add_get() {
        $this->permissionslibrary->require_edit_perm($this->_feature);
        $this->edit_get(0);
    }

    public function edit_get($estimate_id)
    {
        $this->permissionslibrary->require_view_perm($this->_feature, $estimate_id);

        if (isset($estimate_id))
        {
            $estimate = $this->estimate_model->getProfile($estimate_id);
            if ($estimate && $estimate->deleted) {
                redirect();
            }
            if (empty($estimate) && !empty($estimate_id)) {
                redirect('/estimates');
            }

            $data = array(
                'content'  => 'modules/estimates/edit',
                'estimate' => $estimate,
                'windows'  => '',
                'mode'     => 'edit'
            );
            $data['subitems'] = $this->estimate_model->get_subitems($estimate_id);

            $message = $this->session->flashdata('message');

            if (!empty($message)) {
                $data['message'] = $message;
            }

            if (!empty($estimate->dealer_id)) 
            {
                $dealer_id = $estimate->dealer_id;

            } else {

                $dealer_id  = $this->user_model->get_group_id($this->ion_auth->get_user_id());
            }
            $data['nav']                  = "estimates";
            $data['title']                = 'Estimates';
            $data['subtitle']             = 'Add/Edit Estimate';
            $data['add_path']             = '/estimates/add';
            $data['form']                 = '/estimates/list';
            $data['delete_path']          = '/estimates/delete';
            $data['manager']              = 'Estimates';
            $data['section']              = 'Edit Estimate';
            $data['estimate_id']          = (integer) $estimate_id;
            $data['edging_options']       = $this->estimate_model->id_name_array('edging');
            $data['product_type_options'] = $this->estimate_model->id_name_array('product_types', 'description');
            $data['product_options']      = $this->product_model->get_all();
            $data['product_info']         = $this->product_model->get_product_info($dealer_id, $estimate ? date('Y-m-d', strtotime($estimate->created)) : date('Y-m-d'));
            $data['fee_info']             = $this->sales_modifiers_model->get_active_fees($estimate_id, true, $dealer_id);
            $data['fees_sorted']          = $this->sales_modifiers_model->sortmodifiers($data['fee_info']);
            $data['active_fees']          = $this->sales_modifiers_model->get_estimate_fee_ids($estimate_id);
            $data['user_fees']            = $this->sales_modifiers_model->get_estimate_fees($estimate_id, 1);
            $data['techs']                = $this->group_model->get_tech_members($dealer_id);
            $data['tech']                 = $this->estimate_model->get_tech($estimate_id);
            $data['dealer_id']            = $dealer_id;
            $data['current_user'] = $this->_user->id;
            if ($this->_user->in_admin_group) {
                $data['change_owner_users'] = $this->user_model->simple_list();
                unset($data['change_owner_users']['']);
            } else {
                if ($estimate) {
                    $data['change_owner_users'] = array('' => '') + $this->user_model->associated_rep_list($estimate->dealer_id);
                } else {
                    $data['change_owner_users'] = array('' => '') + $this->user_model->associated_rep_list($this->_user->group_ids[0]);
                }
            }
            $customer_data = array(
            'title'         => 'Customer Info',
            'empty_message' => 'No Info Available',
            'edit_link'     => "#contact_info_popup",
            );

            list($cust_ids, $primary) = $this->customer_model->get_estimate_customers($estimate_id);

            $data['primary']            = $primary;
            $data['start_customers']    = $this->customer_model->customer_manager_get_customer($cust_ids, $primary);
            $customer                   = $this->customer_model->fetch_customer_info($primary);
            $keys                       = array( 'name', 'company_name', 'phone_1_type', 'phone_1', 'phone_2_type', 'phone_2', 'email_1_type', 'email_1', 'email_2_type', 'email_2' );

            $data['contact_info']       = generate_contact_info($primary, $customer_data, $customer, $keys, $mode="primary");
            // Customer Info
            if ($estimate_id) {

            } else {
                $data['groups'] = $this->group_model->simple_list();
                $data['users']  = $this->user_model->simple_list();
            }

            //Notes
            $data['user_name'] = $this->user_model->get_user_name($this->ion_auth->get_user_id());
            $estimate_notes_data = array(
                         'title'        => 'Estimate Notes',
                         'form'         => "/",
                         'form_value'   => "Save Notes",
                         'form_name'    => null,
                         'empty_message'=> "There are no estimate notes associated with this estimate",
                         'jsname'       => "estimate_note",
                         'hidesubmit'   => !$estimate_id,
                         'type'         => 'estimate',
                        );
            $estimate_notes             = $this->estimate_model->fetch_estimate_notes_history($estimate_id);
            $data['estimate_notes']     = generate_notes($estimate_id, $estimate_notes_data, $estimate_notes);

            // Site Info
            $site_id   = isset($estimate->site_id) ? $estimate->site_id : null;
            $site = $this->site_model->fetch_site_info($site_id);
            $site_options = $this->site_model->get_site_options($this->ion_auth->get_user_id());
            $data['site_info'] = show_site_info($site_id, $site, $site_options);

            // Saved Estimates drop-down box
            $data['se_dropdown']        = $this->estimate_model->get_saved_estimates($estimate_id);


            $data['items'] = $this->item_model->fetch_items($estimate_id);

            $this->load->view($this->config->item('theme_home'), $data);

        } else {

            $this->session->set_flashdata('message', 'Unable to find the estimate. Please try again.');
            redirect('estimates/list');
        }
    }

    public function file_download_get($file_id) {
        $this->permissionslibrary->require_view_perm($this->_feature);
        $group_ids = $this->permissionslibrary->_user->allGroupIds;
        $file = $this->db->where('id', $file_id)->get('file_uploads')->row();
        if (!$file || !in_array($file->group_id, $group_ids)) {
            return;
        }
        header('Content-Disposition: attachment; filename="' . $file->filename . '"');
        $this->output->set_content_type('application/force-download');
        $contents = file_get_contents('./uploads/' . $file->group_id . '/' . $file->filename);
        echo $contents;
        
    }

    private function forward_post($url, $data) { //only way to post redirect.  Cant be get because items may excede maxlength. could create a temp db entry, but this is easier
        ob_start();
        ?>
        <script src="/assets/theme/default/js/jquery.js"></script>
        <script type="text/javascript">
          $(document).ready(function() {
            window.document.forms[0].submit();
          });
        </script>
        <form method="post" action="<?= $url ?>">
            <? foreach ($data as $key => $value) { ?>
                <input type="hidden" name="<?= $key ?>" value="<?= htmlspecialchars($value) ?>">
            <? } ?>
        </form>
        <?
        exit(ob_get_clean());
    }

    public function edit_post($estimate_id)
    {
        $this->permissionslibrary->require_edit_perm($this->_feature, $estimate_id);
        $forcenew = $estimate_id === 0;
        $post = $this->post();
        $data = json_decode($post['estimatedata'], true);
        $followup = isset($data['followup']) ? $data['followup'] : 0;
        if ($estimate_id === 0) { //new estimate
            $user_id = $this->ion_auth->get_user_id();
            $dealer_id = $this->user_model->get_dealer_id($user_id);
            $estimate = array(
                'created_by_id' => $user_id,
                'history_id' => 1,
                'created' => date('Y-m-d H:i:s'),
                'dealer_id' => $dealer_id
            );
            $estimate_id = $this->estimate_model->create($estimate);
        }

        if ($data['save']) 
        {
            if ( ! isset($data['customers']) || empty($data['customers']))
            {
                $this->session->set_flashdata('message', 'A primary customer must be added before saving.');
                redirect('/estimates/edit/' . $estimate_id);
            }

            if (isset($data['delete_items'])) {
                $this->estimate_model->delete_items($data['delete_items'], $estimate_id);
            }
            $this->estimate_model->saveitems($data['items'], $estimate_id, $forcenew);
            $this->sales_modifiers_model->set_estimate_modifiers($data['fees'], $estimate_id, $data['user_fees'], $data['delete_user_fees']);
            if (isset($data['estimate_data'])) {
                $this->estimate_model->update($estimate_id, $data['estimate_data']);
            }
            if (isset($data['customers'])) {
                $this->estimate_model->set_estimate_customers($estimate_id, $data['customers']);
            }
            if (isset($data['notes']) && $data['notes']) {
                $this->estimate_model->add_note($estimate_id, $data['notes']);
            }
        }

        if ($data['createnew']) {
            $fwdata = array(
                'estimate_id' => $estimate_id,
                'items' => $data['items'],
                'fees' => $data['fees'],
                'user_fees' => $data['user_fees'],
                'customers' => $data['customers'],
                'site_id' => $data['estimate_data']['site_id']
            );
            $this->forward_post('/estimates/createnew/' . $estimate_id, array(
                'data' => json_encode($fwdata)
            ));
        }
        $this->session->set_flashdata('message', 'Estimate saved.');
        redirect('/estimates/edit/' . $estimate_id);
    }

    public function createnew_get($id=null) {
        $id = (integer) $id;
        redirect('/estimates/edit/' . $id);
    }

    public function createnew_post($id) {
        $this->permissionslibrary->require_view_perm($this->_feature, $id);
        $this->permissionslibrary->require_edit_perm($this->_feature); //view permission on specific estimate needed, edit permission on estimates in general
        if (!$this->data['auth']) {
            redirect();
        }
        $post = $this->post();
        $edata = json_decode($post['data'], true);

        $data = array(
            'content'  => 'modules/estimates/createnew',
            'windows'  => '',
            'mode'     => 'edit'
        );
        $data['parent_id']            = $id;
        $data['items']                = $edata['items'];
        $data['nav']                  = "estimates";
        $data['title']                = 'Estimates';
        $data['subtitle']             = 'Estimate Configuration Calculator';
        $data['add_path']             = '/estimates/add';
        $data['form']                 = '/estimates/list';
        $data['delete_path']          = '/estimates/delete';
        $data['manager']              = 'Estimates';
        $data['customers']            = $edata['customers'];
        $data['fees']                 = $edata['fees'];
        $data['user_fees']            = $edata['user_fees'];
        $data['site_id']              = $edata['site_id'];
        $data['section']              = 'Edit Estimate';
        $data['estimate_id']          = (integer) $edata['estimate_id'];
        $dealer_id = $this->db->where('id', $data['estimate_id'])->get('estimates')->row()->dealer_id;
        $data['edging_options']       = $this->estimate_model->id_name_array('edging');
        $data['product_type_options'] = $this->estimate_model->id_name_array('product_types', 'description');
        $data['product_options']      = $this->estimate_model->id_name_array('products', 'product');
        $data['product_info']         = $this->product_model->get_product_info();
        $data['fee_info']             = $this->sales_modifiers_model->get_active_fees(null, true, $dealer_id);
        $this->load->view($this->config->item('theme_home'), $data);
    }

    public function add_post()
    {
        $this->edit_post(0);
    }

    public function save_notes_post($estimate_id) {
        $this->permissionslibrary->require_edit_perm($this->_feature, $estimate_id);
        $data = $this->post();
        $note = $data['note'];
        $this->estimate_model->add_note($estimate_id, $note);
        $this->response(array(
            'success' => true,
            'date' => date("M j g:ia")
        ), 200);
    }

    public function assign_tech_post($estimate_id) {
        $this->permissionslibrary->require_edit_perm($this->_feature, $estimate_id);
        $tech_id = $this->input->post('tech_id');
        $estimate = $this->estimate_model->getProfile($estimate_id);
        list($cust_ids, $primary) = $this->customer_model->get_estimate_customers($estimate_id);
        $success = false;
        $messages = array();
        if (!$primary) {
            $messages[] = 'The estimate must have a primary customer to continue.';
        }
        if (!$estimate->site_id) {
            $messages[] = 'The estimate must have a job site to continue.';
        }
        if ($estimate->tech_id) {
            $messages[] = 'The estimate already has a tech assigned.';
        }
        if (!$messages) {
            $this->estimate_model->assign_tech($estimate_id, $tech_id);
            $success = true;
            $message = 'The tech has been successfully assigned.';
        } else {
            $message = implode("\n", $messages);
        }
        $this->response(array('success' => $success, 'message' => $message));
    }

    public function from_site_get($site_id) {
        $this->permissionslibrary->require_edit_perm($this->_feature);
        $estimate_id = $this->estimate_model->create_from_site($site_id);
        redirect('/estimates/edit/' . $estimate_id);
    }

    public function export_data_get($estimate_id){
        $this->permissionslibrary->require_view_perm($this->_feature, $estimate_id);
        $data = $this->estimate_model->customer_estimate_csv($estimate_id);
        $name = 'customer_estimates.csv';
        force_download($name, $data);
    }

    public function package_get($estimate_id) 
    {
        $this->permissionslibrary->require_view_perm($this->_feature, $estimate_id);
        if (isset($estimate_id))
        {
            $estimate = $this->estimate_model->getProfile($estimate_id);

            $data = array(
                'content'  => 'modules/estimates/package',
                'estimate' => $estimate,
            );
            $data['subitems'] = $this->estimate_model->get_subitems($estimate_id);

            if (!empty($estimate->dealer_id)) {
                $dealer_id = $estimate->dealer_id;
            } else if (!$estimate_id) {
                $dealer_id  = $this->user_model->get_group_id($this->ion_auth->get_user_id());
            } else {
                $dealer_id = null; //freebird
                $dealer_address_id = null;
            }
            if ($dealer_id) {
                $dealer = $this->Group_model->getProfile($dealer_id);
                $dealer->logo = $this->Group_model->get_logo_url($dealer_id);
                $data['dealer_address'] = $this->Group_model->get_address($dealer_id);
            }
            $data['dealer'] = $dealer;

            $data['entity_id']          = (integer) $estimate_id;
            $data['product_info']         = $this->product_model->get_product_info($dealer_id);
            $data['fee_info']             = $this->sales_modifiers_model->get_active_fees($estimate_id, true, $dealer_id);
            $data['active_fees']          = $this->sales_modifiers_model->get_estimate_fee_ids($estimate_id);
            $data['user_fees']            = $this->sales_modifiers_model->get_estimate_fees($estimate_id, 1);
            $data['creator']              = $this->user_model->getProfile($estimate->created_by_id);
            $data['type']                 = 'estimate';
            $data['logo']                 = $this->group_model->get_logo_url($dealer_id);
            $data['extra_html']           = $this->group_model->get_package_html($dealer_id);

            list($cust_ids, $primary) = $this->customer_model->get_estimate_customers($estimate_id);

            if ($primary) {
                $data['customer']           = $this->customer_model->fetch_customer_info($primary);
                $data['customer_address']   = $this->db->where('user_id', $primary)->limit(1)->get('user_addresses')->row();
            }

            // Site Info
            $site_id   = isset($estimate->site_id) ? $estimate->site_id : null;
            $site = $this->site_model->fetch_site_info($site_id);
            $data['site'] = $this->site_model->fetch_site_info($site_id);
            $this->load->view('modules/estimates/package', $data);

        } else {

            $this->session->set_flashdata('message', 'Unable to find the estimate. Please try again.');
            redirect('estimates/list');
        }
    }
}
