<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Migration_Remove_Extra_Item_Status extends CI_Migration
{
    public function up()
    {
        $this->db->trans_start();

        //$this->db->query("ALTER TABLE  `items` DROP  `status` ;");
        $this->db->query("ALTER TABLE  `items` ADD INDEX (  `manufacturing_status` );");

        $this->db->trans_complete();
    }
}
