<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

    class group_sub_group_model extends MM_Model
    {
        protected  $_table  = 'groupsSubGroups';
        protected  $_key    = 'id';


        public function __construct()
        {
            parent::__construct();
        }

        /**
         *
         * Used to get list in edit / add group form
         * @param $id
         *
         * @return mixed
         */
        public function getSubGroupsByGroupId($id)
        {

            $this->db->select('groups.id,groups.name,groupsSubGroups.rank');
            $this->db->from('groups');
            $this->db->join('groupsSubGroups', 'groupsSubGroups.sub_group_id = groups.id');
            $this->db->where('groupsSubGroups.group_id',$id);
            $this->db->order_by('groupsSubGroups.rank','asc');
            $query = $this->db->get();
            return $query->result();

        }

        public function get_sub_group_ids($group_ids)
        {

            $data = array();

            if(!empty($group_ids))
            {

                foreach($group_ids as $row)
                {

                    $sub_groups = $this->getSubGroupsByGroupId($row);

                    if(!empty($sub_groups))
                    {
                        foreach($sub_groups as $sub_group)
                        {
                            if(!in_array($sub_group->id,$data))
                            {
                                $data[] = $sub_group->id;
                            }
                        }
                    }

                }

            }

            return $data;

        }

        /**
         * @param $groups
         * @param $id
         *
         * @return bool
         */
        public function updateSubGroupsByParentGroupId($groups = array(),$id)
        {

            if(!empty($groups))
            {

                $this->delete_many_by(array('group_id' => $id ));

                foreach($groups as $row)
                {
                    $duplicate_check = $this->get_many_by(array('group_id' => $id ,"sub_group_id" => $row->sub_group_id, "rank" => $row->rank));

                    if(empty($duplicate_check)){
                        $this->insert(array(
                            "group_id" => $id,
                            "sub_group_id" => $row->sub_group_id,
                            "rank" => $row->rank,
                        ));
                        $this->db->where('id', $row->sub_group_id)->update('groups', array('parent_group_id' => $id));
                    }

                }

                return true;

            }
            else
            {
                $this->delete_many_by(array('group_id' => $id ));
                return false;
            }


        }


        public function getSubGroups($groups = array())
        {

            $data = array();


            if(!empty($groups))
            {

                foreach($groups as $row){

                    $name = $this->Group_model->get($row->sub_group_id);
                    $row->name = $name->name;
                    $data[] = $row;

                }

                return $data;

            }
            else
            {
                return $data;
            }





        }



    }
