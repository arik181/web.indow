<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

function generate_chart($id, $data, $keys, $labels)
{   
    $html =  '<div class="chart_view">';
    if ( ! empty($items) ) 
    {
        if (isset($data['title']) && ! empty($data['title']) )
        {
            $html .= '<h4 class="chart_header">' . $data['title'] . '</h4><br/>';
        }
        $html .= '<table class="chart_table">';


        if ( $data['has_add'] === true )
        {
            $html .= form_open($data['form'], array('class'=>'list_form'));
            $html .= '<a href="' . $data['add_path'] . '" class="btn btn-default btn-add chart_button inline">' . $data['add_button'] . '</a>';
        }

        $html .= '<tr class="chart_row">';

        if ( $data['has_actions'] === true )
        {
            $html .= '<th><span class="chart_cell">Actions</span></th>';
        }

        foreach ($keys as $key => $value)
        {
            if ( isset($value) && ! empty($value) )
            {
                $html .= '<th>' . $value['th'] . '</th>';
            }
        }

        if ( $data['has_actions'] === true )
        {
            $html .= '<th><span class="chart_cell">Details</span></th>';
        }

        $html .= "</tr>";

        foreach ($items as $item)
        {
            $html .= '<tr class="chart_row">';

            if ( $data['has_actions'] === true )
            {
                $html .= '<td>';
                $html .= '<a href="" class="chart_cell"><i class="fa fa-times"></i></a>';
                $html .= '</td>';
            }

            foreach ($keys as $key => $value)
            {
                $html .= '<td>';
                if ( isset($value) && ! empty($value) )
                {
                    $html .= '<a href="' . $value['a'] . '" class="chart_cell">' . $item[$key] . '</a>';

                } else {

                    $html .= $item[$key];
                }
                $html .= '</td>';
            }

            if ( $data['has_details'] === true )
            {
                $html .= '<td>';
                $html .= '<a href="" class="chart_cell"><i class="fa fa-info-circle"></i></a>';
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
        $html = '<div class="chart_view">';
        if (isset($data['title']) && ! empty($data['title']) )
        {
            $html .= '<h4 class="chart_header">' . $data['title'] . '</h4><br/>';
        }
        if ( $data['has_add'] === true )
        {
            $html .= '<a href="' . $data['add_path'] . '" class="btn btn-default btn-add chart_button inline">' . $data['add_button'] . '</a>';
        }

        $html .= $data['empty_message'];
        $html .='</div>';
    }

    return $html;
}   
