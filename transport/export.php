<?php
include("../include/session.php");
if ($session->logged_in && ($session->isAdmin() || $session->isManager())) {
    header("Content-type: text/csv");
    header("Content-Disposition: attachment; filename=export.csv");
    header("Pragma: no-cache");
    header("Expires: 0");


    $dbc=mysqli_connect('localhost','','', '') or die ('Negaliu prisijungti prie MySQL: ' . mysql_error() );
    $query = 'SELECt * FROM tr_no where userid = "'.$session->username.'" ORDER BY id';
    $result = @mysqli_query($dbc, $query);
    $no = array();
    while ($row=mysqli_fetch_array($result))
    {
        $no[]=$row;
    }
    $query = 'SELECt * FROM tr_time where userid = "'.$session->username.'" ORDER BY id';
    $result = @mysqli_query($dbc, $query);
    $time = array();
    while ($row=mysqli_fetch_array($result))
    {
        $time[]=$row;
    }

    $query = 'SELECt * FROM tr_value where userid = "'.$session->username.'" ORDER BY id';
    $result = @mysqli_query($dbc, $query);
    $value = array();
    while ($row=mysqli_fetch_array($result))
    {
        $value[]=$row;
    }

    $query = 'SELECT time_id, count(no_id) as cnt, sum(verte) as suma, AVG(verte) as vid FROM tr_value where userid = "'.$session->username.'" GROUP by time_id order by time_id';
    $result = @mysqli_query($dbc, $query);
    $bottom = array();
    while ($row=mysqli_fetch_array($result))
    {
        $bottom[]=$row;
    }

    $table = array();
    for ($i=0; $i<count($time); $i++)
    {
        $table[0][$time[$i]['id']] = $time[$i]['laikas'];
    }
    for ($i=0; $i<count($no); $i++)
    {
        $table[$no[$i]['id']][0] = $no[$i]['no'];
    }

    for ($i=0; $i<count($value); $i++)
    {
        $table[$value[$i]['no_id']][$value[$i]['time_id']] = $value[$i]['verte'];
    }

    $tbottom = array();
    for ($i=0; $i<count($bottom); $i++)
    {
        $tbottom[$bottom[$i]['time_id']]['cnt'] = $bottom[$i]['cnt'];
        $tbottom[$bottom[$i]['time_id']]['suma'] = $bottom[$i]['suma'];
        $tbottom[$bottom[$i]['time_id']]['vid'] = $bottom[$i]['vid'];
    }

    echo ';nommer/data';
    $countas = 0;
    foreach ($table[0] as $key => $value)
    {
        echo ';'.$value;
    }
    echo "\r\n";


    foreach ($table as $key0 => $table0) 
    {
        if ($key0 > 0)
        {
            $countas++;
            echo $countas.';'.$table[$key0][0];
            foreach ($table[0] as $key => $value)
            {
                if ($key > 0) 
                {
                    echo ';'.str_replace('.',',',(isset($table[$key0][$key])?$table[$key0][$key]:''));
                }
            }
            echo "\r\n";
        }
    }

    echo ';Summe';
    foreach ($table[0] as $key => $value)
    {
        echo ';'.str_replace('.',',',(isset($tbottom[$key]['suma'])?$tbottom[$key]['suma']:''));
    }
    echo "\r\n";

    echo ';Count';
    foreach ($table[0] as $key => $value)
    {
        echo ';'.str_replace('.',',',(isset($tbottom[$key]['cnt'])?$tbottom[$key]['cnt']:''));
    }
    echo "\r\n";

    echo ';Avg'; 
    foreach ($table[0] as $key => $value)
    {
        echo ';'.str_replace('.',',',(isset($tbottom[$key]['vid'])?$tbottom[$key]['vid']:''));
    }
    echo "\r\n";
    mysqli_close($dbc);
} else {
    header("Location: ../index.php");
}
?>