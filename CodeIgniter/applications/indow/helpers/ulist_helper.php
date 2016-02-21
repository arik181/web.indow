<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

function generate_ulist($id, $data, $items, $keys) {
    $html = '<div class="ulist_view">';
    if (!empty($items)) {
        if (isset($data['title']) && !empty($data['title'])) {
            $html .= '<h4 class="ulist_header">' . $data['title'] . '</h4><br/>';
        }
        $html .= '<table class="ulist_table">';


        if ($data['has_add'] === true) {
            $html .= form_open($data['form'], array('class' => 'list_form'));
            $html .= '<a href="' . $data['add_path'] . '" class="btn btn-default btn-add ulist_button inline">' . $data['add_button'] . '</a>';
        }

        $html .= '<tr class="ulist_row">';

        if ($data['has_actions'] === true) {
            $html .= '<th><span class="ulist_cell">Actions</span></th>';
        }
        foreach ($keys as $key => $value) {
            if (isset($value) && !empty($value)) {
                $html .= '<th>' . $value['th'] . '</th>';
            }
        }
        if ($data['has_actions'] === true) {
            $html .= '<th><span class="ulist_cell">Details</span></th>';
        }

        $html .= "</tr>";

        foreach ($items as $item) {
            $html .= '<tr class="ulist_row">';

            if ($data['has_actions'] === true) {
                $html .= '<td>';
                $html .= '<a href="" class="ulist_cell"><i class="fa fa-times"></i></a>';
                $html .= '</td>';
            }

            foreach ($keys as $key => $value) {
                $html .= '<td>';
                if (isset($value) && !empty($value)) {
                    $html .= '<a href="' . $value['a'] . '" class="ulist_cell">' . $item[$key] . '</a>';
                } else {

                    $html .= $item[$key];
                }
                $html .= '</td>';
            }

            if ($data['has_details'] === true) {
                $html .= '<td>';
                $html .= '<a href="" class="ulist_cell"><i class="fa fa-info-circle"></i></a>';
                $html .= '</td>';
            }

            $html .= "</tr>";
        }
        $html .='</table>';
        $html .='</div>';
        if ($data['has_add'] === true) {
            $html .= form_close();
        }
    } else {
        $html = '<div class="ulist_view">';
        if (isset($data['title']) && !empty($data['title'])) {
            $html .= '<h4 class="ulist_header">' . $data['title'] . '</h4><br/>';
        }
        if ($data['has_add'] === true) {
            $html .= '<a href="' . $data['add_path'] . '" class="btn btn-default btn-add ulist_button inline">' . $data['add_button'] . '</a>';
        }

        $html .= $data['empty_message'];
        $html .='</div>';
    }

    return $html;
}

/* Function to show Existing Users */

function generate_ulist_new($id, $data, $items, $keys, $siteid) {
    $html = '<div class="ulist_view">';
    if (!empty($items)) {
        if (isset($data['title']) && !empty($data['title'])) {
            $html .= '<h4 class="ulist_header">' . $data['title'] . '</h4><br/>';
        }
        $html .= '<table class="ulist_table">';


        if ($data['has_add'] === true) {
            $html .= form_open($data['form'], array('class' => 'list_form'));
            $html .= '<a href="' . $data['add_path'] . '" class="btn btn-default btn-add ulist_button inline">' . $data['add_button'] . '</a>';
        }

        $html .= '<tr class="ulist_row">';

        if ($data['has_actions'] === true) {
            $html .= '<th><span class="ulist_cell">Actions</span></th>';
        }
        foreach ($keys as $key => $value) {
            if (isset($value) && !empty($value)) {
                $html .= '<th>' . $value . '</th>';
            }
        }
        if ($data['has_actions'] === true) {
            $html .= '<th><span class="ulist_cell">Details</span></th>';
        }

        $html .= "</tr>";
        foreach ($items as $item) {
            $html .= '<tr class="ulist_row">';

            if ($data['has_actions'] === true) {
                $html .= '<td>';
                $html .= '<a href="" class="ulist_cell"><i class="fa fa-times"></i></a>';
                $html .= '</td>';
            }

            foreach ($keys as $key => $value) {
                $html .= '<td>';
                if (isset($value) && !empty($value)) {
                    $html .= '<a href="' . $value . '" class="ulist_cell">' . $item[$key] . '</a>';
                } else {

                    $html .= $item[$key];
                }
                $html .= '</td>';
            }

            if ($data['has_details'] === true) {
                $html .= '<td>';
                $html .= '<a href="#'.$item['id'].'" class="ulist_cell info-link"><i class="fa fa-info-circle"></i></a>';

                
                $html .= '<div style="display: none; top: 555px; left: 605px;" class="popover fade right in existing-cust-popup"><div class="arrow" style="bottom: -1 !important;"></div><h3 style="display: none;" class="popover-title"></h3><div class="popover-content">        <div class="popover_content">
                        
                          <p class="name-address">ff</p>
                          <p class="contact-number">dd</p>
                          <p class="contact-email">sss</p>
                          
                          <a href="" class="btn btn-default btn-content pull-right mark-as-primary" siteid="'.$siteid.'">Mark as Primary</a>

                        </div></div>';

                $html .= '</td>';
            }

            $html .= "</tr>";
        }
        $html .='</table>';
        $html .='</div>';
        if ($data['has_add'] === true) {
            $html .= form_close();
        }
    } else {
        $html = '<div class="ulist_view">';
        if (isset($data['title']) && !empty($data['title'])) {
            $html .= '<h4 class="ulist_header">' . $data['title'] . '</h4><br/>';
        }
        if ($data['has_add'] === true) {
            $html .= '<a href="' . $data['add_path'] . '" class="btn btn-default btn-add ulist_button inline">' . $data['add_button'] . '</a>';
        }

        $html .= $data['empty_message'];
        $html .='</div>';
    }

    return $html;
}

?>
