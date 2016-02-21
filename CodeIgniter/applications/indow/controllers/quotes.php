<?php if (!defined('BASEPATH')) exit('No direct script access allowed');


class Quotes extends MM_Controller
{

    protected $_user;
    protected $_feature = 3;

    public function __construct()
    {
        parent::__construct();
        $this->load->helper(array('macrolist', 'totallist', 'info', 'contact_info_helper', 'site_info_helper', 'tabbar', 'ulist', 'fees_helper', 'ship_to_helper'));
        $this->load->factory(array('QuoteFactory', 'ItemFactory'));
        $this->_user = $this->data['user'];
    }

    public function index_get()
    {
        redirect();

        $data = array(
            'content'           => 'modules/quotes/list',
            'title'             => 'Quotes',
            'nav'               => 'quotes',
            'subtitle'          => 'Quotes',
            'manager'           => 'Quotes',
            'section'           => 'Quotes',
            'associated_orders' => null,
            'add_path'          => '/quotes/add',
        );

        $this->load->view($this->config->item('theme_list'), $data);

    }

    public function list_json_get($cid)
    {
        $this->permissionslibrary->require_view_perm($this->_feature);

        if($this->_user->in_admin_group)
        {
            $results    = $this->QuoteFactory->getAdminList($this->_user->id);
        }
        else
        {
            $results    = $this->QuoteFactory->getList($this->_user->id);
        }

        $dataTables = array(
                "draw"            => 1,
                "recordsTotal"    => count($results),
                "recordsFiltered" => count($results),
                "data"            => $results
                );

        $this->response($dataTables, 200);

    }


    public function item_list_json_get($quote_id)
    {
        $this->permissionslibrary->require_view_perm($this->_feature, $quote_id);

        $results    = $this->ItemFactory->getQuoteList($quote_id);

        $dataTables = array("draw"            => 1,
                            "recordsTotal"    => count($results),
                            "recordsFiltered" => count($results),
                            "data"            => $results);

        $this->response($dataTables, 200);

    }

    public function add_get()
    {
        $this->edit_get(0);
    }

    public function edit_get($quote_id)
    {
        $this->permissionslibrary->require_view_perm($this->_feature, $quote_id);

        $quote = $this->QuoteFactory->get($quote_id);
        if (empty($quote) && !empty($quote_id)) {
            redirect();
        }


        $data = array(
            'content'     => 'modules/quotes/edit',
            'quote'       => $quote,
            'mode'        => 'edit',
            'nav'         => 'quotes',
            'title'       => 'Quotes',
            'subtitle'    => '',
            'add_path'    => '/quotes/add',
            'form'        => '/quotes/list',
            'delete_path' => '/quotes/delete',
            'manager'     => 'Quotes',
            'section'     => 'Edit Quote',
        );
        if (!$this->session->flashdata('message') == false) {
            $data['message'] = $this->session->flashdata('message');
        }
        // Customer Info
        if (isset($quote->created)) {
            $quote_date = $quote->created;
        } else {
            $quote_date = date('Y-m-d H:i:s');
        }
        $customer_data = array(
            'title'         => 'Primary Customer Info',
            'empty_message' => 'No Info Available',
            'edit_link'     => "#contact_info_popup",
        );
        if (!empty($quote->dealer_id)) {
            $dealer_id = $quote->dealer_id;
        } else {
            $dealer_id  = $this->user_model->get_group_id($this->ion_auth->get_user_id());
        }
        $keys          = array('name', 'company_name', 'phone_1_type', 'phone_1', 'phone_2_type', 'phone_2', 'email_1_type', 'email_1', 'email_2_type', 'email_2');
        list($cust_ids, $primary) = $this->customer_model->get_quote_customers($quote_id);
        $customer                     = $this->customer_model->fetch_customer_info($primary);
        $data['start_customers']      = $this->customer_model->customer_manager_get_customer($cust_ids, $primary);
        $data['contact_info']         = generate_contact_info($primary, $customer_data, $customer, $keys, 'primary');
        $data['product_type_options'] = $this->quote_model->id_name_array('product_types', 'description');
        $data['product_options']      = $this->quote_model->id_name_array('products', 'product');
        $data['frame_step_options']   = array(
            '' => '',
            1  => 1,
            2  => 2,
            3  => 3,
            4  => 4,
            5  => 5,
            6  => 6,
            7  => 7,
            8  => 8,
            9  => 9,
            10 => 10
        );
        $data['frame_depth_options']  = $this->quote_model->id_name_array('frame_depth', 'name', 'id', true);

        $data['window_shapes'] = $this->quote_model->id_name_array('window_shapes', 'name', 'id', true);

        $data['product_info']   = $this->product_model->get_product_info($dealer_id);
        $data['extension_state'] = true;//$this->item_model->get_extension_state($order_id);
        $data['edging_options'] = $this->quote_model->id_name_array('edging');

        $data['fee_info']    = $this->sales_modifiers_model->get_active_fees($quote_date, true, $dealer_id);
        $data['fees_sorted'] = $this->sales_modifiers_model->sortmodifiers($data['fee_info']);
        $data['active_fees'] = $this->sales_modifiers_model->get_quote_fee_ids($quote_id);
        $data['user_fees']   = $this->sales_modifiers_model->get_quote_fees($quote_id, 1);

        // Site Info
        $site_id           = isset($quote->site_id) ? $quote->site_id : null;
        $site              = $this->site_model->fetch_site_info($site_id);
        $site_options      = $this->site_model->get_site_options($this->ion_auth->get_user_id());
        $data['site_info'] = show_site_info($site_id, $site, $site_options);

        $data['is_admin'] = $this->_user->in_admin_group;
        if ($this->permissionslibrary->has_edit_permission(6)) {
            $data['product_info_msrp'] = $this->product_model->get_product_info();
            $data['review_wholesale_discount'] = $this->group_model->get_wholesale_discount($dealer_id);
            $data['dealer_id'] = $dealer_id;
            $data['dealer_address']   = $this->Group_model->get_address($dealer_id);
            
            // Ship To
            $ship_data = array(
                'title'         => 'Ship To',
                'empty_message' => 'No Info Available',
                'edit_link'     => "#ship_to_popup",
                'order_review'  => true,
                'dealer_id'     => $dealer_id,
            );

            /*
            if (!empty($order->shipping_address_id)) {
                $ship = $this->db->where('id', $order->shipping_address_id)->get('user_addresses')->result_array();
                $ship = $ship[0];
            } else {
                $ship = false;
            }*/
            if ($dealer_id != 1) {
                $ship = $this->group_model->get_shipping_address($dealer_id, true);
                if ($ship) {
                    $ship['id'] = 'd' . $ship['id'];
                }
            } else {
                $ship = $this->customer_model->get_shipping_address($primary, true);
            }
            $data['ship_to'] = generate_ship_to(null, $ship_data, $ship, $keys, array(), true, $data['mode']);
        }

        $this->load->view($this->config->item('theme_home'), $data);
    }

