<html>
    <head>
        <title>csv apsilankymai</title>
    </head>
    <body>
        <table>
            <tr>
                <td><b>id</b></td>
                <td><b>data</b></td>
                <td><b>ip/b></td>
                <td><b>naudotojas</b></td>
            </tr>
            <?php
            $dbc=mysql_connect('localhost','root','', 'university_project') or die ('Negaliu prisijungti prie MySQL: ' . mysql_error() );
            mysql_select_db('') or die ('Negaliu pasirinkti duomenu baze: ' . mysql_error() );
            $query = 'SELECT * from Logas order by id desc';
            $result = @mysql_query($query);
            while ($row=mysql_fetch_array($result))
            {
                echo '  <tr>
                            <td>'.$row['id'].'</td>
                            <td>'.$row['data'].'</td>
                            <td>'.$row['ip'].'</td>
                            <td>'.$row['user'].'</td>
                        </tr>';
            }
            mysql_close();
            ?>
        </table>
    </body>
</html>