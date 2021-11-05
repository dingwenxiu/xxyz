<?php

$str2='';
$add2='';

    $str2.= <<<EOF
  $add2{
    "name"        : "casino_record",
    "script"      : "php artisan casino:CasinoRecord",
    "args"        : [],
    "merge_logs"  : true
  }
EOF;
    $add2=',';


$str= <<<EOF

{
  "apps" : [$str2]
}

EOF;

return $str;
