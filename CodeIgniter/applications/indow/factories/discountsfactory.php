<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class DiscountsFactory extends MM_Factory
{
    protected $_model       = "sales_modifiers_model";
    protected $_table       = "sales_modifiers";
    protected $_primaryKey  = "sales_modifiers.id";

    public function __construct()
    {
        parent::__construct();
    }

    public function getList()
    {
        if (!$this->permissionslibrary->_user->in_admin_group) {
            $wholesalefilter = " AND modifier_type != 'wholesale'";
        } else {
            $wholesalefilter = '';
        }
        $now = date('Y-m-d 00:00:00');
        $sql = "SELECT          sales_modifiers.id,
                                groups.name as `group`,
                                sales_modifiers.modifier_type,
                                sales_modifiers.description,
                                sales_modifiers.code,
                                sales_modifiers.amount,
                                sales_modifiers.modifier,
                                sales_modifiers.start_date, 
                                sales_modifiers.end_date    
                FROM            sales_modifiers
                LEFT JOIN sales_modifiers_groups ON sales_modifiers_groups.sales_modifiers_id = sales_modifiers.id
                LEFT JOIN groups ON sales_modifiers_groups.groups_id = groups.id
                
                WHERE           sales_modifiers.deleted = 0 
                AND             
                (  sales_modifiers.end_date >= '".$now."' 
                OR sales_modifiers.end_date IS NULL ) AND single_use=0 AND " . $this->permissionslibrary->get_where_string(13, 'sales_modifiers.created_by', 'groups.id') . $wholesalefilter;

                //AND             (sales_modifiers.end_date >= '".$now."' OR sales_modifiers.end_date='')
        $results = $this->db->query($sql)->result();
        foreach ($results as $result)
        {
            $result->modifier_type =  ucfirst($result->modifier_type);

            if($result->modifier == 'percent')
            {
                $result->amount =  $result->amount . '&#37;';
            }
            else
            {
                $result->amount =  '&#36;' . $result->amount;
            }

            if ($result->start_date == ''){
                $result->start_date = "N/A";
            }else{
                $result->start_date = date("m/d/Y", strtotime($result->start_date));
            }
            if ($result->end_date == ''){
                $result->end_date = "N/A";
            }else{
                $result->end_date = date("m/d/Y",strtotime($result->end_date));
            }
                        
        }
        return $results;
    }
}
