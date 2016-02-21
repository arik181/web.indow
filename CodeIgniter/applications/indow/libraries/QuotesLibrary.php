<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

    /**
     * Class QuotesLibrary
     */
    class QuotesLibrary {

        protected $ci;
        protected $_user;

        public function __construct()
        {
            $this->ci =& get_instance();

        }

        public function getQuoteListByCurrentUser()
        {

            $this->_user = $this->ci->data['user'];
            $data = array();

            if($this->_user->is_customer == 1)
            {
                // customer code goes here
            }
            else
            {
                if(!is_array($this->_user->groups))
                {

                    if(!empty($this->_user->groups))
                    {


                        if($this->_user->in_admin_group == true)
                        {
                            return $this->ci->Quote_model->get_all();
                        }
                        else
                        {
                            foreach($this->_user->groups as $group)
                            {
                                $data[] = $this->ci->Quote_model->getByGroupId($group->id);
                            }
                        }


                    }

                }
            }

            return $data;



        }




    }