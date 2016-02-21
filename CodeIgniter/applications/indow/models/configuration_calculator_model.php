<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 *	Model for configuration calculator AJAX API
 */
class configuration_calculator_model extends MM_Model
{
/**
 *	-> Display Items List
 *	 -> There will be a list of items on the saved estimate
 *	 -> There will be a list of items below the saved estimate, that are from the parent estimate
 *	 -> The list of items in the parent estimate should not include the list of items in the saved estimate
 */
 public function get_list($id)
 {
 	// First we do very basic error checking
 	if(empty($id) || is_array($id) || !is_numeric($id))
 	{
 		// If there's a problem, we log an error, and return false
 		log_message('error', 'configuration_calculator_model -> get_list passed an invalid value: '.print_r($id, TRUE));
 		return FALSE;
 	}
 	
	// Get the info on the estimate itself, limited to saved estimates
	$query = $this->db->get_where('estimates', array('id' => $id));
	$result = $query->result_array();
	$return['estimate'] = $result[0];
		
	// Get the items that are associated to the estimate
	$sql = "SELECT items.* FROM items, estimates_has_item
					WHERE estimates_has_item.estimate_id = ?
					AND estimates_has_item.item_id = items.id
					and estimates_has_item.deleted = 0";
	$query = $this->db->query($sql, $id);
	$return['items'] = $query->result_array();
	
	// Get the items associated with the parent estimate, excluding the items that are already on the estimate
	$sql = "SELECT items.* FROM items, estimates_has_item
					WHERE estimates_has_item.estimate_id = ?
					AND estimates_has_item.item_id = items.id
					AND estimates_has_item.deleted = 0
					AND items.id NOT IN 
						(
							SELECT item_id FROM estimates_has_item WHERE estimate_id = ? AND deleted = 0
						)";
	$query = $this->db->query($sql, array($return['estimate']['parent_estimate_id'], $id));
 	$return['available_items'] = $query->result_array();
 	
 	// Return all this awesome data!
 	return $return;
 }
 
/**
 *	-> Add item to saved estimate
 */
 public function add_items($estimate_id, $item_array)
 {
 	// Insert SQL
 	$sql = "INSERT INTO `indowwin_db`.`estimates_has_item` 
 		(`estimate_id`, `estimate_history_id`, `estimate_created_by_id`
 				, `item_id`, `item_quality_control_id`) 
 				VALUES ";
 	$i = 1; // Single use integer
  foreach($item_array as $item_id)
  {
  	if($i != 1)
  	{
  		$sql .= ", ";
  	}
  	$sql .= "('".$estimate_id."', '1', '1', '".$item_id."', '1')";
  	$i++;
  } 	
  $sql .= ";";
  
 	// Execute SQL
	return 	$this->db->query($sql);
 }
 
/**
 *	-> Remove item from saved estimate
 */
 public function remove_items($estimate_id, $item_array)
 {
		$sql = "UPDATE indowwin_db.estimates_has_item SET deleted = 1
						WHERE estimate_id = ".$estimate_id." AND item_id in ( ".implode(',', $item_array)." );";
		return 	$this->db->query($sql);
 }
 
 public function get_estimate_info($id)
 {
 	// Get the info on the estimate itself
	$query = $this->db->get_where('estimates', array('id' => $id));
	$result = $query->result();
	return $result[0];
 }
 
 public function update_name($id, $name)
 {	
 	$this->db->where('id', $id);
 	$this->db->update('estimates', array('name' => $name));
 }
 
 public function get_items($id)
 {
 	$sql = "SELECT items.id
	, items.room
	, concat(sites.address,' ', sites.city, ', ', sites.state,' ', sites.zipcode) location
	, items.width
	, items.height
	, products.name product
	, product_types.name product_type
	, items.edging_id edging
	, if(items.special_geom = 1, 'Yes', 'No') geometry
	, items.price retail
	FROM items, sites, products, product_types, estimates_has_item
	WHERE estimates_has_item.item_id = items.id
	AND items.site_id = sites.id
	AND items.product_id = products.id
	AND products.product_type_id = product_types.id
	AND estimates_has_item.deleted = 0
	AND estimates_has_item.estimate_id = ".$id;
	$query = $this->db->query($sql);
	return $query->result();
 }
 
 public function get_available_items($id, $parent_id)
 {
 	$sql = "SELECT items.id
, items.room
, concat(sites.address,' ', sites.city, ', ', sites.state,' ', sites.zipcode) location
, items.width
, items.height
, products.name product
, product_types.name product_type
, items.edging_id edging
, if(items.special_geom = 1, 'Yes', 'No') geometry
, items.price retail
FROM items, sites, products, product_types, estimates_has_item
WHERE estimates_has_item.item_id = items.id
AND items.site_id = sites.id
AND items.product_id = products.id
AND products.product_type_id = product_types.id
AND estimates_has_item.deleted = 0
AND estimates_has_item.estimate_id = ".$parent_id."
AND items.id NOT IN (select item_id from estimates_has_item where estimate_id = ".$id." and deleted = 0)";
	$query = $this->db->query($sql);
	return $query->result();
 }
}
