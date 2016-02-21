<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Api_auth
{
    var $CI;
    var $session_data;
    private $_logout;

    var $session_id;
    var $last_activity;
    var $user_id;

    var $error;
    var $status;

    // --------------------------------------------------------------------

    /**
     * Api_auth constructor
     *
     * @access	public
     * @param   array
     * @return	void
     */
    function __construct($post)
    {
        // Allows use of CI libraries
        $this->CI =& get_instance();

        // check if session is set
        if ( isset($post['session']) )
        {
            $this->session_id       = $post['session']['id'];
            $this->user_id          = $post['session']['user_id'];
            $this->last_activity    = $post['session']['last_activity'];
        }

        // Check if session id is set.
        if ($this->session_id)
        {
            // Check if valid session id.
            if ($this->logged_in())
                $this->status = 'Session ID is valid.';

            else
            {
                $this->error = 'Invalid session ID. Please login again.';
                return;
            }

            // Check if logout is requested.
            if ($this->_logout) {
                $this->destroy_session();
                break;
            }
        }

        elseif ( isset($post['email']) && isset($post['password']) )
            $this->login($post['email'], $post['password']);

        else
            $this->error = 'Invalid login information. Please try again.';
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
    public function login($email, $password)
    {
        $query = $this->CI->db->select('id, password, salt')
                          ->where('email', $email)
                          ->limit(1)
                          ->get('users');

        $hash_password_db = $query->row();

        if ($query->num_rows() !== 1)
        {
            $this->error = 'Invalid login information. Please try again.';
            return;
        }

        // Update value if salt length has change in ION Auth config
        $salt         = substr($hash_password_db->password, 0, 10);
        $db_password  = $salt . substr(sha1($salt . $password), 0, -10);

        if ($db_password == $hash_password_db->password)
        {
            $this->user_id = $hash_password_db->id;
            $this->set_session();
            $this->status = 'User is logged in.';
        }
        else
            $this->error = 'Invalid login information. Please try again.';
    }

    // --------------------------------------------------------------------

    /**
     * Generate a new session
     *
     * @access	private
     * @return	void
     */
    private function generate_session()
    {
        $sessid = '';
        while (strlen($sessid) < 32)
        {
            $sessid .= mt_rand(0, mt_getrandmax());
        }

        // To make the session ID even more secure we'll combine it with the user's IP
        $sessid .= $this->CI->input->ip_address();

        $custom_data = array(
            'user_id' => $this->user_id,
        );

        $this->session_data = array(
            'session_id'    => md5(uniqid($sessid, TRUE)),
            'ip_address'    => $this->CI->input->ip_address(),
            'user_agent'    => substr($this->CI->input->user_agent(), 0, 120),
            'last_activity' => time(),
            'user_data'     => $this->_serialize($custom_data)
        );
    }

    // --------------------------------------------------------------------

    /**
     * Set session in DB
     *
     * @access	private
     * @return	void
     */
    private function set_session()
    {
        $this->generate_session();

        $this->CI->db->query($this->CI->db->insert_string('api_sessions', $this->session_data));
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
        $this->generate_session();

        // Update session_id and last_activity in DB
        $query = $this->CI->db->where('session_id', $this->session_id)
                              ->limit(1)
                              ->update('api_sessions', $this->session_data);
    }

    // --------------------------------------------------------------------

    /**
     * Destroy session in DB
     *
     * @access	private
     * @return	void
     */
    private function destroy_session()
    {
        $this->CI->db->query('
            DELETE FROM         api_sessions
            WHERE               session_id = "'.$this->session_id.'"
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