<?php

require_once('report_row.php');
$loadId = $_GET['loadId'];
$vehicle = $_GET['vehicle'];

$dbc = mysqli_connect( 'localhost', 'root', '', 'university_project' );

	$query = 'SELECT  t.Lie_d,  Max(salis) as Lan, Nam, sum(Men_suma) as Men_suma,  max(Blue_suma) as Blue_suma, max(kodas) as kodas from
(
	SELECT Lie_d, salis,Nam, 			
			Sum(Men) as Men_suma, 
			0 as Blue_suma,
			kodas
		FROM `prad_d`
		left join salis on Lan = salis.id
		left join kol_kodai on SUBSTRING(sst,1,4)=kodas
		where replace(Ken," ","") = "' . str_replace( ' ', '', $vehicle ) . '"
                    /*TRINTI PO 2016 06 01 and War in ("0009 DIESEL","0036 EURO 95 (SUPER)","0028 TRUCK DIESEL", "0000 DIESEL")*/
		and SUBSTRING(War,1,4) in ("0009","0036","0028","0000","0033","0048")
                and load_id=' . $loadId . '
		group by Lie_d, Lan, Nam,kodas


		union
		SELECT Lie_d, salis, Nam,  0 as Men_suma, Sum(Men) as Blue_suma, kodas
		 FROM `prad_d`
		left join salis on Lan = salis.id
		left join kol_kodai on SUBSTRING(sst,1,4)=kodas
		where replace(Ken," ","") = "' . str_replace( ' ', '', $vehicle ) . '"
		and War in ("0016 ADBLUE (lose Ware)")
                and load_id=' . $loadId . '
		group by Lie_d, Lan, Nam, kodas
		
		order by substring(Lie_d,7,4), substring(Lie_d,4,2), substring(Lie_d,1,2)


) t
group by t.Lie_d, t.Nam';
$result      = @mysqli_query( $dbc, $query );

$dataReportQuery = $dbc -> prepare(
	" SELECT  
 				a.FirstTankMonthEnd,
 				a.SecondTankMonthEnd,
 				a.SpeedometerMonthEnd,
 				a.FirstTankMonthStart,
 				a.SecondTankMonthStart,
 				a.Driver,
 				a.SpeedometerMonthStart,
 				l.filename
 			from auto_data a 
		    INNER JOIN csv_load l ON l.id = a.LoadId
			WHERE a.LoadId = ? AND a.Vehicle = ?");
$dataReportQuery -> bind_param('is', $loadId, $vehicle);
$dataReportQuery -> execute();
$dataReportResult = $dataReportQuery -> get_result();
$dataReportRows = array();
if ($dataReportResult -> num_rows > 0) {
	while ($row = mysqli_fetch_array($dataReportResult)) {
		array_push($dataReportRows, ReportRow::fromDbRow($row));
	}
}

header("Content-type: text/csv");
header("Content-Disposition: attachment; filename=".$vehicle.".csv");
header("Pragma: no-cache");
header("Expires: 0");

echo 'Data,Litrai,Spidometras,Norma,Salis,Miestas,Kortele,Frigo,Ad Blue,Vairuotojai'."\r\n";
while ($row=mysqli_fetch_array($result)){
	echo $row['Lie_d'].','.str_replace('.',',',$row['Men_suma']).',,,'.$row['Lan'].','.$row['Nam'].',,,'.str_replace('.',',',$row['Blue_suma']).",,,".$row['kodas']."\r\n";
	}

echo "\r\n"."\r\n"."\r\n"."\r\n"."\r\n";
echo 'Viso ltr,Likutis men. prad,,Likutis men. pab.,,Faktiskai sunaudota degalu,,,ltr/100 km,,Spidom. nuo,,Spidom. iki,, Importuota is'."\r\n";
foreach ($dataReportRows as $data_report_row) {
	echo $data_report_row -> totalLitersSum .',' .
	     $data_report_row -> monthStartLitersRemainings .
	     ',,' .$data_report_row -> monthEndLitersRemainings .
	     ',,'. $data_report_row -> usedLiters .
	     ',,,' . $data_report_row -> litersAverage .
	     ',,' . $data_report_row -> speedometerMonthStart .
	     ',,' . $data_report_row -> speedometerMonthEnd .
	     ',,' . $data_report_row -> importedFrom ."\r\n";
}
echo "\r\n"."\r\n".'MB'."\r\n".'1 bakas, 2 bakas,,Viso litru'."\r\n";
$log='insert into Logas (data, ip) values (NOW(),\''.$_SERVER['REMOTE_ADDR'].'\')';
@mysqli_query ($dbc, $log);
mysqli_close($dbc);