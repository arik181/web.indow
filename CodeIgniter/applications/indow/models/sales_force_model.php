<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Sales_force_model extends CI_Model {

    protected $_token_url   = 'https://login.salesforce.com/services/oauth2/token';
    protected $_username    = 'rich@indowwindows.com';
    //protected $_password    = 'Ind0w1115';
    protected $_password    = 'Indow0216';
    protected $_customerid  = '3MVG9y6x0357HlecUkBtf.wO1QdisBZGYuvigKNaZKcdXwuAs244mrlE2mPDVlp4bqv_0_rdfgftPV6_v9SAO';
    protected $_secret      = '2553861846346296839';
   // protected $_token       = 'zixEz48OrcOrYKbjAwEmMgdIP';
   // protected $_token       = 'ct30WKuOBBvSqAOUF19r8rRbs';
    protected $_token       = 'KPxganSljXDBWqz9x7lOLoMef';
    private $_security_key  = '';
    private $_url           = '';   

    public function get_all_leads() 
    {
        $query = "SELECT+FirstName,LastName,MailingStreet,MailingCity,MailingState,MailingPostalCode,Phone,Email,Lead_Ref_Dealer_Name__c+FROM+Contact";
        $result = $this->get_data($query);
        $return = array('code' => $result['code'], 'data' => $result['data']->records);
        return $return;
    }

    public function get_todays_leads() 
    {
        $query = "SELECT+FirstName,LastName,MailingStreet,MailingCity,MailingState,MailingCountry,MailingPostalCode,Phone,Email,Lead_Ref_Dealer_Name__c,MODI_2_Export_Ready__c+FROM+Contact+WHERE+CreatedDate=LAST_N_DAYS:1";
        $result = $this->get_data($query);
        $data = array();
        foreach ($result['data']->records as $record) { //did this like this as opposed to adding to query because could compare to "true" in the where query which is what MODI_2_Export_Ready__c comes through as
            if ($record->MODI_2_Export_Ready__c) {
                $data[] = $record;
            }
        }
        $return = array('code' => $result['code'], 'data' => $data);
        return $return;
    }    

    public function get_data($query)
    {
        // Refresh Security Token
        $this->update_token();

        // Convert human-readable string into a URL string
        $SoQL = '/services/data/v20.0/query/?q='.$query;

        $curl = curl_init($this->_url.$SoQL);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Authorization: Bearer '.$this->_security_key));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $data = json_decode(curl_exec($curl));
        $responseCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);    

        $return = array('code' => $responseCode, 'data' => $data);
        return $return;
    }

    private function update_token()
    {
        $postdata = http_build_query(
            array(
                  'grant_type'      => 'password'
                , 'client_id'       => $this->_customerid
                , 'client_secret'   => $this->_secret
                , 'username'        => $this->_username
                , 'password'        => $this->_password // . $this->_token
            )
        );

        $opts = array('http' =>
            array(
                'method'  => 'POST',
                'header'  => 'Content-type: application/x-www-form-urlencoded',
                'content' => $postdata
            )
        );

        $context = stream_context_create($opts);

        // Open the file using the HTTP headers set above
        $file = file_get_contents($this->_token_url, false, $context);
        $result = json_decode($file);
        $this->_security_key = $result->access_token;
        $this->_url = $result->instance_url;
    }
   
}
