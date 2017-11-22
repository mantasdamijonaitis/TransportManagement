<?php
include("../include/session.php");
if ($session->logged_in && ($session->isAdmin() || $session->isManager())) {
$dbc=mysqli_connect('localhost','root','', 'university_project') or die ('Negaliu prisijungti prie MySQL: ' . mysql_error() );
$query='SELECT count(1) as cnt from tr_time where userid = "'.$session->username.'"';
echo $query;
$result = mysqli_query($dbc, $query);
while ($row = mysqli_fetch_row($result))
{
    $cnt = $row[0];
}
if (isset($cnt) && $cnt > 0)
{
    mysqli_close($dbc);
    header('Location: data.php');
    exit();
}
if (isset($_POST['ok']))
{
    $turinys=file_get_contents($_FILES['csv']['tmp_name']);
    $mas=explode("\r\n",$turinys);
    $m = array();
    for ($i=1; $i<count($mas); $i++)
    {
        $nm = explode(';', $mas[$i]);
        if (!empty($nm[1])) $m[]=$nm;
        else break;
    }
    
    $data = explode(';', $mas[0]);
   
    for ($i=0;$i<count($m); $i++)
    {
        $query = 'INSERT INTO tr_no (no, userid) values("'.str_replace('-', '', str_replace(' ', '', trim($m[$i][1]))).'","'.$session->username.'")';
        mysqli_query($dbc, $query);
    }
    $nm = explode(';', $mas[0]);
    for($i=2;$i<count($nm);$i++)
    {
        if (!empty($nm[$i]))
        {
            $query = 'INSERT INTO tr_time (laikas, userid) values ("'.str_replace(' ', '', trim($nm[$i])).'","'.$session->username.'")';
            mysqli_query($dbc, $query);
        }
        else
            break;
    }    

    for ($i=0;$i<count($m);$i++)
    {
        $query = 'SELECT id from tr_no where no = "'.str_replace('-', '', str_replace(' ', '', trim($m[$i][1]))).'" and userid = "'.$session->username.'"';
        $result = mysqli_query($dbc, $query);
        $j = 0;
        while ($row = mysqli_fetch_row($result))
        {
            $id = $row[0];
        }
        for ($j=2;$j<count($nm);$j++)
        {
            if (!empty($nm[$j]))
            {
                $querytime='SELECT id from tr_time where laikas = "'.str_replace(' ', '', trim($nm[$j])).'" and userid = "'.$session->username.'"';
                $resulttime = mysqli_query($dbc, $querytime);
                while ($rowt = mysqli_fetch_row($resulttime))
                {
                    $tid = $rowt[0];
                }
                if (is_numeric(str_replace('.','',$m[$i][$j])) && str_replace('.','',$m[$i][$j]) > 0)
                {
                    $querySk = 'INSERT INTO tr_value (no_id, time_id, verte, userid) VALUES ('.$id.', '.$tid.', '.str_replace('.','',$m[$i][$j]).', "'.$session->username.'")';
                    mysqli_query($dbc, $querySk);
                }   
            }
        }
//        if (is_numeric(str_replace('.','',$m[$i][3])) && str_replace('.','',$m[$i][3]) > 0)
//        {
//            $querySk = 'INSERT INTO tr_value (no_id, time_id, verte) VALUES ('.$id.', 2, '.str_replace('.','',$m[$i][3]).')';
//            mysqli_query($dbc, $querySk);
//        }
//        if (is_numeric(str_replace('.','',$m[$i][4])) && str_replace('.','',$m[$i][4]) > 0)
//        {
//            $querySk = 'INSERT INTO tr_value (no_id, time_id, verte) VALUES ('.$id.', 3, '.str_replace('.','',$m[$i][4]).')';
//            mysqli_query($dbc, $querySk);
//        }
//        if (is_numeric(str_replace('.','',$m[$i][5])) && str_replace('.','',$m[$i][5]) > 0)
//        {
//            $querySk = 'INSERT INTO tr_value (no_id, time_id, verte) VALUES ('.$id.', 4, '.str_replace('.','',$m[$i][5]).')';
//            mysqli_query($dbc, $querySk);
//        }
//        if (is_numeric(str_replace('.','',$m[$i][6])) && str_replace('.','',$m[$i][6]) > 0)
//        {
//            $querySk = 'INSERT INTO tr_value (no_id, time_id, verte) VALUES ('.$id.', 5, '.str_replace('.','',$m[$i][6]).')';
//            mysqli_query($dbc, $querySk);
//        }
//        if (is_numeric(str_replace('.','',$m[$i][7])) && str_replace('.','',$m[$i][7]) > 0)
//        {
//            $querySk = 'INSERT INTO tr_value (no_id, time_id, verte) VALUES ('.$id.', 6, '.str_replace('.','',$m[$i][7]).')';
//            mysqli_query($dbc, $querySk);
//        }
//        if (is_numeric(str_replace('.','',$m[$i][8])) && str_replace('.','',$m[$i][8]) > 0)
//        {
//            $querySk = 'INSERT INTO tr_value (no_id, time_id, verte) VALUES ('.$id.', 7, '.str_replace('.','',$m[$i][8]).')';
//            mysqli_query($dbc, $querySk);
//        }
//        if (is_numeric(str_replace('.','',$m[$i][9])) && str_replace('.','',$m[$i][9]) > 0)
//        {
//            $querySk = 'INSERT INTO tr_value (no_id, time_id, verte) VALUES ('.$id.', 8, '.str_replace('.','',$m[$i][9]).')';
//            mysqli_query($dbc, $querySk);
//        }
//        if (is_numeric(str_replace('.','',$m[$i][10])) && str_replace('.','',$m[$i][10]) > 0)
//        {
//            $querySk = 'INSERT INTO tr_value (no_id, time_id, verte) VALUES ('.$id.', 9, '.str_replace('.','',$m[$i][10]).')';
//            mysqli_query($dbc, $querySk);
//        }

//        print_r($m[$i]);
    }
    mysqli_close($dbc);
    header('Location: data.php');
    exit();
}
?>
<!doctype html>
<html>  
    <head>  
        <meta http-equiv="X-UA-Compatible" content="IE=9; text/html; charset=utf-8"/> 
        <title>Importuoti</title>
        <link href="../include/styles.css" rel="stylesheet" type="text/css" />
        <meta http-equiv="Pragma" content="no-cache" />
    </head>
    <body>       
        <table class="center"><tr><td>
                    <img src="../pictures/top.png"/>
                </td></tr><tr><td> 
                    <?php
                    include("../include/meniu.php");
                    ?>                   
                    <table style="border-width: 2px; border-style: dotted;"><tr><td>
                                Atgal į [<a href="../index.php">Pradžia</a>]
                            </td></tr></table>               
                    <br> 
                    <div style="text-align: center;color:green">                   
                        <form method="post" enctype="multipart/form-data" >
                        <input name="csv" type="file" /> <br>
                        <input name="ok" type="submit" value="Ikelti" /> <br>
                        <iframe name="csviukas" style="visibility:hidden;display:none"></iframe>
                        </form>                
                    </div> 
                    <br>                         
            <tr><td>
                    <?php
                    include("../include/footer.php");
                    ?>
                </td></tr>                       
        </table>     
    </body>
</html>
<?php
mysqli_close($dbc);
} else {
    header("Location: ../index.php");
}
?>