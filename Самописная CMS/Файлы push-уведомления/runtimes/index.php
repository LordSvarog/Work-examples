<?
$_PAGE[template]='none';

# case catches
$catch=$_URI[1];
if($catch == 'user'){ include_once "runtimes/user.php"; }
elseif($catch == 'calendar'){ include_once "runtimes/calendar.php"; }
elseif($catch == 'map'){ include_once "runtimes/map.php"; }
elseif($catch == 'getPhones'){ include_once "runtimes/getPhones.php"; }
elseif($catch == 'api'){ include_once "runtimes/api.php"; }
elseif($catch == 'check_regmaster'){ include_once "runtimes/check_regmaster.php"; }
elseif($catch == 'torrentlike'){ include_once "runtimes/torrentlike.php"; }
elseif($catch == 'push'){ include_once "lib/push/save_push.php"; }


?>
