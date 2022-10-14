<html>
<head>
<meta http-equiv=Content-Type content="text/html; charset=windows-1251">
</head>
<body>
<?php
$db = mssql_connect("SERVKALMENERGO,1433", "web_user", "web_user");																											// $db - база данных
mssql_select_db("KalmEnerg",$db);



//Форма задающая месяц и год для показаний ==========
	$d=date("m")-1;
	$god=date("Y");

?>
	<FORM ACTION="<?php echo $_SERVER['PHP_SELF'] ?>" METHOD="GET">
	<SELECT NAME="second" SIZE=1>
	<?php if (isset($_GET["second"])) {$d = $_GET["second"];}?>
		<option value=01<?php if ($d==01) {echo " selected";}?>>январь</option>
		<option value=02<?php if ($d==02) {echo " selected";}?>>февраль</option>
		<option value=03<?php if ($d==03) {echo " selected";}?>>март</option>
		<option value=04<?php if ($d==04) {echo " selected";}?>>апрель</option>
		<option value=05<?php if ($d==05) {echo " selected";}?>>май</option>
		<option value=06<?php if ($d==06) {echo " selected";}?>>июнь</option>
		<option value=07<?php if ($d==07) {echo " selected";}?>>июль</option>
		<option value=08<?php if ($d==8) {echo " selected";}?>>август</option>
		<option value=09<?php if ($d==9) {echo " selected";}?>>сентябрь</option>
		<option value=10<?php if ($d==10) {echo " selected";}?>>октябрь</option>
		<option value=11<?php if ($d==11) {echo " selected";}?>>ноябрь</option>
		<option value=12<?php if ($d==12) {echo " selected";}?>>декабрь</option>
	</SELECT>
	<SELECT NAME="fird" SIZE=1>
	<?php if (isset($_GET["fird"])) {$god = $_GET["fird"];}?>
		<option value=2025<?php if ($god==2025) {echo " selected";}?>>2025</option>
		<option value=2024<?php if ($god==2024) {echo " selected";}?>>2024</option>
		<option value=2023<?php if ($god==2023) {echo " selected";}?>>2023</option>
		<option value=2022<?php if ($god==2022) {echo " selected";}?>>2022</option>
		<option value=2021<?php if ($god==2021) {echo " selected";}?>>2021</option>
		<option value=2020<?php if ($god==2020) {echo " selected";}?>>2020</option>
		<option value=2019<?php if ($god==2019) {echo " selected";}?>>2019</option>
		<option value=2018<?php if ($god==2018) {echo " selected";}?>>2018</option>
		<option value=2017<?php if ($god==2017) {echo " selected";}?>>2017</option>
		<option value=2016<?php if ($god==2016) {echo " selected";}?>>2016</option>
		<option value=2015<?php if ($god==2015) {echo " selected";}?>>2015</option>
		<option value=2014<?php if ($god==2014) {echo " selected";}?>>2014</option>
		<option value=2013<?php if ($god==2013) {echo " selected";}?>>2013</option>
		<option value=2012<?php if ($god==2012) {echo " selected";}?>>2012</option>
		<option value=2011<?php if ($god==2011) {echo " selected";}?>>2011</option>
		<option value=2010<?php if ($god==2010) {echo " selected";}?>>2010</option>
	</SELECT>
	<INPUT TYPE="Submit" name="submit" VALUE="Найти">
	</FORM>
	<?php
	if ((isset($_GET["second"]))&&(isset($_GET["fird"])))
	{
		$period_m = $_GET["second"];
		$period_Y = $_GET["fird"];
	}
	else
	{
		$period_m = date("m")-1;
		$period_Y = date("Y");
	}
//Конец формы задающей месяц и год для показаний ==========



