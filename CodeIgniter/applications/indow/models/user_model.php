<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

    class User_model extends MM_Model
    {
        protected $_table = 'users';
        protected $soft_delete = true;
        protected $_key = 'id';

        public $id;
        public $ip_address;
        public $first_name;
        public $last_name;
        public $username;
        public $password;
        public $salt;
        public $activation_code;
        public $forgotten_password_code;
        public $forgotten_password_time;
        public $remember_code;
        public $created_on;
        public $last_login;
        public $active;
        public $organization_name;
        public $bio;
        public $ios_token;
        public $android_token;
        public $email_1;
        public $email_type_1;
        public $email_2;
        public $email_type_2;
        public $phone_1;
        public $phone_type_1;
        public $phone_2;
        public $phone_type_2;
        public $company_id;
        public $certified;
        public $deleted;
        public $disabled;
        public $email;

        public function __construct()
        {
            parent::__construct();
            $key = $this->_key;
            if ($this->$key)
                return;
        }

        public function defaultSettings()
        {
            $user                    = new stdClass();
            $user->ip_address        = '';
            $user->first_name        = '';
            $user->last_name         = '';
            $user->username          = '';
            $user->password          = '';
            $user->active            = 1;
            $user->organization_name = '';
            $user->bio               = '';
            $user->email_1           = '';
            $user->email_type_1      = '';
            $user->email_2           = '';
            $user->email_type_2      = '';
            $user->phone_1           = '';
            $user->phone_type_1      = '';
            $user->phone_2           = '';
            $user->phone_type_2      = '';
            $user->address_1         = '';
            $user->address_1_ext     = '';
            $user->address_2         = '';
            $user->address_2_ext     = '';
            $user->city_1            = '';
            $user->city_2            = '';
            $user->state_1           = '';
            $user->state_2           = '';
            $user->zipcode_1         = '';
            $user->zipcode_2         = '';
            $user->company_id        = 1;
            $user->certified         = 0;
            $user->deleted           = 0;
            $user->disabled          = 0;

            return $user;
        }

        public function save()
        {
            $sql = "";
            if ($this->id > 0) // update operation
            {
                $sql = "UPDATE      $this->_table
                    SET         ip_address = ?,
                                first_name = ?,
                                last_name = ?,
                                username = ?,
                                password = ?,
                                salt = ?,
                                activation_code = ?,
                                forgotten_password_code = ?,
                                forgotten_password_time = ?,
                                remember_code = ?,
                                created_on = ?,
                                last_login = ?,
                                active = ?,
                                organization_name = ?,
                                bio = ?,
                                ios_token = ?,
                                android_token = ?,
                                email_1 = ?,
                                email_type_1 = ?,
                                email_2 = ?,
                                email_type_2 = ?,
                                phone_1 = ?,
                                phone_type_1 = ?,
                                phone_2 = ?,
                                phone_type_2 = ?,
                                company_id = ?,
                                certified = ?,
                                deleted = ?,
                                disabled = ?,
                                email = ?
                    WHERE       id = ?
                   ;
                   ";
            } else //insert operation
            {
                $history = new History_model();
                $history->save();
                $this->history_id = $history->id;
                $sql              = "INSERT INTO $this->_table
                    SET         ip_address = ?,
                                first_name = ?,
                                last_name = ?,
                                username = ?,
                                password = ?,
                                salt = ?,
                                activation_code = ?,
                                forgotten_password_code = ?,
                                forgotten_password_time = ?,
                                remember_code = ?,
                                created_on = ?,
                                last_login = ?,
                                active = ?,
                                organization_name = ?,
                                bio = ?,
                                ios_token = ?,
                                android_token = ?,
                                email_1 = ?,
                                email_type_1 = ?,
                                email_2 = ?,
                                email_type_2 = ?,
                                phone_1 = ?,
                                phone_type_1 = ?,
                                phone_2 = ?,
                                phone_type_2 = ?,
                                company_id = ?,
                                certified = ?,
                                deleted = ?,
                                disabled = ?,
                                email = ?
                   ;
                   ";
            }

            $this->db->query($sql, array($this->id,
                $this->ip_address,
                $this->first_name,
                $this->last_name,
                $this->username,
                $this->password,
                $this->salt,
                $this->activation_code,
                $this->forgotten_password_code,
                $this->forgotten_password_time,
                $this->remember_code,
                $this->created_on,
                $this->last_login,
                $this->active,
                $this->organization_name,
                $this->bio,
                $this->ios_token,
                $this->android_token,
                $this->email_1,
                $this->email_type_1,
                $this->email_2,
                $this->email_type_2,
                $this->phone_1,
                $this->phone_type_1,
                $this->phone_2,
                $this->phone_type_2,
                $this->company_id,
                $this->certified,
                $this->deleted,
                $this->disabled,
                $this->email,
                $this->id));

            if ($this->id == 0)
                $this->id = $this->db->insert_id();

            return array('success' => true, 'message' => 'Updated user successfully.');
        }


        public function emailExists($email)
        {

            if ($this->count_by(array('email' => $email)) > 0) {
                return true;
            } else {
                return false;
            }

        }

        public function updateEmailCheck($email, $userID)
        {
            $this->db->select('*');
            $this->db->where('email', $email);
            $query = $this->db->get($this->_table);

            if (!empty($query) && $query->num_rows() > 0) {
                $row = $query->row();

                if ($row->id == $userID) {
                    return true;
                } else {
                    return false;
                }
            } else {
                return true;
            }
        }


        public function create_pass($user_id, $only_if_not_exists=false)
        {

            $user = $this->db->where('id', $user_id)->get('users')->row();
            if (!$user->password || !$only_if_not_exists) {
                $password = random_string('alnum', 10);
                $this->update_user_pass($user_id, $password, true);
                return array($user->email_1, $password);
            }else
            {
                return array($user->email_1, null);
            }

        }

        public function update_user_pass($user_id, $password, $activate = false)
        {
            $data = array();
            if ($activate) {
                $data['active'] = 1;
            }
            $data['password'] = $this->Ion_auth_model->hash_password($password);
            $this->db->where('id', $user_id)->update('users', $data);
        }

        public function set_pass_by_email($email, $password) {
            $password = $this->Ion_auth_model->hash_password($password);
            $this->db->where('email_1', $email)->update('users', array('password' => $password));
        }

        public function update_user($id, $data, $admin=false)
        {
            $this->form_validation->set_rules('first_name', 'First Name', 'required');
            $this->form_validation->set_rules('last_name', 'Last Name', 'required');
            if($this->input->post('username') === '') {
                $this->form_validation->set_rules('username', 'Username', 'required|callback_unique_username');
            }
            $this->form_validation->set_rules('email_1', 'Email', 'required|valid_email');
            if ($admin) {
                $this->form_validation->set_rules('groupid[]', 'Groups', 'required');
            }

            if (!empty($data['password'])) {
                //$this->form_validation->set_rules('password', 'Password', 'required|matches[confirm_password]');
                $this->form_validation->set_rules('password', 'Password', 'min_length[8]|callback_password_check|matches[confirm_password]');
                $this->form_validation->set_message('password_check', 'Passwords must contain at least one uppercase letter, one lowercase letter, and 1 digit.');
                $this->form_validation->set_rules('confirm_password', 'Confirm Password', 'required');
            }


            $email_check = $this->emailEdit_check($data['email_1'],$id);

            if ($this->form_validation->run() == true AND $email_check === true) {

                if (!empty($data['password'])) {
                    $data['password'] = $this->Ion_auth_model->hash_password($data['password']);
                }
                if (!$admin) {
                    unset($data['username']);
                }

                $address1fields = array(
                    'address_1'     => 'address',
                    'address_1_ext' => 'address_ext',
                    'city_1'        => 'city',
                    'state_1'       => 'state',
                    'zipcode_1'     => 'zipcode',
                );
                $address2fields = array(
                    'address_2'     => 'address',
                    'address_2_ext' => 'address_ext',
                    'city_2'        => 'city',
                    'state_2'       => 'state',
                    'zipcode_2'     => 'zipcode',
                );



                $address1update = false;
                $address2update = false;
                $address1       = array('address_type' => 'address1', 'user_id' => $id);
                $address2       = array('address_type' => 'address2', 'user_id' => $id);

                //permissions
                if ($admin) {
                    $this->db->where('user_id', $id)->delete('user_feature_permissions');
                    $this->db->where('user_id', $id)->delete('users_groups');
                }
                $count = 1;
                while (isset($data['toolname_' . $count])) {
                    $perm = array(
                        'user_id' => $id,
                        'feature_id' => $data['toolname_' . $count],
                        'permission_level_id'   => $data['permlevel_' . $count],
                    );
                    if ($admin) {
                        $this->db->insert('user_feature_permissions', $perm);
                    }
                    unset($data['toolname_' . $count]);
                    unset($data['permlevel_' . $count]);
                    $count++;
                }
                if ($admin) {
                    foreach ($data['groupid'] as $gid) {
                        $perm = array(
                            'user_id'  => $id,
                            'group_id' => $gid,
                        );
                        $this->db->insert('users_groups', $perm);
                    }
                }

                foreach ($data as $key => $value) {
                    if (isset($address1fields[$key])) {
                        $address1update                  = true;
                        $address1[$address1fields[$key]] = $value;
                    } elseif (isset($address2fields[$key])) {
                        $address2update                  = true;
                        $address2[$address2fields[$key]] = $value;
                    } else {
                        if ($key !== 'submit'
                            && $key !== 'confirm_password'
                            && ($value !== '' || $key === 'permission_set')
                        ) {
                            $user[$key] = $value;
                        }
                    }
                }

                if ($admin) {
                    if (!isset($user['disabled'])) {
                        $user['disabled'] = 0;
                        $user['active'] = 1;
                    } else {
                        $user['active'] = 0;
                    }
                    if (!isset($user['certified'])) {
                        $user['certified'] = 0;
                    }
                } else {
                    unset($user['disabled']);
                    unset($user['active']);
                }

                unset($user['groupid']);
                if (isset($user['permission_set']) && $user['permission_set'] === '') {
                    $user['permission_set'] = null;
                }

                $this->update($id, $user);

                $addressq = $this->db->from('user_addresses')->where('user_id', $id)->where_in('address_type', array('address1', 'address2'))->get();

                $types    = array();
                foreach ($addressq->result() as $row) {
                    $types[$row->address_type] = true;
                }

                if ($address1update) {
                    if (isset($types['address1'])) {
                        $this->db->where('user_id', $id)->where('address_type', 'address1');
                        $this->db->update('user_addresses', $address1);
                    } else {
                        $this->db->insert('user_addresses', $address1);
                    }
                }
                if ($address2update) {
                    if (isset($types['address2'])) {
                        $this->db->where('user_id', $id)->where('address_type', 'address2');
                        $this->db->update('user_addresses', $address2);
                    } else {
                        $this->db->insert('user_addresses', $address2);
                    }
                }
                return array('success' => true, 'message' => 'Updated user successfully.');

            }
            else
            {
                $additional_message = $email_check !== true ? $email_check : validation_errors();
                return array('success' => false, 'message' => validation_errors() . $additional_message);
            }


        }

        public function callback_password_check($str)
        {
            return true;
        }

        public function fetch_admin_users($limit, $start)
        {
            $sql = "SELECT users.id,
                users.first_name,
                users.last_name,
                users.email,
                users.last_login,
                users.active
                FROM `users`
                JOIN user_permissions ON users.id = user_permissions.user_id
                WHERE users.deleted = 0 AND user_permissions.permissions_id = 1
                LIMIT $start , $limit";

            $query = $this->db->query($sql);

            if (!empty($query) && $query->num_rows() > 0) {
                foreach ($query->result() as $row) {

                    if ($row->active == 0) {
                        $row->active = 'inactive';
                    } else {
                        $row->active = 'active';
                    }

                    $data[] = $row;
                }

                return $data;

            }

            return false;

        }

        public function getProfile($id, $includeAddress = true, $includePerm = true)
        {

            $user = $this->get($id);

            if (is_object($user)) {

                if ($includeAddress) {

                    $addresses = $this->db->get_where('user_addresses', array('user_id' => $id))->result();

                    if (!empty($addresses)) {

                        foreach ($addresses as $row) {

                            if ($row->address_type == 'address1') {
                                $user->address_1     = $row->address;
                                $user->address_1_ext = $row->address_ext;
                                $user->city_1        = $row->city;
                                $user->state_1       = $row->state;
                                $user->zipcode_1     = $row->zipcode;
                            } else {
                                $user->address_2     = $row->address;
                                $user->address_2_ext = $row->address_ext;
                                $user->city_2        = $row->city;
                                $user->state_2       = $row->state;
                                $user->zipcode_2     = $row->zipcode;
                            }

                        }
                    }
                }

                if ($includePerm) {
                    $this->db->where('user_id', $id);
                    $user->perms = $this->db->get('user_feature_permissions')->result();
                }

                return $user;

            }
            else
            {
                return false;
            }


        }

        public function get_simple_users($userids)
        {
            if (empty($userids)) {
                return array();
            }
            $users  = $this->db->where_in('id', $userids)->where('deleted', 0)->get('users')->result();
            $simple = array();
            foreach ($users as $user) {
                $simple[] = array(
                    'id'        => $user->id,
                    'full_name' => $user->first_name . ' ' . $user->last_name,
                    'username'  => $user->username,
                );
            }

            return $simple;
        }

        public function simple_group_users($groupid)
        {
            $this->db->select('users.first_name, users.last_name, users.id, users.username, users.active');
            $this->db->from('users_groups');
            $this->db->join('users', 'users_groups.user_id = users.id');
            $users  = $this->db->where('users_groups.group_id', $groupid)->where('users.deleted', 0)->get()->result();
            $simple = array();
            foreach ($users as $user) {
                $simple[] = array(
                    'id'        => $user->id,
                    'full_name' => $user->first_name . ' ' . $user->last_name,
                    'username'  => $user->username,
                    'active'    => $user->active
                );
            }

            return $simple;
        }

        public function fetch_users($limit, $start)
        {
            $sql = 'SELECT  *
                FROM   `users`
                WHERE   users.deleted = 0
                LIMIT   ?, ?';
            //WHERE   users.deleted = 0 AND user_permissions.permission_id = 1

            $start = (int)$start;
            $limit = (int)$limit;
            $query = $this->db->query($sql, array($start, $limit));

            if (!empty($query) && $query->num_rows() > 0) {
                foreach ($query->result() as $row) {
                    if ($row->active == 0) {
                        $row->active = 'inactive';
                    } else {
                        $row->active = 'active';
                    }

                    $data[] = $row;
                }

                return $data;

            }

            return false;

        }

        public function rep_list() {
            return $this->simple_list(array(1));
        }

        public function associated_rep_list($group_id)
        {
            $users = $this->db
                    ->select("users.id, CONCAT(first_name, ' ', last_name) AS name", false)
                    ->join('users', 'user_id=users.id')
                    ->where('users.deleted', 0)
                    ->where('group_id', $group_id)
                    ->get('users_groups')
                    ->result();
            $ret = array();
            foreach ($users as $u) {
                $ret[$u->id] = $u->name;
            }
            return $ret;
        }

        public function simple_rep_list($user_id=null)
        {
            $sql = "
                SELECT first_name, last_name, users.id
                FROM users
                INNER JOIN users_groups ON users.id = users_groups.user_id
                INNER JOIN groups ON groups.id = users_groups.group_id
                WHERE groups.id = (SELECT users.company_id FROM users WHERE users.id = ?)
                ORDER BY first_name, last_name
                ;
            ";
            
            $query  = $this->db->query($sql, $user_id);
            $users  = $query->result();

            $usersa = array('' => '-- Select One --');
            foreach ($users as $user) {
                if (empty($user->first_name) && empty($user->last_name)) {
                    $usersa[$user->id] = 'Un-named';
                } else {
                    $usersa[$user->id] = $user->first_name . ' ' . $user->last_name;
                }
            }

            return $usersa;
        }

        public function simple_list($group_ids=null, $user_id=null)
        {
            $addWhere = '';
            if ($group_ids) {
                if ($user_id) {
                    $addWhere = 'AND (group_id IN (' . implode(',', $group_ids) . ') OR users.id=' . $user_id . ')';
                } else {
                    $addWhere = 'AND group_id IN (' . implode(',', $group_ids) . ')';
                }
            }
            $this->db->join('users_groups', 'users.id=users_groups.user_id');
            $users = $this->db->select('first_name, last_name, users.id')->join('customers', 'customers.user_id = users.id', 'left')->where('users.active', 1)->where('customers.user_id IS NULL ' . $addWhere)->order_by('first_name, last_name')->get('users')->result();
            $usersa = array('' => '-- Select One --');
            foreach ($users as $user) {
                if (empty($user->first_name) && empty($user->last_name)) {
                    $usersa[$user->id] = 'Un-named';
                } else {
                    $usersa[$user->id] = $user->first_name . ' ' . $user->last_name;
                }
            }

            return $usersa;
        }

        public function get_users_list()
        {
            $userq = $this->db->order_by('first_name, last_name')->get_where('users', array('active' => '1', 'deleted' => '0', 'is_customer' => 0));
            $users = array('' => '');
            foreach ($userq->result() as $row) {
                $users[$row->id] = $row->first_name . ' ' . $row->last_name;
            }

            return $users;
        }

        public function get_rep_users_list()
        {
            $userq = $this->db->order_by('first_name, last_name')->join('users_groups', 'users.id=user_id')->get_where('users', array('active' => '1', 'deleted' => '0', 'is_customer' => 0, 'group_id' => 1));
            $users = array('' => '');
            foreach ($userq->result() as $row) {
                $users[$row->id] = $row->first_name . ' ' . $row->last_name;
            }

            return $users;
        }

        public function delete($id)
        {

            $this->db->trans_start();
            $sql   = "DELETE FROM user_feature_permissions WHERE user_id = ? ;";
            $query = $this->db->query($sql, $id);
            $this->user_group_model->delete_many_by(array('user_id' => $id));
            $this->update($id,array('deleted' => 1, 'disabled' => 1));
            $this->db->trans_complete();

            return true;
        }

        public function user_exists($username)
        {
            $this->db->select('id');
            $this->db->where('username', $username);
            $query = $this->db->get('users');

            if ($query->num_rows() > 0) {
                return $query->row()->id;
            } else {
                return false;
            }
        }

        public function add_user($data)
        {
            $this->form_validation->set_rules('first_name', 'First Name', 'required');
            $this->form_validation->set_rules('last_name', 'Last Name', 'required');
            $this->form_validation->set_rules('username', 'Username', 'required|callback_unique_username');
            $this->form_validation->set_rules('confirm_password', 'Confirm Password', 'required');
            $this->form_validation->set_rules('password', 'Password', 'min_length[8]|callback_password_check|matches[confirm_password]');
            $this->form_validation->set_message('password_check', 'Passwords must contain at least one uppercase letter, one lowercase letter, and 1 digit.');
            $this->form_validation->set_rules('email_1', 'Email', 'required|valid_email|callback_emailEdit_check');
            $this->form_validation->set_rules('groupid[]', 'Groups', 'required');

            if (!empty($data['password'])) {
                //$this->form_validation->set_rules('password', 'Password', 'required|matches[confirm_password]');
                //$this->form_validation->set_rules('password', 'Password', 'callback_password_check');
                $this->form_validation->set_rules('confirm_password', 'Password Confirmation', 'required');
            }

            if ($this->form_validation->run() == true)
            {
                if (!empty($data['password'])) {
                    $data['password'] = $this->ion_auth->hash_password($this->input->post('password'));
                }

                foreach ($data as $key => $value)
                {
                    if ($key !== 'submit'
                        && $value !== ''
                    ) {
                        $user[$key] = $value;
                    }
                }

                $user = array();

                $user_keys = array(
                    'company_id',
                    'first_name',
                    'last_name',
                    'username',
                    'password',
                    'email_1',
                    'email_type_1',
                    'phone_1',
                    'phone_type_1',
                    'email_2',
                    'email_type_2',
                    'phone_2',
                    'phone_type_2',
                    'salt',
                    'disabled'
                );

                foreach ($user_keys as $key) {
                    if (isset($data[$key]) && $data[$key] !== '') {
                        $user[$key] = $data[$key];
                    }
                }

                if (empty($user['disabled']))
                {
                    $user['disabled'] = 0;
                    $user['active']   = 1;
                }
                else
                {
                    $user['disabled'] = 1;
                    $user['active'] = 0;
                }

                if (!isset($user['certified'])){
                    $user['certified'] = 0;
                }

                $userid   = $this->insert($user);


                $address1 = array(
                    'user_id'      => $userid,
                    'address_type' => 1,
                    'address'      => $data['address_1'],
                    'address_ext'  => $data['address_1_ext'],
                    'city'         => $data['city_1'],
                    'state'        => $data['state_1'],
                    'zipcode'      => $data['zipcode_1'],
                );


                $address2 = array(
                    'user_id'      => $userid,
                    'address_type' => 2,
                    'address'      => $data['address_2'],
                    'address_ext'  => $data['address_2_ext'],
                    'city'         => $data['city_2'],
                    'state'        => $data['state_2'],
                    'zipcode'      => $data['zipcode_2'],
                );

                $this->db->insert('user_addresses', $address1);
                $this->db->insert('user_addresses', $address2);

                //permissions
                $count = 1;
                while (isset($data['toolname_' . $count])) {
                    $perm = array(
                        'user_id' => $userid,
                        'feature_id' => $data['toolname_' . $count],
                        'permission_level_id'   => $data['permlevel_' . $count],
                    );
                    unset($data['toolname_' . $count]);
                    unset($data['permlevel_' . $count]);
                    $this->db->insert('user_feature_permissions', $perm);
                    $count++;
                }

                foreach ($data['groupid'] as $gid) {
                    $perm = array(
                        'user_id'  => $userid,
                        'group_id' => $gid,
                    );
                    $this->db->insert('users_groups', $perm);
                }

            } else {
                return array('success' => false, 'message' => validation_errors());
            }

            return array('success' => true, 'message' => 'Added user successfully.');
        }

        public function name_search($string, $require_customer=1)
        {
            $parts = preg_split('/ /', $string);
            if ($require_customer) {
                $this->db->select('users.id, first_name, last_name, email_1, IF(address IS NULL, 0, 1) as has_address', false);
            } else {
                $this->db->select('users.id, first_name, last_name, email_1', false);
            }
            $this->db->from('users');
            if ($require_customer) {
                $this->db->join('customers', 'customers.user_id = users.id');
                $this->db->join('user_addresses', "user_addresses.user_id=users.id AND address != ''");
            }
            if (count($parts) === 1) {
                $first = $this->db->escape_str($parts[0]);
                $this->db->where("(first_name LIKE '%$first%' OR last_name LIKE '%$first%' OR email_1 LIKE '%$first%')");
            } else {
                $first = $this->db->escape_str($parts[0]);
                $last = $this->db->escape_str($parts[count($parts) - 1]);
                $this->db->where("(first_name LIKE '%$first%' OR last_name LIKE '%$last%')");
            }
            $this->db->where('users.deleted', 0);
            $where = $this->permissionslibrary->get_where_string(1, 'customer_referred_by', 'company_id');
            $this->db->where($where . ' && 1=', '1', false); //couldnt get codeigniter not to insert IS NULL after query string if second param left blank. dont think you can do raw query string if you have built ones as well.  the && 1=1 is ugly workaround
            $this->db->group_by('users.id');
            $this->db->limit(10);
            $query = $this->db->get();
          //  prd($this->db->last_query());
            return $query->result();
        }

        public function get_user_info($id, $includeAddress = true, $includePerm = true)
        {
            $sql = "
            SELECT  CONCAT(`first_name`,' ',`last_name`) as name,users.email_1 as email ,users.id, user_addresses.address, user_addresses.city, user_addresses.state, user_addresses.zipcode, users.phone_1
            FROM    users INNER JOIN user_addresses on users.id =  user_addresses.user_id
            WHERE   users.id = ?;
            ";

            $query = $this->db->query($sql, $id);

            if (!empty($query) && $query->num_rows() > 0) {
                $user = $query->row();

                return $user;
            } else {
                return false;
            }


        }

        public function get_dealer_id($user_id)
        {
            return $this->get_group_id($user_id);
        }

        public function get_group_id($user_id)
        {
            $row = $this->db->where('user_id', $user_id)->get('users_groups')->row();
            if (!$row) {
                return 0;
            }

            return $row->group_id;
        }
        public function get_group_name($user_id)
        {
            $row = $this->db->where('user_id', $user_id)->join('groups', 'groups.id=users_groups.group_id')->get('users_groups')->row();
            if (!$row) {
                return 0;
            }

            return $row->name;
        }

        public function my_group_name() {
            return $this->get_group_name($this->ion_auth->get_user_id());
        }

        public function get_user_name($user_id)
        {
            return $data['user_name'] = $this->db
                ->select("CONCAT(first_name, ' ', last_name) AS name", false)
                ->where('id', $user_id)
                ->get('users')->row()->name;
        }

        public function get_users_by_group_id($group_id)
        {

            $sql = "SELECT
                users.id,
                users.first_name,
                users.last_name,
                users.username,
                user_addresses.zipcode,
                user_addresses.id as address_id,
                groups.name as group_name,
                groups.id as group_id
                FROM users
                JOIN user_addresses ON user_addresses.user_id = users.id
                JOIN users_groups ON users_groups.user_id = users.id
                JOIN groups ON  groups.id = users_groups.group_id
                WHERE group_id = ? AND users.deleted = 0
                GROUP BY users.id ORDER BY users.last_name,users.first_name";

            return $this->db->query($sql, $group_id);


        }

        //--------------------------------------------------------------------
        // Form Validation Callbacks
        //--------------------------------------------------------------------


        public function emailEdit_check($email,$id)
        {
            $sql = "SELECT id FROM users LEFT JOIN customers ON customers.user_id=users.id WHERE users.deleted=0 AND customers.user_id IS NULL AND email_1 = ?";
            $result = $this->db->query($sql, $email)->row();

            if (!$result || $result->id == $id)
            {
                return true;
            }
            else
            {
                return "<p>The email address for address 1 is already taken.</p>";
            }

        }

        public function user_manager_get_user($ids, $primary = null) {
            if (!count($ids)) {
                return array();
            }

            $this->db->select('
            users.id AS id,
            groups.name AS group_name,
            users.first_name as first_name,
            users.last_name as last_name,
            users.username as username,
            user_addresses.zipcode as zipcode
        ');
            $this->db->from('users');
            $this->db->join('users_groups', 'users_groups.user_id = users.id', 'LEFT');
            $this->db->join('groups', 'groups.id = users_groups.group_id', 'LEFT');
            $this->db->join('user_addresses', "user_addresses.user_id = users.id AND user_addresses.address_type = 'address1'", 'LEFT');
            $this->db->where_in('users.id', $ids);
            $customers = $this->db->get()->result();

            $ret = array();
            foreach ($customers as $customer) {
                $ret[] = $customer;
            }
            return $ret;
        }

        public function get_sales_leads($user) {
            if (!count($user->group_ids)) {
                return array();
            }
            $group_id = $user->group_ids[0];
            $results = $this->db
                    ->select("
                        CONCAT(users.first_name, ' ', users.last_name) as customer,
                        (SELECT CONCAT(address, ' ', address_ext, '<br>', city, IF(city != '', ', ', ' ') , state, ' ', zipcode) FROM site_customers JOIN sites ON sites.id=site_customers.site_id WHERE customer_id=users.id ORDER BY `primary` DESC LIMIT 1) as site,
                        (SELECT site_id FROM site_customers WHERE customer_id=users.id ORDER BY `primary` DESC LIMIT 1) as site_id,
                        customers.user_id AS customer_id,
                        users.phone_1,
                        users.email_1,
                        FROM_UNIXTIME(users.created_on) as created
                    ", false)
                    ->from('customers')
                    ->join('users', 'users.id=customers.user_id')
                    ->where('company_id', $group_id)
                    ->where('users.deleted', 0)
                    ->where('opportunity_id >', 0)
                    ->where('users.created_on >=', time() - (60*60*24*30))
                    ->order_by('users.created_on DESC')
                    ->get()->result();
            return $results;
        }
    }
