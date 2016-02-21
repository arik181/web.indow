<!doctype html>
<html>
    <head>
        <title><?= $title ?></title>
    </head>
    <body>
        <style>
            #data_table {
                width: 100%;
                border-collapse: collapse;
            }
            #data_table td, #data_table th {
                border: 1px solid #000000;
                padding: 4px;
            }
            #data_table tr {
                page-break-inside: avoid;
            }
        </style>
        <h2><?= $title ?></h2>
        <? if (count($data)) { ?>
            <table cellspacing="0" cellpadding="0" id="data_table">
                <tr>
                    <? foreach(array_keys($data[0]) as $key) { ?>
                        <th><?= $key ?></th>
                    <? } ?>
                </tr>
                <? foreach ($data as $row) { ?>
                    <tr>
                        <? foreach ($row as $k => $v) { ?>
                            <td><?= $v ?></td>
                        <? } ?>
                    </tr>
                <? } ?>
            </table>
        <? } else { ?>
            No Results
        <? } ?>
    </body>
</html>