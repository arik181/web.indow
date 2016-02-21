<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

function gen_options($optionsn, $value) {
    if ($optionsn == 'states') {
        $options = array(
            'AL'=>'AL',
            'AK'=>'AK',
            'AZ'=>'AZ',
            'AR'=>'AR',
            'CA'=>'CA',
            'CO'=>'CO',
            'CT'=>'CT',
            'DE'=>'DE',
            'DC'=>'DC',
            'FL'=>'FL',
            'GA'=>'GA',
            'HI'=>'HI',
            'ID'=>'ID',
            'IL'=>'IL',
            'IN'=>'IN',
            'IA'=>'IA',
            'KS'=>'KS',
            'KY'=>'KY',
            'LA'=>'LA',
            'ME'=>'ME',
            'MD'=>'MD',
            'MA'=>'MA',
            'MI'=>'MI',
            'MN'=>'MN',
            'MS'=>'MS',
            'MO'=>'MO',
            'MT'=>'MT',
            'NE'=>'NE',
            'NV'=>'NV',
            'NH'=>'NH',
            'NJ'=>'NJ',
            'NM'=>'NM',
            'NY'=>'NY',
            'NC'=>'NC',
            'ND'=>'ND',
            'OH'=>'OH',
            'OK'=>'OK',
            'OR'=>'OR',
            'PA'=>'PA',
            'RI'=>'RI',
            'SC'=>'SC',
            'SD'=>'SD',
            'TN'=>'TN',
            'TX'=>'TX',
            'UT'=>'UT',
            'VT'=>'VT',
            'VA'=>'VA',
            'WA'=>'WA',
            'WV'=>'WV',
            'WI'=>'WI',
            'WY'=>'WY',
        );
    } elseif ($optionsn == 'countries') {
        $options = array(
            "Afghanistan",
            "Albania",
            "Algeria",
            "American Samoa",
            "Andorra",
            "Angola",
            "Anguilla",
            "Antarctica",
            "Antigua and Barbuda",
            "Argentina",
            "Armenia",
            "Aruba",
            "Australia",
            "Austria",
            "Azerbaijan",
            "Bahamas",
            "Bahrain",
            "Bangladesh",
            "Barbados",
            "Belarus",
            "Belgium",
            "Belize",
            "Benin",
            "Bermuda",
            "Bhutan",
            "Bolivia",
            "Bosnia and Herzegowina",
            "Botswana",
            "Bouvet Island",
            "Brazil",
            "British Indian Ocean Territory",
            "Brunei Darussalam",
            "Bulgaria",
            "Burkina Faso",
            "Burundi",
            "Cambodia",
            "Cameroon",
            "Canada",
            "Cape Verde",
            "Cayman Islands",
            "Central African Republic",
            "Chad",
            "Chile",
            "China",
            "Christmas Island",
            "Cocos (Keeling) Islands",
            "Colombia",
            "Comoros",
            "Congo",
            "Congo, the Democratic Republic of the",
            "Cook Islands",
            "Costa Rica",
            "Cote d'Ivoire",
            "Croatia (Hrvatska)",
            "Cuba",
            "Cyprus",
            "Czech Republic",
            "Denmark",
            "Djibouti",
            "Dominica",
            "Dominican Republic",
            "East Timor",
            "Ecuador",
            "Egypt",
            "El Salvador",
            "Equatorial Guinea",
            "Eritrea",
            "Estonia",
            "Ethiopia",
            "Falkland Islands (Malvinas)",
            "Faroe Islands",
            "Fiji",
            "Finland",
            "France",
            "France Metropolitan",
            "French Guiana",
            "French Polynesia",
            "French Southern Territories",
            "Gabon",
            "Gambia",
            "Georgia",
            "Germany",
            "Ghana",
            "Gibraltar",
            "Greece",
            "Greenland",
            "Grenada",
            "Guadeloupe",
            "Guam",
            "Guatemala",
            "Guinea",
            "Guinea-Bissau",
            "Guyana",
            "Haiti",
            "Heard and Mc Donald Islands",
            "Holy See (Vatican City State)",
            "Honduras",
            "Hong Kong",
            "Hungary",
            "Iceland",
            "India",
            "Indonesia",
            "Iran (Islamic Republic of)",
            "Iraq",
            "Ireland",
            "Israel",
            "Italy",
            "Jamaica",
            "Japan",
            "Jordan",
            "Kazakhstan",
            "Kenya",
            "Kiribati",
            "Korea, Democratic People's Republic of",
            "Korea, Republic of",
            "Kuwait",
            "Kyrgyzstan",
            "Lao, People's Democratic Republic",
            "Latvia",
            "Lebanon",
            "Lesotho",
            "Liberia",
            "Libyan Arab Jamahiriya",
            "Liechtenstein",
            "Lithuania",
            "Luxembourg",
            "Macau",
            "Macedonia, The Former Yugoslav Republic of",
            "Madagascar",
            "Malawi",
            "Malaysia",
            "Maldives",
            "Mali",
            "Malta",
            "Marshall Islands",
            "Martinique",
            "Mauritania",
            "Mauritius",
            "Mayotte",
            "Mexico",
            "Micronesia, Federated States of",
            "Moldova, Republic of",
            "Monaco",
            "Mongolia",
            "Montserrat",
            "Morocco",
            "Mozambique",
            "Myanmar",
            "Namibia",
            "Nauru",
            "Nepal",
            "Netherlands",
            "Netherlands Antilles",
            "New Caledonia",
            "New Zealand",
            "Nicaragua",
            "Niger",
            "Nigeria",
            "Niue",
            "Norfolk Island",
            "Northern Mariana Islands",
            "Norway",
            "Oman",
            "Pakistan",
            "Palau",
            "Panama",
            "Papua New Guinea",
            "Paraguay",
            "Peru",
            "Philippines",
            "Pitcairn",
            "Poland",
            "Portugal",
            "Puerto Rico",
            "Qatar",
            "Reunion",
            "Romania",
            "Russian Federation",
            "Rwanda",
            "Saint Kitts and Nevis",
            "Saint Lucia",
            "Saint Vincent and the Grenadines",
            "Samoa",
            "San Marino",
            "Sao Tome and Principe",
            "Saudi Arabia",
            "Senegal",
            "Seychelles",
            "Sierra Leone",
            "Singapore",
            "Slovakia (Slovak Republic)",
            "Slovenia",
            "Solomon Islands",
            "Somalia",
            "South Africa",
            "South Georgia and the South Sandwich Islands",
            "Spain",
            "Sri Lanka",
            "St. Helena",
            "St. Pierre and Miquelon",
            "Sudan",
            "Suriname",
            "Svalbard and Jan Mayen Islands",
            "Swaziland",
            "Sweden",
            "Switzerland",
            "Syrian Arab Republic",
            "Taiwan, Province of China",
            "Tajikistan",
            "Tanzania, United Republic of",
            "Thailand",
            "Togo",
            "Tokelau",
            "Tonga",
            "Trinidad and Tobago",
            "Tunisia",
            "Turkey",
            "Turkmenistan",
            "Turks and Caicos Islands",
            "Tuvalu",
            "Uganda",
            "Ukraine",
            "United Arab Emirates",
            "United Kingdom",
            "United States",
            "United States Minor Outlying Islands",
            "Uruguay",
            "Uzbekistan",
            "Vanuatu",
            "Venezuela",
            "Vietnam",
            "Virgin Islands (British)",
            "Virgin Islands (U.S.)",
            "Wallis and Futuna Islands",
            "Western Sahara",
            "Yemen",
            "Yugoslavia",
            "Zambia",
            "Zimbabwe"
        );
    } else {
        $options = $optionsn;
    }
    $ret = '';
    if ($optionsn == 'countries') {
        foreach($options as $k) {
            $selected = '';
            if ($k == $value) {
                $selected = "selected='selected'";
            }
            $ret .= "<option value='$k' $selected>$k</option>";
        }
    } else {
        foreach($options as $k => $v) {
            $selected = '';
            if ($k == $value) {
                $selected = "selected='selected'";
            }
            $ret .= "<option value='$k' $selected>$v</option>";
        }
    }
    return $ret;
}

