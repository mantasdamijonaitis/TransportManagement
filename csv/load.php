<html>
<head>
<title>
Load'u sarasas</title>
</head>    
<body> 
<!--cia formuojama lentele-->
<!--komentuojama taip, parasomas tekstas, tada jis pazymimas, +ctr & /-->
<table>
<?php
//prisijungimas prie db
$dbc=mysql_connect('localhost','','') or die ('Negaliu prisijungti prie MySQL: ' . mysql_error() );
mysql_select_db('') or die ('Negaliu pasirinkti duomenu baze: ' . mysql_error() );

$load = @mysql_query ('select * from CSV_load');
while ($row=mysql_fetch_array($load))
    {
     echo '<tr><td>'.'Data: '.'<b><a href="pagr_forma.php?l='.$row['id'].'">'.$row['Data'].'</a></b></td></tr>';
    }
     
        
//atsijungimas nuo DB
mysql_close();


?>





    
    
</table>
    
</body>
</html>

