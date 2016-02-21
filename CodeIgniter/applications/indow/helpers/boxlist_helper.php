<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

function generate_boxlist($id, $data, $items, $keys)
{
$html = "";
ob_start();?>
<div class="row">
    <div class="col-xs-12">
        <div class="panel-group" id="accordion">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h2 class="panel-title collapsed" data-toggle="collapse" data-target="#boxlistCollapse">
                        <?= $data['title'] ?> <span class="glyphicon glyphicon-chevron-down pull-right"></span>
                    </h2>
                </div>
                <div id="boxlistCollapse" class="panel-collapse collapse">
                    <div class="panel-body">
                        <?php
    if ( ! empty($items) )
    {
        $html .= '<table class="boxlist_table boxlist_view">';
        $html .= '<tr class="boxlist_row">';
        $html .= '<th><span class="boxlist_cell">Actions</span></th>';

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
            $html .= '<tr class="boxlist_row">';
            $html .= '<td>';
            $html .= '<a href="" class="boxlist_cell"><i class="fa fa-times"></i></a>';
            $html .= '</td>';

            foreach ($keys as $key => $value)
            {
                $html .= '<td>';
                if ( isset($value) && ! empty($value) )
                {
                    $html .= '<a href="' . $value['a'] . '" class="boxlist_cell">' . $item[$key] . '</a>';

                } else {

                    $html .= $item[$key];
                }
                $html .= '</td>';
            }
        }
        $html .='       </tr> 
                    </table>
            ';

    } else {
        $html .= '<div class="notesempty alert alert-warning">'.$data['empty_message'].'</div>';
    }
                        ?>
                        <?= $html ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
    <?php

    $html = ob_get_clean();


    return $html;
}   

?>
