<?php  if (! defined('BASEPATH')) exit('No direct script access allowed');

require(APPPATH.'libraries/REST_Controller.php');

class MM_Controller extends REST_Controller
{
    var $pagination;

    function __construct()
    {
        parent::__construct();

        $this->data['title'] = "";   //overwrite this if you want additional title information
        $this->data["nav"] = "home"; //overwrite this with a key unique to this navigation item (handles selected states for navigation)
        $this->data['auth'] = false;
        $this->data['phpToJavaScript'] = array();
        $this->data['user'] = $this->user();
        $this->data['js_header'] = array();
        $this->data['js_footer'] = array();
        $this->data['js_views'] = array();
        date_default_timezone_set('America/Los_Angeles');

        // Security

        $uri    = $this->uri->segment(1);
        $uri2   = $this->uri->segment(2);
		if($this->data['user'] == false && (!in_array($uri, array('login', 'sf', 'reset_pass', 'change_password', 'login_contents'))))
		{
            if ($uri == 'orders' && ($uri2 == 'measure' || $uri2 == 'confirmation'))
            {
                $this->session->set_userdata('redirect', uri_string());
                if ($uri2 == 'measure') {
                    $cookie = array(
                        'name'   => 'cust',
                        'value'  => 1,
                        'expire' => 8650000,
                        'secure' => false
                    );
                    $this->input->set_cookie($cookie);
                }
            }
			redirect('/login');
		} elseif ($this->data['user'] && ($uri != 'login' && $uri != 'sf' && $uri != 'logout') && $this->data['user']->is_customer && !($uri == 'orders' && ($uri2 == 'measure' || $uri2 == 'confirmation' || $uri2 == 'order_item_list_json' || $uri2 == 'update_item_ajax' || $uri2 == 'delete_order_items_ajax'))) { //prevent freebird from accessing any pages
            //$this->response('Page not found.', 404);
            $this->order_model->freebird_redirect();
            $this->data['customer_header'] = true;
            $data = array(
                'title'   => 'Measure Order',
                'content' => 'themes/fullwidth/message_screen',
                'message' => 'No orders are available for measurement at this time.'
            );
            echo $this->load->view('themes/fullwidth/main', $data, true);
        }

    }

    public function configure_pagination($base_url, $table)
    {
        $this->pagination = new CI_Pagination();

        $config['base_url'] = base_url($base_url);
        $config['total_rows'] = $this->db->count_all($table);
        $config['per_page'] = 25;
        $config['num_links'] = 10;
        $config["uri_segment"] = 3;
        $config['full_tag_open'] = '<div class="pagination"><ul>';
        $config['full_tag_close'] = '</ul></div><!--pagination-->';
        $config['first_link'] = '&laquo; First';
        $config['first_tag_open'] = '<li class="prev page">';
        $config['first_tag_close'] = '</li>';
        $config['last_link'] = 'Last &raquo;';
        $config['last_tag_open'] = '<li class="next page">';
        $config['last_tag_close'] = '</li>';
        $config['next_link'] = 'Next &rarr;';
        $config['next_tag_open'] = '<li class="next page">';
        $config['next_tag_close'] = '</li>';
        $config['prev_link'] = '&larr; Previous';
        $config['prev_tag_open'] = '<li class="prev page">';
        $config['prev_tag_close'] = '</li>';
        $config['cur_tag_open'] = '<li class="active"><a href="">';
        $config['cur_tag_close'] = '</a></li>';
        $config['num_tag_open'] = '<li class="page">';
        $config['num_tag_close'] = '</li>';

        $this->pagination->initialize($config);
    }

    /**
     *
     *  This will set current user profile information. Also set $this->data['auth']
     *  to be used to verify authentication status.
     *
     * @return bool
     */
    private function user()
    {
        if($this->ion_auth->logged_in())
        {
            $this->data['auth'] = true;
            return $this->permissionslibrary->getCurrentUserProfile();
        }
        else
        {
            return false;
        }
    }
}
