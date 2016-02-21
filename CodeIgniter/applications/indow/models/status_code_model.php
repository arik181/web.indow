<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class status_code_model extends MM_Model
{
    protected $_table      = 'status_codes';
    public function get_codes_list($code_as_index = false, $all_option=false, $gte=null, $lte=null, $in=null, $open=false) {
        if ($gte) {
            $this->db->where('code >=', $gte);
        }
        if ($lte) {
            $this->db->where('code <=', $lte);
        }
        if ($in) {
            $this->db->where_in('code', $in);
        }
        $codes = $this->db->order_by('code')->get('status_codes')->result();
        if ($all_option) {
            $codes_assoc = array('' => $all_option === true ? 'Show All Orders' : $all_option);
        } else {
            $codes_assoc = array();
        }
        if ($open) {
            $codes_assoc['open'] = 'Show Open Orders';
        }
        foreach ($codes as $code) {
            if ($code_as_index) {
                $key = $code->code;
            } else {
                $key = $code->id;
            }
            $codes_assoc[$key] ='(' . $code->code . ')&nbsp;&nbsp;&nbsp;' . $code->description; 
        }
        return $codes_assoc;
    }

    public function get_code_id($code) {
        return $this->db->where('code', $code)->get('status_codes')->row()->id;
    }

    public function get_code_by_id($id) {
        return $this->db->where('id', $id)->get('status_codes')->row()->code;
    }

    public function code_options() {
        $codes = array();
        $dbc = $this->db->select('code, description')->order_by('code')->get('status_codes')->result();
        foreach ($dbc as $c) {
            $codes[$c->code] = ' (' . $c->code . ')  ' .  $c->description;
        }
        return $codes;
    }
}
