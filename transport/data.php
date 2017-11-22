<?php
include("../include/session.php");
if ($session->logged_in && ($session->isAdmin() || $session->isManager())) {
    ?>
    <!doctype html>
    <html>  
        <head>  
            <meta http-equiv="X-UA-Compatible" content="IE=9; text/html; charset=utf-8"/> 
            <title>Transport</title>
            <link href="../include/styles.css" rel="stylesheet" type="text/css" />
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
                            <?php
                            $dbc=mysqli_connect('localhost','root','', 'university_project') or die ('Negaliu prisijungti prie MySQL: ' . mysql_error() );
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

                            $query = 'SELECT time_id, count(no_id) as cnt, sum(verte) as suma, AVG(verte) as vid FROM tr_value  where userid = "'.$session->username.'" GROUP by time_id order by time_id';
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
                            //print_r($table);
                            ?>
                                <table>
                                    <thead>
                                    <th>
                                        <a href="new.php">Naujas</a><br>
                                        <a href="export.php" target="export">Eksportuoti</a><br>
                                        <a href="delete.php" onclick="return confirm('Ar tikrai norite ištrinti visus duomenis?');">Trinti</a>
                                    </th>
                                        <?php
                                        if (isset($table[0]))
                                        {
                                            foreach ($table[0] as $key => $value)
                                            {
                                                ?><th align="center">
                                                <a href="deltd.php?d=<?php echo $key; ?>" onclick="return confirm('Ar tikrai norite ištrinti šį stulpelį?');">x</a><br>
                                                    <?php echo $value ?>
                                                  </th><?php
                                            }
                                        }
                                        ?>
                                    </thead>
                                    <tbody>
                                        <?php
                            //            for ($i=1; $i<count($table); $i++)
                                        foreach ($table as $key0 => $table0) 
                                        {
                                            if ($key0 > 0)
                                            {
                                                ?>
                                                <tr>
                                                    <th><a href="deltr.php?d=<?php echo $key0;?>" onclick="return confirm('Ar tikrai norite ištrinti numerio <?php echo $table[$key0][0];?> visus įrašus?');">x</a> <?php echo $table[$key0][0];?></th>
                                                    <?php
                                                    if (isset($table[0]))
                                                    {
                                                        foreach ($table[0] as $key => $value)
                                                        {
                                                            if ($key > 0) 
                                                            {
                                                                ?><td align="center"><?php echo (isset($table[$key0][$key])?$table[$key0][$key]:'&nbsp;'); ?></td><?php
                                                            }
                                                        }
                                                    }
                                                        ?>
                                                </tr><?php
                                            }
                                        }
                                        ?>
                                        <tr>
                                            <th>Summa</th>
                                            <?php
                                            if (isset($table[0]))
                                            {
                                                foreach ($table[0] as $key => $value)
                                                {
                                                    ?><th align="center"><?php echo (isset($tbottom[$key]['suma'])?$tbottom[$key]['suma']:'&nbsp;'); ?></th><?php
                                                }
                                            }
                                                ?>
                                        </tr>
                                        <tr>
                                            <th>Count</th>
                                            <?php
                                            if (isset($table[0]))
                                            {
                                                foreach ($table[0] as $key => $value)
                                                {
                                                    ?><th align="center"><?php echo (isset($tbottom[$key]['cnt'])?$tbottom[$key]['cnt']:'&nbsp;'); ?></th><?php
                                                }
                                            }
                                                ?>
                                        </tr>
                                        <tr>
                                            <th>Average</th>
                                            <?php
                                            if (isset($table[0]))
                                            {
                                                foreach ($table[0] as $key => $value)
                                                {
                                                    ?><th align="center"><?php echo (isset($tbottom[$key]['vid'])?round($tbottom[$key]['vid'], 3):'&nbsp;'); ?></th><?php
                                                }
                                            }
                                                ?>
                                        </tr>
                                    </tbody>
                                </table>
                                <iframe name="export" style="visibility:hidden;display:none"></iframe>
                            <?php
                            mysqli_close($dbc);
                            ?>              
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
} else {
    header("Location: ../index.php");
}
?>