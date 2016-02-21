<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class Migration_user_preset_perm extends CI_Migration
{
    public function up()
    {
        $this->db->trans_start();
        $this->db->query("ALTER TABLE  `users` ADD  `permission_set` INT NULL");
        $this->db->trans_complete();
    }
}

