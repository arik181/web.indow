<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class MM_Model extends CI_Model
{

    protected $_model;
    protected $_table;
    protected $_key;
    protected $_soft_delete = false;

    public function __construct(){
        parent::__construct();
    }

    /**
     * This function returns the query result as an array of objects, or an empty array on failure.
     * @param $id - id of the record to return.
     * @return mixed - return result
     */

    public function get($id){
        $sql = "SELECT * FROM " . $this->_table . " WHERE " . $this->_key . " = ?;";
        $query = $this->db->query($sql,$id);
        return $query->row();
    }

    /**
     * This function returns a single result row. If your query has more than one row, it returns only the first row.
     * The result is returned as an object.
     * @param $args
     * @param null $field
     * @param string $order
     * @return object The result is returned as an object.
     */

    public function get_by($args , $field = null , $order = 'desc'){

        if($field == null){
            $field = $this->_key;
        }

        $conditions = $this->where_builder($args);
        $sql = "SELECT * FROM " .  $this->_table . " WHERE " . $conditions . " ORDER BY " . $field . ' ' . $order . ' LIMIT 1';
        $query = $this->db->query($sql);
        return $query->row();

    }


    /**
     * This function returns the query result as an array of objects, or an empty array on failure.
     * @param Array() - $args array('field_name' => 'value') - supply as many values as you need.
     * @param null $field
     * @param string $order
     * @return Array of Objects
     */

    public function get_many_by($args , $field = null , $order = 'desc'){

        if($field == null){
            $field = $this->_key;
        }

        $conditions = $this->where_builder($args);
        $sql = "SELECT * FROM " .  $this->_table . " WHERE " . $conditions . " ORDER BY " . $field . ' ' . $order;
        $query = $this->db->query($sql);
        return $query->result();

    }

    public function getManyById($ids){

        $sql = "SELECT * FROM " . $this->_table . " WHERE " . $this->_key . " IN ( " . implode(",",array_values($ids)) .");";

        return $this->db->query($sql)->result();

    }




    /**
     * Get the total number of record in a table.
     * @return int - The number or records in a table.
     */

    public function count_all(){
        $sql = "SELECT count(". $this->_key .") as count FROM " . $this->_table;
        $result = $this->db->query($sql)->row();
        return (int) $result->count;
    }

    /**
     * To get a count of all record based on conditions provided.
     * @param $args - Array of conditions
     * @return int the number of results returned.
     */

    public function count_by($args){

        $conditions = $this->fieldsBuilder($args);
        $sql = "SELECT count(" . $this->_key . ") as count FROM " . $this->_table . " WHERE " . $conditions['fields'];
        $result = $this->db->query($sql,$conditions['values'])->row();
        return (int) $result->count;

    }

    /**
     * @param $id - Id of the record to update
     * @param $args - the fields to update.
     * @return Boolean - TRUE or FALSE
     */

    public function update($id,$args){
        $data = $this->fieldsBuilder($args);
        $values = $data['values'];
        $values[] = $id;
        $sql = "UPDATE " . $this->_table . " SET " . $data['fields'] . " WHERE " . $this->_key . " = ?";
        $query = $this->db->query($sql,$values);
        return $query;
    }

    public function update_table($id,$args,$table,$key){
        $data = $this->fieldsBuilder($args);
        $values = $data['values'];
        $values[] = $id;
        $sql = "UPDATE " . $table . " SET " . $data['fields'] . " WHERE " . $key . " = ?";
        $query = $this->db->query($sql,$values);
        return $query;
    }



    /**
     * @param $id - record to delete
     * @return mixed
     */

    public function delete($id)
    {
        $sql = "DELETE FROM " . $this->_table . " WHERE  " . $this->_key . " = ? ;";
        $query = $this->db->query($sql,$id);

        return $query;
    }

    public function delete_by($args)
    {
        $conditions = $this->where_builder($args);

        $sql = "DELETE FROM " . $this->_table . " WHERE  " . $conditions . ";";

        $query = $this->db->query($sql);

        return $query;
    }

    /**
     * @param $args
     * @return mixed
     */

    public function delete_many_by($args){

        $conditions = $this->where_builder($args);

        $sql = "DELETE FROM " . $this->_table . " WHERE  " . $conditions . ";";

        $query = $this->db->query($sql);

        return $query;

    }

    /**
     * @param $id - record to delete
     * @return mixed
     */

    public function soft_delete($id){
        $sql = "UPDATE " . $this->_table . " SET deleted = 1 " . " WHERE ". $this->_key ." = ?;";
        $query = $this->db->query($sql,$id);
        return $query;
    }

    /**
     * A basic pagination method.
     * This function returns the query result as an array of objects, or an empty array on failure.
     * @param $limit - Number of records to return
     * @param $start - The offset ( WHERE TO START PULLING RECORDS)
     * @param $soft - Look for record with soft deleted enabled
     * @return mixed - Results Object
     */
 

    public function fetch_items($limit,$start,$soft = false){

        if($soft == false){
            $sql = "SELECT * FROM " . $this->_table . " LIMIT " . $limit . " OFFSET " . $start;
        }else{
            $sql = "SELECT * FROM " . $this->_table . " WHERE deleted = 0 LIMIT " . $limit . " OFFSET " . $start;
        }

        $query = $this->db->query($sql);

        return $query->result();

    }

    /**
     *
     * Fetch all the records in the table.
     * This function returns the query result as an array of objects, or an empty array on failure.
     * @return Array of Objects
     *
     */

    public function get_all()
    {
        if($this->_soft_delete === false){
            $sql = "SELECT * FROM " . $this->_table;
        }else{
            $sql = "SELECT * FROM " . $this->_table . " WHERE deleted = 0";
        }

        $query = $this->db->query($sql);
        return $query->result();
    }


    /**
     *
     * This function returns the query result as an array of objects, or an empty array on failure.
     * @param Array() - $args array('field_name' => 'value') - supply as many values as you need.
     * @return Array of Objects
     *
     */

    public function get_all_by($args){

        $conditions = $this->where_builder($args);
        $sql = "SELECT * FROM " . $this->_table . " WHERE " . $conditions;
        $query = $this->db->query($sql);
        return $query->result();

    }

    /**
     * @param $id
     *
     * @return mixed
     */

    public function get_all_except($id)
    {
        $sql = "SELECT * FROM " . $this->_table . " WHERE " . $this->_key . " != ? AND deleted = 0";
        $query = $this->db->query($sql,$id);
        return $query->result();
    }

    /**
     * @param $args
     * @return INT returns the id of record just inserted into the table.
     */

    public function insert($args)
    {
        $conditions = $this->insertFieldsBuilder($args);
        $sql = "INSERT INTO " . $this->_table . "(" .  $conditions['fields']  .  ") VALUES("  . $conditions['placeholders']  . ")";
        $this->db->query($sql,$conditions['values']);

        return $this->db->insert_id();
    }


    /**
     * Insert multiple rows into the table. Returns an array of multiple IDs.
     */

    public function insert_many($data)
    {

        $results = $this->insert_many_builder($data);

        $sql = "INSERT INTO " . $this->_table. " " . $results['fields'] . " VALUES " . $results['values'] . ";";

        return $this->db->query($sql);

    }


    /**
     * @param $args - an array of key value pairs to to build an insert or update string.
     * @return array - Return an associative array with fields string and the values to enter.
     */

    public function fieldsBuilder($args)
    {
        $data = array();
        foreach ($args as $key => $value) 
        {
            if($key !== 'submit')
            {
                $keys[]   = "`$key` = ?";
                $values[] = $value;
            }
        }
        return array('fields' => implode(",", $keys), 'values' => array_values($values));

    }

    public function insertFieldsBuilder($args)
    {
        $fields = array();
        $values = array();
        $placeholders = array();
        foreach ($args as $key => $value)
        {
            $fields[] = $key;
            $placeholders[] = '?';
            $values[] = $value;
        }
        return array('fields' => implode(",", $fields) , 'placeholders' => implode(",", $placeholders) , 'values' => $values );
    }

    public function where_builder($args)
    {
        $sqlString = '';
        $numberOfArgs = count($args);
        $count = 1;
        foreach ($args as $key => $value) {
            if ($count == $numberOfArgs) {
                $sqlString = $sqlString . $key . " = " . $this->db->escape($value);
            } else {
                $sqlString = $sqlString . $key . " = " . $this->db->escape($value) . " AND ";
            }
            $count++;
        }
        return $sqlString;
    }

    public function insert_many_builder($args)
    {

        $fields = "(" . implode(",", array_keys($args[0])) . ")";
        $values = '';
        $ids = array();
        $count = 1;

        foreach($args as $value){

            $ids[] = array_values($value);

        }

        $numberOfArgs = count($ids);

        foreach($ids as $value){

            if($count < $numberOfArgs)
            {
                $values = $values . "(" . implode(",",array_values($value)) . "),";

            } else{

                $values = $values . "(" . implode(",",array_values($value)) . ")";
            }

            $count++;
        }

        return array('fields' => $fields, 'values' => $values);

    }

    public function save()
    {
        $reflectionObject =new ReflectionObject($this);
        $properties = $reflectionObject->getProperties(ReflectionProperty::IS_PUBLIC);
        $key = $this->_key;
        $where = null;
        $values = array();
        $keys = array();
        foreach ($properties as $property)
        {
            $property_key = $property->name;
            $property_value =$this->$property_key;
            if ($property_key == $key)
                $where = $property_value;
            else
            {
                $keys[] = $property_key . " = ?";
                $values[] = $property_value;
            }
        }
        $values[] = $where;
        if ($this->$key) // Update
        {
            if (count($properties)<2) //
                return false;
            $sql = "UPDATE      $this->_table
                    SET         ";
            $sql .= implode(",", $keys) . " WHERE $key = ?";
            return $this->db->query($sql,$values);
        }
        else
        {
            $sql = "INSERT INTO $this->_table ";
            $sql .= "SET ";
            if (count($properties) >1)
            {
                $sql .= implode(",", $keys);
            }
            else
            {
                $sql .= $key . " = NULL";
            }
            $this->db->query($sql, $values);
            $this->$key = $this->db->insert_id();
        }

    }
    
    public function id_name_array($tablename, $valuefield = 'name', $keyfield = 'id', $blankoption=false) {
        if ($blankoption) {
            $retarray = array('' => '');
        } else {
            $retarray = array();
        }
        $results = $this->db->select($valuefield . ',' . $keyfield)->from($tablename)->get()->result();
        foreach ($results as $result) {
            $retarray[$result->$keyfield] = $result->$valuefield;
        }
        return $retarray;
        
    }

    public function id_list($items) {
        $ids = array();
        foreach ($items as $item) {
            $ids[] = $item->id;
        }
        return $ids;
    }

    public function group_assoc($items, $key, $unset_key=false) {
        $assoc = array();
        foreach ($items as $item) {
            $ikey = $item->$key;
            if ($unset_key) {
                unset($item->$key);
            }
            if (isset($assoc[$ikey])) {
                $assoc[$ikey][] = $item;
            } else {
                $assoc[$ikey] = array($item);
            }
        }
        return $assoc;
    }


}
