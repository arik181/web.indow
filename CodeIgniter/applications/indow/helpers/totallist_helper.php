<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

function generate_totallist($id, $data, $items, $keys)
{   
    $html = '<div class="totallist_view">';
    $html .= '<h4 class="inline totallist_header">' . $data['title'] . '</h4>';
    $html .='<a class="btn btn-default window-btn">Launch in MAPP</a>';
    $html .= form_open($data['form'], array('id'=>'search_form','class'=>'list_form'));
    //echo "<pre>"; print_r($keys); print_r($items);exit;
    if ( ! empty($items) ) 
    {
        $html .= '<table class="totallist_table">';

        $html .= '<a href="' . $data['button_1_path'] . '" class="btn btn-default totallist_button inline">' . $data['button_1'] . '</a>';
        $html .= '<a href="' . $data['button_2_path'] . '" class="btn btn-default totallist_button inline">' . $data['button_2'] . '</a>';

        $html .= '<tr class="totallist_row">';
        $html .= '<th><span class="totallist_cell"></span></th>';

        foreach ($keys as $key => $value)
        {
            if ( isset($value) && ! empty($value) )
            {
                $html .= '<th>' . $value['th'] . '</th>';
            }
        }

        $html .= "</tr>";
       
        foreach ($items as $item)
        {
            $html .= "\n";
            $html .= '<tr class="totallist_row">';
            $html .= '<td>';
            $html .= '<a href="" class="totallist_cell"><i class="fa fa-times"></i></a>';
            $html .= '</td>';

            foreach ($keys as $key => $value)
            {
                $html .= '<td>';
                if ( isset($value) && ! empty($value) )
                {
                    $html .= '<a href="' . $value['a'] . '" class="totallist_cell">' . $item[$key] . '</a>';

                } else {

                    $html .= $item[$key];
                }
                $html .= '</td>';
            }
        }
        $html .= '</tr>';
        $html .= '</table>';

    } else {
        $html .= '<p class="totallist_empty_message">' . $data['empty_message'] . '</p>';
    }
    
    $html .= form_close(); 

    $html .= '</div>';

    return $html;
}

?>
