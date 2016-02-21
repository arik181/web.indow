<?php

/**
 * Builds a select box for choosing a state. Includes a blank option as the first option.
 * @param  string  $name        The name for the form element.
 * @param  string  $selectedVal The default value.
 * @param  boolean $abbrevVals  Whether or not to use the state abbreviation for the value. (AL or Alabama).
 * @param  array   $htmlOptions Associative array of attributes and their values to add to the select tag.
 * @return string               HTML to be echoed.
 */
function state_select($name = "state", $selectedVal = "", $abbrevVals = false, $htmlOptions = null, $abbrevNames = false)
{
$states = array(
        'AL' => "AL",
        'AK' => "AK",
        'AZ' => "AZ",
        'AR' => "AR",
        'CA' => "CA",
        'CO' => "CO",
        'CT' => "CT",
        'DE' => "DE",
        'DC' => "DC",
        'FL' => "FL",
        'GA' => "GA",
        'HI' => "HI",
        'ID' => "ID",
        'IL' => "IL",
        'IN' => "IN",
        'IA' => "IA",
        'KS' => "KS",
        'KY' => "KY",
        'LA' => "LA",
        'ME' => "ME",
        'MD' => "MD",
        'MA' => "MA",
        'MI' => "MI",
        'MN' => "MN",
        'MS' => "MS",
        'MO' => "MO",
        'MT' => "MT",
        'NE' => "NE",
        'NV' => "NV",
        'NH' => "NH",
        'NJ' => "NJ",
        'NM' => "NM",
        'NY' => "NY",
        'NC' => "NC",
        'ND' => "ND",
        'OH' => "OH",
        'OK' => "OK",
        'OR' => "OR",
        'PA' => "PA",
        'RI' => "RI",
        'SC' => "SC",
        'SD' => "SD",
        'TN' => "TN",
        'TX' => "TX",
        'UT' => "UT",
        'VT' => "VT",
        'VA' => "VA",
        'WA' => "WA",
        'WV' => "WV",
        'WI' => "WI",
        'WY' => "WY",
    );
    $canada = array (
        'AB' => 'AB',
        'BC' => 'BC',
        'MB' => 'MB',
        'NB' => 'NB',
        'NL' => 'NL',
        'NT' => 'NT',
        'NS' => 'NS',
        'NU' => 'NU',
        'ON' => 'ON',
        'PE' => 'PE',
        'QC' => 'QC',
        'SK' => 'SK',
        'YT' => 'YT'
    );
    $attributes = array();

    if ( is_array($htmlOptions) )
        foreach ($htmlOptions as $k => $v)
            $attributes[] = "$k='$v'";

    $attributes = implode(" ", $attributes);

    $html = "\n<select name='$name' $attributes>\n\t<option></option><optgroup label='- United States'>\n";
        
    foreach ($states as $name => $abbrev)
    {
        $val = $abbrevVals ? $abbrev : $name;
        $display = $abbrevNames ? $abbrev : $name;
        $html .= "\t<option value='$val'";
        if ($selectedVal == $val)
            $html .= " selected='selected'";
        $html .= ">$display</option>\n";
    }
        $html .= "</optgroup><optgroup label='- Canadian Provinces'>";
        foreach($canada as $name => $abbrev)
        {
                $val = $abbrevVals ? $abbrev : $name;
        $display = $abbrevNames ? $abbrev : $name;
        $html .= "\t<option value='$val'";
        if ($selectedVal == $val)
            $html .= " selected='selected'";
        $html .= ">$display</option>\n";
        }

    $html .= "</optgroup></select>";

    return $html;
}

// recursively delete directories
function rrmdir($dir)
{
    foreach ( glob($dir . '/*') as $file )
        if(is_dir($file)) rrmdir($file); else unlink($file);

    rmdir($dir);
}

function default_cards(array $cards = null, $name = 'cc_type') {
    if(!$cards) {
        $cards = array(
            'visa' => 'Visa',
            'master_card' =>
            'Master Card',
            'american_express' => 'American Express',
            'discover' => 'Discover Card'
        );
    }
    $html = "<select name='".$name."' id='".$name."'>";
    $html .= "<option value=''>--Select--</option>";
    foreach($cards as $value => $title) {
        $html .= "<option value='".$value."'>".$title."</option>";
    }
    $html .= "</select>";
    return $html;
}

