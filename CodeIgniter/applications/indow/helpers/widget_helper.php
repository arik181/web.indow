<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

function generate_widget($id, $data)
{   
    $html  =  '<div class="widget_view">';
    $html .=  $data['widget_count'];
    $html .=  $data['widget_title'];
    $html .=  $data['widget_icon'];
    $html .=  '</div>';

    return $html;
}

function create_preorder_button($site_id) {
    ob_start();
    ?>
        <div id="preorderbuttoncont">
            <style>
                #preorderbuttoncont .popover {
                    min-width: 630px;
                }
                #preorderbuttoncont tr[role="row"] {
                    cursor: pointer;
                }
            </style>
            <button id="create_preorder_button" class="btn btn-blue">Create Preorder</button>
            <script>
                $(function () {
                    /*
                    $('#create_preorder_button').click(function () {
                        var elem = $(this);
                        elem.prop('disabled', true);
                        $.post('/orders/create_preorder/<?= $site_id ?>', function (response) {
                            elem.prop('disabled', false);
                            alert(response.message);
                            if (response.success) {
                                window.location = '/orders/edit/' + response.order_id;
                            }
                        });
                    });
                    */
                });
            </script>
        </div>
        
    <?
    return ob_get_clean();
}