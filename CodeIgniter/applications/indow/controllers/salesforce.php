<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Salesforce extends REST_Controller
{
    function __construct()
    {
        parent::__construct();
        // $this->output->enable_profiler(TRUE);
        $this->load->model(array('site_model', 'group_model'));

        $this->site       = $this->site_model;
        $this->salesforce = $this->Sales_force_model;
        $this->group      = $this->group_model;

        $this->session->userdata['user_id'] = '1';
        $this->opportunity_array = $this->site->get_opportunity();
        $this->groups = $this->group->fetch_groups(99999, 0);
    }

    public function refresh_all_get()
    {
      //if (isset($_SERVER['HTTP_HOST']))
      //{
      //  die("Not accessible via web, this must be run from the command line.");
      //}

      $result = $this->salesforce->get_all_leads();
      if($result['code'] == 200)      
      {
        if(count($result['data']) == 0)
          {
            log_message('debug', 'No Records');
            exit(0);
          } else {
            log_message('debug', count($result['data']).' Records Found');
            foreach($result['data'] as $data)
            {
              $url = explode('/', $data->attributes->url);
              if(in_array($url[6], $this->opportunity_array))
              {
                log_message('debug', 'Duplicate Record Found: '.$url[6]);
              } else {
                $leads[] = array(
                      'opportunity_id' => $url[6]
                    ,  'site_opportunity_id' => $url[6]                      
                    , 'first_name' => $data->FirstName
                    , 'last_name' => $data->LastName
                    , 'address' => $data->MailingStreet
                    , 'site_address' => $data->MailingStreet                    
                    , 'city' => $data->MailingCity
                    , 'site_city' => $data->MailingCity                    
                    , 'state' => $data->MailingState
                    , 'zipcode' => $data->MailingPostalCode
                    , 'phone_1' => $data->Phone
                    , 'site_state' => $data->MailingState
                    , 'site_zipcode' => $data->MailingPostalCode                    
                    , 'phone_type_1' => '0'
                    , 'email_1' => $data->Email
                    , 'email_type_1' => '0'
                    , 'organization_name' => $data->Lead_Ref_Dealer_Name__c
                  );
              }
            }
            if(empty($leads))
            {
              log_message('debug', 'No New Records');
              exit(0);
            }

            foreach($leads as $lead)
            {
              foreach($this->groups as $i_group)
              {
                if($i_group->name == $lead['organization_name'])
                {
                  $lead['company_id'] = $i_group->id;
                } else {
                  $lead['company_id'] = 0;
                }
              }
                $response = $this->site->add_site($lead);

                if($lead['company_id'] != 0)
                {
                  $this->group->add_user_to_group( $this->site->tmp_userid, $lead['company_id'] );
                }

                log_message('debug', $response);
            }


        }
      } else {
        log_message('debug', 'Error Recorded from SalesForce - '.$result['code']);
        exit(0);
      }
    }

    public function refresh_today_get()
    {
      //if (isset($_SERVER['HTTP_HOST']))
      //{
      //  die("Not accessible via web, this must be run from the command line.");
      //}

      $result = $this->salesforce->get_todays_leads();
      // prd($result);
      if($result['code'] == 200)      
      {
        if(count($result['data']) == 0)
          {
            log_message('debug', 'No Records');
            exit(0);
          } else {
            log_message('debug', count($result['data']).' Records Found');
            foreach($result['data'] as $data)
            {
              $url = explode('/', $data->attributes->url);
              if(in_array($url[6], $this->opportunity_array))
              {
                log_message('debug', 'Duplicate Record Found: '.$url[6]);
              } else {
                if (empty($data->MailingCountry)) {
                    $country = 'United States';
                } else if ($data->MailingCountry === 'USA') {
                    $country = 'United States';
                } else {
                    $country = $data->MailingCountry;
                }
                $leads[] = array(
                      'opportunity_id' => $url[6]
                    ,  'site_opportunity_id' => $url[6]                      
                    , 'first_name' => $data->FirstName
                    , 'last_name' => $data->LastName
                    , 'address' => $data->MailingStreet
                    , 'site_address' => $data->MailingStreet                    
                    , 'city' => $data->MailingCity
                    , 'site_city' => $data->MailingCity                    
                    , 'state' => $data->MailingState
                    , 'zipcode' => $data->MailingPostalCode
                    , 'country' => $country
                    , 'phone_1' => $data->Phone
                    , 'site_state' => $data->MailingState
                    , 'site_zipcode' => $data->MailingPostalCode                    
                    , 'phone_type_1' => '0'
                    , 'email_1' => $data->Email
                    , 'email_type_1' => '0'
                    , 'organization_name' => $data->Lead_Ref_Dealer_Name__c
                  );
              }
            }
            if(empty($leads))
            {
              log_message('debug', 'No New Records');
              exit(0);
            }

            foreach($leads as $lead)
            {
              $lead['company_id'] = 0;
              foreach($this->groups as $i_group) {
                if($i_group->name == $lead['organization_name']) {
                  $lead['company_id'] = $i_group->id;
                }
              }
              unset($lead['organization_name']);
                $response = $this->site->add_site($lead, true);
                if($lead['company_id'] != 0)
                {
                  $this->group->add_user_to_group( $this->site->tmp_userid, $lead['company_id'] );
                }                
                log_message('debug', $response);
            }


        }
      } else {
        log_message('debug', 'Error Recorded from SalesForce - '.$result['code']);
        exit(0);
      }
    }    
}
