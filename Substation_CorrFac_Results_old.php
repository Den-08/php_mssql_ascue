<html>
<head>
<meta http-equiv=Content-Type content="text/html; charset=windows-1251">
</head>
<body>
<?php
$db = mssql_connect("SERVKALMENERGO,1433", "web_user", "web_user");																											// $db - база данных
mssql_select_db("KalmEnerg",$db);



$result_Substations = mssql_query("SELECT * FROM dbo.Substations WHERE Type='MT851'",$db);																							// $result_Substations - выборка терминалов MT851 (используются поля ID_Substation и Name)
if ($myrow_Substations = mssql_fetch_row($result_Substations))  																												// $myrow_Substations - строка терминалов
{
	$mytime_beginofmounth = date('m.d.Y H:i:s', mktime(0, 0, 0, date("m"), date("1"), date("Y")));																						// $mytime_beginofmounth - начало текущего месяца
	$mytime_beginofmounth2 = date('m.d.Y H:i:s', mktime(0, 0, 0, date("m")-1, date("1"), date("Y")));																					// $mytime_beginofmounth2 - начало предыдущего месяца
	$mytime_beginofmounth_heater = date('d.m.Y', mktime(0, 0, 0, date("m"), date("1"), date("Y")));																						// $mytime_beginofmounth_heater - начало текущего месяца без указания часов и минут для заголовка таблицы
	$mytime_beginofmounth2_heater = date('d.m.Y', mktime(0, 0, 0, date("m")-1, date("1"), date("Y")));																					// $mytime_beginofmounth2_heater - начало предыдущего месяца без указания часов и минут для заголовка таблицы

	// print table header	===================================		
	echo "<table border=1>\n";
	echo "<tr><td>Наименование присоединения</td><td>Коэф.учета А(+)</td><td>Показания А (+) на $mytime_beginofmounth_heater</td><td>Показания А (+) на $mytime_beginofmounth2_heater</td><td>Разница с коэф. А(+)</td><td>Коэф.учета А(-)</td><td>Показания А (-) на $mytime_beginofmounth_heater</td><td>Показания А (-) на $mytime_beginofmounth2_heater</td><td>Разница с коэф. А(-)</td></tr>\n";
	do
	{
		$result_MeasurementPlaces = mssql_query("SELECT ID_MeasurementPlace FROM dbo.MeasurementPlaces WHERE ID_Substation=$myrow_Substations[0]",$db);													// $result_MeasurementPlaces - выбор идентификатора места измерения
		$myrow_MeasurementPlaces = mssql_fetch_row($result_MeasurementPlaces);																									// $myrow_MeasurementPlaces - строка места измерения

	// A(+) 1.8.0 	===================================
		$result_MeasurementPlaceResults = mssql_query("SELECT CorrFac FROM dbo.MeasurementPlaceResults WHERE ID_ResultType=23 AND ID_MeasurementPlace=$myrow_MeasurementPlaces[0]",$db);  							// $result_MeasurementPlaceResults - выбор результата по идентификатору места измерения
		$myrow_MeasurementPlaceResults = mssql_fetch_row($result_MeasurementPlaceResults);																							// $myrow_MeasurementPlaceResults - строка результата по идентификатору

		$result_Results = mssql_query("SELECT ResultValue FROM dbo.Results WHERE ResultTimeStamp='$mytime_beginofmounth' AND ID_ResultType=23 AND ID_MeasurementPlace=$myrow_MeasurementPlaces[0]",$db);				// $result_Results
		$myrow_Results = mssql_fetch_row($result_Results);																												// $myrow_Results
		$myresults = $myrow_Results[0] / $myrow_MeasurementPlaceResults[0];																									// $myresults

		$result_Results2 = mssql_query("SELECT ResultValue FROM dbo.Results WHERE ResultTimeStamp='$mytime_beginofmounth2' AND ID_ResultType=23 AND ID_MeasurementPlace=$myrow_MeasurementPlaces[0]",$db);				// $result_Results2
		$myrow_Results2 = mssql_fetch_row($result_Results2);																												// $myrow_Results2
		$myresults2 = $myrow_Results2[0] / $myrow_MeasurementPlaceResults[0];																									// $myresults2

		if ($myrow_Results[0] >= $myrow_Results2[0]) { $mysaldo = $myrow_Results[0] - $myrow_Results2[0]; } else { $mysaldo = 0; }																// $mysaldo

	// A(-) 2.8.0 	===================================
		$result_MeasurementPlaceResults_A_minus = mssql_query("SELECT CorrFac FROM dbo.MeasurementPlaceResults WHERE ID_ResultType=27 AND ID_MeasurementPlace=$myrow_MeasurementPlaces[0]",$db);  					// $result_MeasurementPlaceResults_A_minus
		$myrow_MeasurementPlaceResults_A_minus = mssql_fetch_row($result_MeasurementPlaceResults_A_minus);																				// $myrow_MeasurementPlaceResults

		$result_Results_A_minus = mssql_query("SELECT ResultValue FROM dbo.Results WHERE ResultTimeStamp='$mytime_beginofmounth' AND ID_ResultType=27 AND ID_MeasurementPlace=$myrow_MeasurementPlaces[0]",$db);			// $result_Results_A_minus
		$myrow_Results_A_minus = mssql_fetch_row($result_Results_A_minus);																									// $myrow_Results_A_minus
		$myresults_A_minus = $myrow_Results_A_minus[0] / $myrow_MeasurementPlaceResults_A_minus[0];																					// $myresults_A_minus

		$result_Results2_A_minus = mssql_query("SELECT ResultValue FROM dbo.Results WHERE ResultTimeStamp='$mytime_beginofmounth2' AND ID_ResultType=27 AND ID_MeasurementPlace=$myrow_MeasurementPlaces[0]",$db);			// $result_Results2_A_minus
		$myrow_Results2_A_minus = mssql_fetch_row($result_Results2_A_minus);																									// $myrow_Results2_A_minus
		$myresults2_A_minus = $myrow_Results2_A_minus[0] / $myrow_MeasurementPlaceResults_A_minus[0];																					// $myresults2_A_minus

		if ($myrow_Results_A_minus[0] >= $myrow_Results2_A_minus[0]) { $mysaldo_A_minus = $myrow_Results_A_minus[0] - $myrow_Results2_A_minus[0]; } else { $mysaldo_A_minus = 0; }								// $mysaldo_A_minus

	// print table data	===================================		
		printf("<tr><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td></tr>", $myrow_Substations[1], $myrow_MeasurementPlaceResults[0], $myresults, $myresults2, $mysaldo, $myrow_MeasurementPlaceResults_A_minus[0], $myresults_A_minus, $myresults2_A_minus, $mysaldo_A_minus);
	}
	while ($myrow_Substations = mssql_fetch_array($result_Substations));
	echo "</table>\n";
}



else
{
    echo "Sorry";
}
mssql_close($db);
?>
</body>
</html>