$result_Substations = mssql_query("SELECT * FROM dbo.Substations WHERE Type='MT851'",$db);																							// $result_Substations - выборка терминалов MT851 (используются поля ID_Substation и Name)
if ($myrow_Substations = mssql_fetch_row($result_Substations))  																												// $myrow_Substations - строка терминалов
{
	$mytime_beginofmounth = date('m.d.Y H:i:s', mktime(0, 0, 0, $period_m+1, date("1"), $period_Y));																						// $mytime_beginofmounth - начало текущего месяца
	$mytime_beginofmounth2 = date('m.d.Y H:i:s', mktime(0, 0, 0, $period_m, date("1"), $period_Y));																					// $mytime_beginofmounth2 - начало предыдущего месяца
	$mytime_beginofmounth_heater = date('d.m.Y', mktime(0, 0, 0, $period_m+1, date("1"), $period_Y));																						// $mytime_beginofmounth_heater - начало текущего месяца без указания часов и минут для заголовка таблицы
	$mytime_beginofmounth2_heater = date('d.m.Y', mktime(0, 0, 0, $period_m, date("1"), $period_Y));																					// $mytime_beginofmounth2_heater - начало предыдущего месяца без указания часов и минут для заголовка таблицы

	// print table header	===================================		
	echo "<table border=1>\n";
	echo "<tr><td bgcolor=\"#CCCCFF\">Наименование присоединения</td><td bgcolor=\"#CCCCFF\">Коэф.учета </td><td bgcolor=\"#CCCCFF\">Показания А(+) на $mytime_beginofmounth_heater</td><td bgcolor=\"#CCCCFF\">Показания А(+) на $mytime_beginofmounth2_heater</td><td bgcolor=\"#CCCCFF\">Разница с коэф. А(+)</td><td bgcolor=\"#CCCCFF\">Показания А(-) на $mytime_beginofmounth_heater</td><td bgcolor=\"#CCCCFF\">Показания А(-) на $mytime_beginofmounth2_heater</td><td bgcolor=\"#CCCCFF\">Разница с коэф. А(-)</td><td bgcolor=\"#CCCCFF\">Сальдо</td></tr>\n";
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

	// SumSaldo = A(+) - A(-) 	===================================
		$SumSaldo = $mysaldo - $mysaldo_A_minus;																														// $SumSaldo

	// print table data	===================================
		if ($myrow_Results[0] < $myrow_Results2[0] or $myrow_Results_A_minus[0] < $myrow_Results2_A_minus[0]) 
			{ printf("<tr><td bgcolor=\"#CCCCFF\">%s</td><td bgcolor=\"#CCFF99\">%s</td><td>%s</td><td>%s</td><td bgcolor=\"#CCFF99\">%s</td><td>%s</td><td>%s</td><td bgcolor=\"#CCFF99\">%s</td><td bgcolor=\"#FF0000\">%s</td></tr>", $myrow_Substations[1], $myrow_MeasurementPlaceResults[0], $myresults, $myresults2, $mysaldo, $myresults_A_minus, $myresults2_A_minus, $mysaldo_A_minus, $SumSaldo); }
			else	if ($myrow_Results[0]==0 && $myrow_Results2[0]==0 && $myrow_Results_A_minus[0]==0 && $myrow_Results2_A_minus[0]==0)
					{ printf("<tr><td bgcolor=\"#CCCCFF\">%s</td><td bgcolor=\"#CCFF99\">%s</td><td>%s</td><td>%s</td><td bgcolor=\"#CCFF99\">%s</td><td>%s</td><td>%s</td><td bgcolor=\"#CCFF99\">%s</td><td bgcolor=\"#FFFF00\">%s</td></tr>", $myrow_Substations[1], $myrow_MeasurementPlaceResults[0], $myresults, $myresults2, $mysaldo, $myresults_A_minus, $myresults2_A_minus, $mysaldo_A_minus, $SumSaldo); } 
					else	if ($myrow_Results2[0]==0 && $myrow_Results2_A_minus[0]==0)
							{ printf("<tr><td bgcolor=\"#CCCCFF\">%s</td><td bgcolor=\"#CCFF99\">%s</td><td>%s</td><td>%s</td><td bgcolor=\"#CCFF99\">%s</td><td>%s</td><td>%s</td><td bgcolor=\"#CCFF99\">%s</td><td bgcolor=\"#FFA500\">%s</td></tr>", $myrow_Substations[1], $myrow_MeasurementPlaceResults[0], $myresults, $myresults2, $mysaldo, $myresults_A_minus, $myresults2_A_minus, $mysaldo_A_minus, $SumSaldo); }
							else { printf("<tr><td bgcolor=\"#CCCCFF\">%s</td><td bgcolor=\"#CCFF99\">%s</td><td>%s</td><td>%s</td><td bgcolor=\"#CCFF99\">%s</td><td>%s</td><td>%s</td><td bgcolor=\"#CCFF99\">%s</td><td bgcolor=\"#00FF66\">%s</td></tr>", $myrow_Substations[1], $myrow_MeasurementPlaceResults[0], $myresults, $myresults2, $mysaldo, $myresults_A_minus, $myresults2_A_minus, $mysaldo_A_minus, $SumSaldo); }
					
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
