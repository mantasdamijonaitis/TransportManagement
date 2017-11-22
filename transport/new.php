<?php
include("../include/session.php");
if ($session->logged_in && ($session->isAdmin() || $session->isManager())) {
    if (isset($_POST['ok']))
    {
        $dbc=mysqli_connect('localhost','root','', 'university_project') or die ('Negaliu prisijungti prie MySQL: ' . mysql_error() );
        $turinys=file_get_contents($_FILES['csv']['tmp_name']);
        $mas=explode("\r\n",$turinys);
        $m = array();
        for ($i=1; $i<count($mas); $i++)
        {
            $nm = explode(';', $mas[$i]);
                $m[]=$nm;
        }

        $a = array();
        for ($i=0;$i<count($m);$i++)
        {
            if (is_numeric(str_replace(',', '.', $m[$i][1])))
            {
                $a[]['no']=str_replace('-', '', str_replace(' ', '', $m[$i][0]));
                $a[count($a)-1]['val']=str_replace(',', '.', $m[$i][1]);
            }
        }
        $query = 'SELECT id, no from tr_no  where userid = "'.$session->username.'"';
        $result = mysqli_query($dbc, $query);
        $tr_no = array();
        while ($row = mysqli_fetch_row($result))
        {
            $tr_no[] = $row;
        }

        for ($i=0;$i<count($a);$i++)
        {
            for ($j=0;$j<count($tr_no);$j++)
            {
                if (strpos($a[$i]['no'], $tr_no[$j][1]) !== false)
                {
                    $a[$i]['id'] = $tr_no[$j][0];
                    $a[$i]['realno'] = $tr_no[$j][1];
                }
            }
        }
        $query = 'INSERT INTO tr_time (laikas, userid) VALUE ("'.trim(str_replace(' ', '', $_POST['laikas'])).'","'.$session->username.'")';
        mysqli_query($dbc, $query);
        $query = 'SELECT * FROM tr_time where userid = "'.$session->username.'" ORDER BY id DESC LIMIT 0,1';
        $result = mysqli_query($dbc, $query);
        $time_id = 0;
        while ($row = mysqli_fetch_array($result))
        {
            $time_id = $row['id'];
        }
        for ($i=0; $i<count($a);$i++)
        {
            if (isset($a[$i]['id'])) 
            {
                $query = 'INSERT INTO tr_value (no_id, time_id, verte, userid) VALUES ('.$a[$i]['id'].', '.$time_id.', '.$a[$i]['val'].', "'.$session->username.'")';
    //            echo $query.'<br>';
                mysqli_query($dbc, $query);
            }
            else
            {
                $query = 'INSERT INTO tr_no (no, userid) VALUES ("'.$a[$i]['no'].'", "'.$session->username.'")';
                mysqli_query($dbc, $query);
                $query = 'SELECT id FROM tr_no WHERE no = "'.$a[$i]['no'].'" and userid = "'.$session->username.'"';
                $result = mysqli_query($dbc, $query);
                $no_id = 0;
                while ($row = mysqli_fetch_array($result))
                {
                    $no_id = $row['id'];
                }
                $query = 'INSERT INTO tr_value (no_id, time_id, verte, userid) VALUES ('.$no_id.', '.$time_id.', '.$a[$i]['val'].',"'.$session->username.'")';
                mysqli_query($dbc, $query);
            }
        }
        mysqli_close($dbc);
        header('Location: data.php');
        exit();
    }
    ?>
    <!doctype html>
    <html>
        <head>
            <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
            <meta name="language" content="lt" />
            <meta http-equiv="Pragma" content="no-cache" />
        <title>Išrašas</title>
        </head>
    <body> 
    <form method="post" enctype="multipart/form-data" >
    Įveskite mėnesio užrašą: <input name="laikas" type="text" placeholder="11-NOV"><br>
    <input name="csv" type="file" /> <br>
    <input name="ok" type="submit" value="Ikelti" /> <br>
    <a href="data.php">Išrašas</a>
    <iframe name="csviukas" style="visibility:hidden;display:none"></iframe>
    </form>
    </body>
    </html>
    <?php
} else {
    header("Location: ../index.php");
}
?>
