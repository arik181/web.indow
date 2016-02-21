<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

function generate_tabbar( $css_id, $tabs, $default_active_tab )
{   
    $html = '<div style="clear: both" class="addtab_stripe">';
    $html .= '<ul class="nav nav-tabs" id="'; 
    $html .= $css_id;
    $html .= '">';

    $i = 0;
    foreach ($tabs as $tab)
    {
        if (! empty($tab) )
        {
            $class  = ($i === $default_active_tab) ? 'active' : ''; 
            $hidden = ($i === $default_active_tab) ? '' : 'style="display: none;"'; 

            $html .= '<li id="';
            $html .= $tab['id'];
            $html .= '_tab" class="addtab-cell '; 
            $html .= $class;
            $html .= '">';
            $html .= '<a href="#' . $tab['id'] . '" data-toggle="tab">';
            $html .= $tab['name'];
            $html .= '</a>';

            $html .= '<div id="'; 
            $html .= $tab['id'];
            $html .= '_tab_speech_balloon';
            $html .= '" class="speech-balloon" ';
            $html .= $hidden;
            $html .= '';
            $html .= '></div>';

            $html .= '</li>';

        }
        ++$i;
    }

    $html .= '</ul>';

    $html .= '</div>';

    return $html;
}   

?>