function month_select($selected_month = null, $name='month', $abbr = false, $numeric = false) {
    $months = array(
        '01' => array('full' => 'January', 'abbr' => 'Jan'),
        '02' => array('full' => 'February', 'abbr' => 'Feb'),
        '03' => array('full' => 'March', 'abbr' => 'Mar'),
        '04' => array('full' => 'April', 'abbr' => 'Apr'),
        '05' => array('full' => 'May', 'abbr' => 'May'),
        '06' => array('full' => 'June', 'abbr' => 'June'),
        '07' => array('full' => 'July', 'abbr' => 'July'),
        '08' => array('full' => 'August', 'abbr' => 'Aug'),
        '09' => array('full' => 'September', 'abbr' => 'Sep'),
        '10' => array('full' => 'October', 'abbr' => 'Oct'),
        '11' => array('full' => 'November', 'abbr' => 'Nov'),
        '12' => array('full' => 'December', 'abbr' => 'Dec')
    );
    $html = "<select name='".$name."' id='".$name."'>";
    $html .= "<option value=''>--Select--</option>";
    foreach($months as $num => $array) {
        $value = ($numeric) ? $num : (($abbr) ? $array['abbr'] : $array['full']);
        $select = ($selected_month) ? $selected_month : false;
        $html .= "<option value='".$value. "'";
        $html .= ($select) ? (($select == $num || in_array(ucfirst($selected_month), $array)) ? " selected='selected'" : '') : '';
        $html .= ">";
        $html .= ($abbr) ? $array['abbr'] : $array['full'];
        $html .= "</option>";
    }
    $html .= "</select>";
    return $html;
}

function build_years($num = 12, $name = 'year') {
    for ($i = 0; $i <= $num; $i++) {
        $years[] = date('Y', strtotime('+'.$i.' years'));
//        $dates = new DateTime();
//        $dates->add(new DateInterval('P'.$i.'Y'));
//        $years[] = $dates->format('Y');
    }
    $html = "<select name='".$name."' id='".$name."'>";
    $html .= "<option value=''>--Select--</option>";
    foreach($years as $year) {
        $html .= "<option value='".$year."'>".$year."</option>";
    }
    $html .= "</select>";
    return $html;
}

function truncate($string, $limit, $pad="..."){
    // return with no change if string is shorter than $limit
    if(strlen($string) <= $limit) { return $string; }
    $string = substr($string, 0, $limit) . $pad;
    return $string;
}

function object_to_array($obj) {
    if(is_object($obj)) $obj = (array) $obj;
    if(is_array($obj)) {
        $new = array();
        foreach($obj as $key => $val) {
            $new[$key] = object_to_array($val);
        }
    }
    else $new = $obj;
    return $new;       
}

function display_address($address, $show_placeholder=true) {
    if (!$address) {
        return $show_placeholder ? 'Address unavailable' : '';
    }
    $ret = '';
    if ($address->address || $address->address_ext) {
        $ret .= $address->address . ' ' . $address->address_ext . '<br>';
    }
    $ret .= $address->city;
    if ($address->city && $address->state) {
        $ret .= ', ';
    }
    $ret .= $address->state;
    if ($address->city || $address->state) {
        $ret .= ' ';
    }
    if ($address->zipcode) {
        $ret .= $address->zipcode;
    }
    if ($ret === '') {
        return $show_placeholder ? 'Address unavailable' : '';
    } else {
        return $ret;
    }
}

function render_contact_info($customer, $addr) {
    $ret = '';
    if ($customer->customer_company_name) {
        $ret .= $customer->customer_company_name . '<br>&nbsp;<br>';
    }
    $ret .= $customer->first_name . ' ' . $customer->last_name . '<br>';
    if ($customer->phone_1) {
        $ret .= '<b>' . $customer->phone_1 . '</b><br>';
    }
    if ($customer->phone_2) {
        $ret .= '<b>' . $customer->phone_2 . '</b><br>';
    }
    if ($customer->phone_3) {
        $ret .= '<b>' . $customer->phone_3 . '</b><br>';
    }
    if ($customer->email_1) {
        $ret .= '<b>' . $customer->email_1 . '</b><br>';
    }
    if ($customer->email_2) {
        $ret .= '<b>' . $customer->email_2 . '</b><br>';
    }
    $address = display_address($addr, false);
    if ($address) {
        $ret .= $address . '<br>';
    }
    return $ret;
}
