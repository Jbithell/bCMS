<?php
//Re-send a verification email
require_once __DIR__ . '/../apiHeadSecure.php';

header("Content-Type: text/plain");

if ($USERDATA['users_changepass'] != '1') die('Error'); //This page only works if the user if forced

$DBLIB->where ('users_userid', $USERDATA['users_userid']);
if ($DBLIB->update ('users', ["users_password" => hash($CONFIG['nextHash'], $USERDATA['users_salty1'] . $_GET['pass']. $USERDATA['users_salty2']), "users_changepass" => 0])) die('1');
else die('2');