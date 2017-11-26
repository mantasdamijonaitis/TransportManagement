<?php
$turinys = file_get_contents( $_FILES['csv']['tmp_name'] );
$eilutes     = explode( "\r\n", $turinys );
$outputas[0] = $eilutes[0];
for ( $i = 8; $i < count( $eilutes ); $i ++ ) {
	$outputas[] = $eilutes[ $i ];
}
$matrica_gera = array();
for ( $i = 0; $i < count( $outputas ); $i ++ ) {
	$matrica[ $i ] = explode( ';', $outputas[ $i ] );
}

for ( $i = 0; $i < count( $matrica ) - 1; $i ++ ) {
	$matica_gera[ $i ]["Ken"]   = str_replace( " ", "", $matrica[ $i ][1] );
	$matica_gera[ $i ]["Lie_d"] = $matrica[ $i ][4];
	$matica_gera[ $i ]["Lie_z"] = $matrica[ $i ][5];
	$matica_gera[ $i ]["Lan"]   = $matrica[ $i ][8];
	$matica_gera[ $i ]["Nam"]   = $matrica[ $i ][10];
	$matica_gera[ $i ]["War"]   = $matrica[ $i ][11];
	$matica_gera[ $i ]["Men"]   = $matrica[ $i ][12];
	$matica_gera[ $i ]["Sst"]   = $matrica[ $i ][9];

}

$load_id = false;
$dbc = mysqli_connect( 'localhost', 'root', '', 'university_project' );

$check = @mysqli_query( $dbc, 'select id as load_id from CSV_load where filename = "' . $_FILES['csv']['name'] . '" and filesize = ' . $_FILES['csv']['size'] );
while ( $row = mysqli_fetch_array( $check ) ) {
	$load_id = $row['load_id'];
}
if ( ! $load_id ) {
	@mysqli_query( $dbc, 'insert into CSV_load (data, filename, filesize) values(now(), "' . $_FILES['csv']['name'] . '", ' . $_FILES['csv']['size'] . ')' );
	$rload = @mysqli_query( $dbc, 'select max(id) as load_id from CSV_load' );
	//while ciklas reikalingas tik del rezultato pasiemimo ??????
	while ( $row = mysqli_fetch_array( $rload ) ) {
		$load_id = $row['load_id'];
	}

	for ( $i = 1; $i < count( $matica_gera ); $i ++ ) {
		mysqli_query( $dbc, 'INSERT INTO prad_d (`Ken`, `Lie_d`, `Lie_z`, `Lan`, `Nam`, `War`, `Men`,`Sst`,load_id) VALUES ("' . $matica_gera[ $i ]["Ken"] . '", "' . $matica_gera[ $i ]["Lie_d"] . '","' . $matica_gera[ $i ]["Lie_z"] . '", "' . $matica_gera[ $i ]["Lan"] . '","' . $matica_gera[ $i ]["Nam"] . '", "' . $matica_gera[ $i ]["War"] . '", "' . str_replace( ',', '.', $matica_gera[ $i ]["Men"] ) . '", "' . $matica_gera[ $i ]["Sst"] . '",' . $load_id . ');' );
	}
}
mysqli_close( $dbc );

$dbc = mysqli_connect( 'localhost', 'root', '', 'university_project' );
if ( isset( $load_id ) ) {
	$query = 'SELECT  t.Lie_d,  Max(salis) as Lan, Nam, sum(Men_suma) as Men_suma,  max(Blue_suma) as Blue_suma, max(kodas) as kodas from
(
	SELECT Lie_d, salis,Nam, 			
			Sum(Men) as Men_suma, 
			0 as Blue_suma,
			kodas
		FROM `prad_d`
		left join salis on Lan = salis.id
		left join kol_kodai on SUBSTRING(sst,1,4)=kodas
		where replace(Ken," ","") = "' . str_replace( ' ', '', $_POST['auto'] ) . '"
                    /*TRINTI PO 2016 06 01 and War in ("0009 DIESEL","0036 EURO 95 (SUPER)","0028 TRUCK DIESEL", "0000 DIESEL")*/
		and SUBSTRING(War,1,4) in ("0009","0036","0028","0000","0033","0048")
                and load_id=' . $load_id . '
		group by Lie_d, Lan, Nam,kodas


		union
		SELECT Lie_d, salis, Nam,  0 as Men_suma, Sum(Men) as Blue_suma, kodas
		 FROM `prad_d`
		left join salis on Lan = salis.id
		left join kol_kodai on SUBSTRING(sst,1,4)=kodas
		where replace(Ken," ","") = "' . str_replace( ' ', '', $_POST['auto'] ) . '"
		and War in ("0016 ADBLUE (lose Ware)")
                and load_id=' . $load_id . '
		group by Lie_d, Lan, Nam, kodas
		
		order by substring(Lie_d,7,4), substring(Lie_d,4,2), substring(Lie_d,1,2)


) t
group by t.Lie_d, t.Nam';
} else {
	echo( "Error description: " . mysqli_error( $dbc ) );
}
$result      = @mysqli_query( $dbc, $query );
$resultArray = array();
$arrayIndex = 0;
if ( $result ) {
	while ( $row = mysqli_fetch_array( $result ) ) {
		$resultElement         = new stdClass();
		$resultElement->date   = $row['Lie_d'];
		$resultElement->liters = $row['Men_suma'];
		$resultElement->speedometer   = $row['Lan'];
		$resultElement->country   = $row['Nam'];
		$resultElement->adBlueSum   = $row['Blue_suma'];
		$resultElement->driverId   = $row['kodas'];
		$resultArray[$arrayIndex] = $resultElement;
	}
}
/*header("Content-type: text/csv");
header("Content-Disposition: attachment; filename=".$_POST['auto'].".csv");
header("Pragma: no-cache");
header("Expires: 0");

echo 'Data;Litrai;Spidometras;Norma;Salis;Miestas;Kortele;Frigo;Ad Blue;Vairuotojai'."\r\n";
while ($row=mysqli_fetch_array($result)){
	echo $row['Lie_d'].';'.str_replace('.',',',$row['Men_suma']).';;;'.$row['Lan'].';'.$row['Nam'].';;;'.str_replace('.',',',$row['Blue_suma']).";;;".$row['kodas']."\r\n";
	}

echo "\r\n"."\r\n"."\r\n"."\r\n"."\r\n";
echo 'Viso, ltr;Likutis men. prad;;Likutis men. pab.;;Faktiskai sunaudota degalu;ltr/100 km;;Spidom. nuo;Spidom. iki'."\r\n";

echo "\r\n"."\r\n".'MB'."\r\n".'1 bakas; 2 bakas;;Viso litru'."\r\n";
$log='insert into Logas (data, ip) values (NOW(),\''.$_SERVER['REMOTE_ADDR'].'\')';
@mysqli_query ($dbc, $log);
mysqli_close($dbc);*/

?>
<?php echo json_encode( $resultArray ); ?>;
