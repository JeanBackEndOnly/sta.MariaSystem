<?php
echo "get_current_user: " . get_current_user() . "<br>";
$user = posix_getpwuid(posix_geteuid());
echo "Effective user: " . $user['name'] . " (UID: " . $user['uid'] . ")";