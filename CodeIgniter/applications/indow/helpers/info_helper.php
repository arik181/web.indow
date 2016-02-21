<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

function generate_info($id, $data, $items, $keys) 
{   

    $html = '<div class="info_view">
                <div class="contact_group">
                    <h4 class="inline info_header">' . $data['title'] . '</h4>';

    if ( isset($data['edit_link']) )
    {
        $type_0="";
        $type_1="";
        if($items['address_type']==0){
            $type_0="selected";
        }else{
            $type_1="selected";
        }

        $state_select = state_select('state',$items['state'], true, array('class' => 'form-control input-sm'));

        $edit_form = <<<EOT
        <label for='site_address'>Address</label>
        <input type='hidden' id='site_id' name='id' value='{$id}'>
        <input class='form-control input-sm' name='address' id='site_address' value='{$items['address']}'></input>
        <label for='site_address_ext'>Address ext</label>
        <input class='form-control input-sm' name='address_ext' id='site_address_ext' value='{$items['address_ext']}'></input>
        <label for='city'>City</label>
        <input class='form-control input-sm' name='city' id='city' class='large_input' value='{$items['city']}'></input>
        <label for='state'>State</label>
        {$state_select}
         
        <label for='zipcode'>Zip-Code</label>
        <input class='form-control input-sm' name='zipcode' id='zipcode' value='{$items['zipcode']}'></input>
        <select class='input-sm form-control' id='address_type' name='address_type'>
            <option value='0' {$type_0}>Residential</option>
            <option value='1' {$type_1}>Business</option>
        </select>
        <button id='site_info_update_button' type='submit' name='submit' value='Save' class='btn btn-default btn-content pull-right'>Save</button><br><br>

EOT;
        $html .= '<div style="display: none"><div id="site_edit_form"">' . $edit_form . '</div></div>';
         $html .= '<form id="site_info_form" class="inline" method="post">';
        $html .= '<a id="edit_site_button" href="' . $data['edit_link'] . '" class="inline btn btn-default btn-content aeditform">Edit</a></div>';
         $html .= '<script>$("a[data-toggle=popover]").popover({
                html: true,
                title: function () {
                    return $(this).parent().find(".head").html();
                },
                content: function () {
                    return $(this).parent().find(".content").html();
                }
        });</script>';
          $html .= '</form>';
    }
    /*Added condtion to add EDIT link in front of Customer info*/
    if(isset($data['primary_customer']) && ($data['primary_customer'] == 1)){
     $html .= '<a href="javascript:void(0);" class="inline btn btn-default btn-content" id="remove-tab" data-placement="right" data-content="">Manage</a>';
    }

    if ( ! empty($items) ) 
    {
        foreach ($items as $key=>$item)
        {
           $html.='<span id="json' . $key .'" >';
            $html .= $item;
            $html .= '</span><br/>';
     
        }

    } else {
            
            $html .= '<span id="jsonname"></span>
            <br>
            <span id="jsonphone_1"></span>
            <br>
            <span id="jsonemail_1"></span>
            <br>';
        $html .= '<span id="no-primary">'.$data['empty_message']."</span>";
    }

    $html .= '</div>';

    return $html;
}   




?>
