<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
require APPPATH . 'third_party' . DIRECTORY_SEPARATOR .  'vendor/laracasts/utilities/src/Laracasts/Utilities/JavaScript/PHPToJavaScriptTransformer.php';
require APPPATH . 'third_party' . DIRECTORY_SEPARATOR .  'vendor/laracasts/utilities/src/Laracasts/Utilities/JavaScript/ViewBinder.php';

use Laracasts\Utilities\JavaScript\PHPToJavaScriptTransformer;

    /**
     *
     * CodeIgniter PHP To JavaScript
     *
     * A Library to transfer PHP to JavaScript. Using JefferyWay's PHPToJavaScriptTransformer.
     *
     * @package        	CodeIgniter
     * @subpackage    	Libraries
     * @category    	Libraries
     * @author        	Danny Nunez
     * @version 		0.0.1
     *
     */

    class PHPToJavaScript implements Laracasts\Utilities\JavaScript\ViewBinder {

        protected $ci;
        protected $javascript;
        protected $newJavaScript;
        protected $javaScriptNameSpace;

        public function __construct(){

            $this->ci =& get_instance();
            $this->javaScriptNameSpace = 'indow';

        }

        public function phpToJavaScript($vars){

            if(!empty($vars)){
                $this->javascript = new PHPToJavaScriptTransformer($this,$this->javaScriptNameSpace);
                $this->javascript->put($vars);
                $vars = $this->newJavaScript;
            }

            return $vars;

        }

        public function bind($js)
        {
            $this->newJavaScript =  '<script>' . $js . '</script>';
        }



    }