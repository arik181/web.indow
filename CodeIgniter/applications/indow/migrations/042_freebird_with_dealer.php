<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class Migration_freebird_with_dealer extends CI_Migration
{
    public function up()
    {
        $this->db->trans_start();
        $this->db->query("ALTER TABLE  `orders` ADD  `freebird` INT NOT NULL DEFAULT 0");
        $this->db->query("INSERT INTO  `features` (`id` ,`feature_key` ,`feature_display_name`) VALUES (14 ,  'selfmeasure',  'Self Measure')");
        $this->db->trans_complete();
    }
}

