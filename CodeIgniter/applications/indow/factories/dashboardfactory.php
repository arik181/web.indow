<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class DashboardFactory extends MM_Factory
{
	protected $_model = "";
	protected $_table = "";
	protected $_primaryKey = "id";

    public function __construct()
    {
        parent::__construct();
        $this->load->model(array('Estimate_model', 'quote_model', 'order_model'));        
    }

	public function getList($user_id)
	{
        @$group = $this->permissionslibrary->_user->group_ids[0];
        $results = $this->Estimate_model->get_followup($group);
        array_splice($results, 0, 0, $this->quote_model->get_followup($group));
        array_splice($results, 0, 0, $this->order_model->get_followup($group));
        return $results;
	}

    public function getAdminList()
    {
        $results = $this->Estimate_model->get_admin_follow_up();
        array_splice($results, 0, 0, $this->quote_model->get_admin_followup());
        array_splice($results, 0, 0, $this->order_model->get_admin_followup());
        return $results;
    }
}
