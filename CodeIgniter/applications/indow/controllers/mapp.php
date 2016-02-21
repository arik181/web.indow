<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Mapp extends REST_Controller
{
    function __construct()
    {
        parent::__construct();
        $method = $_SERVER['REQUEST_METHOD'];
        if($method == "OPTIONS") {
            header('Access-Control-Allow-Origin: *');
            header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
            header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
            die();
        }
        $this->load->model(array('user_model', 'customer_model', 'mapp_model'));
        $this->load->library('ion_auth');
    }

    public function login_post()
    {
        $username = $this->post('username', FALSE);
        $password = $this->post('password', FALSE);

        if ($this->ion_auth->login($username, $password))
        {
            $fresh_key = $this->generate_mapp_key($username, $password);

            $user_id = $this->ion_auth->get_user_id();
            $sql = "
                UPDATE  users
                SET     users.mapp_key = ? 
                WHERE   users.id = ?
                AND     users.mapp_key = ''
                ;
            ";

            $this->db->query($sql, array( $fresh_key, $user_id ));

            $sql = "
                SELECT  users.mapp_key
                FROM    users
                WHERE   users.id = ?
                ;
            ";

            $key_query   = $this->db->query($sql, array( $user_id ));
            $key_results = $key_query->row();
            $final_key   = $key_results->mapp_key;

            $response = array( 'code' => '2000', 'mapp_key' => $final_key );
            $this->response($response, 200);
        }

        $response = array( 'code' => '3000', 'error' => "Invalid username password combination");
        $this->response($response, 200);
    }

    private function generate_mapp_key($username, $password)
    {
        return md5( $username . rand() . time() );
    }

    public function test2_get() {
        error_reporting(E_ALL);
        $ref = $this->input->get('ref');
        if ($ref) {
            $error = $this->db->where('id', $ref)->get('sync_errors')->row();
            $errors = json_decode($error->errors);
            $jobsites = json_decode($error->jobsites);
            foreach ($errors as $e) {
                pr(json_decode($e));
            }
            prd($jobsites);
        }
        //set_error_handler(array($this, 'handle_errors'), E_ALL);
        $content = file_get_contents('/tmp/aco');
        $sites = json_decode($content);
        $this->mapp_model->save_site_data($sites, 1);
        $data = $this->mapp_model->fetch_mapp_sites(1);
        prd($sites);
    }

    function handle_errors() 
    { 
        $argv = func_get_args();
        $this->syncerrors[] = @json_encode($argv);
    }

    public function fetch_dropdowns_get()
    {
        $data = $this->mapp_model->fetch_dropdowns();
        $this->response($data, 200);
    }

    public function test_get() {
        $data = @file_get_contents('/tmp/mapp');
        $this->fetch_job_sites_post($data);
    }

    public function fetch_job_sites_post($data=null)
    {
       // set_error_handler(array($this, 'handle_errors'), E_ALL);
        $this->syncerrors = array();
        if (empty($data)) {
            $raw_post = file_get_contents('php://input');
            $post = json_decode($raw_post);
            $fh = fopen('/tmp/mapp', 'w');
            fwrite($fh, $raw_post);
            fclose($fh);
        } else {
            $post = json_decode($data);
        }
        if (!isset($post->mapp_key) || !isset($post->sites)) 
        {
            return;
        }

        $sites = json_decode($post->sites);
        $fh = fopen('/tmp/aco', 'w'); fwrite($fh, $post->sites); fclose($fh);

        if ( ! $this->validate_mapp_key($post->mapp_key) )
        {
            $response = array( 'code' => '3050', 'error' => "Invalid MAPP key"); 
            $this->response($response, 200);
            return;
        }

        $user_id = $this->get_user_id_from_mapp_key($post->mapp_key);

        $this->db->trans_begin();
        if ( isset($sites) 
        && ! empty($sites) )
        {
            $current = $this->mapp_model->save_site_data($sites, $user_id);
        } else {
            $current = null;
        }

        if (!empty($post->deleted)) 
        {
            $this->db->where_in('item_id', $post->deleted)->update('site_has_items', array('deleted' => 1));
        }

        $data = $this->mapp_model->fetch_mapp_sites($user_id);
        $response = array('code' => 2000, 'data' => $data, 'current' => $current);
        if (count($this->syncerrors)) 
        {
            $this->db->trans_rollback();
            $this->db->insert(
                'sync_errors', 
                    array(
                        'user_id' => $user_id,
                        'errors' => json_encode($this->syncerrors),
                        'jobsites' => $post->sites
                    )
            );
            $error_id = $this->db->insert_id();
            $this->response(array('code' => 3000, 'error' => 'An error occured in sync.', 'reference' => $error_id));

        } else {
            $this->db->trans_commit();
        }

        $this->response($response, 200);
    }

    public function fetch_job_sites_options() 
    {
    }

    private function get_user_id_from_mapp_key($key)
    {
        $sql = "
            SELECT users.id
            FROM   users
            WHERE  users.mapp_key = ?
            LIMIT 1
            ;
        ";

        return $this->db->query($sql, $key)->row()->id;
    }

    private function validate_mapp_key($key)
    {
        $sql = "
            SELECT EXISTS(
                SELECT
                users.id
                FROM   users
                WHERE  users.mapp_key = ? AND mapp_key != ''
                LIMIT 1
            ) AS key_exists
            ;
        ";

        return $this->db->query($sql, $key)->row()->key_exists;
    }
}
