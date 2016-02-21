<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class UserFactory extends MM_Factory
{
	protected $_model = "User_model";
	protected $_table = "users";
	protected $_primaryKey = "users.id";

    public function __construct()
    {
        parent::__construct();
	}

	public function getList($cid)
	{
        $sql = "SELECT          users.id,
                                COALESCE(groups.name,'Not Assigned') AS group_name,
                                users.first_name,
                                users.last_name,
                                users.username,
                                (
                                    CASE
                                       WHEN users.id = ? THEN 'users'
                                       ELSE
                                       'usersall'
                                    END
                                ) as check_user,
                                active
                                user_addresses.zipcode
                FROM            users
                LEFT JOIN       user_addresses
                ON              user_addresses.user_id = users.id AND user_addresses.address_type = 'address1'
                LEFT JOIN       users_groups as ugroup 
                ON              ugroup.user_id = users.id
                LEFT JOIN       groups
                ON              groups.id = ugroup.group_id
                WHERE           users.deleted = 0  AND ugroup.group_id IN (SELECT group_id FROM users_groups Where users_groups.user_id =  ?)
                ;
                ";

		$results = $this->db->query($sql,array($cid,$cid))->result();

		return $results;

	}

    public function getAdminList($cid)
    {
        $sql = "SELECT          users.id,
                                COALESCE(groups.name,'Not Assigned') AS group_name,
                                users.first_name,
                                users.last_name,
                                users.username,
                                (
                                    CASE
                                       WHEN users.id = ? THEN 'users'
                                       ELSE
                                       'usersall'
                                    END
                                ) as check_user,
                                user_addresses.zipcode,
                                active
                FROM            users
                LEFT JOIN       user_addresses
                ON              user_addresses.user_id = users.id AND user_addresses.address_type = 'address1'
                LEFT JOIN       users_groups as ugroup
                ON              ugroup.user_id = users.id
                LEFT JOIN       groups
                ON              groups.id = ugroup.group_id
                WHERE           users.deleted = 0 AND users.is_customer=0
                ;
                ";

        $results = $this->db->query($sql,array($cid,$cid))->result();

        return $results;

    }
}
