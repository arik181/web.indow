<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Product_model extends MM_Model
{

    protected  $_table = "products";
    protected  $_soft_delete = TRUE;
    protected  $_key = "id";

    public $id;
    public $name; 
    public $description;
    public $number;
    public $price;
    public $product_type_id;

    public function __construct()
    {
        parent::__construct();
        $key = $this->_key;
        if ($this->$key)
            return;
        $this->number = 0;
        $this->product_type_id = 1;
    }

    public function save()
    {
        $sql = "";
        if ($this->id > 0) // update operation
        {
            $sql = "UPDATE      $this->_table
                                name = ?,
                                description = ?,
                                number = ?,
                                price = ?,
                                product_type_id = ?,
                    WHERE       id = ?";
        }
        else //insert operation
        {
            $history = new History_model();
            $history->save();
            $this->history_id = $history->id;
            $sql = "INSERT INTO $this->_table
                    SET         name = ?,
                                description = ?,
                                number = ?,
                                price = ?,
                                product_type_id = ?";
        }

        $this->db->query($sql, array($this->name,
                                     $this->description,
                                     $this->number,
                                     $this->price,
                                     $this->product_type_id,
                                     $this->id));
        if ($this->id == 0)
            $this->id = $this->db->insert_id();

        return array('success' => true, 'message' => 'Updated product successfully.');
    }

    public function fetch_products($limit, $start)
    {
        $sql = "SELECT *
                FROM products
            ";

        $start = (int) $start;
        $limit = (int) $limit;
        $query = $this->db->query($sql, array($start, $limit));

        if ( ! empty($query)  && $query->num_rows() > 0) 
        {
            foreach ($query->result() as $row) 
            {
                $data[] = $row;
            }
            return $data;
        }

        return false;
    }
   public function fetch_products_types(){
        $sql="SELECT * FROM products";
          $query = $this->db->query($sql);
    
           $results = $query->result();
            $data=array();
            foreach ($results as $row) {
                $data[$row->id]=$row->product;
            }
           return $data;
    }

    public function fetch_products_data($id){
       $query = $this->db->get_where('product_types',array('id'=>$id));
       $data=array();
       foreach($query->result_array() as $row){
        $data[]=$row;
       }
       if(!empty($data)){
            return $data[0];
       }else{
           return false;
       }
    }

    public function insert_product(){
        $data=$_POST;
        $check=$this->db->insert('product_types',$data);
        if($check){
            return $check;
        }

    }
    public function update_product($id){
         
        $data=$_POST;
        if ($data['cut_image_offset'] === '') {
            $data['cut_image_offset'] = null;
        }
        $this->db->where('id', $id);
        $check = $this->db->update('product_types', $data); 
       return $check;

    }
  
    public function delete_product($pid){
        if($pid){
            // $this->delete_items($pid);
             $this->db->where('id',$pid);
             $this->db->delete('product_types');
             return true;
        }
        return false;
        
    }
    
    public function delete_items($pid){
        $this->db->select('id');
        $query=$this->db->get_where('items',array('product_id'=>$pid));
        $data=array();
        foreach($query->result_array() as $row){
            $data[]=$row['id'];  
         }
         if(!empty($data)){
            $this->delete_estimate_has_item($data);
            $this->delete_site_has_items($data);
            $this->db->or_where_in('id',$data);
            $this->db->delete('items');
         }
    }
      
    
  
    
     public function delete_site_has_items($item_id){
          if(!empty($item_id)){
             $this->db->select('site_id');
             $this->db->or_where_in('item_id',$item_id);
             $query=$this->db->get('site_has_items');
             $data=array();
             foreach($query->result_array() as $row){
               $data[]=$row['site_id'];
             }
             if(!empty($data)){
                 $this->db->or_where_in('site_id',$data);
                 $this->db->delete('site_has_items');
             }             
        }
    }
    
   
    public function delete_estimate_has_item($item_id){
        if(!empty($item_id)){
            $this->db->select('id');
            $this->db->or_where_in('item_id',$item_id);
            $query=$this->db->get('estimates_has_item');
            $data=array();
            foreach($query->result_array() as $row){
               $data[]=$row['id'];
            }
            if(!empty($data)){
                $this->db->or_where_in('id',$data);
                $this->db->delete('estimates_has_item');
            }
        }
    }
    public function get_product_info($dealer_id=null, $date=null) {
        $this->load->model('group_model');
        $msrp = $this->group_model->get_msrp($dealer_id);
        $res = $this->db->select('*, IF(product_id=3, product_types.product_type, product_types.abbrev) as product_type', false);
        if ($date) {
            $this->db->where('discontinued_date IS NULL OR discontinued_date > ' . $this->db->escape($date));
        }
        $res = $this->db->get('product_types')->result();
        $res_array = array();
        foreach ($res as $row) {
            if (!empty($row->size)) {
                $row->product_type .= ' (' . $row->size . ' in.)';
            }
            $row->unit_price = round($row->unit_price * $msrp, 2);
            $row->min_price = round($row->min_price * $msrp, 2);
            $res_array[$row->id] = $row;
        }
        return $res_array;
    }

    public function get_all() {
        $products = $this->id_name_array('products', 'product');
        unset($products[4]);
        $first = array(4 => 'T2 Insert');
        return $first + $products;
    }
}
