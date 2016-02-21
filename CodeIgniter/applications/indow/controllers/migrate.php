<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Migrate extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->dbforge();
        $this->load->library('migration');
    }

	public function index( $dummyData = false )
	{

		if (isset($_SERVER['HTTP_HOST']))
		{
			die("Not accessible via web, this must be run from the command line.");
		}

        $this->migration->version(1);
        $this->migration->version(2);
        $this->migration->version(3);
        $this->migration->version(4);
        $this->migration->version(5);
        $this->migration->version(6);

        echo "Successfully migrated to latest db version...\n";

		if ($dummyData)
		{
			echo "Inserting Dummy Data\n";
			$this->db->query("SET foreign_key_checks = 0");
			$sql = file_get_contents(getcwd() . "/../sql/dummy.sql");
			$commands = explode("\n", $sql);
			foreach ($commands as $command)
			{
                if (isset($command) && ! empty($command))
                    $this->db->query($command);
			}
			$this->db->query("SET foreign_key_checks = 1");
		}

        $this->migration->version(7);
        $this->migration->version(8);
        $this->migration->version(9);
        $this->migration->version(10);
        $this->migration->version(11);
        $this->migration->version(12);
        $this->migration->version(13);
        $this->migration->version(14);
        $this->migration->version(15);
        $this->migration->version(16);
        $this->migration->version(17);
        $this->migration->version(18);
        $this->migration->version(19);
        $this->migration->version(20);
        $this->migration->version(21);
        $this->migration->version(22);
        $this->migration->version(23);
        $this->migration->version(24);
        $this->migration->version(25);
        $this->migration->version(26);
        $this->migration->version(27);
        $this->migration->version(28);
        $this->migration->version(29);
        $this->migration->version(30);
        $this->migration->version(31);
        $this->migration->version(32);
        $this->migration->version(33);
        $this->migration->version(34);
        $this->migration->version(35);
        $this->migration->version(36);
        $this->migration->version(37);
        $this->migration->version(38);
        $this->migration->version(39);
        $this->migration->version(40);

        echo "Migration Complete\n";


	}
}
