<?php defined('BASEPATH') OR exit('No direct script access allowed');


    class group_seeder extends MM_Controller
    {

        public function __construct()
        {
            parent::__construct();
        }


        public function index_get()
        {



            $addGroups = "INSERT INTO `groups` (`name`) VALUES

                            ('IndowAdmin'),
                            ('IndowRep'),
                            ('PacificNorthwest'),
                            ('SouthWestUS'),
                            ('SouthernUS'),
                            ('NorthEast'),
                            ('MidWest'),
                            ('DealerRep'),
                            ('Freebird'),
                            ('Cartwright Group'),
                            ('Douglas, Swaniawski and Deckow'),
                            ('Huel, Hettinger and Collins'),
                            ('Stroman-Mosciski'),
                            ('Spinka Inc'),
                            ('Tremblay, Barton and McKenzie'),
                            ('Connelly, White and Schaden'),
                            ('Hodkiewicz, Haley and Brakus'),
                            ('Ruecker, Weimann and Miller'),
                            ('Heaney, Reichel and Harris'),
                            ('Murphy Inc'),
                            ('Ullrich Ltd'),
                            ('Hand, Wintheiser and Crooks'),
                            ('Bauch, Sporer and King'),
                            ('Trantow-Thiel'),
                            ('Marvin Group'),
                            ('Kessler LLC'),
                            ('Graham, Nader and Cassin'),
                            ('Kertzmann, Sawayn and O''Connell'),
                            ('Pfeffer LLC'),
                            ('Abbott-Hodkiewicz'),
                            ('Gerhold-Kirlin'),
                            ('Schmitt, Predovic and Russel'),
                            ('Hermiston and Sons'),
                            ('Fadel-Gutmann'),
                            ('Crooks-Boehm'),
                            ('Hammes, Ward and Mante'),
                            ('Christiansen, Schowalter and Stanton'),
                            ('Dickens-Weimann'),
                            ('Nader, Lynch and Koelpin'),
                            ('Moore, Ledner and Stokes'),
                            ('Prohaska, Cassin and Stokes'),
                            ('Bergnaum-Cummings'),
                            ('Hammes Inc'),
                            ('O''Kon, Torphy and Leannon'),
                            ('Steuber-Durgan'),
                            ('Roberts, Ankunding and Rath'),
                            ('Hilll, Gislason and Hayes'),
                            ('Spinka-Thiel'),
                            ('Kautzer, Walsh and Kulas'),
                            ('Lowe, Buckridge and Senger'),
                            ('Greenfelder, Bergnaum and Terry'),
                            ('Lesch, Ratke and Herzog'),
                            ('Funk, Johnson and Renner'),
                            ('Blick Ltd'),
                            ('Torp Group'),
                            ('Lubowitz-Larkin'),
                            ('Lehner, Kris and Tillman'),
                            ('Bogan Inc'),
                            ('Cormier-Smitham'),
                            ('Wisozk LLC'),
                            ('Dickinson-Haag'),
                            ('Stehr Ltd'),
                            ('Shanahan LLC'),
                            ('Dooley-McDermott'),
                            ('Eichmann-Mills'),
                            ('Mitchell, Daniel and Murray'),
                            ('Fahey, Kassulke and Lang'),
                            ('Schultz, Quigley and Blick'),
                            ('Koss, Kreiger and Vandervort'),
                            ('Fahey-Ratke'),
                            ('Denesik, Gerhold and Boehm'),
                            ('Farrell Ltd'),
                            ('Abernathy, Schamberger and Doyle'),
                            ('Blanda LLC'),
                            ('Ratke, Ledner and Gerhold'),
                            ('Towne-Quigley'),
                            ('Fay-Becker'),
                            ('Terry, Hackett and Kreiger'),
                            ('Kessler, Olson and Corkery'),
                            ('Russel LLC'),
                            ('Ruecker and Sons'),
                            ('Willms-Gorczany'),
                            ('Rippin-Gutkowski'),
                            ('Brakus-Gerlach'),
                            ('Rodriguez Group'),
                            ('Harris, Lowe and Beatty'),
                            ('Beer, Torphy and Schoen'),
                            ('Windler-Wintheiser'),
                            ('Kihn-Eichmann'),
                            ('O''Reilly, Orn and VonRueden'),
                            ('Kuhic, Runte and Boyle'),
                            ('Ankunding and Sons'),
                            ('Schiller Group'),
                            ('Beer, Mohr and Bogan'),
                            ('Daniel LLC'),
                            ('Nicolas, Thiel and King'),
                            ('Will-Hayes'),
                            ('Kozey-Dach'),
                            ('Murphy Inc');";

            $this->db->query($addGroups);

            $query = $this->db->get('groups')->result_array();

            $data = array();
            $addresses = array();
            foreach($query as $row){

                $cleaned_business_name = clean_string($row['name']);

                $row['email_1']               = 'sales@' . $cleaned_business_name . '.com';
                $row['email_type_1']          = rand(0, 1);
                $row['email_2']               = 'contact@' . $cleaned_business_name . '.com';
                $row['email_type_2']          = rand(0, 1);
                $row['phone_1']               = random_string('numeric', 3) . '-' . random_string('numeric', 3) . '-' . random_string('numeric', 4);
                $row['phone_type_1']          = rand(0, 1);
                $row['phone_2']               = random_string('numeric', 3) . '-' . random_string('numeric', 3) . '-' . random_string('numeric', 4);
                $row['phone_type_2']          = rand(0, 1);
                $row['signed_agreement_name'] = $row['name'] . 'Agreement';
                $row['rep_id']                = 1;


                if($row['id'] == 1){
                    $row['permissions_id']        = 2;
                    $row['group_type_id']         = 1;
                }
                else
                {
                    $row['permissions_id']        = rand(3,5);
                    $row['group_type_id']         = rand(2,7);
                }

                $data[] = $row;

                // delete all group address created by provision script
                $this->db->delete('group_addresses', array('group_id' => $row['id']));

                $state = getRandomState();

                // home address
                $address_1 = getRandomRandomAddress();
                $addresses[] = array(
                    'group_id' => $row['id'],
                    'addressnum' => 1,
                    'address_type' => 'HOME',
                    'address' => $address_1['address'],
                    'address_ext' => $address_1['address_ext'],
                    'city' => $address_1['city'],
                    'state' => $state,
                    'zipcode' => $address_1['zipcode']
                );

                // work address
                $address_2 = getRandomRandomAddress();
                $addresses[] = array(
                    'group_id' => $row['id'],
                    'addressnum' => 2,
                    'address_type' => 'Work',
                    'address' => $address_2['address'],
                    'address_ext' => $address_2['address_ext'],
                    'city' => $address_2['city'],
                    'state' => $state,
                    'zipcode' => $address_2['zipcode']
                );

                if($row['id'] == 1)
                {
                    $this->db->insert('group_permissions', array('group_id' => 1, 'permission_preset_id' => 1));
                }
                else
                {
                    $this->db->insert('group_permissions', array('group_id' => rand(2,99), 'permission_preset_id' => rand(2,7)));
                }


            }

            $this->db->update_batch('groups', $data, 'id');
            $this->db->insert_batch('group_addresses', $addresses);

        }








    }