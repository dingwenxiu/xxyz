<?php

$str= <<<EOF

daemonize no
port {$redis['port']}
timeout 0
tcp-keepalive 0
logfile '$logfile'
databases 16
dir /tmp/
stop-writes-on-bgsave-error yes
slave-serve-stale-data yes
slave-read-only yes
repl-disable-tcp-nodelay no
bind 0.0.0.0
{$slaveConfig}
appendonly no
no-appendfsync-on-rewrite no
lua-time-limit 0
zset-max-ziplist-entries 0
zset-max-ziplist-value 0
hll-sparse-max-bytes 3000
activerehashing no

EOF;

return $str;
