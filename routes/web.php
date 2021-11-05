<?php
if (defined('ADMIN')) {
    include_once ("admin_api.php");
} else if (defined('PARTNER')){
    include_once ("partner_api.php");
} else if (defined('CASINO')){
    include_once ("casino_api.php");
} else {
    include_once ("api.php");
}
