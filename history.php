<?php
declare(strict_types=1);

require_once('./../../config.php');
global $DB, $PAGE;

$query = "SELECT * FROM mdl_block_calculator";
$data = $DB->get_records_sql($query);

$home_url = 'http://localhost:8080/moodle/my';

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
            <th>root1</th>
            <th>root2</th>
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
                    $row->a
                </td>  
                <td>
                    $row->b
                </td> 
                <td>
                    $row->c
                </td> 
                <td>
                    $row->root_1
                </td>
                <td>
                    $row->root_2
                </td>
            </tr>";
}


echo $page . $style ;
