<?php
include("../include/session.php");
if ($session->logged_in && ($session->isAdmin() || $session->isManager())) {
    $dbc=mysqli_connect('localhost','','', '') or die ('Negaliu prisijungti prie MySQL: ' . mysql_error() );
    $query = 'DELETE FROM tr_no where userid = "'.$session->username.'"';
    mysqli_query($dbc, $query);
    $query = 'DELETE FROM tr_time where userid = "'.$session->username.'"';
    mysqli_query($dbc, $query);
    $query = 'DELETE FROM tr_value where userid = "'.$session->username.'"';
    mysqli_query($dbc, $query);
    mysqli_close($dbc);
    header('Location: index.php');
    exit();
} else {
    header("Location: ../index.php");
}
?>