<?php

$str2   =   '';
$add2   =   '';

foreach($configs as $key => $redis){
    if(!is_array($redis) || !isset($redis['port'])) {
        continue;
    }

    // client配置去掉
    if ($key == 'client') {
        continue;
    }

    $port = $redis['port'];
    $conf = $_confs[$key];
    $str2.= <<<EOF
  $add2{
    "name"        : "redis_{$port}_{$key}",
    "script"      : "$server",
    "args"        : ["$conf"],
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
