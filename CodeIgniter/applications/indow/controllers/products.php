<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Products extends MM_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->_user = $this->data['user'];
        if(!$this->_user->in_admin_group)
        {
            redirect();
        }
        $this->load->model(array('user_model', 'estimate_model', 'order_model', 'product_model'));
        $this->load->helper(array('language', 'ulist'));
        $this->load->factory("ProductFactory");
    }

    public function index_get($start = 0, $limit = 25)
    {

        $data = array(
            'content'           => 'modules/products/list',
            'title'             => 'Product Manager',
            'nav'               => '',
            'subtitle'          => 'Products List',
            'manager'           => 'Products Manager',
            'section'           => 'Products',
            'associated_orders' => NULL,
            'add_button'        => 'Add Product',
            'add_path'          => 'products/add/'
        );
        
         if (!$this->session->flashdata('message') == FALSE) {
            $data['message'] = $this->session->flashdata('message');
        }

        $data["products"] = $this->product_model->fetch_products($limit, $start);
        $this->load->view($this->config->item('theme_list'), $data);
    }

    public function list_json_get()
    {
        $results = $this->ProductFactory->getList();
        $dataTables = array("draw"=>1,
                            "recordsTotal"=>count($results),
                            "recordsFiltered"=>count($results),
                            "data"=>$results);

        $this->response($dataTables,200);
    }
    
  
   public function add_get(){

         $data = array(
                'content' => 'modules/products/edit'
         );
           $data["check_page"] = 'add';
           $data['nav']         = "products";
            $data['title']       = 'Product Manager';
            $data['subtitle']    = 'Product';
            $data['add_path']    = '/products/add/';
            $data['form']        = '/products/list';
            $data['delete_path'] = '/products/delete';
            $data['manager']     = 'Product';
            $data['section']     = 'Product Add';

            $data['product_types']=$this->product_model->fetch_products_types();

            $this->load->view($this->config->item('theme_home'), $data);
        
   }
    public function edit_get($id=null){

    
        $data = array(
            'content' => 'modules/products/edit',
            'Yes'     => false,
            'No'      => false,
            'Both'    => false
        );

        if (!$this->session->flashdata('message') == false) {
            $data['message'] = $this->session->flashdata('message');
        }
       
        if($id){
            $data["edit_contents"] = $this->product_model->fetch_products_data($id);
            if($data["edit_contents"]==false){
                $data['message'] = 'Invalid Product id. Please try again.';
                $this->session->set_flashdata('message',$data['message']);
                redirect('/products');
            }
             
            
            $data["check_page"] = "edit";
        }else{
            redirect('/products');
        }
            $data['nav']         = "products";
            $data['title']       = 'Product Manager';
            $data['subtitle']    = 'Product';
            $data['add_path']    = '/products/edit/';
            $data['form']        = '/products/list';
            $data['delete_path'] = '/products/delete';
            $data['manager']     = 'Product';
            $data['section']     = 'Product Edit';
            
            if($data["edit_contents"]['opening_specific']==1){
                $data['Yes']=true;
             }else{
                $data['No']=true;
             }       
             if($data["edit_contents"]['opening_specific']==1 and $data["edit_contents"]['not_opening_specific']==1){
                   $data['Both']=true;
             }

            $data['product_types']=$this->product_model->fetch_products_types();

            $this->load->view($this->config->item('theme_home'), $data);
    }
    public function edit_post($id=null){


        if($id){
                $data = array();
                 switch($_POST['opening_status']){
                        Case 1:
                            $_POST['opening_specific']=1;
                             $_POST['not_opening_specific']=0;
                            break;
                        Case 2:
                           $_POST['not_opening_specific']=1;
                            $_POST['opening_specific']=0;
                            break;
                        Case 3:
                            $_POST['opening_specific']=1;
                            $_POST['not_opening_specific']=1;
                            break;
                 }
                 if ($_POST['discontinued_date'] === '') {
                    $_POST['discontinued_date'] = null;
                 }
                
                unset($_POST['opening_status']);
                unset($_POST['submit']);
                
                $check = $this->product_model->update_product($id);
                if ($check) {
                    $data['message'] = 'Product has been successfully updated.';
                } else {
                    $data['message'] = 'Product has not been updated. Please Try again.';
                } 
                $this->session->set_flashdata('message',$data['message']);
                redirect('/products');
            }
        }
        public function add_post(){
            if(!$this->data['auth'])
            {
                redirect();
            } 
            switch($_POST['opening_status']){
                Case 1:
                    $_POST['opening_specific']=1;
                    break;
                Case 2:
                   $_POST['not_opening_specific']=1;
                    break;
                Case 3:
                    $_POST['opening_specific']=1;
                    $_POST['not_opening_specific']=1;
                    break;
            }
            unset($_POST['opening_status']);
            unset($_POST['submit']);

			if ($_POST['discontinued_date'] === '') {
				$_POST['discontinued_date'] = null;
			}
            
            $check=$this->product_model->insert_product();
            if($check){
                $data['message'] = 'Product has been successfully inserted.';
                 $this->session->set_flashdata('message',$data['message']);
                redirect('/products');
            }else{
                $data['message'] = 'Product has not been inserted. Please Try again.';
                 $this->session->set_flashdata('message',$data['message']);
                 redirect('/products');
            }
        }
        public function delete_get($id=null){
            if($id != null){
                $check=$this->product_model->delete_product($id);
                if($check){
                    $data['message']="Product has been successfully deleted. ";
                    $this->session->set_flashdata('message',$data['message']);
                    redirect('/products');
                }else{
                    $data['message']="Product has not been deleted. Please, try again. ";
                    $this->session->set_flashdata('message',$data['message']);
                    redirect('/products');
                }
            }
        }
 
}
