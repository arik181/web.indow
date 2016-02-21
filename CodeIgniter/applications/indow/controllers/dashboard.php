<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

    /**
     * Class Dashboard
     */
    class Dashboard extends MM_Controller
    {

        protected $_user;

        public function __construct()
        {
            parent::__construct();
            $this->load->helper(array('chart', 'ulist', 'html'));
            $this->load->factory("DashboardFactory");
            $this->_user = $this->data['user'];
            if ($this->_user->is_customer) {
                exit;
            }
        }

        /**
         * Dashboard
         */
        public function index_get()
        {

            if($this->_user->in_admin_group)
            {
                $estimate_count = $this->Estimate_model->getEstimatesForCurrentMonth();
                $order_count    = $this->order_model->getOrdersForCurrentMonth();
                $quote_count    = $this->quote_model->getQuotesForCurrentMonth();

            }
            else
            {
                $estimate_count = $this->Estimate_model->getEstimatesForCurrentMonth($this->_user->id);
                $order_count    = $this->order_model->getOrdersForCurrentMonth($this->_user->id);
                $quote_count    = $this->quote_model->getQuotesForCurrentMonth($this->_user->id);

            }

            // Graph Data
            $data = $this->order_model->graph_data($this->_user->id);

            $color_array = array("#f6891f", "#7acbd7", "#428d98", "#f26722", "green", "black", "gray", "maroon", "lime", "olive", "navy", "fuchsia", "teal", "aqua", "silver", "white");

            $results = array();
            foreach ($data as $value => $key) {
                $results[] = array('data' => $key->data, 'label' => $key->label, 'color' => $color_array[$value]);
            }

            $data = array(
                'content'           => 'modules/dashboard/dashboard',
                'title'             => 'Dashboard',
                'nav'               => 'dashboard',
                'subtitle'          => 'This Month',
                'manager'           => 'Dashboard',
                'section'           => 'Dashboard',
                'active_estimates'  => null,
                'open_orders'       => null,
                'associated_quotes' => null,
                'follow_up_list'    => null,
                'associated_orders' => null,
                'estimate_count'    => count($estimate_count),
                'order_count'       => count($order_count),
                'quote_count'       => count($quote_count),
                'user_id'           => $this->_user->id,
                'chart_data'        => $results,
                'sales_leads'             => $this->user_model->get_sales_leads($this->_user)
            );
            if (!$this->session->flashdata('message') == false) {
                $data['message'] = $this->session->flashdata('message');
            }
            $this->load->view($this->config->item('theme_dashboard'), $data);
        }

        /**
         * Used for ajax call from Data Tables.
         */
        public function list_json_get()
        {

            if($this->_user->in_admin_group && false)
            {
                $results = $this->DashboardFactory->getAdminList();
            }
            else
            {
                $results = $this->DashboardFactory->getList($this->_user->id);
            }

            $dataTables = array(
                "draw"            => 1,
                "recordsTotal"    => count($results),
                "recordsFiltered" => count($results),
                "data"            => $results
            );

            $this->response($dataTables, 200);

        }

        /**
         * @param $type
         * @param $id
         */
        public function followup_get($type, $id)
        {
            switch ($type) {
                case 'quote':
                    $this->quote_model->followup($id);
                    break;

                case 'estimate':
                    $this->Estimate_model->followup($id);
                    break;

                case 'order':
                    $this->order_model->followup($id);
                    break;

                default:
                    break;
            }
        }
    }
