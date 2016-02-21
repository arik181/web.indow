<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

function generate_macrolist($id, $data, $items, $keys)
{   
    $html =  '<div class="macrolist_view">';
    if ( ! empty($items) ) 
    {
        if (isset($data['title']) && ! empty($data['title']) )
        {
            $html .= '<h4 class="macrolist_header">' . $data['title'] . '</h4><br/>';
        }
        $html .= '<table class="macrolist_table">';


        if ( $data['has_add'] === true )
        {
            $html .= form_open($data['form'], array('class'=>'list_form'));
            $html .= '<a href="' . $data['add_path'] . '" class="btn btn-default btn-add macrolist_button inline">' . $data['add_button'] . '</a>';
        }

        $html .= '<tr class="macrolist_row">';

        if ( $data['has_actions'] === true )
        {
            $html .= '<th><span class="macrolist_cell">Actions</span></th>';
        }

        foreach ($keys as $key => $value)
        {
            if ( isset($value) && ! empty($value) )
            {
                $html .= '<th>' . $value['th'] . '</th>';
            }
        }

        if ( $data['has_details'] === true )
        {
            $html .= '<th><span class="macrolist_cell">Details</span></th>';
        }

        $html .= "</tr>";
        if (empty($data['action_url'])) {
            $data['action_url'] = '';
        }
        foreach ($items as $item)
        {
            $html .= '<tr class="macrolist_row">';

            if ( $data['has_actions'] === true )
            {
                if (!isset($item->id)) {
                    $item->id = '';
                }
                $html .= <<<ACTIONS
                <td class="macrolist_icon_cell">
                <a href="{$data['action_url']}/{$item->id}" class="icon"><i class="sprite-icons view"></i></a>
                </td>
ACTIONS;
            }

            foreach ($keys as $key => $value)
            {
                $html .= '<td class="macrolist_cell">';
                if ( isset($value) && ! empty($value['a']) )
                {
                    $html .= '<a href="' . $value['a'] . '">' . $item->$key . '</a>';

                } else {
                    $html .= $item->$key;
                }
                $html .= '</td>';
            }

            if ( $data['has_details'] === true )
            {
                $html .= '<td>';
                $html .= '<a href=""><i class="fa fa-info-circle"></i></a>';
                $html .= '</td>';
            }

            $html .= "</tr>";
        }
        $html .='</table>';
        $html .='</div>';
        if ( $data['has_add'] === true )
        {
            $html .= form_close(); 
        }

    } else {

        $html = '<div class="row show-grid-sm">';

        if (isset($data['title']) && ! empty($data['title']) )
        {
            $html .= '<div class="col-xs-6"><h4 class="inline">' . $data['title'] . '</h4></div>';
        }

        if ( $data['has_add'] === true )
        {
            $html .= '<div class="col-xs-6 text-right"><a href="' . $data['add_path'] . '" class="btn btn-default btn-sm">' . $data['add_button'] . '</a></div>';
        }

        $html .='</div>';


        $html .='<div class="alert alert-warning">';
        $html .= $data['empty_message'];
        $html .='</div>';

    }

    return $html;
}   

?>
