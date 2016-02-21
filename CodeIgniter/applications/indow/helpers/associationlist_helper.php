<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

function generate_association_list($id, $data, $items, $keys)
{
    $html = "";
    ob_start();?>
    <div class="row">
        <div class="col-xs-12">
            <div class="panel-group" id="accordion">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h2 class="panel-title collapsed" data-toggle="collapse" data-target="#assocCollapse">
                            <?= $data['title'] ?> <span class="glyphicon glyphicon-chevron-down pull-right"></span>
                        </h2>
                    </div>
                    <div id="assocCollapse" class="panel-collapse collapse">
                        <div class="panel-body">

<?php

    // TODO find as type
    if ( ! empty($items) ) 
    {
        $i = 0;
        $flag = 0;

        foreach ($items as $item)
        {
            // We've hit a new column
            if (($i % 4) === 0)
            {
                $html .= '<div class="row"><table class="association_list_table ass-tbl">';
                $html .= '<tr class="association_list_row">';
                $html .= '<th><span class="association_list_cell">Actions</span></th>';

                foreach ($keys as $key => $value)
                {
                    if ( isset($value) && ! empty($value) )
                    {
                        $html .= '<th>' . $value['th'] . '</th>';
                    }
                }

                $html .= "</tr>";
            }

            $html .= '<tr class="association_list_row">';
            $html .= '<td>';
            $html .= '<a href="" class="association_list_cell"><i class="fa fa-times"></i></a>';
            $html .= '</td>';

            foreach ($keys as $key => $value)
            {
                $html .= '<td>';
                if ( isset($value) && ! empty($value) )
                {
                    $html .= '<a href="' . $value['a'] . '" class="association_list_cell">' . $item[$key] . '</a>';
                } else {
                    $html .= $item[$key];
                }
                $html .= '</td>';
                $flag++;
            }

            $html .= "</tr>";
            // We've hit the max number of rows
            if ($i==3 && ($i % 4) === 3)
            {
                $html .='</table></div>';
                $i = -1;
            }
            ++$i;
            
        }
        if($i < 3){
            $html .='</table></div>';
        }
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

function generate_association_list_all($data, $items, $keys)
{   
    $html = ' 
        <h4 class="inline association_list_header">' . $data['title'] . '</h4>
            <div class="association_list_view">';

    // TODO find as type
    if ( ! empty($items) ) 
    {
        $i = 0;

        foreach ($items as $item)
        {
            // We've hit a new column
            if (($i % 4) === 0)
            {
                $html .= '<div class="association_list_column">';
                $html .= '<table class="association_list_table">';
                $html .= '<tr class="association_list_row">';
                $html .= '<th><span class="association_list_cell">Actions</span></th>';

                foreach ($keys as $key => $value)
                {
                    if ( isset($value) && ! empty($value) )
                    {
                        $html .= '<th>' . $value['th'] . '</th>';
                    }
                }

                $html .= "</tr>";
            }


            $html .= '<tr class="association_list_row">';
            $html .= '<td>';
            $html .= '<a href="" class="association_list_cell"><i class="fa fa-times"></i></a>';
            $html .= '</td>';

            foreach ($keys as $key => $value)
            {
                $html .= '<td>';
                if ( isset($value) && ! empty($value) )
                {
                    $html .= '<a href="javascript:void(0);' . $value['a'] . '" class="association_list_cell">' . $item[$key] . '</a>';

                } else {

                    $html .= $item[$key];
                }
                $html .= '</td>';
            }

            $html .= "</tr>";

            // We've hit the max number of rows
            if (($i % 4) === 3)
            {
                $html .='
                  </table>
               </div>
                    ';
            }

            ++$i;
        }

    } else {
        $html .= $data['empty_message'];
    }

    $html .='</div>';

    return $html;
}  
?>