    public function add_post()
    {
        $this->edit_post(0);
    }

    public function edit_post($quote_id)
    {
        $this->permissionslibrary->require_edit_perm($this->_feature, $quote_id);
        $post = $this->post();
        $data = json_decode($post['quotedata'], true);
        if (!$quote_id) 
        {
            $quote_id = $this->quote_model->create_new();
        }

        //$this->item_model->delete_quote_items($data['delete_items'], $quote_id);
        //$this->item_model->save_quote_items($data['items'], $quote_id);
        $this->sales_modifiers_model->set_quote_modifiers($data['fees'], $quote_id, $data['user_fees'], $data['delete_user_fees']);
        $quotedata = array();
        $this->quote_model->update($quote_id, array('followup' => $data['followup'], 'site_id' => $data['site_id']));
        $this->customer_model->set_quote_customers($data['customers'], $quote_id);
        $this->session->set_flashdata('message', 'Quote saved.');
        redirect('/quotes/edit/' . $quote_id);
    }

    public function from_estimate_post($estimate_id)
    {
        $this->permissionslibrary->require_view_perm(2, $quote_id);
        $this->permissionslibrary->require_edit_perm($this->_feature); //require view permission for the estimate being copied from, and require edit perm for quotes
        $problems = array();
        list($cust_ids, $primary) = $this->customer_model->get_estimate_customers($estimate_id);
        if (!$primary) {
            $problems[] = 'The estimate must have a primary customer to continue.';
        }
        $site_id = $this->db->where('id', $estimate_id)->get('estimates')->row()->site_id;
        if (!$site_id) {
            $problems[] = 'The estimate must have a job site to continue.';
        }
        if (count($problems)) {
            $this->session->set_flashdata('message', implode('<br>', $problems));
            redirect('/estimates/edit/' . $estimate_id);
        } else {
            $order_id = $this->quote_model->create_from_estimate($estimate_id);
            redirect('/quotes/edit/' . $order_id);
        }

    }

    public function delete_get($current_id)
    {
        $this->permissionslibrary->require_edit_perm($this->_feature, $current_id);
        $site_id = $this->db->where('id', $current_id)->get('quotes')->row()->site_id;
        $quotes_id = $this->quote_model->delete($current_id);
        redirect('/sites/edit/' . $site_id);
    }

    public function print_get($quote_id) {
        $this->permissionslibrary->require_view_perm($this->_feature, $quote_id);
        if (isset($quote_id))
        {
            $quote = $this->db->where('id', $quote_id)->get('quotes')->row();
            if (!$quote) {
                redirect();
            }

            $data = array(
                'content'  => 'modules/estimates/package',
                'quote' => $quote,
            );

            if (!empty($quote->dealer_id)) 
            {
                $dealer_id = $quote->dealer_id;

            } else if (!$quote_id) {

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

            $data['entity_id']             = (integer) $quote_id;
            $data['product_info']         = $this->product_model->get_product_info($dealer_id);
            $data['fee_info']             = $this->sales_modifiers_model->get_active_fees(null, true, $dealer_id);
            $data['active_fees']          = $this->sales_modifiers_model->get_quote_fee_ids($quote_id);
            $data['user_fees']            = $this->sales_modifiers_model->get_quote_fees($quote_id, 1);
            $data['creator']              = $this->user_model->getProfile($quote->created_by);
            $data['type']                 = 'quote';
            $data['logo']                 = $this->group_model->get_logo_url($dealer_id);

            list($cust_ids, $primary) = $this->customer_model->get_quote_customers($quote_id);

            if ($primary) {
                $data['customer']           = $this->customer_model->fetch_customer_info($primary);
                $data['customer_address']   = $this->db->where('user_id', $primary)->limit(1)->get('user_addresses')->row();
            }

            // Site Info
            $site_id   = isset($quote->site_id) ? $quote->site_id : null;
            $site = $this->site_model->fetch_site_info($site_id);
            $data['site'] = $this->site_model->fetch_site_info($site_id);
            $this->load->view('modules/estimates/package', $data);

        }
    }
}
