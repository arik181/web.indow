<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Api_auth_new
{
    /**
     * Api_auth constructor
     *
     * @access	public
     * @param   array
     * @return	void
     */
    function __construct()
    {
        // Allows use of CI libraries
        $this->CI =& get_instance();
    }

    // --------------------------------------------------------------------

    /**
     * Login a user
     *
     * @access	private
     * @param   string
     * @param   string
     * @return	void
     */
    public function login($email, $password,$iosToken=null,$androidToken=null)
    {
        $query = $this->CI->db->query('
            SELECT          `users`.`id`,
                            `users`.`password`,
                            `users`.`salt`,
                            `groups`.`name` AS `type`
            FROM            `users`
            INNER JOIN      `users_groups` ON `users_groups`.`user_id` = `users`.`id`
            INNER JOIN      `groups` ON `groups`.`id` = `users_groups`.`group_id`
            WHERE           `users`.`email` = "'.$email.'"
            LIMIT           1
        ');

        // username not found
        if ($query->num_rows() !== 1)
        {
            return array(
                'status' => array(
                    'condition' => 'error',
                    'message'   => 'Invalid login information. Please try again.'
                )
            );
        }

        $db_password_hash   = $query->row()->password;
        $user_id            = $query->row()->id;
        $user_type          = $query->row()->type;

        // Update value if salt length has change in ION Auth config
        $salt           = substr($db_password_hash, 0, 10);
        $password_hash  = $salt . substr(sha1($salt . $password), 0, -10);

        // password matches
        if ( $password_hash == $db_password_hash && $user_type != 'loan-officer' && $user_type != 'admin' )
        {
            $session_data = $this->set_session($user_id);
            if($iosToken!=null)
            {
                $this->CI->db->query('
                    UPDATE          `users`
                    SET             `users`.`device_token`="' . $iosToken . '"
                    WHERE           `users`.`email` = "'.$email.'"
                ');
            }
            if($androidToken!=null)
            {
                $this->CI->db->query('
                    UPDATE          `users`
                    SET             `users`.`android_token`="' . $androidToken . '"
                    WHERE           `users`.`email` = "'.$email.'"
                ');
            }
            return array(
                'status' => array(
                    'condition' => 'ok',
                    'message'   => 'Login successful.'
                ),
                'session' => array(
                    'user_id'       => $user_id,
                    'id'            => $session_data['session_id'],
                    'last_activity' => $session_data['last_activity'],
                    'user_type'     => $user_type
                )
            );
        }

        // password incorrect
        else
        {
            return array(
                'status' => array(
                    'condition' => 'error',
                    'message'   => 'Invalid login information. Please try again.'
                )
            );
        }
    }

    // --------------------------------------------------------------------

    /**
     * Generate a new session
     *
     * @access  public
     * @param   String
     * @return  void
     */
    public function verify_session($session_id)
    {
        $query = $this->CI->db->select('user_id')
                              ->where('session_id', $session_id)
                              ->limit(1)
                              ->get('api_sessions');

        if ($query->num_rows() !== 1)
            return false;

        return $query->row()->user_id;
    }

    // --------------------------------------------------------------------

    /**
     * Generate a new session
     *
     * @access	private
     * @return	void
     */
    private function generate_session($user_id)
    {
        $session_id = '';

        while ( strlen($session_id) < 32 )
        {
            $session_id .= mt_rand( 0, mt_getrandmax() );
        }

        // To make the session ID even more secure we'll combine it with the user's IP
        $session_id .= $this->CI->input->ip_address();

        $custom_data = array(
            'user_id' => $user_id,
        );

        return array(
            'session_id'    => md5( uniqid($session_id, TRUE) ),
            'user_id'       => $user_id,
            'ip_address'    => $this->CI->input->ip_address(),
            'user_agent'    => substr($this->CI->input->user_agent(), 0, 120),
            'last_activity' => time(),
            'user_data'     => $this->_serialize($custom_data)
        );
    }

    // --------------------------------------------------------------------

    /**
     * Set new session
     *
     * @access	private
     * @return	void
     */
    private function set_session($user_id)
    {
        $session_data = $this->generate_session($user_id);

        $this->CI->db->query(
            $this->CI->db->insert_string('api_sessions', $session_data)
        );

        return $session_data;
    }

    // --------------------------------------------------------------------

    /**
     * Update session in DB
     *
     * @access	private
     * @return	void
     */
    private function update_session()
    {
        $session_data = $this->generate_session();

        // Update session_id and last_activity in DB
        $query = $this->CI->db->where('session_id', $session_data['session_id'])
                              ->limit(1)
                              ->update('api_sessions', $session_data['session_data']);
    }

    // --------------------------------------------------------------------

    /**
     * Destroy session in DB
     *
     * @access	private
     * @return	void
     */
    private function destroy_session($session_id)
    {
        $this->CI->db->query('
            DELETE FROM         api_sessions
            WHERE               session_id = "'.$session_id.'"
        ');
    }

    // --------------------------------------------------------------------

    /**
     * Check if user is logged in currently
     *
     * @access	private
     * @return	bool
     */
    private function logged_in()
    {
        // Search DB for current session_id
        $query = $this->CI->db->where('session_id', $this->session_id)
                              ->limit(1)
                              ->get('api_sessions');

        // Return false if key not found
        if ($query->num_rows() !== 1)
        {
            return FALSE;
        }

        $user_data = $this->_unserialize($query->row()->user_data);

        if ($user_data['user_id'] != $this->user_id)
        {
            return FALSE;
        }

        // Update session information each time
        //$this->update_session();

        return TRUE;
    }

	// --------------------------------------------------------------------

	/**
	 * Serialize an array
	 *
	 * This function first converts any slashes found in the array to a temporary
	 * marker, so when it gets unserialized the slashes will be preserved
	 *
	 * @access	private
	 * @param	array
	 * @return	string
	 */
	private function _serialize($data)
	{
		if (is_array($data))
		{
			foreach ($data as $key => $val)
			{
				if (is_string($val))
				{
					$data[$key] = str_replace('\\', '{{slash}}', $val);
				}
			}
		}
		else
		{
			if (is_string($data))
			{
				$data = str_replace('\\', '{{slash}}', $data);
			}
		}

		return serialize($data);
	}

	// --------------------------------------------------------------------

	/**
	 * Unserialize
	 *
	 * This function unserializes a data string, then converts any
	 * temporary slash markers back to actual slashes
	 *
	 * @access	private
	 * @param	array
	 * @return	string
	 */
	private function _unserialize($data)
	{
		$data = @unserialize(strip_slashes($data));

		if (is_array($data))
		{
			foreach ($data as $key => $val)
			{
				if (is_string($val))
				{
					$data[$key] = str_replace('{{slash}}', '\\', $val);
				}
			}

			return $data;
		}

		return (is_string($data)) ? str_replace('{{slash}}', '\\', $data) : $data;
	}
}

/* End of file api_auth.php */
/* Location: ./application/libraries/api_auth.php */