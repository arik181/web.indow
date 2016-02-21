<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class ProductFactory extends MM_Factory
{
	protected $_model = "Product_model";
	protected $_table = "products";
	protected $_primaryKey = "products.id";

    public function __construct()
    {
        parent::__construct();
	}

	public function getList()
	{
        $sql = "SELECT         type.id AS id,
                                products.id AS product_id,
                                products.product AS product_name,
                                type.product_type AS product_type,
                                type.abbrev AS abrev ,
                                type.size AS product_size,
                                (
                                CASE 
                                    WHEN (type.opening_specific = '1' and  type.not_opening_specific = '1') THEN 'Both'
                                    WHEN type.opening_specific = '1' THEN 'Yes'
                                    WHEN type.not_opening_specific = '1' THEN 'No'
                                    ELSE 'No'
                                END
                                ) AS opening,
                                CONCAT(type.unit_price,' ',type.unit_price_type) AS price_unit,
                                type.min_price AS min_price,
                                CONCAT(type.max_width,' X ' , type.max_height) AS max_width
                FROM            product_types AS type
                INNER JOIN      products 
                ON              type.product_id = products.id
                ;
                ";
        
		$results = $this->db->query($sql)->result();
         
		return $results;
	}
}
