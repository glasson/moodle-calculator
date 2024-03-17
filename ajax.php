<?php

require_once('./../../config.php');


$result = optional_param('result', null, PARAM_RAW);
$a = optional_param('a', null, PARAM_RAW);
$b = optional_param('b', null, PARAM_RAW);
$c = optional_param('c', null, PARAM_RAW);

save_result($a, $b, $c, $result);
echo json_encode([$a, $b, $c, $result]);




function save_result($a, $b, $c, $result){
    global $DB;

    $ins = new stdClass();
    $ins->a = $a;
    $ins->b = $b;
    $ins->c = $c;
    $ins->result = $result;
    $ins->id = $DB->insert_record('block_calculator', $ins);
}