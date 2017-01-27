<?php


$dbhost = '[dbhost]';
$dbuser = '[dbuser]';
$dbpassword = '[dbpassword]';
$dbschema = 'craigslack'; // default value

$link = mysqli_connect($dbhost, $dbuser, $dbpassword, $dbschema);

if (!$link) {
    die('Connect Error (' . mysqli_connect_errno() . ') '
            . mysqli_connect_error());
}

?>
