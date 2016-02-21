<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class product_model extends MM_Model {
    public function get_product_info() {
        $products_array = array();
        $products = $this->db->get('products')->result();
        foreach ($products as $product) {
            $products_array[$product->id] = array(
                'id' => $product->id,
                'price' => $product->price,
                'product_type_id' => $product->product_type_id,
            );
        }
        return $product_array;
    }
}