<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Mapp_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->model(array('item_model', 'site_model'));
    }

    public function fetch_dropdowns()
    {
        $today = date('Y-m-d');
        $where = '(discontinued_date IS NULL OR discontinued_date > ' . $this->db->escape($today) . ')';
        $dropdowns = array();

        // Inserts : id = 1
        $sql = "
            SELECT  product_type
            FROM    product_types 
            WHERE   product_id = 1 AND $where
            ;
        ";
//            AND (discontinued_date IS NULL OR discontinued_date > '" . date('Y-m-d') . "')
        $query = $this->db->query($sql);
        $results = $query->result();
        foreach( $results as $result )
        {
            $dropdowns['insert_product_types'][] =  $result->product_type;
        }

        // Insert Max Lookup
        $sql = "
            SELECT  max_width,
                    max_height
            FROM    product_types 
            WHERE   product_id = 1 AND $where
            ;
        ";
        $query = $this->db->query($sql);
        $result = $query->result();
        $dropdowns['insert_max_lookup'] =  $result;

        // Skylights : id = 2
        $sql = "
            SELECT  product_type
            FROM    product_types 
            WHERE   product_id = 2 AND $where
            ;
        ";
        $query = $this->db->query($sql);
        $results = $query->result();
        foreach( $results as $result )
        {
            $dropdowns['skylight_product_types'][] =  $result->product_type;
        }

        // Skylight Max Lookup
        $sql = "
            SELECT  max_width,
                    max_height
            FROM    product_types 
            WHERE   product_id = 2 AND $where
            ;
        ";
        $query = $this->db->query($sql);
        $result = $query->result();
        $dropdowns['skylight_max_lookup'] =  $result;

        
        
        // T2 Inserts : id = 4
        $sql = "
            SELECT  product_type
            FROM    product_types 
            WHERE   product_id = 4 AND $where
            ;
        ";
        $query = $this->db->query($sql);
        $results = $query->result();
        foreach( $results as $result )
        {
            $dropdowns['t2_product_types'][] =  $result->product_type;
        }

        // T2 Inserts Max Lookup
        $sql = "
            SELECT  max_width,
                    max_height
            FROM    product_types 
            WHERE   product_id = 4 AND $where
            ;
        ";
        $query = $this->db->query($sql);
        $result = $query->result();
        $dropdowns['t2_max_lookup'] =  $result;

        // Gaskets
        $sql = "
            SELECT name
            FROM   edging
            ;
        ";
        $query = $this->db->query($sql);
        $results = $query->result();
        foreach( $results as $result )
        {
            $dropdowns['gaskets'][] =  $result->name;
        }


        // Frame Depths
        $sql = "
            SELECT id, 
                   name AS string, 
                   validation_value AS value
            FROM   frame_depth
            ;
        ";
        $query = $this->db->query($sql);
        $results = $query->result();
        foreach( $results as $result )
        {
            $dropdowns['frame_depths'][] =  $result;
        }

        $dropdowns['code'] = 2000;

        return $dropdowns;
    }

    public function fetch_mapp_sites($user_id)
    {
        $frame_depths = array(
            1 => .5,
            2 => .75,
            3 => 1.5,
            4 => 2.1
        );
        $frame_depth_names = array(
            'Less than 5/8 in.',
            'Narrow (5/8in.-1in.)',
            'Med (1in.-2in.)',
            'Deep (2+ in.)'
        );
        // Fetch list of jobsites
        $sql = "
            SELECT      IFNULL(estimates.id, '') AS estimate_id,
                        sites.id,        
                        sites.address,
                        sites.address_ext,
                        sites.city,
                        sites.state,
                        sites.zipcode AS zip,
                        sites.address_type AS 'type',
                        sites.tech_notes as notes,
                        default_frame_depth,
                        default_tubing,
                        default_product
            FROM        sites
            LEFT JOIN   sites_techs
                ON      sites_techs.site_id = sites.id
            LEFT JOIN   estimates
                ON estimates.site_id = sites.id
            WHERE       estimates.tech_id = ?
            AND         sites.deleted = 0
            OR          sites_techs.tech_id = ?
            AND         sites.deleted = 0
            GROUP BY    sites.id
            ;
        ";

        $jobsiteinfo_query   = $this->db->query($sql,array($user_id, $user_id));
        $jobsiteinfo_results = $jobsiteinfo_query->result();
        //return array('jobsites' => $jobsiteinfo_results );

        // Fetch list of jobsite ids
        $sql = "
                SELECT      sites.id
                FROM        sites
                LEFT JOIN   sites_techs
                    ON      sites_techs.site_id = sites.id
                LEFT JOIN  estimates
                    ON estimates.site_id = sites.id
                WHERE       estimates.tech_id = ?
                AND         sites.deleted = 0
                OR          sites_techs.tech_id = ?
                AND         sites.deleted = 0
                GROUP BY    sites.id
            ;
        ";

        $jobsiteinfo_id_query   = $this->db->query($sql,array($user_id, $user_id));
        $jobsiteinfo_id_results = $jobsiteinfo_id_query->result();

        $site_ids = array();
        foreach ( $jobsiteinfo_id_results as $jobsiteinfo_id )
        {
            $site_ids[] = $jobsiteinfo_id->id;
        }

        // Fetch list of estimate ids
        $sql = "
                SELECT      estimates.id
                FROM        sites
                INNER JOIN  estimates
                    ON estimates.site_id = sites.id
                WHERE       estimates.tech_id = ? 
                AND         sites.deleted = 0
                AND         estimates.deleted = 0
            ;
        ";

        $estimate_id_query   = $this->db->query($sql,$user_id);
        $estimate_id_results = $estimate_id_query->result();
        


        foreach ($estimate_id_results as $estimate)
        {
            $site_id = $this->db->where('id', $estimate->id)->get('estimates')->row()->site_id;
            if (!$site_id) {
                continue; // shouldnt happen but just in case;
            }
            // Fetch list of items from the estimates
            $sql = "
                    SELECT item_id AS id
                    FROM estimates_has_item
                    WHERE estimate_id = ? AND deleted = 0
                ;
            ";
            $items_query   = $this->db->query($sql,$estimate->id);
            $items_results = $items_query->result();

            $item_ids = array();
            foreach ( $items_results as $items_id )
            {
                $item_ids[] = $items_id->id;
            }
            // If a site has no items, populate it from the estimate
            if ( ! $this->site_has_items($site_id) )
            {
                $j = 0;
                foreach ( $item_ids as $item_id )
                {
                    // Copy the item from the estimate for the new jobsite
                    $sql = "
                        INSERT INTO items 
                            ( manufacturing_status,
                              floor,
                              room,
                              site_id,
                              product_attributes_id,
                              quality_control_id,
                              price,
                              image_url,
                              width,
                              height,
                              edging_id,
                              special_geom,
                              deleted,
                              location,
                              product_types_id,
                              acrylic_panel_size,
                              acrylic_panel_sq_ft,
                              acrylic_panel_linear_ft,
                              acrylic_panel_thickness,
                              top_spine,
                              side_spines,
                              frame_step,
                              frame_depth_id,
                              notes,
                              drafty,
                              window_shape_id,
                              unit_num )
                                ( SELECT
                                old_items.manufacturing_status,
                                old_items.floor,
                                old_items.room,
                                old_items.site_id,
                                old_items.product_attributes_id,
                                old_items.quality_control_id,
                                old_items.price,
                                old_items.image_url,
                                old_items.width,
                                old_items.height,
                                old_items.edging_id,
                                old_items.special_geom,
                                old_items.deleted,
                                old_items.location,
                                old_items.product_types_id,
                                old_items.acrylic_panel_size,
                                old_items.acrylic_panel_sq_ft,
                                old_items.acrylic_panel_linear_ft,
                                old_items.acrylic_panel_thickness,
                                old_items.top_spine,
                                old_items.side_spines,
                                old_items.frame_step,
                                old_items.frame_depth_id,
                                old_items.notes,
                                old_items.drafty,
                                old_items.window_shape_id,
                                old_items.unit_num
                                FROM        items AS old_items
                                INNER JOIN  estimates_has_item
                                ON          estimates_has_item.item_id = old_items.id
                                WHERE       estimates_has_item.item_id = ? )
                        ;
                    ";

                    $item_copy_query   = $this->db->query($sql,$item_id);
                    $item_copy_id      = $this->db->insert_id();
                    $item = $this->db->where('id', $item_id)->get('items')->row();
                    $this->item_model->setmeasurements(array('B' => $item->width, 'D' => $item->height), $item_copy_id);

                    // Insert the new items into the jobsite
                    $sql = "
                            INSERT INTO site_has_items ( site_id, item_id )
                            VALUES ( ?, ? )
                        ;
                    ";


                    $this->db->query($sql,array( $site_id, $item_copy_id ));

                    ++$j;
                }
            }
        }

        $jobsites = array();

        // For each jobsite
        $i = 0;
        foreach ( $jobsiteinfo_results as $jobsiteinfo )
        {
            $jobsiteinfo->default_frame_depth = array('id' => $jobsiteinfo->default_frame_depth, 'value' => @$frame_depths[$jobsiteinfo->default_frame_depth], 'string' => @$frame_depth_names[$jobsiteinfo->default_frame_depth]);
            // Fetch primary customer
            $sql = "
                SELECT users.id,
                       users.first_name,
                       users.last_name,
                       customers.customer_company_name as company,
                       users.email_1 as emailOne,
                       users.email_type_1 as emailOneType,
                       users.email_2 as emailTwo,
                       users.email_type_2 as emailTwoType,
                       users.phone_1 as phoneOneType,
                       users.phone_2 as phoneTwoType,
                       users.phone_3 as phoneThreeType
                FROM   users
                INNER JOIN customers 
                    ON users.id = customers.user_id
                INNER JOIN site_customers 
                    ON site_customers.customer_id = customers.user_id
                WHERE site_customers.site_id = ?
                AND   site_customers.primary = 1
                ;  
            ";

            $customerinfo_query   = $this->db->query($sql,intval($site_ids[$i]));
            $customerinfo_results = $customerinfo_query->row();

            // Fetch a count of the rooms associated with this jobsite
            $sql = "
                SELECT COUNT(items.id) as window_count
                FROM   items
                INNER JOIN site_has_items
                    ON site_has_items.item_id = items.id
                WHERE  site_has_items.site_id = ? AND site_has_items.deleted=0
            ";
            $itemcount_query   = $this->db->query($sql,intval($site_ids[$i]));
            $itemcount_results = $itemcount_query->row();

            $sql = "
                SELECT COUNT(items.id) as window_count
                FROM   items
                INNER JOIN site_has_items
                    ON site_has_items.item_id = items.id
                WHERE  site_has_items.site_id = ? AND site_has_items.deleted=0 AND measured=1
            ";
            $itemcount_measured_query   = $this->db->query($sql,intval($site_ids[$i]));
            $itemcount_measured_results = $itemcount_measured_query->row();


            $jobsiteinfo->window_count   = intval($itemcount_results->window_count);
            $jobsiteinfo->measured_count = intval($itemcount_measured_results->window_count);



            // Fetch the room names from the new jobsite item list
            $sql = "
                SELECT  DISTINCT items.room as name
                FROM    items
                INNER JOIN site_has_items 
                    ON site_has_items.item_id = items.id
                WHERE   site_has_items.site_id = ? AND site_has_items.deleted=0
            ";

            $room_query   = $this->db->query($sql,intval($site_ids[$i]));
            $room_results = $room_query->result();

            $rooms = array();

            $room_names = array();
            foreach ( $room_results as $room_name )
            {
                $room_names[] = $room_name->name;
            }

            if ( ! empty($room_names) 
            &&     isset($room_names) ) 
            {
                $j = 0;
                foreach ( $room_names as $room_name ) 
                {
                    // Fetch room items
                    $sql = "
                        SELECT  
                        items.id,
                        CASE 
                        WHEN items.window_shape_id = 1 THEN 'Rectangle'
                        WHEN items.window_shape_id = 2 THEN 'Trapezoid'
                        ELSE 'Custom'
                        END AS shape,
                        'false' AS dirty,
                        IF(items.extension=0, 'false', 'true') AS extension,
                        'false' AS active,
                        '' AS validation_status,
                        'false' AS validation_visibility,
                        items.room,
                        items.location,
                        items.floor,
                        items.valid as validation_passed,
                        CASE
                        WHEN items.manufacturing_status = 1 THEN 'Include'
                        WHEN items.manufacturing_status = 2 THEN 'Hold'
                        ELSE 'Include'
                        END AS status,
                        product_type,
                        products.product,
                        edging.name AS gasket,
                        IFNULL(items.frame_step, '') AS frame_step,
                        frame_depth.name as frame_depth,
                        IF(frame_depth_id=0, 1, frame_depth_id) as frame_depth_id, 
                        IF(items.top_spine=1 AND items.side_spines!=1, 'true', 'false') AS top_spine,
                        IF(items.top_spine!=1 AND items.side_spines=1, 'true', 'false') AS side_spines,
                        IF(items.top_spine=1 AND items.side_spines=1, 'true', 'false') AS top_side_spines,
                        IF (measured=0, 'false', 'true') AS measured,
                        product_types.max_width,
                        product_types.max_height,
                        IF(items.drafty=0, 'false', 'true') AS drafty_window,
                        items.notes
                        FROM    items
                        INNER JOIN site_has_items 
                        ON site_has_items.item_id = items.id
                        LEFT JOIN edging
                        ON items.edging_id = edging.id
                        LEFT JOIN frame_depth
                        ON IF(items.frame_depth_id=0, 1, items.frame_depth_id) = frame_depth.id
                        LEFT JOIN product_types
                        ON items.product_types_id = product_types.id
                        LEFT JOIN products ON product_id=products.id
                        WHERE   site_has_items.site_id = ?
                        AND     items.room = ?
                        AND     site_has_items.deleted = 0
                        AND     items.deleted = 0
                    ";


                    $window_query   = $this->db->query($sql, array( intval( $site_ids[$i] ), $room_name ));
                    $window_results = $window_query->result();
                    foreach ($window_results as $win) {
                        $win->frame_depth = array('id' => $win->frame_depth_id, 'value' => $frame_depths[$win->frame_depth_id], 'string' => $win->frame_depth);
                        $win->validation_passed = intval($win->validation_passed);
                    }

                    if ( ! empty($window_results)
                    &&     isset($window_results) )
                    {
                        // Add measurements to window
                        foreach ( $window_results as $window ) 
                        {

                            $j = 0;

                            $sql = "
                                SELECT      CASE
                                            WHEN measurements.measurement_key = 'A' THEN 0
                                            WHEN measurements.measurement_key = 'B' THEN 1
                                            WHEN measurements.measurement_key = 'C' THEN 2
                                            WHEN measurements.measurement_key = 'D' THEN 3
                                            WHEN measurements.measurement_key = 'E' THEN 4
                                            WHEN measurements.measurement_key = 'F' THEN 5
                                            WHEN measurements.measurement_key = 'G' THEN 6
                                            WHEN measurements.measurement_key = 'H' THEN 7
                                            WHEN measurements.measurement_key = 'I' THEN 8
                                            WHEN measurements.measurement_key = 'J' THEN 9
                                            ELSE 0
                                            END AS measurement_index,
                                            measurement_key, 
                                            measurement_value 
                                FROM        measurements
                                INNER JOIN  items_measurements
                                    ON      measurements.id = items_measurements.measurement_id
                                WHERE       item_id = ?
                                ;
                            ";

                            $point_query   = $this->db->query($sql, intval( $window->id));
                            $point_results = $point_query->result();

                            $points     = array( 0, 0, 0, 0, 0, 0 );

                            foreach ( $point_results as $point )
                            {
                                if (isset($points[$point->measurement_index])) { //prevent extra points from appearing;
                                    $points[$point->measurement_index]
                                        = (float) $point->measurement_value;
                                }
                            }

                            if ( ! empty($points) )
                            {
                                $window->points = $points;

                            } else {
                                $window->points = array( 0, 0, 0, 0, 0, 0 );
                            }

                            // Extensions take place in the corner measurements, 
                            // which are positions 4 and 5 of the points/extensions arrays
                            if ( $window->extension == 'true' )
                            {
                                $window->extensions = array( 0, 0, 0, 0, 1, 1 );

                            } else {

                                $window->extensions = array( 0, 0, 0, 0, 0, 0 );
                            }
                        }

                        $windows = $window_results;

                    } else {

                        $windows = "";
                    }

                    // Create the room
                    $room = array( 
                        "name"    => $room_name,
                        "windows" => $windows
                    );

                    array_push($rooms, $room);
                }

            } else {

                $rooms = array();
            }


            if ( $customerinfo_results === array() )
            {
                $site->customerinfo = 
                    array(
                        "first_name"     => "",
                        "last_name"      => "",
                        "company"        => "",
                        "emailOne"       => "",
                        "emailOneType"   => "",
                        "emailTwo"       => "",
                        "emailTwoType"   => "",
                        "phoneOneType"   => "",
                        "phoneTwoType"   => "",
                        "phoneThreeType" => ""
                    );
            }
            else
            {
                $site->customerinfo = $customerinfo_results;
            }

            $site->jobsiteinfo  = $jobsiteinfo;
            $site->rooms        = $rooms;

            array_push($jobsites, clone $site);


            ++$i;
        }

        $data  = array('jobsites' => $jobsites);

        return $data;
    }

    public function save_measurement($measurement)
    {
        $sql = "
            INSERT INTO         items 
                                (id, manufacturing_status, room, age) 
            VALUES              (1, 'A', 19) 
            ON DUPLICATE KEY 
            UPDATE name = VALUES(name), age = VALUES(age)
            ;
        ";
        return 200;
    }

    private function site_has_items($site_id)
    { 
        $sql = "
            SELECT  COUNT(site_id) AS count
            FROM    site_has_items
            WHERE   site_has_items.site_id = ?
            ;
        ";

        $query = $this->db->query($sql,$site_id);
        $count = $query->row()->count;

        return ( $count > 0 ) ? true : false;
    }

    protected function get_id_lookups() {
        $this->load->model('user_model');
        $products_id = $this->user_model->id_name_array('products', 'product');
        $product_types = $this->db->select('id, product_type, product_id')->get('product_types')->result();
        $products = array();
        foreach ($products_id as $k => $v) {
            $products[$v] = array();
        }
        foreach ($product_types as $p) {
            $products[$products_id[$p->product_id]][$p->product_type] = $p->id;
        }
        return array(
            'shapes' => $this->user_model->id_name_array('window_shapes', 'id', 'name'),
            'edging' => $this->user_model->id_name_array('edging', 'id', 'name'),
            'frame_depth' => $this->user_model->id_name_array('frame_depth', 'id', 'name'),
            'product_types' => $products,
            'status' => $this->user_model->id_name_array('manufacturing_status', 'id', 'name')
        );
    }

    public function save_site_data($sites, $user_id) 
    {
        $current = null;
        if (empty($sites)) {
            $sites = array();
        }
        foreach ($sites as $site) {
            $new = false;
            if (empty($site->jobsiteinfo->id)) {
                $new = true;
                $j = $site->jobsiteinfo;
                $c = $site->customerinfo;
                $now = date('Y-m-d H:i:s');

                #CREATE SITE
                $jsite = array(
                    'address' => isset($j->address) ? $j->address : '',
                    'address_ext' => isset($j->address_ext) ? $j->address_ext : '',
                    'city' => isset($j->city) ? $j->city : '',
                    'state' => isset($j->state) ? $j->state : '',
                    'zipcode' => isset($j->zipcode) ? $j->zipcode : '',
                    'address_type' => isset($j->type) ? $j->type : '',
                    'created' => $now,
                    'updated' => $now,
                    'created_by' => $user_id,
                    'tech_notes' => $j->notes,
                    'default_product' => empty($j->default_product) ? 'Standard' : $j->default_product,
                    'default_tubing' => empty($j->default_tubing) ? 'White' : $j->default_tubing,
                    'default_frame_depth' => empty($j->default_frame_depth->id) ? 1 : $j->default_frame_depth->id
                );

                $this->db->insert('sites', $jsite);
                $site_id = $this->db->insert_id();

                #ASSIGN TECH TO SITE
                $this->db->insert('sites_techs', array(
                    'site_id' => $site_id,
                    'tech_id' => $user_id
                ));

                #CREATE USER
                $user = array(
                    'first_name' => isset($c->first_name) ? $c->first_name : '',
                    'last_name' => isset($c->last_name) ? $c->last_name : '',
                    'email_1' => isset($c->emailOne) ? $c->emailOne : '',
                    'email_2' => isset($c->emailTwo) ? $c->emailTwo : '',
                    'email_type_1' => isset($c->emailOneType) ? $c->emailOneType : '',
                    'email_type_2' => isset($c->emailTwoType) ? $c->emailTwoType : '',
                    'phone_1' => isset($c->phoneOne) ? $c->phoneOne : '',
                    'phone_2' => isset($c->phoneTwo) ? $c->phoneTwo : '',
                    'phone_3' => isset($c->phoneThree) ? $c->phoneThree : '',
                    'phone_type_1' => isset($c->phoneOneType) ? $c->phoneOneType : '',
                    'phone_type_2' => isset($c->phoneTwoType) ? $c->phoneTwoType : '',
                    'phone_type_3' => isset($c->phoneThreeType) ? $c->phoneThreeType : '',
                );
                $this->db->insert('users', $user);
                $c_id = $this->db->insert_id();

                #CREATE CUSTOMER
                $customer = array(
                    'customer_company_name' => isset($c->company) ? $c->company : '',
                    'customer_preferred_contact' => $user_id,
                    'sales_modifier_id' => 1,
                    'user_id' => $c_id,
                    'customer_referred_by' => $user_id
                );
                $this->db->insert('customers', $customer);

                $this->db->insert('site_customers', array(
                    'customer_id' => $c_id,
                    'site_id' => $site_id,
                    'primary' => 1
                ));
            } else {
                $site_id = $site->jobsiteinfo->id;
                if (!empty($site->jobsiteinfo->default_product) || !empty($site->jobsiteinfo->default_tubing) || !empty($site->jobsiteinfo->default_frame_depth->id)) {
                    $this->db->where('id', $site_id)->update('sites', array(
                        'tech_notes' => $site->jobsiteinfo->notes,
                        'default_product' => @$site->jobsiteinfo->default_product,
                        'default_tubing' => @$site->jobsiteinfo->default_tubing,
                        'default_frame_depth' => @$site->jobsiteinfo->default_frame_depth->id,
                    ));
                }
            }
            $saveolditems = false; //new items will always be saved as there is no chance of data colision.  old items will check timestamps to determine whether the changes should be saved or overwritten.
            if ($new) {
                $saveolditems = true;
            } elseif (isset($site->jobsiteinfo->modified)) {
                $time = $site->jobsiteinfo->modified;
                $parts = localtime($time / 1000);
                $localtime = (1900 + $parts[5]) . '-' . ($parts[4] + 1) . '-' . $parts[3] . ' ' . $parts[2] . ':' . $parts[1] . ':' . $parts[0];
                $mapptime = date('Y-m-d H:i:s', strtotime($localtime)); //adds preceding zeros to match mysql format, not sure if thats necessary, but cant hurt
                $moditime = $this->db->where('id', $site_id)->get('sites')->row()->updated;
                if ($mapptime >= $moditime) {
                    $saveolditems = true;
                }
            }
            if (!isset($site->rooms)) {
                $site->rooms = array();
            }
            $l = $this->get_id_lookups();


            foreach ($site->rooms as $room) {
                if (!isset($room->windows) || !$room->windows) {
                    $room->windows = array();
                }
                foreach ($room->windows as $w) {
                    if (empty($w->id) || ($saveolditems && $w->dirty)) {
                        if ($w->product === 'Indow Windows' || $w->product === 'Insert') {
                            $w->product = 'Legacy Insert';
                        } elseif ($w->product === 'Skylight Indow Windows') {
                            $w->product = 'Skylight Insert';
                        }
                        
                        $product = isset($l['product_types'][$w->product][$w->product_type]) ? $l['product_types'][$w->product][$w->product_type] : null;
                        if (!$product) {
                            continue; //this should never happen but if it does i dont want it to stop the entire sync
                        }
                        $item = array(
                            'window_shape_id'     => isset($l['shapes'][$w->shape]) ? $l['shapes'][$w->shape] : '',
                            'room'                => $room->name,
                            'location'            => $w->location,
                            'floor'               => $w->floor,
                            'manufacturing_status'=> $l['status'][$w->status],
                            'edging_id'           => !empty($l['edging'][$w->gasket]) ? $l['edging'][$w->gasket] : null,
                            'drafty'              => (!isset($w->drafty_window)|| $w->drafty_window == 'false') ? 0 : 1,
                            'notes'               => $w->notes,
                            'product_types_id'    => $product,
                            'site_id'             => $site_id,
                            'frame_step'          => $w->frame_step,
                            'frame_depth_id'      => $w->frame_depth->id,
                            'extension'           => (!isset($w->extension)|| $w->extension == 'false') ? 0 : 1,
                            'measured'            => (!isset($w->measured) || $w->measured == 'false') ? 0 : 1,
                            'top_spine'           => ($w->top_spine   == 1 || $w->top_side_spines == 1)  ? 1 : 0,
                            'side_spines'         => ($w->side_spines == 1 || $w->top_side_spines == 1) ? 1 : 0,
                            'valid'               => (isset($w->validation_passed) && $w->validation_passed == 1) ? 1 : 0
                        );
                        $this_current = false;
                        if (empty($w->id)) {
                            if (!empty($w->current)) {
                                $this_current = true;
                                unset($w->current);
                            }
                            $this->db->insert('items', $item);
                            $item_id = $this->db->insert_id();
                            $this->db->insert('site_has_items', array(
                                'site_id' => $site_id,
                                'item_id' => $item_id
                            ));
                            if ($this_current) {
                                $current = $item_id;
                            }
                        } else {
                            if (!empty($w->current)) {
                                unset($w->current);
                                $current = $w->id;
                            }
                            $this->db->where('id', $w->id)->update('items', $item);
                            $item_id = $w->id;
                        }
                        $letters = str_split('ABCDEFGGIJ');
                        $measurements = array();
                        foreach ($w->points as $k => $v) {
                            $measurements[$letters[$k]] = $v;
                        }
                        $this->item_model->setmeasurements($measurements, $item_id);
                    }
                    if (!empty($w->current) && !empty($w->id)) {
                        $current = $w->id;
                    }
                }
            }
        }
        return $current;
    }
}
