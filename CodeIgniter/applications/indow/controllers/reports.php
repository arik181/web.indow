<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Reports extends MM_Controller
{

    protected $_user;

    function __construct() {
        parent::__construct();
        $this->load->model(array('report_model'));
        $this->load->helper(array('language', 'ulist'));
        $this->load->factory("ReportFactory");
        $this->_user = $this->data['user'];

    }

    public function index_get() {
        $data = array(
            'content'           => 'modules/reports/list',
            'title'             => 'Reports',
            'nav'               => 'reports',
            'subtitle'          => 'Dealer Estimate Reports',
            'section'           => 'reports',
            'add_path'          => '#',
            'add_button'        => 'Create Report',
        );
        $message = $this->session->flashdata('message');
        if (!empty($message)) {
            $data['message'] = $message;
        }


        $this->load->view($this->config->item('theme_list'), $data);
    }

    public function index_post() {

        $this->report_model->create($this->post());

        $this->session->set_flashdata('message', 'Report created.');
        redirect('/reports');
    }

    public function view_get($report_id) {
        $data = array(
            'content'           => 'modules/reports/view',
            'title'             => 'Reports',
            'nav'               => 'reports',
            'subtitle'          => 'Dealer Estimate Reports',
            'section'           => 'reports',
            'report_id'         => $report_id,
            'report'            => $this->report_model->get($report_id),
        );

        $message = $this->session->flashdata('message');
        if (!empty($message)) {
            $data['message'] = $message;
        }

        $this->load->view($this->config->item('theme_list'), $data);
    }

    public function list_json_get() {

        $results = $this->ReportFactory->getList();

        $dataTables = array("draw"=>1,
                            "recordsTotal"=>count($results),
                            "recordsFiltered"=>count($results),
                            "data"=>$results);

        $this->response($dataTables,200);
    }

    public function report_json_get($report_id) {


        if($this->_user->in_admin_group)
        {
            $results = $this->ReportFactory->getAdminReport($report_id);
        }
        else
        {
            $results = $this->ReportFactory->getReport($report_id);
        }

        $dataTables = array("draw"=>1,
                            "recordsTotal"=>count($results),
                            "recordsFiltered"=>count($results),
                            "data"=>$results);

        $this->response($dataTables,200);
    }

    public function delete_get($report_id) {
        $this->report_model->delete($report_id);
        $this->session->set_flashdata('message', 'Report deleted.');
        redirect('/reports');
    }
}
