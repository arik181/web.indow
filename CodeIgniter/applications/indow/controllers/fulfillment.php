<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Fulfillment extends MM_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->_user = $this->data['user'];
        $this->_feature = 9;
        if(!$this->_user->in_admin_group)
        {
            redirect();
        }
        $this->load->model(array('user_model', 'estimate_model', 'order_model'));
        $this->load->helper(array('language', 'ulist','html','tabbar'));
        $this->load->factory(array('FulfillmentFactory','OrderFactory','EstimateFactory','GroupFactory'));
        // $this->output->enable_profiler(TRUE);
    }

    public function index_get()
    {
        $this->permissionslibrary->require_view_perm($this->_feature);
        
        $data = array(
            'content'           => 'modules/fulfillment/fulfillment',
            'title'             => 'Fulfillment',
            'nav'               => 'fulfillment',
            'subtitle'          => 'Dashboard',
            'manager'           => 'Fulfillment',
            'section'           => 'Fulfillment',
            'status_codes'      => $this->order_model->id_name_array('status_codes', 'description', 'code')
        );
        
         $this->load->view($this->config->item('theme_stub'), $data);
        
        //$this->production_get();
    }

    public function packaging_json_get($flag=0)
    {
        $this->permissionslibrary->require_view_perm($this->_feature);
        $results = $this->FulfillmentFactory->packaging_orders();
        $dataTables = array("draw"=>1,
                            "recordsTotal"=>count($results),
                            "recordsFiltered"=>count($results),
                            "data"=>$results);
        
        $this->response($dataTables,200);
    }
    
    public function schedule_json_get($flag=0)
    {
        $this->permissionslibrary->require_view_perm($this->_feature);
        $results = $this->FulfillmentFactory->getScheduleList($flag);
        $dataTables = array("draw"=>1,
                            "recordsTotal"=>count($results),
                            "recordsFiltered"=>count($results),
                            "data"=>$results);

        $this->response($dataTables,200);
   
    }

    public function sleeve_json_get($order_id=0)
    {
        $this->permissionslibrary->require_view_perm($this->_feature);
        $results = $this->FulfillmentFactory->sleeve_cut_list($order_id);
        $dataTables = array("draw"=>1,
                            "recordsTotal"=>count($results),
                            "recordsFiltered"=>count($results),
                            "data"=>$results);
        $this->response($dataTables,200);
    }
    
    public function schedule_get()
    {
        $this->permissionslibrary->require_view_perm($this->_feature);
        
        $data = array(
            'content'           => 'modules/fulfillment/schedule',
            'title'             => 'Fulfillment',
            'nav'               => 'fulfillment',
            'subtitle'          => 'Schedule Measurement (200 Status)',
            'manager'           => 'Fulfillment',
            'section'           => 'Fulfillment',
        );
         $this->load->view($this->config->item('theme_stub'), $data);
    }

    public function order_by_status_json_get($status, $flag=0) {
        $this->permissionslibrary->require_view_perm($this->_feature);
        $results = $this->FulfillmentFactory->get_orders_in_status($status, $flag, true);
        $dataTables = array("draw"=>1,
                            "recordsTotal"=>count($results),
                            "recordsFiltered"=>count($results),
                            "data"=>$results);

        $this->response($dataTables,200);
    }
    
    public function order_json_get($flag=0)
    {
         $results = $this->FulfillmentFactory->getNewOrderList($flag);
        $dataTables = array("draw"=>1,
                            "recordsTotal"=>count($results),
                            "recordsFiltered"=>count($results),
                            "data"=>$results);

        $this->response($dataTables,200);
   
    }
    
    public function order_get()
    {
        $this->permissionslibrary->require_view_perm($this->_feature);
        
        $data = array(
            'content'           => 'modules/fulfillment/orders',
            'title'             => 'Fulfillment',
            'nav'               => 'fulfillment',
            'subtitle'          => 'New Orders (300 Status)',
            'manager'           => 'Fulfillment',
            'section'           => 'Fulfillment',
        );
         $this->load->view($this->config->item('theme_stub'), $data);
    }
    
    public function final_review_json_get($flag)
    {
        $this->permissionslibrary->require_view_perm($this->_feature);
        $results = $this->FulfillmentFactory->getFinalReviewList($flag);
        $dataTables = array("draw"=>1,
                            "recordsTotal"=>count($results),
                            "recordsFiltered"=>count($results),
                            "data"=>$results);

        $this->response($dataTables,200);
    }
    
    public function review_get()
    {
        $this->permissionslibrary->require_view_perm($this->_feature);
        
        $data = array(
            'content'           => 'modules/fulfillment/reviews',
            'title'             => 'Fulfillment',
            'nav'               => 'fulfillment',
            'subtitle'          => 'Final Review (330 Status)',
            'manager'           => 'Fulfillment',
            'section'           => 'Fulfillment',
        );

         $this->load->view($this->config->item('theme_stub'), $data);
    }

    public function orders_by_status_get($status_code, $mode=null)
    {
        $this->permissionslibrary->require_view_perm($this->_feature);
        $status = $this->db->where('code', $status_code)->get('status_codes')->row();
        if (!$status) {
            redirect('/fulfillment');
        }
        
        $data = array(
            'content'           => 'modules/fulfillment/orders_by_status',
            'title'             => 'Fulfillment',
            'nav'               => 'fulfillment',
            'status_code'       => $status_code,
            'subtitle'          => $status->description . ' (' . $status_code . ' Status)',
            'manager'           => 'Fulfillment',
            'section'           => 'Fulfillment',
			'mode'				=> $mode
        );

         $this->load->view($this->config->item('theme_stub'), $data);
    }
    
    public function approve_json_get($flag)
    {
        $this->permissionslibrary->require_view_perm($this->_feature);
        $results = $this->FulfillmentFactory->getApprovedList();
        $dataTables = array("draw"=>1,
                            "recordsTotal"=>count($results),
                            "recordsFiltered"=>count($results),
                            "data"=>$results);

        $this->response($dataTables,200); 
    }
    
    public function approve_get()
    {
        $this->permissionslibrary->require_view_perm($this->_feature);
         if(!$this->data['auth'])
        {
            redirect();
        }
        
        $data = array(
            'content'           => 'modules/fulfillment/approves',
            'title'             => 'Fulfillment',
            'nav'               => 'fulfillment',
            'subtitle'          => 'Approved Orders (350 Status)',
            'manager'           => 'Fulfillment',
            'section'           => 'Fulfillment',
        );
         $this->load->view($this->config->item('theme_stub'), $data);
    }

    public function production_get()
    {
        $this->permissionslibrary->require_view_perm($this->_feature);

        $data = array(
            'content'           => 'modules/fulfillment/production',
            'title'             => 'Fulfillment',
            'nav'               => 'production',
            'subtitle'          => 'Production',
            'manager'           => 'Production',
            'section'           => 'Production',
            'status_options'    => $this->status_code_model->get_codes_list($code_as_index = true, $all_option = false, null, null, array(300, 320, 330, 350, 380, 400, 480, 500, 580, 600, 650, 680, 700)),
        );
        
       $add_production_tab_data      = array( array('name' => 'Scheduling',
                                                   'id'   => 'scheduling_tab'),
                                             array('name' => 'Manufacturing',
                                                   'id'   => 'manufacturing_tab')
                                               );
        $tabbar_css_id              = "production";
        $active_tab_index           = 0;
        $data['manufacturing_data'] = "";
        $data['production_tabbar']    = generate_tabbar( $tabbar_css_id, $add_production_tab_data, $active_tab_index );

        $this->load->view($this->config->item('theme_stub'), $data);
    }

    public function production_planning_json_get()
    {
        $this->permissionslibrary->require_view_perm($this->_feature);
         $results = $this->FulfillmentFactory->getPlanningData();
        $dataTables = array("draw"=>1,
                            "recordsTotal"=>count($results),
                            "recordsFiltered"=>count($results),
                            "data"=>$results);
        
        $this->response($dataTables,200); 
        
    }

    public function logistics_get()
    {
        $this->permissionslibrary->require_view_perm($this->_feature);

        $data = array(
            'content'           => 'modules/fulfillment/logistics',
            'title'             => 'Logistics',
            'nav'               => 'logistics',
            'subtitle'          => 'Logistics',
            'manager'           => 'Logistics',
            'section'           => 'Logistics',
            'status_options'    => $this->status_code_model->get_codes_list($code_as_index = true, $all_option = false, null, null, array(600, 650, 680, 700)),
            'shipping_status_options'    => $this->status_code_model->get_codes_list($code_as_index = true, $all_option = false, 350, 700), //array(500, 580, 600, 650, 680, 700)
            'filter_status_options'    => $this->status_code_model->get_codes_list($code_as_index = true, $all_option = true, 350, 680),
            'combined_orders'   => $this->FulfillmentFactory->combined_orders(500, 680),
            'combined_orders_packaging'   => $this->FulfillmentFactory->combined_orders(600, 600),
        );

        $add_logistics_tab_data      = array( array('name' => 'Shipping',
                                                   'id'   => 'shipping_tab'),
                                             array('name' => 'Packaging',
                                                   'id'   => 'packaging_tab')
                                               );
        $tabbar_css_id              = "logistics";
        $active_tab_index           = 0;
        $data['logistics_data'] = "";
        $data['logistics_tabbar']    = generate_tabbar( $tabbar_css_id, $add_logistics_tab_data, $active_tab_index );
        $this->load->view($this->config->item('theme_stub'), $data);
    }
    public function filtered_orders_json_get($group_id, $start_date, $end_date)
    {
        $results = $this->OrderFactory->getPipelineList($group_id, $start_date, $end_date);
        $dataTables = array("draw"=>1,
                            "recordsTotal"=>count($results),
                            "recordsFiltered"=>count($results),
                            "data"=>$results);
        
        $this->response($dataTables,200);
    }
    public function billing_get()
    {
        $this->permissionslibrary->require_view_perm($this->_feature);

        $data = array(
            'content'           => 'modules/fulfillment/billing',
            'title'             => 'Billing',
            'nav'               => '',
            'subtitle'          => 'Billing',
            'manager'           => 'Billing',
            'section'           => 'Billing',
        );

        $this->load->view($this->config->item('theme_stub'), $data);
    }

    public function shipping_get()
    {
        $this->permissionslibrary->require_view_perm($this->_feature);

        $data = array(
            'content'           => 'modules/fulfillment/shipping',
            'title'             => 'Shipping',
            'nav'               => '',
            'subtitle'          => 'Shipping',
            'manager'           => 'Shipping',
            'section'           => 'Shipping',
        );

        $this->load->view($this->config->item('theme_stub'), $data);
    }

    public function planning_get()
    {
        $this->permissionslibrary->require_view_perm($this->_feature);
        $data = array(
            'content'           => 'modules/fulfillment/planning',
            'title'             => 'Fulfillment | Demand Planning',
            'nav'               => 'planning',
            'subtitle'          => '',
            'manager'           => 'planning',
            'section'           => 'planning',
        );
        $data['queued'] = $this->OrderFactory->queuedPanelCount();
        $data['estimate_funnel'] = $this->EstimateFactory->getFunnelData();
        $data['opening_funnel'] = $this->OrderFactory->getOpeningFunnelData();
        $data['product_funnel'] = $this->OrderFactory->getProductFunnelData();
        $data['groups'] = $this->GroupFactory->getList();
        if ($this->post())
        {
            $data['form'] = $this->post();
            $data['form']['start_date'] = date("Y-m-d", strtotime($data['form']['start_date']));
            $data['form']['end_date'] = date("Y-m-d", strtotime($data['form']['end_date']));
        }
        else
        {
            $data['form']['group_id'] = 0;
            $data['form']['start_date'] = date("Y-m-d", strtotime("this month"));
            $data['form']['end_date'] = date("Y-m-d", strtotime("next month"));
        }
        $data['pipeline_counts'] = $this->OrderFactory->getPipelineCounts($data['form']['group_id'], $data['form']['start_date'], $data['form']['end_date']);
        $this->load->view($this->config->item('theme_stub'), $data);
    }
    public function planning_post()
    {
        $this->planning_get();
    }
    
    public function planning_download_post()
    {
        $this->permissionslibrary->require_view_perm($this->_feature);
        $val=time();
        $result=array('bool'=>false,'filepath'=>null);
        //$_POST['values'][]='302';
        if($_POST['values']){
            $orders_ids=$_POST['values'];
            
            $fp = fopen('assets/downloads/Demo'.$val.'.csv', 'w');
            $temp=array('Order #','Customer Name','Dealer','Product Serial #','Item/Unit Id','Product/Product Type','Room Name ','Window Location','Full Measured Data Set','Dealer PO#','Acrylic panel size','sq ft of panel','linear ft of panel','panel thickness','Unit retail value','Top spine','Side spines','Frame depth','Jobsite Address','Customer phone','Customer email','Dealer name','Dealer shipping address','Dealer billing address','Primary user’s name','Primary user’s contact information');
            fputcsv($fp, $temp);
            if(!empty( $orders_ids)){
            foreach($orders_ids as $id){
               // $itemresult = $this->FulfillmentFactory->order_related_items($id);
               
                $elseresult = $this->FulfillmentFactory->getdownloaddata($id);
               
                /*
                foreach($itemresult as $items){
                    fputcsv($fp, $items);
                }*/
                foreach($elseresult as $elseitem){
                    fputcsv($fp, $elseitem);
                }
               
            }
            }
             fclose($fp);
             $result['bool']=true;      
        }
        $result['filepath']='/assets/downloads/Demo'.$val.'.csv';
        echo json_encode($result);
       
    }
    
    public function manufacturing_json_get()
    {
        $this->permissionslibrary->require_view_perm($this->_feature);
        $results = $this->FulfillmentFactory->getManufactureOrders();
        $dataTables = array("draw"=>1,
                            "recordsTotal"=>count($results),
                            "recordsFiltered"=>count($results),
                            "data"=>$results);
        
        $this->response($dataTables,200);
    }

    public function shipments_json_get()
    {
        $this->permissionslibrary->require_view_perm($this->_feature);
        $results = $this->FulfillmentFactory->shipments_orders();
        $dataTables = array("draw"=>1,
                            "recordsTotal"=>count($results),
                            "recordsFiltered"=>count($results),
                            "data"=>$results);
        
        $this->response($dataTables,200);
    }
   
    public function order_items_get($order_id)
    {
        $this->permissionslibrary->require_view_perm($this->_feature);
        $data = $this->FulfillmentFactory->order_related_items($order_id);
        $html = $this->load->view('modules/fulfillment/order_items',array('data' => $data,'orderid' => $order_id),true);
        echo $html;  
    }
    
    public function combine_orders_get()
    {
        $this->permissionslibrary->require_view_perm($this->_feature);
        $data = array(
            'content'           => 'modules/fulfillment/combined_orders',
            'title'             => 'Logistics',
            'nav'               => 'logistics',
            'subtitle'          => 'Shipping Combine View',
            'manager'           => 'planning',
            'section'           => 'planning',
            'combine_order_data' => '',
            'mode'               => 'get'
        );
        
        $this->load->view($this->config->item('theme_stub'),$data);

   
    }

    public function combine_orders_post()
    {
        $this->permissionslibrary->require_edit_perm($this->_feature);
        
        $data = array(
            'content'           => 'modules/fulfillment/combined_orders',
            'title'             => 'Logistics',
            'nav'               => 'logistics',
            'subtitle'          => 'Shipping Combine View',
            'manager'           => 'planning',
            'section'           => 'planning',
            'combine_order_data' => '',
            'mode'               => 'post',
            'query_string'      => $this->post('searchText')
        );
        
        $this->load->view($this->config->item('theme_stub'),$data);
    }

    public function combine_view_get($batch_id = "")
    {
        $this->permissionslibrary->require_view_perm($this->_feature);
        
        if(!empty($batch_id)){
        
                $data = array(
                    'content'           => 'modules/fulfillment/combine_view',
                    'title'             => 'Logistics',
                    'nav'               => 'logistics',
                    'subtitle'          => 'Shipping Combine View',
                    'manager'           => 'planning',
                    'section'           => 'planning',
                    'combine_order_data' => '',
                    'mode'               => 'get',
                    'batch_id'           => $batch_id
                    
                );

                $this->load->view($this->config->item('theme_stub'),$data);
         }
         
     }

    public function combine_view_post($combine_id)
    {
        $this->permissionslibrary->require_view_perm($this->_feature);
         $data = $this->post();
         $combine_order_ids = array();
         
         foreach($data as $key => $val)
         {
             $combine_order_ids[] = $key;
         }

         if($this->FulfillmentFactory->delete_combine_order($combine_order_ids))
         {
            redirect('/combine_view/' . $combine_id);
        }       
    }

    public function batch_order_json_get($batch_id)
    {
        $this->permissionslibrary->require_view_perm($this->_feature);

        $results = $this->FulfillmentFactory->search_batch_orders($batch_id);
        $dataTables = array("draw"=>1,
                            "recordsTotal"=>count($results),
                            "recordsFiltered"=>count($results),
                            "data"=>$results);
        
        $this->response($dataTables,200);
         
    }

    public function search_order_json_get(){
        $searchText = $this->input->get('q');
        $this->permissionslibrary->require_view_perm($this->_feature);

        $results = $this->FulfillmentFactory->search_orders($searchText);
        $dataTables = array("draw"=>1,
                            "recordsTotal"=>count($results),
                            "recordsFiltered"=>count($results),
                            "data"=>$results);
        
        $this->response($dataTables,200);
    }

    public function dealer_get($search_text)
    {
        $this->permissionslibrary->require_view_perm($this->_feature);
        if(!empty($search_text)){
            $search_text = urldecode($search_text);
            $ret_html = $this->FulfillmentFactory->getAllDealers($search_text);
            echo $ret_html;
        }else{
            echo "";
        }
        exit;
    }
    
    public function add_combine_order_post()
    {
        $this->permissionslibrary->require_edit_perm($this->_feature);
        $group_id = $this->post('dealer_id');
        $data = $this->post();
        $response = $this->FulfillmentFactory->save_combined_order($data,$group_id);
        if($response){
                redirect('/fulfillment/combine_view/' . $response);
        }else{
            redirect();
        }
        
    }

    public function all_combined_orders_get($group_id)
    {
        $this->permissionslibrary->require_view_perm($this->_feature);

        $data = array(
            'content'           => 'modules/fulfillment/all_combined_orders',
            'title'             => 'Logistics',
            'nav'               => 'planning',
            'subtitle'          => 'Shipping Combine View',
            'manager'           => 'planning',
            'section'           => 'planning',
            'combine_order_data' => '',
            'mode'               => 'get',
            'group_id'           => $group_id
        );
        
        $this->load->view($this->config->item('theme_stub'),$data);
    }
    
    public function search_combine_order_json_get($group_id)
    {
        $this->permissionslibrary->require_view_perm($this->_feature);
        $results = $this->FulfillmentFactory->search_combined_orders($group_id);
        $dataTables = array("draw"=>1,
                            "recordsTotal"=>count($results),
                            "recordsFiltered"=>count($results),
                            "data"=> $results);
        
        $this->response($dataTables,200);
    }

    protected function error_modal($error = '', $content = '', $data = array()) {
        $data['message'] = $error;
        $data['content'] = $content;
        echo $this->load->view('/themes/default/partials/error_modal.php', $data, true);
        exit;
    }

    public function cut_image_get($item_id) {
        $this->permissionslibrary->require_view_perm($this->_feature);
        $item = $this->item_model->get_item_and_measurements($item_id);
        $calcs = $this->db->where('item_id', $item_id)->where('deleted', 0)->get('cut_calculations')->row();
        if ($calcs) {
            $calcs->cuts = json_decode($calcs->cuts);
        }
        if (!$item) {
            $this->error_modal('There is no such item id.');
        } else if (!$this->item_model->has_measurements($item)) {
            $this->error_modal('This item is missing required measurements.  Values A-F must be filled in in order to generate a cut image.');
        } else if (!$calcs || $calcs->status != 1) {
            $this->error_modal("The image calculations have not run yet.  This process can take up to 8 hours from the time the order is submitted or updated.<br><br>
                    You can run the calculations manually by clicking <a data-image_id='$item_id' id='run_image_calcs' href='#'>here</a>.  This will force the calculations to run, and the image will appear in several minutes.
            ");
        } else {
            $data = array(
                'calcs' => $calcs,
                'item' => $item
            );
            $this->load->view('modules/fulfillment/cut_image', $data);
        }
    }

    public function update_order_post($order_id) {
        $this->permissionslibrary->require_edit_perm($this->_feature);
        $data = $this->input->post();
        foreach ($data as $key => $value) {
            if ($value === '') {
                $data[$key] = null;
            }
        }
        $this->order_model->update_order($order_id, $data);
    }
    
    public function force_cut_script_get($item_id) {
        $this->permissionslibrary->require_view_perm($this->_feature);
        error_reporting(0); //the script seems to be taking thousands of sqaure roots of negative numbers which throw php errors and make the page several MB.  These errors may be a problem but for now the messages themselves are the problem.
        $this->item_model->process_item($item_id);
    }

    public function export_orders_post() {
        $this->permissionslibrary->require_view_perm($this->_feature);
        $this->load->library('ExportLibrary');
        $ids = $this->input->post('id');
        $mode = $this->input->post('mode');
        $this->exportlibrary->export_csv($ids, 'planning', $mode);
    }

    protected function get_items_with_key($items, $key) {
        $fitems = array();
        foreach ($items as $item) {
            $print = $this->print_lookup[$item->id];
            if (!empty($print[$key])) {
                $fitems[] = $item;
            }
        }
        return $fitems;
    }

    protected function do_print($data) {
        $print_vars = array();
        foreach ($data as $item) {
            if ($item->combined) {
                foreach ($this->order_model->get_combined_ids($item->id) as $id) {
                    $print_vars[$id] = $item;
                }
            } else {
                $print_vars[$item->id] = $item;
            }
        }
        $order_ids = array();
        foreach ($print_vars as $order_id => $vars) {
            $order_ids[] = $order_id;
        }
        $orders = $this->item_model->get_print_orders_with_items($order_ids);
        $orders_assoc = array();
        foreach ($orders as $order) {
            $orders_assoc[$order->id] = $order;
        }

        $this->load->view('/themes/default/partials/header_print_full');
        foreach ($print_vars as $order_id => $vars) {
            $order = $orders_assoc[$order_id];
            if (!empty($vars->lists)) {
                $this->load->view('/modules/fulfillment/print/packing_list', array('order' => $order, 'items' => $order->items, 'assoc_products' => $order->assoc_products, 'multiple' => true));
            }
            if (!empty($vars->labels)) {
                foreach ($order->items as $item) {
                    $this->load->view('/modules/fulfillment/print/product_labels', array('order' => $order, 'item' => $item, 'multiple' => true));
                }
            }
        }
        $this->load->view('/themes/default/partials/footer_min');
    }

    public function logistics_print_post() {
        $this->permissionslibrary->require_view_perm($this->_feature);
        $data = json_decode($this->input->post('data'));
        $this->do_print($data);
    }

    public function print_items_post() {
        $this->permissionslibrary->require_view_perm($this->_feature);
        $print_items = json_decode($this->input->post('data'), true);
        $item_ids = array();
        $this->print_lookup = array();
        foreach ($print_items as $item) {
            $item_ids[] = $item['id'];
            $this->print_lookup[$item['id']] = $item;
        }
        $orders = $this->item_model->get_print_items_and_orders($item_ids, true);

        $this->load->view('/themes/default/partials/header_print_full');
        foreach ($orders as $order) {
            $group_keys = array('tubing', 'sleeves'); //views that print 1 per order
            foreach ($group_keys as $key) {
                $items = $this->get_items_with_key($order->items, $key); //do not call this function without first assigning print_lookup.  see above.
                if (count($items)) {
                    $this->load->view('/modules/fulfillment/print/' . $key, array('items' => $this->get_items_with_key($items, $key), 'order' => $order));
                }
            }
            

            foreach ($order->items as $item) {
                $print = $this->print_lookup[$item->id];
                $pages = array('laser', 'traveler', 'product_labels', 'sleeve_labels'); //views that print 1 per item
                foreach ($pages as $page) {
                    if (!empty($print[$page])) {
                        if ($page === 'sleeve_labels') {
                            for ($i = 0; $i < $item->sleevecount; ++$i) {
                                $this->load->view('/modules/fulfillment/print/' . $page, array('item' => $item, 'order' => $order));
                            }
                        } else {
                            $this->load->view('/modules/fulfillment/print/' . $page, array('item' => $item, 'order' => $order));
                        }
                    }
                }
            }
        }
        $this->load->view('/themes/default/partials/footer_min');
    }

    public function sleeve_cut_list_get($order_id) 
    {
        $this->permissionslibrary->require_view_perm($this->_feature);
        if(!$this->data['auth']){
            redirect();
        }

        $special_instructions = $this->order_model->fetch_instructions($order_id);

        $data = array(
            'id'                   => $order_id,
            'content'              => '/modules/fulfillment/print/sleeves',
            'title'                => 'Fulfillment',
            'nav'                  => 'fulfillment',
            'subtitle'             => 'Dashboard',
            'manager'              => 'Fulfillment',
            'section'              => 'Fulfillment',
            'special_instructions' => $special_instructions
        );

        $this->load->view($this->config->item('theme_print'), $data);
    }

    public function update_order_combined_post($id) {
        $data = $this->input->post();
        $combined = !empty($data['combined']);
        unset($data['combined']);
        if ($combined) {
            $this->order_model->update_combined_order($id, $data);
        } else {
            $this->order_model->update_order($id, $data);
        }
    }

    public function print_packing_list_get($order_id=null) {
        $this->permissionslibrary->require_view_perm($this->_feature);
        $orders = $this->item_model->get_print_orders_with_items(array($order_id));
        if (!count($orders)) {
            redirect();
        }
        $order = $orders[0];
        $data = array(
            'content'           => '/modules/fulfillment/print/packing_list',
            'title'             => 'Fulfillment',
            'nav'               => 'fulfillment',
            'subtitle'          => 'Dashboard',
            'manager'           => 'Fulfillment',
            'section'           => 'Fulfillment',
            'order'             => $order,
            'assoc_products'    => $order->assoc_products,
            'items'             => $order->items
        );

        $this->load->view($this->config->item('theme_print'), $data);
    }

    // Temp
    public function print_sleeve_label_get (){
        $this->permissionslibrary->require_view_perm($this->_feature);
        if(!$this->data['auth']){
            redirect();
        }

        $data = array(
            'content'           => '/modules/fulfillment/print/sleeve_labels'
        );

        $this->load->view($this->config->item('theme_print_label'), $data);
    }

    public function product_ship_label_get($id=null) {
        $this->permissionslibrary->require_view_perm($this->_feature);
        if(!$id) {
            redirect();
        }
        $combined = substr($id, 0, 1) === 'c';
        if ($combined) {
            $id = substr($id, 1, 15);
        }
        $print = new stdClass();
        $print->labels = true;
        $print->id = $id;
        $print->combined = $combined;
        $data = array($print);
        $this->do_print($data);
    }

    public function laser_csv_get() {
        $this->permissionslibrary->require_view_perm($this->_feature);
        $ids = $this->input->get('id');
        $ids = explode(',', $ids);
        $items = $this->db
                ->select('items.*, orders_has_item.order_id')
                ->join('orders_has_item', 'orders_has_item.item_id=items.id')
                ->where_in('id', $ids)->get('items')->result();
        $row = array();
        foreach($items as $item) {
            $key = $item->id . ' ' . $item->room . ' ' . $item->location;
            $value = $item->unit_num . ' ' . $item->order_id;
            $row[$key] = $value;
        }
        $this->load->library('ExportLibrary');
        $this->exportlibrary->create_csv(array($row), 'laser.csv');
    }

    public function reports_get() {
        $this->load->model('group_model');
        $this->permissionslibrary->require_view_perm($this->_feature);
        $data = array(
            'title'             => 'Reports',
            'nav'               => 'fulfillment_reports',
            'content'           => 'modules/fulfillment/reports',
            'groups'            => $this->group_model->get_groups_array(array('all' => 'All')),
            'codes'             => $this->status_code_model->code_options()
        );
        $this->load->view($this->config->item('theme_stub'), $data);
    }

    public function report_get($output_type) {
        $this->permissionslibrary->require_view_perm($this->_feature);
        $this->load->library('ExportLibrary');
        $type = $this->input->get('type');
        $code = $this->input->get('status');
        $codeparts = explode('-', $code);
        $code_from = @$codeparts[0];
        $code_to = @$codeparts[count($codeparts) - 1];
        $dealer = $this->input->get('dealer');
        $date_from = $this->input->get('date_from');
        $date_to = $this->input->get('date_to');
        $itemtype = $this->input->get('itemtype');

        if ($type === 'manufacturing') {
            $date_filter = $this->input->get('date_filter');
            $data = $this->exportlibrary->get_manufacturing_report_data($code_from, $code_to, $dealer, $date_filter, $date_from, $date_to);
        } else if ($type === 'orders') { 
            $date_filter = $this->input->get('date_filter');
            $data = $this->exportlibrary->get_order_report_data($code_from, $code_to, $dealer, $date_filter, $date_from, $date_to);
        } else if ($type === 'estimates') {
            $data = $this->exportlibrary->get_estimate_report_data($dealer, $date_from, $date_to);
        } else if ($type === 'panels') {
            $data = $this->exportlibrary->get_panels_report_data($date_from, $date_to);
        } else if ($type === 'users') {
            $data = $this->exportlibrary->get_users_report_data();
        } else if ($type === 'balance') {
            $data = $this->exportlibrary->get_balance_report_data($dealer, $date_from, $date_to);
        } else if ($type === 'jobsites') {
            $data = $this->exportlibrary->get_jobsites_report_data($itemtype);
        } else {
            exit('Invalid report type');
        }

        if ($output_type === 'csv') {
            $this->exportlibrary->create_csv($data, $type . '.csv');
        } else {
            $vdata = array(
                'data'  => $data,
                'title' => ucwords($type) . ' Report'
            );
            $this->load->view('modules/fulfillment/data_table_print', $vdata);
        }
    }

    public function dxf_download_get() {
        $this->permissionslibrary->require_view_perm($this->_feature);
        $ids = explode(',', $this->input->get('id'));
        
        $data = array(
            'items' => $this->item_model->get_print_items_and_orders($ids)
        );
        $this->load->view('modules/fulfillment/dxf_download', $data);
    }

    public function dxf_download_post() {
        $this->permissionslibrary->require_view_perm($this->_feature);
        $data = json_decode($this->input->post('data', false), true);
        if (!file_exists('/tmp/dxf')) {
            mkdir('/tmp/dxf', 0777, true);
        }
        chdir('/tmp/dxf');
        $filename = str_replace(' ', '', microtime() . '.zip');
        $files = array();
        foreach ($data as $id => $item) {
            $id = (integer) $id;
            $fh = fopen("/tmp/dxf/$id.svg", 'w');
            fwrite($fh, $item);
            fclose($fh);
            shell_exec("cairosvg /tmp/dxf/$id.svg -o /tmp/dxf/$id.ps");
            shell_exec("pstoedit -f dxf /tmp/dxf/$id.ps /tmp/dxf/$id.dxf");
            $files[] = "$id.dxf";
        }
        shell_exec("zip -Z store -r $filename " . implode(' ', $files));
        header('Content-Type:application/zip');
        header('Content-Disposition: attachment; filename="dxf_' . date('Y_m_d') . '.zip"');
        echo file_get_contents($filename);
    }
}
