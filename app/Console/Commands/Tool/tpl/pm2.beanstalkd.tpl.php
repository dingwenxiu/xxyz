<?php

$str= <<<EOF

{
  "apps" : [{
    "name"        : "beanstalkd",
    "script"      : "$server",
    "args"        : ["-l","0.0.0.0","-p","11300"],
    "merge_logs"  : true
  }]
}

EOF;

return $str;