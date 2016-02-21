<?php defined('BASEPATH') OR exit('No direct script access allowed');


class configuration_calculator_api extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model(array('configuration_calculator_model'));
    }

		public function get_list()
		{
			$id = $this->input->post('estimate_id');
			$estimate = $this->configuration_calculator_model->get_list($id);
			$this->output
				->set_content_type('application/json')
				->set_output(json_encode($estimate)); 
		}
		
		public function add_items()
		{
			$estimate_id = $this->input->post('estimate_id');
			$item_array = $this->input->post('items');
			
			if($this->configuration_calculator_model->add_items($estimate_id, $item_array))
			{
				$return = array('message' => 'Items Added!');
			} else {
				$return = array('message' => 'An Error was Encountered!');
				log_message('error', 'Items not added to estimate! -> '.$estimate_id.' Items: '.$print_r($item_array, TRUE));
			}
			
			$this->output
				->set_content_type('application/json')
				->set_output(json_encode($return)); 
		}
		
		public function remove_items()
		{
			$estimate_id = $this->input->post('estimate_id');
			$item_array = $this->input->post('items');
			
			if($this->configuration_calculator_model->remove_items($estimate_id, $item_array))
			{
				$return = array('message' => 'Items Removed!');
			} else {
				$return = array('message' => 'An Error was Encountered!');
				log_message('error', 'Items not be removed! -> '.$estimate_id.' Items: '.print_r($item_array, TRUE));
			}
			
			$this->output
				->set_content_type('application/json')
				->set_output(json_encode($return)); 
		}
}
