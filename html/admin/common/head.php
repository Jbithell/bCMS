<?php
require_once __DIR__ . '/../../common/coreHead.php';


try {
    //session_set_cookie_params(0, '/', '.' . $_SERVER['SERVER_NAME']); //Fix for subdomain bug
    ini_set('session.gc_maxlifetime', 3600*8); //8 hours
    session_set_cookie_params(3600*8);
    session_start(); //Open up the session
} catch (Exception $e) {
    //Do Nothing
}

$PAGEDATA = array('CONFIG' => $CONFIG, 'BODY' => true);
//TWIG
//Twig_Autoloader::register();
$TWIGLOADER = new Twig_Loader_Filesystem(__DIR__ . '/../');
$TWIG = new Twig_Environment($TWIGLOADER, array(
    'debug' => true
));
$TWIG->addExtension(new Twig_Extension_Debug());
$TWIG->addFilter(new Twig_SimpleFilter('timeago', function ($datetime) {
    $time = time() - strtotime($datetime);
    $units = array (
        31536000 => 'year',
        2592000 => 'month',
        604800 => 'week',
        86400 => 'day',
        3600 => 'hour',
        60 => 'minute',
        1 => 'second'
    );
    foreach ($units as $unit => $val) {
        if ($time < $unit) continue;
        $numberOfUnits = floor($time / $unit);
        return ($val == 'second')? 'a few seconds ago' :
            (($numberOfUnits>1) ? $numberOfUnits : 'a')
            .' '.$val.(($numberOfUnits>1) ? 's' : '').' ago';
    }
}));
$TWIG->addFilter(new Twig_SimpleFilter('formatsize', function ($var) {
    global $bCMS;
    return $bCMS->formatSize($var);
}));
$TWIG->addFilter(new Twig_SimpleFilter('unclean', function ($var) {
    global $bCMS;
    return $bCMS->unCleanString($var);
}));
$TWIG->addFilter(new Twig_SimpleFilter('permissions', function ($permissionid) {
    global $AUTH;
    if (!$AUTH->login) return false;
    else return $AUTH->permissionCheck($permissionid);
}));
$TWIG->addFilter(new Twig_SimpleFilter('modifyGet', function ($array) {
    global $bCMS;
    return http_build_query(($bCMS->modifyGet($array)));
}));
$TWIG->addFilter(new Twig_SimpleFilter('randomString', function ($characters) {
    global $bCMS;
    return $bCMS->randomString($characters);
}));
$TWIG->addFilter(new Twig_SimpleFilter('s3URL', function ($fileid, $size = false) {
    global $bCMS;
    return $bCMS->s3URL($fileid, $size);
}));
$TWIG->addFilter(new Twig_SimpleFilter('s3DATA', function ($fileid) {
    global $bCMS;
    return $bCMS->s3URL($fileid, null, false, '+1 minute', true);
}));
$TWIG->addFilter(new Twig_SimpleFilter('articleThumbnail', function ($article) {
    global $bCMS;
    return $bCMS->articleThumbnail($article);
}));

$GLOBALS['AUTH'] = new bID;