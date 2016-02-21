<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class GroupFactory extends MM_Factory
{
	protected $_model = "Group_model";
	protected $_table = "groups";
	protected $_primaryKey = "groups.id";

    public function __construct()
    {
        parent::__construct();
	}

	public function getList()
	{
        $sql = "SELECT          groups.id,
                                groups.name AS group_name,
                                groups.email_1 AS email,
                                groups.phone_1 AS phone,
                                (SELECT count(*) FROM users_groups JOIN users ON users.id=user_id WHERE group_id=groups.id AND users.deleted != 1) as usercount, 
                                CONCAT(group_addresses.address,' ', group_addresses.address_ext) AS address
                FROM            groups
                LEFT JOIN       group_addresses
                ON              groups.id=group_addresses.group_id
                GROUP BY        groups.id
                ;
                ";

		$results = $this->db->query($sql)->result();

                /* TODO add group_id to users
		//This could be slow for large data sets, may need to switch to trigger or some other approach
		foreach ($results as $result)
		{
            $sql = "SELECT      count(users.id) as count
                    FROM        groups
                    INNER JOIN  users ON users.group_id = groups.id
                    WHERE       groups.id = ?
                ;
                ";
			$row = $this->db->query($sql,$result->id)->row();
			$result->user_count = $row->count;
        }
                 */
		return $results;
	}
    public function getIdsInHeirarchy($baseGroup)
    {
        $base = array($baseGroup);
        $sql = "SELECT id FROM groups WHERE parent_group_id = ?";
        $query = $this->db->query($sql,$baseGroup);
        if ($query->num_rows())
        {
            $result = $query->result();
            foreach ($result as $subgroup) 
            {
                $base = array_merge($base,$this->getIdsInHeirarchy($subgroup->id));
            }
        }
        return $base;
    }
}