function generate_contact_info($customer_id, $data, $customer, $keys, $mode='default')
{
    $html = '';
    ob_start();
    $phone_options = array('Home', 'Work', 'Mobile');
    $email_options = array('Home', 'Work');
    ?>
    <div id="contact_info_view" class="info_view">
    <?
    $html = ob_get_clean();
    ob_start();
    ?>
        <div class='row'>
            <form id="editcustomerform" class='form-horizontal' method="post" action='/customers/edit/<?= $customer_id ?>' onsubmit="return contactinfo_submit(this);">
                <? if (isset($data['module'])) { ?>
                    <input type='hidden' name='module' value='<?= $data['module'] ?>'>
                <? } ?>
                <input type='hidden' id='customer_id' name='customer_id' value='<?= $customer_id ?>'>
                <div class='form-group'>
                    <h3 class='col-xs-12'>Contact Info</h3>
                </div>

                <div class='form-group'>

                    <label for='first' class='col-xs-3 control-label'>First Name</label>
                    <div class='col-xs-9'>
                        <input id='first' name='first_name' class='form-control input-sm' value='<?= $customer['first_name'] ?>'>
                    </div>
                </div>

                <div class='form-group'>
                    <label for='last' class='col-xs-3 control-label'>Last Name</label>
                    <div class='col-xs-9'>
                        <input class='form-control input-sm' id='last' name='last_name' value='<?= $customer['last_name'] ?>'>
                    </div>
                </div>

                <div class='form-group'>
                    <label for='company' class='control-label col-xs-3'>Company</label>
                    <div class='col-xs-9'>
                        <input class='form-control input-sm' id='company' name='organization_name' value='<?= $customer['organization_name'] ?>'>
                    </div>
                </div>

                <div class='form-group'>
                    <label for='email_1' class='control-label col-xs-3'>Email</label>
                    <div class='col-xs-6'>
                        <input type='email' class='form-control input-sm' id='email_1' name='email_1'  value='<?= $customer['email_1'] ?>'>
                    </div>
                    <div class='col-xs-3 text-right'>
                        <?= form_dropdown('email_type_1',$email_options,$customer['email_type_1_int'], "class='form-control input-sm'");?>
                    </div>
                </div>

                <div class='form-group'>
                    <label for='email_2' class='control-label col-xs-3'>Email</label>
                    <div class='col-xs-6'>
                        <input type='email' class='form-control input-sm' id='email2' name='email_2' value='<?= $customer['email_2'] ?>'>
                    </div>
                    <div class='col-xs-3'>
                        <?= form_dropdown('email_type_2',$email_options,$customer['email_type_2_int'], "class='form-control input-sm'");?>
                    </div>
                </div>

                <div class='form-group'>
                    <label for='phone1' class='control-label col-xs-3'>Phone</label>
                    <div class='col-xs-6'>
                        <input class='form-control input-sm' id='phone1' name='phone_1' value='<?= $customer['phone_1'] ?>'>
                    </div>
                    <div class='col-xs-3'>
                        <?= form_dropdown('phone_type_1',$phone_options, $customer['phone_type_1_int'], "class='form-control input-sm'");?>
                    </div>
                </div>

                <div class='form-group'>
                    <label for='phone_2' class='control-label col-xs-3'>Phone</label>
                    <div class='col-xs-6'>
                        <input class='form-control input-sm' id='phone_2' name='phone_2' value='<?= $customer['phone_2'] ?>'>
                    </div>
                    <div class='col-xs-3'>
                        <?= form_dropdown('phone_type_2',$phone_options, $customer['phone_type_2_int'], "class='form-control input-sm'");?>
                    </div>
                </div>

                <div class='form-group'>
                    <label for='phone_3' class='control-label col-xs-3'>Phone</label>
                    <div class='col-xs-6'>
                        <input class='form-control input-sm' id='phone_3' name='phone_3' value='<?= $customer['phone_3'] ?>'>
                    </div>
                    <div class='col-xs-3'>
                        <?= form_dropdown('phone_type_3',$phone_options, $customer['phone_type_3_int'], "class='form-control input-sm'");?>
                    </div>
                </div>

                <div class='form-group'>
                    <div class='col-xs-12 text-right'>
                        <input id="editcustomersave" type='submit' name='submit' value='Save' class='btn btn-blue'>
                    </div>
                </div>
            </form>
        </div>
    <?
    $edit_form = str_replace('"', "'", ob_get_clean()); // why not to store huge html sections in tag attributes... edit: also because its really hard to bind stuff to that html using jquery :\ so much nicer to clone from hidden containers
    if ( isset($data['edit_link']))
    {
        ob_start();
        ?>
        <a id="customer_edit_btn" href="<?= $data['edit_link'] ?>" class="btn btn-blue btn-sm" data-toggle="popover" data-placement="right" data-content="<?= $edit_form ?>">Manage</a>
        <script>
            $("a[data-toggle=popover]").popover({
                html: true,
                title: function () {
                    return $(this).parent().find(".head").html();
                },
                content: function () {
                    return $(this).parent().find(".content").html();
                }
            });
        </script>
        <?
        $edit_form = ob_get_clean();
    } else {
        $edit_form = '';
    }

    ob_start();
    ?>
    <div class='contact_group'>
        <h4 class="inline info_header"><?= $data['title'] ?></h4>
        <?
            if ($mode === 'primary') {
                echo "<button class='btn btn-blue btn-sm' id='manage_customers'>Manage</button>";
            } elseif($mode== 'view') {

            } else {
                echo $edit_form;
            }
        ?>
    </div>
    <div class='show-grid'>
        <span class="contact_name" id='contact_first_name'><?= $customer['first_name'] ?></span>
        <span class="contact_name" id='contact_last_name'><?= $customer['last_name'] ?></span><br />
        <div id='contact_organization_name' class='contact_item'><?= $customer['company_name'] ?></div>
    </div>
    <div class='show-grid'>
        <? if (!empty($customer['phone_1'])) { ?>
            <div class='contact_set'>
                <div id='contact_phone_type_1' class='contact_type'><?= $customer['phone_type_1'] ?></div>
                <div id='contact_phone_1' class='contact_item'><?= $customer['phone_1'] ?></div>
            </div>
        <? } ?>
        <? if (!empty($customer['phone_2'])) { ?>
            <div class='contact_set'>
                <div id='contact_phone_type_1' class='contact_type'><?= $customer['phone_type_2'] ?></div>
                <div id='contact_phone_1' class='contact_item'><?= $customer['phone_2'] ?></div>
            </div>
        <? } ?>
        <? if (!empty($customer['phone_3'])) { ?>
            <div class='contact_set'>
                <div id='contact_phone_type_1' class='contact_type'><?= $customer['phone_type_3'] ?></div>
                <div id='contact_phone_1' class='contact_item'><?= $customer['phone_3'] ?></div>
            </div>
        <? } ?>
    </div>
    <div class='show-grid'>
        <? if (!empty($customer['email_1'])) { ?>
            <div class='contact_set'>
                <div id='contact_email_type_1' class='contact_type'><?= $customer['email_type_1'] ?></div>
                <div id='contact_email_1' class='contact_item'><?= $customer['email_1'] ?></div>
            </div>
        <? } ?>
        <? if (!empty($customer['email_2'])) { ?>
            <div class='contact_set'>
                <div id='contact_email_type_2' class='contact_type'><?= $customer['email_type_2'] ?></div>
                <div id='contact_email_2' class='contact_item'><?= $customer['email_2'] ?></div>
            </div>
        <? } ?>
    </div>
</div>
<?
    $html .= ob_get_clean();
    return $html;
}
?>
