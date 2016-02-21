<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require APPPATH . 'third_party' . DIRECTORY_SEPARATOR . 'PasswordHash.php';

class Items extends MM_Controller
{
    protected $_model;
    
    function __construct()
    {
        parent::__construct();

        $this->load->model(array('item_model'));
        $this->load->factory("ItemFactory");
    }

    public function test_factory_get()
    {
        $item = $this->ItemFactory->get(1);
        pr($item);
        $item->item_total = 500;
        $result = $item->save();
        pr($result);

        $newItem = new Item_model();
        $newItem->customer_id = 1;
        $newItem->site_id = 1;
        $newItem->save();
        pr($newItem);

    }

}
