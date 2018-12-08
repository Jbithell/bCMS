<?php
require_once __DIR__ . '/common/headSecure.php';

$PAGEDATA['pageConfig'] = ["TITLE" => "Account Settings", "BREADCRUMB" => true];

if (isset($_GET['new']) and $AUTH->permissionCheck(4)) {
    $PAGEDATA['pageConfig']['TITLE'] = "Create a new user";
    $PAGEDATA['USER'] = ["users_userid" => "NEW"];
} else {
    if (!isset($_GET['uid']) or !$AUTH->permissionCheck(5)) $userid = $AUTH->data['users_userid'];
    else $userid = $bCMS->sanitizeString($_GET['uid']);

    $DBLIB->where("users_userid", $userid);
    $PAGEDATA['USER'] = $DBLIB->getone("users");

    $DBLIB->where("users_userid", $userid);
    $DBLIB->orderBy("userPositions_start", "ASC");
    $DBLIB->orderBy("userPositions_end", "ASC");
    $DBLIB->join("positions", "positions.positions_id=userPositions.positions_id", "LEFT");
    $PAGEDATA['USER']['POSITIONS'] = $DBLIB->get("userPositions");

    $DBLIB->orderBy("positions_rank", "ASC");
    $DBLIB->orderBy("positions_displayName", "ASC");
    $PAGEDATA['POSSIBLEPOSITIONS'] = $DBLIB->get("positions");

    $PAGEDATA['pageConfig']['TITLE'] = "Account Settings for " . $PAGEDATA['USER']['users_name1'] . " " . $PAGEDATA['USER']['users_name2'];
}

echo $TWIG->render('account.twig', $PAGEDATA);
?>
