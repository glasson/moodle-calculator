<?php
declare(strict_types=1);

require_once('./../../config.php');
global $DB, $PAGE, $CFG;

$query = "SELECT * FROM mdl_block_calculator";
$data = $DB->get_records_sql($query);

$home_url = $CFG->wwwroot . '/my/';

$page = '<a href='.$home_url.'>Home</a> 
         <div style="display: flex;
                     flex-direction: column;
                     justify-content: center; 
                     align-items: center;">
         <table>
         <tr>
            <th>a</th>
            <th>b</th>
            <th>c</th>
            <th>корень 1</th>
            <th>корень 2</th>
         </tr>';

$style = '
<style>  
    table {
            border-collapse: collapse;
            width: 100%;
            margin: 10px
        }

    th, td {
        border: 1px solid #ddd;
        padding: 8px;
        text-align: left;
    }

    th {
        background-color: #f2f2f2;
    }
</style>';

foreach ($data as $row) {
    $page .= "<tr>
                <td>
                    ".(float)$row->a."
                </td>  
                <td>
                    ".(float)$row->b."
                </td> 
                <td>
                    ".(float)$row->c."
                </td> 
                <td>
                    ".(float)$row->root_1."
                </td>
                <td>
                    ".(float)$row->root_2."
                </td>
            </tr>";
}


echo $page . $style ;
