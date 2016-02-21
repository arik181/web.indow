<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

    /**
     * Class Sessions
     */
    class Sessions extends MM_Controller
    {


        protected $_message;

        public function __construct()
        {
            parent::__construct();

            if (@!$this->session->flashdata('message') == FALSE)
            {
                $this->_message = $this->session->flashdata('message');
                $this->_fmessage = $this->session->flashdata('message');
            }
            else
            {
                $this->_message = 'Please Log In';
            }

            if (@!$this->session->flashdata('message') == false) {
                $this->_fmessage = $this->session->flashdata('message');
            } else {
                $this->_fmessage = false;
            }

        }

        public function create_get()
        {
            $customer = $this->input->cookie('cust');
            $data = array(
                'title'   => 'Log In',
                'message' => $this->_message,
                'content_view' => 'themes/login/login',
                'fmessage' => $this->_fmessage,
                'customer'  => $customer
            );
            header('X-INDOW-LOGIN: 1');
            $this->load->view('themes/login/display', $data);
        }


        public function create_post()
        {
            if ($this->Ion_auth_model->login($this->post('identity'), $this->post('password')))
            {
                $redirect = $this->session->userdata('redirect'); //this should be flash data, however, flash data strangely is lost between login_get and login_post. - almost like there's a redirect loop, but i cant find it
                if ($redirect)
                {
                    $this->session->unset_userdata('redirect');
                    redirect($redirect);
                }
                else
                {
                    redirect('/');
                }
            }
            else
            {
                $this->session->keep_flashdata('redirect');
                $this->session->set_flashdata('message', 'Username or Password Incorrect');
                redirect('login');
            }

        }

        public function destroy_get($destroy_user_type=false)
        {
            $customer = $this->input->cookie('cust');
            $this->ion_auth->logout();
            if ($destroy_user_type) {
                $this->load->helper('cookie');
                delete_cookie('cust');
            }
            $this->session->set_flashdata('message', 'You have been logged out');
            redirect('login');
        }

        public function reset_pass_post() {
            $this->load->model('email_model');
            $email = $this->input->post('email');
            if (!empty($email)) {
                $user = $this->db->where('email_1', $email)->get('users')->row();
                $reset_code = $this->Ion_auth_model->salt() . $this->Ion_auth_model->salt();
                $reset_code = str_replace('/', '', $reset_code);
                if ($user) {
                    $reset = array(
                        'email' => $email,
                        'reset_code' => $reset_code,
                        'created' => time()
                    );
                    $this->db->where('email', $email)->delete('password_reset');
                    $this->db->insert('password_reset', $reset);
                    $this->email_model->send_password_reset_email($user->first_name . ' ' . $user->last_name, $email, $reset_code);
                }
            }
            $this->response(array('message' => 'Password reset instructions have been sent to your email.'), 200);
        }

        public function change_password_get($key) {
            $data = array(
                'title'   => 'Change Password',
                'message' => $this->_message,
                'content_view' => 'themes/login/change_pass',
                'key' => $key,
                'fmessage' => $this->_fmessage
            );
            $resp = $this->check_reset_key($key);
            if (!$resp['success']) {
                $data['content_view'] = null;
                $data['content_message'] = $resp['message'];
            }

            $this->load->view('themes/login/display', $data);
        }

        public function change_password_post($key) {
            $password = $this->input->post('password');
            $cpassword = $this->input->post('cpassword');
            $fail = true;
            $resp = $this->check_reset_key($key);
            if (!$resp['success']) {
                $message = $resp->message;
            } elseif (empty($password)) {
                $message = 'You must enter a password.';
            } else if ($password !== $cpassword) {
                $message = 'Your passwords did not match.';
            } else {
                $fail = false;
                $message = 'Your password has been updated.';
                $this->user_model->set_pass_by_email($resp['user'], $password);
                $this->db->where('reset_code', $key)->delete('password_reset');
            }
            
            $this->session->set_flashdata('message', $message);
            if ($fail) {
                redirect('/change_password/' . $key);
            } else {
                redirect('/login');
            }
        }

        protected function check_reset_key($key) {
            $password_reset = $this->db->where('reset_code', $key)->get('password_reset')->row();
            if (!$password_reset) {
                return array(
                    'success' => false,
                    'message' => 'Invalid access key',
                    'user' => null
                );
            } else if (time() - $password_reset->created > (60*60*24)) {
                return array(
                    'success' => false,
                    'message' => 'Expired access key',
                    'user' => null
                );
            } else {
                return array(
                    'success' => true,
                    'message' => 'Password reset successful',
                    'user' => $password_reset->email
                );
            }
        }
        protected function unset_reset_key($key) {
            $this->db->where('reset_code', $key)->delete('password_reset');
        }


    }