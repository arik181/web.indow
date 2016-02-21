<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Report_model extends MM_Model
{
    protected  $_table      = 'reports';

    public function __construct() {
        parent::__construct();
    }

    public function create($data) {
        $data['created_by'] = $this->ion_auth->get_user_id();
        $data['created'] = date('Y-m-d H:i:s');
        $this->db->insert($this->_table, $data);
        return $this->db->insert_id();
    }

    public function createAdmin($data) {
        $data['created_by'] = $this->ion_auth->get_user_id();
        $data['created'] = date('Y-m-d H:i:s');
        $this->db->insert($this->_table, $data);
        return $this->db->insert_id();
    }

    public function get($report_id) {
        return $this->db->where('id', $report_id)->get($this->_table)->row();
    }

    public function delete($report_id) {
        $this->db->where('id', $report_id)->update('reports', array('deleted' => 1));
    }

}
