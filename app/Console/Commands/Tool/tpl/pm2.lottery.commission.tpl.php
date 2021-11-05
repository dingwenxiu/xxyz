<?php

$str2   =   '';
$add2   =   '';

foreach($commissionConfigArr as $key => $item){
    $str2.= <<<EOF
  $add2{
    "name"        : "lottery_commission_slot_{$item["index"]}",
    "script"      : "{$item['command']}",
    "args"        : ["{$item['args'][0]}", "{$item['args'][1]}", "{$item['args'][2]}"],
    "merge_logs"  : true
  }
EOF;
    $add2 = ',';
}

$str= <<<EOF

{
  "apps" : [$str2]
}

EOF;

return $str;
