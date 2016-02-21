<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Migration_remove_history_key_from_estimates extends CI_Migration
{
    public function up()
    {
        $this->db->trans_start();

        $this->db->query("ALTER TABLE estimates DROP FOREIGN KEY `fk_estimates_history1`");

        $this->db->trans_complete();
    }
}
