<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

function generate_notes($id, $data, $items, &$mode='default')
{
    //form_open($data['form'], array('class'=>'notes_form form-group'));
    $rand = rand(0, 10000);
    $collapseid = 'collapse' . $rand;
    $parentid = 'parent' . $rand;
    ob_start();
    ?>
    <div class="notescont">
        <?php if($mode!='view'): ?>
        <div class="form-group">
            <label class="control-label" for="customerNotes<?= $rand ?>"><?= $data['title'] ?></label>
            <textarea data-jsname="<?= isset($data['jsname']) ? $data['jsname'] : '' ?>" name="<?= $data['form_name'] ?>" id="customerNotes<?= $rand ?>" class="notes_text form-control" rows="3" placeholder="Insert notes here."><?= isset($data['fieldvalue']) ? $data['fieldvalue'] : '' ?></textarea>
        </div>
        <?php endif; ?>
        <? if (empty($data['hidesubmit'])) { ?>
        <div class="row show-grid">
            <div class="<?= $mode=='view' ? 'col-xs-12' : 'col-xs-9' ?>">
                <div class="panel-group" id="<?= $parentid ?>">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h4 class="panel-title">
                                <a data-toggle="collapse" data-parent="#<?= $parentid ?>" href="#<?= $collapseid ?>">
                                    <? $hidden = !count($items) ? 'hidden' : ''; ?>
                                    <?= $data['title'] ?> History <?= '<span class="notes-count ' . $hidden . '">(' . count($items) . ')</span>' ?><span class="glyphicon glyphicon-chevron-down pull-right">
                                </a>
                            </h4>
                        </div>
                        <div id="<?= $collapseid ?>" class="panel-collapse collapse">
                            <div class="panel-body">
                                <dl class="dl-horizontal notesinner">
                                    <? if (!empty($items)) { ?>
                                        <? foreach ($items as $item) { ?>
                                            <dl class="dl-horizontal">
                                                <dt><?= date("M j g:ia", strtotime($item->created)) ?><?= isset($item->name) ? '<br>by ' . $item->name : '' ?></dt>
                                                <dd>
                                                    <?= htmlspecialchars($item->text) ?>
                                                </dd>
                                            </dl>
                                        <? } ?>
                                    <? } ?>
                                </dl>
                                <? if(empty($items)) { ?>
                                    <div class="notesempty alert alert-warning"><?= $data['empty_message'] ?></div>
                                <? } ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php if($mode!='view'): ?>
            <div class="col-xs-3 text-right">
                <input type="submit" name="submit" value="<?= $data['form_value'] ?>" class="savenotes btn btn-blue btn-sm" data-type="<?= isset($data['type']) ? $data['type'] : '' ?>"/>
            </div>
            <?php endif; ?>
        </div>
        <? } ?>
    </div>

<?php
    $html = ob_get_clean();
    //$html .= form_close();
/*
    if ( ! empty($items) ) 
    {
        $html .= '<table class="notes_table notes_view">';

        foreach ($items as $item)
        {
            $html .= '<tr class="notes_row">';
            $html .= '<td>';
            $html .= $item;
            $html .= '</td>';
            $html .= '</tr>';
        }

        $html .='</table>';

    } else {

        $html .= '<br><div class="alert alert-warning">' . $data['empty_message'] . '</div>';
    }
*/
    //$html = ob_get_clean();
    return $html;
}   

?>
