<?php

$str2='';
$add2='';
foreach($configs as $key=> $memcached){
    if(!is_array($memcached) || !isset($memcached['port'])) continue;
    $msize=$memcached['msize'];
    $port=$memcached['port'];
    $str2.= <<<EOF
  $add2{
    "name"        : "memcached_$port",
    "script"      : "$server",
    "args"        : ["-l","0.0.0.0","-p","$port","-m","$msize"],
    "merge_logs"  : true
  }
EOF;
    $add2=',';
}

$str= <<<EOF

{
  "apps" : [$str2]
}

EOF;

return $str;