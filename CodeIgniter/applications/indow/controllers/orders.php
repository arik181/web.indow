<?php if (!defined('BASEPATH')) exit('No direct script access allowed');


    class Orders extends MM_Controller
    {
        protected $_feature = 6;
        protected $email_from = 'test@test.com';
        protected $email_from_name = 'Indow Windows';
        protected $test_email = 'aaronopela@gmail.com';
        protected $frame_step_options = array(
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
        protected $_user;


        public function __construct()
        {
            parent::__construct();
            $this->legacy_date = $this->config->item('legacy_date');
            $this->load->model('group_model', 'customer_model', 'user_model', 'quote_model' );
            $this->load->helper(array('language', 'macrolist', 'notes', 'totallist', 'info', 'contact_info_helper', 'site_info_helper', 'ship_to_helper', 'tabbar', 'ulist', 'functions', 'fees_helper'));
            $this->load->factory("OrderFactory");
            $this->load->factory("ItemFactory");
            $this->_user = $this->data['user'];


            if (@!$this->session->flashdata('message') == false) {
                $this->_message = $this->session->flashdata('message');
            } else {
                $this->_message = false;
            }
        }
    
        protected function is_legacy($order) {
            if (empty($order->freebird)) {
                return true;
            }
            $time = empty($order) ? time() : strtotime($order->created);
            return date('Y-m-d H:i:s', $time) < $this->config->item('legacy_date');
        }

        public function index_get($start = 0, $limit = 25)
        {
            $this->permissionslibrary->require_view_perm($this->_feature);

            $data = array(
                'content'           => 'modules/orders/list',
                'title'             => 'Orders',
                'nav'               => 'orders',
                'subtitle'          => 'Orders',
                'manager'           => 'Orders',
                'section'           => 'Orders',
                'associated_orders' => null,
                'status_options'    => $this->status_code_model->get_codes_list($code_as_index = true, $all_option = true, null, null, null, true)
            );
            if ($this->_user->in_admin_group) {
                $data['add_path'] = '/orders/add';
                $data['add_button'] = 'Add Order';
            }

            if ($this->_message != false) {
                $data['message'] = $this->_message;
            }

            $data["orders"] = $this->order_model->fetch_orders($limit, $start);
            $this->load->view($this->config->item('theme_list'), $data);

        }

        public function edit_get($order_id)
        {
            $this->permissionslibrary->require_view_perm($this->_feature, $order_id);


            if (!isset($order_id)) {
                $this->session->set_flashdata('message', 'Unable to find the order. Please try again.');
                redirect('orders/list');
            }

            $order = $this->OrderFactory->get($order_id);
            if ($order && $order->deleted) {
                $this->session->set_flashdata('message', 'That order has been deleted.');
                redirect('/orders');
            }
            if (empty($order) && !empty($order_id)) {
                redirect('/orders');
            }
            if (!empty($order->dealer_id)) {
                $dealer_id = $order->dealer_id;
            } else if (!$order_id) {
                $dealer_id  = $this->user_model->get_group_id($this->ion_auth->get_user_id());
            } else {
                $dealer_id = null; //should no longer happen since indow freebirds should be associated with indow
            }
            if ($dealer_id) {
                $dealer = $this->Group_model->getProfile($dealer_id);
            }

            $data = array(
                'content'     => 'modules/orders/edit',
                'order'       => $order,
                'mode'        => 'edit',
                'nav'         => 'orders',
                'title'       => 'Orders',
                'subtitle'    => 'Orders',
                'add_path'    => '/orders/add',
                'form'        => '/orders/list',
                'delete_path' => '/orders/delete',
                'manager'     => 'Orders',
                'section'     => 'Edit Order',
                'order_id'    => $order_id,
                'freebird'    => !empty($order->freebird),
                'is_indow'    => $this->_user->in_admin_group,
                '_user'       => $this->_user,
                'wholesale_discount' => $this->Group_model->get_wholesale_discount($dealer_id),
                'legacy'            => $this->is_legacy($order)
            );

            if ($this->_message != false) {
                $data['message'] = $this->_message;
            }

            if (isset($order->dealer_id))
            {
               $data['dealer_id'] = $order->dealer_id;
            }

            $data['mode'] = ($this->_user->in_admin_group || (empty($order) && $this->permissionslibrary->has_edit_permission(6)))?'primary':'view';

            $data['extramessage'] = $this->session->flashdata('extramessage');


            if (isset($order->created)) {
                $order_date = $order->created;
            } else {
                $order_date = date('Y-m-d H:i:s');
            }

            $data['payment_type_options'] = $this->order_model->id_name_array('payment_types');
            $data['status_options']       = $this->status_code_model->get_codes_list();
            $data['dealer'] = isset($dealer) ? $dealer : null;
            if ($order_id) {
				if ($this->_user->in_admin_group) {
					$data['change_owner_users'] = $this->user_model->simple_list();
				}
                $data['measure_tech'] = $this->order_model->get_tech($order_id);
                $data['creator'] = $this->db->where('id', $order->created_by)->get('users')->row();
                $data['status_history'] = $this->order_model->get_status_history($order_id);
                $data['payments']       = $this->payment_model->fetch_order_payments($order_id);
                $data['status_code']    = $this->order_model->get_status($order_id);
                $data['bundle_orders']  = $this->order_model->get_bundle($order_id);
                if (!empty($order->estimate_id)) {
                    $data['estimate_totals'] = $this->item_model->get_estimate_totals($order->estimate_id);
                    //this data should be loaded with the ajax, but that would require a bunch of extra work with modifying the data table helper function
                    //if there's ever some spare time (hahahahaha ha... ha)  it should be moved so the page loads faster.
                }
            } else {
                $data['status_history'] = array();
                $data['payments']       = array();
                $data['status_code']    = 300;
            }

            //disable post manufacturing statuses if status is before manufacturing
            $data['disabled_status'] = array();
            if ($data['status_code'] < 500) {
                foreach ($this->db->where('code > ', 580)->get('status_codes')->result() as $status) {
                    $data['disabled_status'][] = $status->id;
                }
            }

            $data['user_name'] = $this->user_model->get_user_name($this->ion_auth->get_user_id());

            $data['fee_info']    = $this->sales_modifiers_model->get_active_fees(null, true, $dealer_id ? $dealer_id : -1);
            $data['fees_sorted'] = $this->sales_modifiers_model->sortmodifiers($data['fee_info']);


            $options = $this->get_window_options($dealer_id, $order ? date('Y-m-d', strtotime($order->created)) : date('Y-m-d'), !$order_id ? false : $order->js_pricing);
            $data = array_merge($data, $options);

            if ($order_id) {
                $data['active_fees'] = $this->sales_modifiers_model->get_order_fee_ids($order_id);
                $data['user_fees']   = $this->sales_modifiers_model->get_order_fees($order_id, 1);
            } else {
                $data['active_fees'] = array();
            }

            if (!empty($order)) {
                $dealer_id = $order->dealer_id;
                $data['dealer_address'] = $this->Group_model->get_address($order->dealer_id);
            } else {
                $dealer_id = $this->user_model->get_dealer_id($this->ion_auth->get_user_id());
                $data['dealer_address'] = $this->Group_model->get_address($dealer_id);
            }
            $data['dealer_id'] = $dealer_id;

            // Customer Info
            list($cust_ids, $primary) = $this->customer_model->get_order_customers($order_id);
            $customer = $this->customer_model->fetch_customer_info($primary);

            $data['start_customers'] = $this->customer_model->customer_manager_get_customer($cust_ids, $primary);
            $customer_data           = array(
                'title'         => 'Customer Info',
                'empty_message' => 'No Info Available',
                'edit_link'     => "#contact_info_popup",
            );

            $customer             = $this->customer_model->fetch_customer_info($primary);
            $keys                 = array('name', 'company_name', 'phone_1_type', 'phone_1', 'phone_2_type', 'phone_2', 'email_1_type', 'email_1', 'email_2_type', 'email_2');
            $data['contact_info'] = generate_contact_info($primary, $customer_data, $customer, $keys, $data['mode']);

            // Site Info
            $site_id           = isset($order->site_id) ? $order->site_id : null;
            $site              = $this->site_model->fetch_site_info($site_id);

            $data['site_info'] = show_site_info($site_id, $site, array(), $data['mode']);


            // Ship To
            $ship_id   = $primary;
            $ship_data = array(
                'title'         => 'Ship To',
                'empty_message' => 'No Info Available',
                'edit_link'     => "#ship_to_popup",
                'dealer_id'     => $dealer_id,
            );

            if ($order) {
                $ship = $this->order_model->get_shipping_address($order, true);
            } else {
                $ship = $this->group_model->get_primary_address($dealer_id, true);
            }
            $data['ship_to'] = generate_ship_to($ship_id, $ship_data, $ship, $keys, array(), true, $data['mode']);


            // Create order notes
            $order_notes_data    = array(
                'title'         => 'Order Notes',
                'form'          => "orders/edit/$order_id",
                'form_value'    => "Save Order Notes",
                'form_name'     => null,
                'empty_message' => "There are no order notes associated with this order",
                'jsname'        => "order_note",
                'hidesubmit'    => !$order_id,
                'type'          => 'order',
            );
            $order_notes         = $this->order_model->fetch_order_notes_history($order_id);
			$notesedit = $data['mode'] !== 'view' || $data['status_code'] < 330 && $this->permissionslibrary->has_edit_permission(6, $order_id) ? 'default' : 'view';
            $data['order_notes'] = generate_notes($order_id, $order_notes_data, $order_notes, $notesedit);

            if ($data['is_indow']) {
                // Create internal notes
                $internal_notes_data    = array(
                    'title'         => 'Internal Notes',
                    'form'          => "orders/edit/$order_id",
                    'form_value'    => "Save Internal Notes",
                    'form_name'     => null,
                    'empty_message' => "There are no internal notes associated with this order",
                    'jsname'        => 'internal_note',
                    'hidesubmit'    => !$order_id,
                    'type'          => 'internal',
                );
                $internal_notes         = $this->order_model->fetch_internal_notes_history($order_id);
                $data['internal_notes'] = generate_notes($order_id, $internal_notes_data, $internal_notes);
            }

            // TODO create history

            // Create order type tabbar
            $order_type_tab_data  = array(array('name' => 'Order Items',
                                                'id'   => 'order_items_tab'),
                array('name' => 'Back Order',
                      'id'   => 'backorder_items_tab'),
                array('name' => 'Re-Order',
                      'id'   => 'reorder_items_tab'));
            $tabbar_css_id        = "order";
            $active_tab_index     = 0;
            $data['order_tabbar'] = generate_tabbar($tabbar_css_id, $order_type_tab_data, $active_tab_index);

            //$data['items'] = $this->item_model->fetch_items($order_id);

            $this->load->view($this->config->item('theme_home'), $data);
        }

        public function edit_post($order_id)
        {
            $this->permissionslibrary->require_edit_perm($this->_feature, $order_id);
            if ($order_id) {
                $this->permissionslibrary->require_admin_rep();
                $order = $this->OrderFactory->get($order_id);
            }
            $post          = $this->post();
            $data          = json_decode($post['orderdata'], true);
            $post_order_id = $order_id;

            //disallow status advances into manufacturing if no items or measurements
            if ($order_id) {
                $status = $this->order_model->get_status($order_id);
            } else {
                $status = 0;
            }

            $k = 0;

            foreach($data['items'] as $item)
            {
                if ( !isset($item['room']) || empty($item['room']) )
                {
                    $data['items'][$k]['room'] = 'unknown';
                }
                ++$k;
            }

            $new_status = $this->status_code_model->get_code_by_id($data['order_data']['status_code']);

            if ($status <= 500 && $new_status >= 500) {
                if (!count($data['items'])) {
                    $message = 'Data has been saved, however the status could not be advanced into manufacturing status as there are no associated items.';
                } else {
                    foreach ($data['items'] as $item) {
                        if (!isset($item['measurements']['B']) || !$item['measurements']['B'] || !isset($item['measurements']['D']) || !$item['measurements']['D']) {
                            $message = 'Data has been saved, however the status could not be advanced as not all the items contain measurements.';
                        }
                    }
                }

                if (isset($message)) {
                    if ($status == 0) {
                        $data['order_data']['status_code'] = $this->status_code_model->get_code_id(300);
                    } else {
                        unset($data['order_data']['status_code']);
                    }
                }

            }

            $data['order_data']['updated'] = date('Y-m-d H:i:s');

            if (!$this->_user->in_admin_group) {
                unset($data['order_data']['js_pricing']);
            } else {
                if ($order_id) {
                    if (isset($data['order_data']['js_pricing']) && $order->js_pricing != $data['order_data']['js_pricing']) {
                        $this->session->set_flashdata('refresh', true);
                    }
                }
            }
            if (!$order_id) {
                if (count($this->_user->group_ids)) {
                    $data['order_data']['dealer_id'] = $this->_user->group_ids[0];
                }
                $order_id = $this->order_model->create_new($data['order_data']);
            } else {
                $this->order_model->update_data($data['order_data'], $order_id);
                $combined_order = $this->order_model->get_combined_order($order_id);
                if ($combined_order) {
                    $bulk_update = array();
                    $copy_keys = array('shipping_address_id', 'dealer_shipping_address_id', 'carrier', 'ship_method', 'tracking_num');
                    foreach ($copy_keys as $key) {
                        if (!empty($data['order_data'][$key])) {
                            $bulk_update[$key] = $data['order_data'][$key];
                        }
                    }
                    if (count($bulk_update)) {
                        $this->order_model->update_combined_order($combined_order, $bulk_update);
                    }
                }
            }

            $this->item_model->delete_order_items($data['delete_items'], $order_id);

            $this->item_model->save_order_items($data['items'], $order_id, !$post_order_id, null, true);

            $this->sales_modifiers_model->set_order_modifiers($data['fees'], $order_id, $data['user_fees'], $data['delete_user_fees']);
            $quotedata = array();
            $this->customer_model->set_order_customers($data['customers'], $order_id);
            $this->payment_model->add_payments($order_id, $data['addpayments']);
            $this->payment_model->delete_payments($order_id, $data['deletepayments']);
            $this->order_model->update_order_totals($order_id);
            if (isset($data['bundle'])) {
                $this->order_model->set_bundle($order_id, $data['bundle']);
            }

            if (isset($data['order_note'])) {
                $this->order_model->add_order_note($order_id, $data['order_note']);
            }

            if (isset($data['internal_note'])) {
                $this->order_model->add_internal_note($order_id, $data['internal_note']);
            }

			if (!empty($data['change_owner']) && $this->_user->in_admin_group) {
				$this->order_model->change_owner($order_id, $data['change_owner']);
			}

            if (isset($message)) {
                $this->session->set_flashdata('extramessage', $message);
            }

            //$this->order_model->queue_cut_scripts($order_id);

            $this->session->set_flashdata('message', 'Order saved.');
            redirect('/orders/edit/' . $order_id);
        }

        public function save_notes_post($order_id)
        {
            $this->permissionslibrary->require_edit_perm($this->_feature, $order_id);

            $data = $this->post();
            $type = $data['type'];
            $note = $data['note'];

            if ($type === 'order') {
                $this->order_model->add_order_note($order_id, $note);
            } elseif ($type === 'internal') {
                $this->order_model->add_internal_note($order_id, $note);
            }

            $this->response(array(
                'success' => true,
                'date'    => date("M j g:ia")
            ), 200);

        }

        public function add_get()
        {
            $this->permissionslibrary->require_edit_perm($this->_feature);
            return $this->edit_get(0);
        }

        public function add_post()
        {
            $this->edit_post(0);
        }


        public function from_estimate_post($estimate_id)
        {
            $this->permissionslibrary->require_view_perm(2, $estimate_id);
            $this->permissionslibrary->require_edit_perm($this->_feature);
            $order_id = $this->order_model->create_from('estimate', $estimate_id);
            redirect('/orders/edit/' . $order_id);
        }

        public function from_quote_post($quote_id)
        {
            $this->permissionslibrary->require_view_perm(3, $quote_id);
            $this->permissionslibrary->require_edit_perm($this->_feature);
            $review_data = json_decode($this->input->post('review_data'), true);
            $order_id = $this->order_model->create_from('quote', $quote_id);
            $this->order_model->save_review_info($order_id, $review_data);
            redirect('/orders/edit/' . $order_id);
        }

        public function bundle_list_get($dealer_id, $order_id) {
            $this->permissionslibrary->require_view_perm($this->_feature, $order_id);
            $results =  $this->OrderFactory->get_bundle_list($dealer_id, $order_id);

            $dataTables = array("draw"            => 1,
                                "recordsTotal"    => count($results),
                                "recordsFiltered" => count($results),
                                "data"            => $results);

            $this->response($dataTables, 200);
        }

        public function list_json_get($curr_uid)
        {
            $this->permissionslibrary->require_view_perm($this->_feature);
            $results = $this->OrderFactory->getList($this->_user->id);


            $dataTables = array("draw"            => 1,
                                "recordsTotal"    => count($results),
                                "recordsFiltered" => count($results),
                                "data"            => $results);

            $this->response($dataTables, 200);
        }

        public function order_item_list_json_get($order_id, $mfg_status, $freebird=false)
        {
            $mfg_status = explode('_', $mfg_status);
            if (empty($this->_user->is_customer)) {
                $this->permissionslibrary->require_view_perm($this->_feature, $order_id);
            } else {
                $this->freebird_perm($order_id);
            }

            $results    = $this->ItemFactory->getOrderList($order_id, $mfg_status, $freebird);
            $dataTables = array("draw"            => 1,
                                "recordsTotal"    => count($results),
                                "recordsFiltered" => count($results),
                                "data"            => $results);

            $this->response($dataTables, 200);

        }

        protected function get_window_options($dealer_id=null, $date=null, $js_pricing=false)
        {
            return array(
                'product_type_options'   => $this->order_model->id_name_array('product_types', 'product_type'),
                'product_options'        => $this->product_model->get_all(),
                'product_info'           => $this->product_model->get_product_info($js_pricing ? $dealer_id : null, $date),
                'frame_step_options'     => $this->frame_step_options,
                'window_shapes'          => $this->order_model->id_name_array('window_shapes', 'name', 'id'),
                'edging_options'         => $this->order_model->id_name_array('edging', 'name', 'id', true),
                'frame_depth_options'    => $this->order_model->id_name_array('frame_depth', 'name', 'id', false)
            );

        }

        public function measure_get($order_id)
        {
            $this->data['customer_header'] = true;
            $customer = $this->freebird_perm($order_id);
            $status = $this->order_model->get_status($order_id);

            if ($status != 200) {

                $data = array(
                    'title'   => 'Measure Order',
                    'content' => 'themes/fullwidth/message_screen',
                    'message' => 'Error: This order is not in the correct status to submit measurements.'
                );

            } else {
                $ship_id   = $customer->id;
                $ship_data = array(
                    'title'         => 'Ship To',
                    'empty_message' => 'No Info Available',
                    'edit_link'     => "#ship_to_popup",
                    'dealer_id'     => null,
                );


                $ship = $this->order_model->get_shipping_address($order_id, true);
                $keys                 = array('name', 'company_name', 'phone_1_type', 'phone_1', 'phone_2_type', 'phone_2', 'email_1_type', 'email_1', 'email_2_type', 'email_2');
                $ship_to = generate_ship_to($ship_id, $ship_data, $ship, $keys, array(), true, $data['mode']);
                list($cust_ids, $primary) = $this->customer_model->get_order_customers($order_id);

                $order = $this->OrderFactory->get($order_id);
                $data = array(
                    'ship_to'           => $ship_to,
                    'title'             => 'Measure Order',
                    'content'           => 'themes/fullwidth/measure',
                    'order_id'          => $order_id,
                    'customer'          => $customer,
                    'customer_ids'      => $cust_ids,
                    'shipping_address'  => $this->order_model->get_shipping_address($order_id),
                    'order'             => $order,
                    'legacy'            => $this->is_legacy($order)
                );

                $options = $this->get_window_options($data['order']->dealer_id, $data['order'] ? date('Y-m-d', strtotime($data['order']->created)) : date('Y-m-d'), $data['order']->js_pricing);
                $data = array_merge($data, $options);
                unset($data['product_options'][3]); //remove accessories;

                $data['window_shapes'] = array(1 => 'Rectangle', 3 => 'Custom');

            }

            $this->load->view('themes/fullwidth/main', $data);

        }

        public function measure_post($order_id)
        {
            $this->data['customer_header'] = true;
            $status = $this->order_model->get_status($order_id);

            if ($status != 200) {

                $data = array(
                    'title'   => 'Measure Order',
                    'content' => 'themes/fullwidth/message_screen',
                    'message' => 'Error: This order is not in the correct status to submit measurements.',
                    'customer_header' => true
                );
                $this->load->view('themes/fullwidth/main', $data);
                return;
            }
            $post = $this->post();
            $data = json_decode($post['measuredata'], true);
            foreach ($data['items'] as &$item) {
                unset($item['price_override']);
            }
            if ($data['cancel']) {
            } else {
                //$this->item_model->delete_order_items($data['delete_items'], $order_id);
                //$this->item_model->save_order_items($data['items'], $order_id);
                //$this->order_model->update_data(array('updated' => date('Y-m-d H:i:s'), 'shipping_address_id' => $data['shipping_address']), $order_id);
            }

            $this->order_model->update_status($order_id, 300, null, true);
            $site_id = $this->site_model->copy_from_order_hold_items($order_id, $data['items']);

        /*    $success_message = "Thank you for submittting you measurements.
                    An Indow representative will review your order and contact you within 24 business hours.
                    <br>Please DO NOT ship your measure kit back until Indow has confirmed your order."; */
            $success_message = '<iframe src="http://www.indowwindows.com/modi-customer-measurements-submitted-iframe/" width="100%" height="1000" frameBorder="0"></iframe>';
            $data = array(
                'title'    => 'Measure Order',
                'content'  => 'themes/fullwidth/message_screen',
                'order_id' => $order_id,
                'message'  => $data['cancel'] ? 'Your order has been cancelled' : $success_message,
            );


            $this->load->view('themes/fullwidth/main', $data);

        }

        public function confirmation_get($order_id, $mode='default')
        {
            if ($this->_user->is_customer) {
                $this->freebird_perm($order_id);
            } else {
                $this->permissionslibrary->require_view_perm($this->_feature, $order_id);
            }

            $order = $this->OrderFactory->get($order_id);
            if ($order->freebird) {
                $this->data['customer_header'] = true;
            }
            if (!$order) {
                redirect('/');
            }
            $creator = $this->db->where('users.id', $order->created_by)->join('users_groups','users.id=users_groups.user_id', 'left')->get('users')->row();

            $curstatus = $this->db->where('id', $order->status_code)->get('status_codes')->row()->code;

            $data = array(
                'title'   => 'Order Confirmation',
                'content' => 'themes/fullwidth/confirmation',
                'order'   => $order
            );
            $data['creator'] = $creator ? $creator->first_name . ' ' . $creator->last_name : null;


            if ($order->dealer_id) {
                $data['dealer_address'] = $this->Group_model->get_address($order->dealer_id);
            } else {
                $data['dealer_address'] = null;
            }


            $data['job_site']         = $this->site_model->getProfile($order->site_id);
            $data['shipping_address'] = $this->order_model->get_shipping_address($order);
            $data['customer']         = $this->order_model->get_primary_customer($order_id, true);
            $s300date                 = $this->order_model->get_status_date($order_id, 300);
            $data['submitted_date']   = $this->order_model->get_status_date($order_id, 300);
            $data['confirmed_date']   = $this->order_model->get_status_date($order_id, 330);
            $data['order_date']       = $s300date ? date('F jS, Y', strtotime($s300date)) : '';
            $data['curstatus']        = $curstatus;
            $data['mode'] = $mode;

            $data['items']  = $this->ItemFactory->getOrderConfList($order_id);
            $data['totals'] = $this->order_model->get_totals($order_id, $data['items']);

            $this->load->view('themes/fullwidth/main', $data);
        }

        public function confirmation_post($order_id)
        {
            $order = $this->OrderFactory->get($order_id);
            if ($order->freebird) {
                $this->data['customer_header'] = true;
            }
            $post = $this->post();
            $this->order_model->update_data($post, $order_id);
            $this->order_model->update_status($order_id, 330, null, true);

            $data = array(
                'title'   => 'Order Confirmation',
                'content' => 'themes/fullwidth/message_screen',
                'message' => '<iframe src="http://www.indowwindows.com/modi-customer-order-submitted-iframe/" width="100%" height="1000" frameBorder="0"></iframe>',
            );

            $this->load->view('themes/fullwidth/main', $data);

        }

        public function print_get($order_id) {
            $this->permissionslibrary->require_view_perm($this->_feature, $order_id);
            $this->confirmation_get($order_id, 'print');
        }

        public function create_preorder_get($site_id, $estimate_id=null)
        {
            $this->permissionslibrary->require_edit_perm(14);
            list($cust_ids, $primary) = $this->customer_model->get_site_customers($site_id);
            if (!$primary) {
                $data = array(
                    'success' => false,
                    'message' => 'This site has no primary customer.'
                );
            } else {
                $order_values = array(
                    'site_id' => $site_id,
                    'status_code' => $this->status_code_model->get_code_id(100),
                    'dealer_id' => count($this->_user->group_ids) ? $this->_user->group_ids[0] : null,
                    'freebird' => 1
                );
                $address = $this->customer_model->get_shipping_address($primary);
                if ($address) {
                    $order_values['shipping_address_id'] = $address->id;
                }
                if ($estimate_id) {
                    $order_values['estimate_id'] = $estimate_id;
                }
                $order_id  = $this->order_model->create_new($order_values, true);
                $customers = array();
                foreach ($cust_ids as $cust_id) {
                    if ($cust_id === $primary) {
                        $customers[] = array('primary' => 1, 'id' => $cust_id);
                    } else {
                        $customers[] = array('primary' => 0, 'id' => $cust_id);
                    }
                    $this->customer_model->set_order_customers($customers, $order_id);
                }
                if ($estimate_id) {
                    $this->item_model->copy_items('estimate', $estimate_id, 'order', $order_id);
                }

                $data = array(
                    'success'  => true,
                    'message'  => 'Pre-order created successfully.',
                    'order_id' => $order_id,
                );

            }

            $this->session->set_flashdata('message', $data['message']);
            if ($data['success'] && $this->permissionslibrary->has_edit_permission($this->_feature)) {
                redirect('/orders/edit/' . $order_id);
            } else {
                redirect('/sites/edit/' . $site_id);
            }

            $this->response($data, 200);

        }

        public function send_measurement_form_post($order_id)
        {
            $this->permissionslibrary->require_edit_perm(14, $order_id);
            list($cust_ids, $primary) = $this->customer_model->get_order_customers($order_id);

            if (!$primary) {
                $data = array(
                    'success' => false,
                    'message' => 'This order has no primary customer.'
                );
            } else {
                $customer = $this->customer_model->fetch_customer_info($primary);

                if (empty($customer['email_1'])) {
                    $data = array(
                        'success' => false,
                        'message' => 'The primary customer has no primary email address on file.'
                    );
                } else {
                    $this->email_model->send_freebird_measure_email($primary, $order_id);
                    $status_id = $this->order_model->update_status($order_id, 200, null, true);
                    $data      = array(
                        'success'   => true,
                        'message'   => 'Measurement form sent.',
                        'status_id' => $status_id
                    );
                }
            }

            $this->response($data, 200);

        }

        public function send_order_confirmation_post($order_id)
        {
            exit;
            //$order = $this->OrderFactory->get($order_id);

            $this->email_model->send_order_confirmation_email($order_id);
            $this->db->where('id', $order_id)->update('orders', array('order_confirmation_sent' => 1));
            //$status_id = $this->order_model->update_status($order_id, 200, null, true);
            $data = array(
                'success' => true,
                'message' => 'Order confirmation sent.',
            );

            $this->response($data, 200);
        }
        /*
        public function backorder_item_list_json_get()
        {
            $results = $this->ItemFactory->getOrderList(2);

            $dataTables = array("draw"            => 1,
                                "recordsTotal"    => count($results),
                                "recordsFiltered" => count($results),
                                "data"            => $results);

            $this->response($dataTables, 200);
        }

        public function reorder_item_list_json_get()
        {

            $results = $this->ItemFactory->getOrderList(3);

            $dataTables = array("draw"            => 1,
                                "recordsTotal"    => count($results),
                                "recordsFiltered" => count($results),
                                "data"            => $results);

            $this->response($dataTables, 200);

        }*/

        public function delete_get($order_id)
        {
            $this->order_model->delete_order($order_id);
            $this->session->set_flashdata('message', 'Order deleted.');
            redirect('/orders');
        }

        public function package_get($order_id) 
        {
            $this->permissionslibrary->require_edit_perm($this->_feature, $order_id);
            $post          = $this->post();
            $data          = array();

            if (isset($order_id)) 
            {
                $data = array();
                $data['user_name'] = $this->user_model->get_user_name($this->ion_auth->get_user_id());
                $this->permissionslibrary->require_view_perm($this->_feature, $order_id);
                // Order Info
                $status = $this->order_model->get_status($order_id);
                $order = $this->OrderFactory->get($order_id);
                $primary = $this->order_model->get_primary_customer_id($order_id);
                $data['order_data']['status_code'] = $this->status_code_model->get_code_id($status);
                $data['order_data']['updated'] = date('Y-m-d H:i:s');


                //disable post manufacturing statuses if status is before manufacturing
                $data['disabled_status'] = array();
                if ($data['order_data']['status_code'] < 500) {
                    foreach ($this->db->where('code > ', 580)->get('status_codes')->result() as $status) {
                        $data['disabled_status'][] = $status->id;
                    }
                }

                if (!empty($order->dealer_id)) {
                    $dealer_id = $order->dealer_id;
                } else if (!$order_id) {
                    $dealer_id  = $this->user_model->get_group_id($this->ion_auth->get_user_id());
                } else {
                    $dealer_id = null; //freebird
                }
                if ($dealer_id) {
                    $dealer = $this->Group_model->getProfile($dealer_id);
                    $dealer->logo = $this->Group_model->get_logo_url($dealer_id);
                }
                $data['dealer'] = $dealer;

                $data['content']    = 'modules/orders/edit';
                $data['order']      = $order;
                $data['mode']       = 'edit';
                $data['nav']        = 'orders';
                $data['title']      = 'Orders';
                $data['subtitle']   = 'Orders';
                $data['add_path']   = '/orders/add';
                $data['form']       = '/orders/list';
                $data['delete_path']= '/orders/delete';
                $data['manager']    = 'Orders';
                $data['section']    = 'Edit Order';
                $data['order_id']   = $order_id;
                $data['freebird']   = $order_id && !$order->dealer_id;
                $data['is_indow']   = $this->_user->in_admin_group;
                $data['_user']      = $this->_user;
                $data['wholesale_discount']= $this->Group_model->get_wholesale_discount($dealer_id);

                $data['payment_type_options'] = $this->order_model->id_name_array('payment_types');
                $data['status_options']       = $this->status_code_model->get_codes_list();
                $data['dealer'] = isset($dealer) ? $dealer : null;
                if ($order_id) {
                    $data['creator'] = $this->db->where('id', $order->created_by)->get('users')->row();
                    $data['status_history'] = $this->order_model->get_status_history($order_id);
                    $data['payments']       = $this->payment_model->fetch_order_payments($order_id);
                    $data['status_code']    = $this->order_model->get_status($order_id);
                    $data['bundle_orders']  = $this->order_model->get_bundle($order_id);
                    if (!empty($order->estimate_id)) {
                        $data['estimate_totals'] = $this->item_model->get_estimate_totals($order->estimate_id);
                        //this data should be loaded with the ajax, but that would require a bunch of extra work with modifying the data table helper function
                        //if there's ever some spare time (hahahahaha ha... ha)  it should be moved so the page loads faster.
                    }
                } else {
                    $data['status_history'] = array();
                    $data['payments']       = array();
                    $data['status_code']    = 300;
                }

                // Customer Info
                $customer_rep_id = $this->customer_model->getProfile($primary)->customer_preferred_contact;
                $customer = $this->customer_model->fetch_customer_info($primary);
                $data['customer'] = $customer;

                if (isset($order->dealer_id))
                {
                   $data['dealer_id'] = $order->dealer_id;
                }
                $data['customer_profile']   = $this->customer_model->getProfile($primary);
                $data['customer_user']      = $this->user_model->getProfile($data['customer_profile']->user_id);

                // Customer Info
                $data['groups'] = $this->Group_model->simple_list();
                $data['users']  = $this->user_model->simple_list();

                // Site Info
                $site_id   = isset($order->site_id) ? $order->site_id : null;
                $site = $this->site_model->fetch_site_info($site_id);
                $site_options = $this->site_model->get_site_options($this->ion_auth->get_user_id());
                $data['site'] = $site;

                // Items
                $data['items'] = $this->item_model->fetch_items($order_id);
                $data['product_info'] = $this->product_model->get_product_info($dealer_id);
                $data['product_options'] = $this->order_model->id_name_array('products', 'product');
                $data['edging_options']       = $this->order_model->id_name_array('edging');
                $data['fee_info']             = $this->sales_modifiers_model->get_active_fees(null, true, $dealer_id ? $dealer_id : -1);
                $data['fees_sorted']          = $this->sales_modifiers_model->sortmodifiers($data['fee_info']);
                $data['active_fees']          = $this->sales_modifiers_model->get_order_fee_ids($order_id);
                $data['user_fees']            = $this->sales_modifiers_model->get_order_fees($order_id, 1);

                $this->load->view('modules/orders/package', $data);

            } else {

                $this->session->set_flashdata('message', 'Unable to find the order. Please try again.');
                redirect('/orders/list');
            }
        }
		public function csv_export_get($order_id=null) {
			if (!$order_id) {
				redirect();
			}
			$this->order_model->generate_csv(array($order_id));
			
		}

        public function freebird_perm($order_id) {
            $customer = $this->order_model->get_primary_customer($order_id, true);
            $allowed = ($this->_user->is_customer && $customer && $this->_user->id == $customer->id) || $this->_user->in_admin_group;
            if (!$allowed) {
                redirect();
            }
            return $customer;
        }

        public function update_item_ajax_post($order_id) {
            $this->freebird_perm($order_id);
            $order = $this->OrderFactory->get($order_id);

            $item = $this->input->post();
            unset($item['subproducts']);
            unset($item['product_id']);
            unset($item['price_override']);

            if (!empty($item['measurements'])) {
                $measurements = $item['measurements'];
                unset($item['measurements']);
            } else {
                $measurements = null;
            }
            if ($item['id'] === 'new') {
                $this->db->insert('items', $item);
                $item_id = $this->db->insert_id();
                $this->db->insert('orders_has_item', array('item_id' => $item_id, 'order_id' => $order_id));
            } else {
                $item_id = $item['id'];
                $this->db->where('id', $item_id)->update('items', $item);
            }
            if ($measurements) {
                if (!empty($item['freebird_laser'])) {
                    foreach ($measurements as &$measurement) {
                        $measurement += 1;
                    }
                }
                if (empty($item['own_tools']) && !$this->is_legacy($order)) {
                    if (isset($measurements['E'])) {
                        $measurements['E'] += 2;
                    }
                    if (isset($measurements['F'])) {
                        $measurements['F'] += 2;
                    }
                }
                $this->item_model->queue_item_if_changed($item_id, $order_id, $item, $measurements);
                $this->item_model->setmeasurements($measurements, $item_id);
            }
            echo $item_id; exit;
        }

        public function delete_order_items_ajax_post($order_id) {
           // $this->freebird_perm($order_id);
            $data = $this->input->post();
            if (!empty($data['items'])) {
                $this->db->where('order_id', $order_id)->where_in('item_id', $data['items'])->update('orders_has_item', array('deleted' => 1));
            }
            if (!empty($data['subitems'])) {
                //Joins in this statement are to keep people from being able to delete subitems for other orders they don't have permissions to
                $this->db
                        ->where_in('subitems.id', $data['subitems'])
                        ->where('orders_has_item.order_id', $order_id)
                        ->update('subitems JOIN items ON subitems.item_id=items.id JOIN orders_has_item ON orders_has_item.item_id=items.id', array('subitems.deleted' => 1));
            }
        }
    
        public function update_shipping_addres_post($order_id, $shipping_address_id) {
            $this->freebird_perm($order_id);
            $this->db->where('id', $order_id)->update('orders', array('shipping_address_id' => $shipping_address_id));
        }

        public function add_items_post($order_id) {
            $this->permissionslibrary->require_admin_rep();
            $items = $this->input->post('items');
            if ($items) {
                $this->order_model->add_items($order_id, $items);
                $this->order_model->update_order_totals($order_id);
            }
        }

        function test_get() {
          //  $item = $this->item_model->get_item_and_measurements(280);
          //  $this->item_model->update_cut_calculations($item);
        }
    }
