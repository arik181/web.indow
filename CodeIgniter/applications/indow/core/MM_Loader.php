<?php  if (! defined('BASEPATH')) exit('No direct script access allowed');


class MM_Loader extends CI_Loader
{
    protected $_mm_factory_paths = array();
    protected $_mm_factories = array();

    function __construct()
    {
        parent::__construct();
        $this->_mm_factory_paths = array(APPPATH);
    }
    public function factory($factory,$name='')
    {

        if (is_array($factory))
        {
            foreach ($factory as $item)
            {
                $this->factory($item);
            }
            return;
        }
        if ($factory == "")
            return;

        $path = '';

        // Is the factory in a sub-folder? If so, parse out the filename and path.
        if (($last_slash = strrpos($factory, '/')) !== FALSE)
        {
            // The path is in front of the last slash
            $path = substr($factory, 0, $last_slash + 1);

            // And the factory name behind it
            $factory = substr($factory, $last_slash + 1);
        }

        if ($name == '')
        {
            $name = $factory;
        }

        if (in_array($name, $this->_mm_factories, TRUE))
        {
            return;
        }

        $CI =& get_instance();
        if (isset($CI->$name))
        {
            show_error('The factory name you are loading is the name of a resource that is already being used: '.$name);
        }
        $factory = strtolower($factory);

        foreach ($this->_mm_factory_paths as $mod_path)
        {
            if ( ! file_exists($mod_path.'factories/'.$path.$factory.'.php'))
            {
                continue;
            }

            // if ($db_conn !== FALSE AND ! class_exists('CI_DB'))
            // {
            //     if ($db_conn === TRUE)
            //     {
            //         $db_conn = '';
            //     }

            //     $CI->load->database($db_conn, FALSE, TRUE);
            // }

            if ( ! class_exists('MM_Factory'))
            {
                load_class('Factory', 'core','MM_');
            }

            require_once($mod_path.'factories/'.$path.$factory.'.php');

            $factory = ucfirst($factory);

            $CI->$name = new $factory();

            $this->_mm_factories[] = $name;
            return;
        }

        // couldn't find the model
        show_error('Unable to locate the factory you have specified: '.$factory);
    }

    // TODO: ideally we want to be extending add_package_path, get_package_paths, and remove_package_paths for maximum compatibility
}
