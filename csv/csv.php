<?php
if (isset($_POST['ok']))
{
//print_r($_POST);
$turinys=file_get_contents($_FILES['csv']['tmp_name']);
$eilutes=explode("\r\n",$turinys);
//echo $turinys;
//print_r($eilutes);
$outputas[0]=$eilutes[0];
for ($i=8; $i<count($eilutes);$i++)
{
$outputas[]=$eilutes[$i];
}
$matrica_gera = array();
for ($i=0; $i<count($outputas);$i++)
{
$matrica[$i]=explode(';',$outputas[$i]);
}

for ($i=0; $i<count($matrica)-1;$i++)
{
//print_r($matrica[$i][1]);
$matica_gera[$i]["Ken"]=str_replace(" ","",$matrica[$i][1]);
$matica_gera[$i]["Lie_d"]=$matrica[$i][4];
$matica_gera[$i]["Lie_z"]=$matrica[$i][5];
$matica_gera[$i]["Lan"]=$matrica[$i][8];
$matica_gera[$i]["Nam"]=$matrica[$i][10];
$matica_gera[$i]["War"]=$matrica[$i][11];
$matica_gera[$i]["Men"]=$matrica[$i][12];
$matica_gera[$i]["Sst"]=$matrica[$i][9];

//print_r($matica_gera[$i]["Sst"]);
}
//Ikelinejami duomenys i DB lentele prad_d
//INSERT INTO `stud`.`prad_d` (`ID`, `Ken`, `Lie_d`, `Lie_z`, `Lan`, `Nam`, `War`, `Men`) VALUES (NULL, 'e', '2015-10-06', 'rr', 'fsd', 'fsds', 'sfsdf', 'fsfgs');

     
    $load_id = false;
$dbc=mysql_connect('localhost','root','', 'university_project') or die ('Negaliu prisijungti prie MySQL: ' . mysql_error() );
mysql_select_db('') or die ('Negaliu pasirinkti duomenu baze: ' . mysql_error() );

$check = @mysql_query ('select id as load_id from CSV_load where filename = "'.$_FILES['csv']['name'].'" and filesize = '.$_FILES['csv']['size']);

while ($row=mysql_fetch_array($check)){
	$load_id = $row['load_id'];
	}

if (!$load_id)
{
    @mysql_query ('insert into CSV_load (data, filename, filesize) values(now(), "'.$_FILES['csv']['name'].'", '.$_FILES['csv']['size'].')');
    $rload = @mysql_query ('select max(id) as load_id from CSV_load');
    //while ciklas reikalingas tik del rezultato pasiemimo ??????
    while ($row=mysql_fetch_array($rload)){
            $load_id = $row['load_id'];
            }

    for ($i=1; $i<count($matica_gera); $i++)
    {
    mysql_query ('INSERT INTO prad_d (`Ken`, `Lie_d`, `Lie_z`, `Lan`, `Nam`, `War`, `Men`,`Sst`,load_id) VALUES ("'.$matica_gera[$i]["Ken"].'", "'.$matica_gera[$i]["Lie_d"].'","'.$matica_gera[$i]["Lie_z"].'", "'.$matica_gera[$i]["Lan"].'","'. $matica_gera[$i]["Nam"].'", "'.$matica_gera[$i]["War"].'", "'.str_replace(',','.',$matica_gera[$i]["Men"]).'", "'.$matica_gera[$i]["Sst"].'",'.$load_id.');');
    }
}

mysql_close();


}



$dbc=mysql_connect('localhost','root','', 'university_project') or die ('Negaliu prisijungti prie MySQL: ' . mysql_error() );
mysql_select_db('') or die ('Negaliu pasirinkti duomenu baze: ' . mysql_error() );
$query = 'SELECT  t.Lie_d,  Max(salis) as Lan, Nam, sum(Men_suma) as Men_suma,  max(Blue_suma) as Blue_suma, max(kodas) as kodas from 

(
	SELECT Lie_d, salis,Nam, 			
			Sum(Men) as Men_suma, 
			0 as Blue_suma,
			kodas
		FROM `prad_d`
		left join salis on Lan = salis.id
		left join kol_kodai on SUBSTRING(sst,1,4)=kodas
		where replace(Ken," ","") = "'.str_replace(' ','',$_POST['auto']).'"
                    /*TRINTI PO 2016 06 01 and War in ("0009 DIESEL","0036 EURO 95 (SUPER)","0028 TRUCK DIESEL", "0000 DIESEL")*/
		and SUBSTRING(War,1,4) in ("0009","0036","0028","0000","0033","0048")
                and load_id='.$load_id.'
		group by Lie_d, Lan, Nam,kodas


		union
		SELECT Lie_d, salis, Nam,  0 as Men_suma, Sum(Men) as Blue_suma, kodas
		 FROM `prad_d`
		left join salis on Lan = salis.id
		left join kol_kodai on SUBSTRING(sst,1,4)=kodas
		where replace(Ken," ","") = "'.str_replace(' ','',$_POST['auto']).'"
		and War in ("0016 ADBLUE (lose Ware)")
                and load_id='.$load_id.'
		group by Lie_d, Lan, Nam, kodas
		
		order by substring(Lie_d,7,4), substring(Lie_d,4,2), substring(Lie_d,1,2)


) t
group by t.Lie_d, t.Nam';
$result = @mysql_query ($query);

header("Content-type: text/csv");
header("Content-Disposition: attachment; filename=".$_POST['auto'].".csv");
header("Pragma: no-cache");
header("Expires: 0");

echo 'Data;Litrai;Spidometras;Norma;Salis;Miestas;Kortele;Frigo;Ad Blue;Vairuotojai'."\r\n";
while ($row=mysql_fetch_array($result)){
	echo $row['Lie_d'].';'.str_replace('.',',',$row['Men_suma']).';;;'.$row['Lan'].';'.$row['Nam'].';;;'.str_replace('.',',',$row['Blue_suma']).";;;".$row['kodas']."\r\n";
	}

echo "\r\n"."\r\n"."\r\n"."\r\n"."\r\n";
echo 'Viso, ltr;Likutis men. prad;;Likutis men. pab.;;Faktiskai sunaudota degalu;ltr/100 km;;Spidom. nuo;Spidom. iki'."\r\n";

echo "\r\n"."\r\n".'MB'."\r\n".'1 bakas; 2 bakas;;Viso litru'."\r\n";
$log='insert into Logas (data, ip) values (NOW(),\''.$_SERVER['REMOTE_ADDR'].'\')';
@mysql_query ($log);
mysql_close();



?>