<html>
<head>
<meta http-equiv=Content-Type content="text/html; charset=windows-1251">
</head>
<body>
<?php
$db = mssql_connect("SERVKALMENERGO", "web_user", "web_user") or die ("Could not connect");
mssql_select_db("KalmEnerg",$db) or die ("No db");
if (!isset($_GET["Substation_name"]))
{
	printf("<a href=\"karta\">Karta</a>");
	printf("&nbsp;&nbsp;&nbsp;");
	printf("<a href=index.php>Halfhour</a>");
	printf("&nbsp;&nbsp;&nbsp;");
	printf("<a href=karta\php\good_Substation_CorrFac_Results.php>CorrFac_Results</a>");
	printf("&nbsp;&nbsp;&nbsp;");
	printf("<a href=karta\php\good_Substation_DeviceID.php>DeviceID</a>");
	printf("&nbsp;&nbsp;&nbsp;");
	printf("<a href=karta\php\good_Substation_PhoneNumber.php>PhoneNumber</a>");
	printf("&nbsp;&nbsp;&nbsp;");

	$result = mssql_query("SELECT Name FROM dbo.Substations WHERE Type='MT851'",$db);
	if ($myrow = mssql_fetch_row($result))
	{
    		echo "<table border=1>\n";
    		echo "<tr><td>Название ТИ (часовки)</td></tr>\n";
    		do
    		{
    			printf("<tr><td><a href=\"%s?Substation_name=%s\">%s</a></td></tr>", $_SERVER['PHP_SELF'], $myrow[0], $myrow[0]);
    		}
    		while ($myrow = mssql_fetch_array($result));
    		echo "</table>\n";
	}
	else
	{
    		echo "Sorry";
	}
}
else
{
	// Установить признак перехода на летнее время принудительно
	// $_GET["perehod"]=1; 
	$d=date("m");
	$god=date("Y");

	// если первый день месяца то получается число - 1 = нуль, прогон!
	if (isset($_GET["first"])) {$dd = $_GET["first"];} else {$dds=date("d"); settype($dds,"integer"); $dd=$dds-1;}
	printf("<a href=\"%s\"><==Назад</a>", $_SERVER['PHP_SELF']);
?>
	<FORM ACTION="<?php echo $_SERVER['PHP_SELF'] ?>" METHOD="GET">
	<SELECT NAME="Substation_name" SIZE=1>
	<?php
	$result = mssql_query("SELECT Name FROM dbo.Substations WHERE Type='MT851'",$db);
	if ($myrow = mssql_fetch_row($result))
	{
			do
    		{
    			if ($myrow[0]==$_GET["Substation_name"]) printf("<option value=\"%s\" selected>%s</option>", $myrow[0], $myrow[0]);
    			else printf("<option value=\"%s\">%s</option>", $myrow[0], $myrow[0]);
    		}
    		while ($myrow = mssql_fetch_array($result));
	}
	?>
	</SELECT>
	<INPUT TYPE="text" NAME="first" VALUE="<?php echo htmlspecialchars($dd);?>" SIZE="1" MAXLENGTH="2">
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
	<INPUT TYPE="checkbox" name="perehod" value=1 <?php if (isset($_GET["perehod"])) {echo " checked";}?>> <?php echo "установить признак перехода на летнее время "?>
	<INPUT TYPE="Submit" name="submit" VALUE="Обновить">
	</FORM>
	<?php
	if ((isset($_GET["first"]))&&(isset($_GET["second"]))&&(isset($_GET["fird"])))
	{
		$period_d = $_GET["first"];
		$period_m = $_GET["second"];
		$period_Y = $_GET["fird"];
	}
	else
	{
		$period_m = date("m");
		$period_d = date("d")-1;
		$period_Y = date("Y");
	}
    // Находим ID_MeasurementPlace по имени терминала
	$result_id_substation = mssql_query("SELECT ID_Substation FROM dbo.Substations WHERE Name='$_GET[Substation_name]'",$db);
	$id_substation = mssql_fetch_row($result_id_substation);
	$result_ID_MeasurementPlace = mssql_query("SELECT ID_MeasurementPlace FROM dbo.MeasurementPlaces WHERE ID_Substation=$id_substation[0]",$db);
	$ID_MeasurementPlace = mssql_fetch_row($result_ID_MeasurementPlace);


    // Выводим показания счетчика на установленную дату

    // Выводим таблицу с получасовыми срезами
   	echo "<table border=1>\n";
	echo "<tr><td bgcolor=\"#CCCCFF\">Время***</td><td bgcolor=\"#CCCCFF\">A(+)</td><td bgcolor=\"#CCCCFF\">A(-)</td><td bgcolor=\"#CCCCFF\">R(+)</td><td bgcolor=\"#CCCCFF\">R(-)</td></tr>\n";

	$my_ap_sum = 0;
	$my_am_sum = 0;
	$my_rp_sum = 0;
	$my_rm_sum = 0;
   
    // Проверяем наличие установки флага летнего времени
	if (isset($_GET["perehod"])) $iii = 1; else $iii = 0; 

    // Выводим результаты в цикле за сутки с интервалом 30 минут (30*2*24-30=1410)
    	$i = 0;
    while($i <= 1410)
   	{
    	// Установка времени для отчета (Вчера в 00:00:00 с коррекцией на летнее время если флаг установлен)
		$i = $i + 60;
		$time_yesterday_45=date('m.d.Y H:i:s', mktime(0-$iii, 0+$i-45, 0, $period_m, $period_d, $period_Y));
		$time_yesterday_30=date('m.d.Y H:i:s', mktime(0-$iii, 0+$i-30, 0, $period_m, $period_d, $period_Y));
		$time_yesterday_15=date('m.d.Y H:i:s', mktime(0-$iii, 0+$i-15, 0, $period_m, $period_d, $period_Y));
		$time_yesterday=date('m.d.Y H:i:s', mktime(0-$iii, 0+$i, 0, $period_m, $period_d, $period_Y));
		$result = mssql_query("SELECT ResultTimeStamp FROM dbo.Results WHERE ID_MeasurementPlace=$ID_MeasurementPlace[0] AND ID_ResultType=19 AND ResultTimeStamp='$time_yesterday'",$db);
		$result_halfhour = mssql_query("SELECT ResultTimeStamp FROM dbo.Results WHERE ID_MeasurementPlace=$ID_MeasurementPlace[0] AND ID_ResultType=1926 AND ResultTimeStamp='$time_yesterday'",$db);
      // Выбираем из БД показания A(+) выбранного терминала за установленное время (вчера)
		$result_ap = mssql_query("SELECT ResultValue FROM dbo.Results WHERE ID_MeasurementPlace=$ID_MeasurementPlace[0] AND ID_ResultType=19 AND ResultTimeStamp='$time_yesterday'",$db);
		$result_ap_15 = mssql_query("SELECT ResultValue FROM dbo.Results WHERE ID_MeasurementPlace=$ID_MeasurementPlace[0] AND ID_ResultType=19 AND ResultTimeStamp='$time_yesterday_15'",$db);
		$result_ap_30 = mssql_query("SELECT ResultValue FROM dbo.Results WHERE ID_MeasurementPlace=$ID_MeasurementPlace[0] AND ID_ResultType=19 AND ResultTimeStamp='$time_yesterday_30'",$db);
		$result_ap_45 = mssql_query("SELECT ResultValue FROM dbo.Results WHERE ID_MeasurementPlace=$ID_MeasurementPlace[0] AND ID_ResultType=19 AND ResultTimeStamp='$time_yesterday_45'",$db);
		// в случае получасовых срезов
		$result_ap_halfhour = mssql_query("SELECT ResultValue FROM dbo.Results WHERE ID_MeasurementPlace=$ID_MeasurementPlace[0] AND ID_ResultType=1926 AND ResultTimeStamp='$time_yesterday'",$db);
		$result_ap_halfhour_30 = mssql_query("SELECT ResultValue FROM dbo.Results WHERE ID_MeasurementPlace=$ID_MeasurementPlace[0] AND ID_ResultType=1926 AND ResultTimeStamp='$time_yesterday_30'",$db);
	// Выбираем из БД показания A(-) выбранного терминала за установленное время (вчера)
		$result_am = mssql_query("SELECT ResultValue FROM dbo.Results WHERE ID_MeasurementPlace=$ID_MeasurementPlace[0] AND ID_ResultType=20 AND ResultTimeStamp='$time_yesterday'",$db);
		$result_am_15 = mssql_query("SELECT ResultValue FROM dbo.Results WHERE ID_MeasurementPlace=$ID_MeasurementPlace[0] AND ID_ResultType=20 AND ResultTimeStamp='$time_yesterday_15'",$db);
		$result_am_30 = mssql_query("SELECT ResultValue FROM dbo.Results WHERE ID_MeasurementPlace=$ID_MeasurementPlace[0] AND ID_ResultType=20 AND ResultTimeStamp='$time_yesterday_30'",$db);
		$result_am_45 = mssql_query("SELECT ResultValue FROM dbo.Results WHERE ID_MeasurementPlace=$ID_MeasurementPlace[0] AND ID_ResultType=20 AND ResultTimeStamp='$time_yesterday_45'",$db);
		// в случае получасовых срезов
		$result_am_halfhour = mssql_query("SELECT ResultValue FROM dbo.Results WHERE ID_MeasurementPlace=$ID_MeasurementPlace[0] AND ID_ResultType=1930 AND ResultTimeStamp='$time_yesterday'",$db);
		$result_am_halfhour_30 = mssql_query("SELECT ResultValue FROM dbo.Results WHERE ID_MeasurementPlace=$ID_MeasurementPlace[0] AND ID_ResultType=1930 AND ResultTimeStamp='$time_yesterday_30'",$db);
	// Выбираем из БД показания R(+) выбранного терминала за установленное время (вчера)
		$result_rp = mssql_query("SELECT ResultValue FROM dbo.Results WHERE ID_MeasurementPlace=$ID_MeasurementPlace[0] AND ID_ResultType=21 AND ResultTimeStamp='$time_yesterday'",$db);
		$result_rp_15 = mssql_query("SELECT ResultValue FROM dbo.Results WHERE ID_MeasurementPlace=$ID_MeasurementPlace[0] AND ID_ResultType=21 AND ResultTimeStamp='$time_yesterday_15'",$db);
		$result_rp_30 = mssql_query("SELECT ResultValue FROM dbo.Results WHERE ID_MeasurementPlace=$ID_MeasurementPlace[0] AND ID_ResultType=21 AND ResultTimeStamp='$time_yesterday_30'",$db);
		$result_rp_45 = mssql_query("SELECT ResultValue FROM dbo.Results WHERE ID_MeasurementPlace=$ID_MeasurementPlace[0] AND ID_ResultType=21 AND ResultTimeStamp='$time_yesterday_45'",$db);
		// в случае получасовых срезов
		$result_rp_halfhour = mssql_query("SELECT ResultValue FROM dbo.Results WHERE ID_MeasurementPlace=$ID_MeasurementPlace[0] AND ID_ResultType=1934 AND ResultTimeStamp='$time_yesterday'",$db);
		$result_rp_halfhour_30 = mssql_query("SELECT ResultValue FROM dbo.Results WHERE ID_MeasurementPlace=$ID_MeasurementPlace[0] AND ID_ResultType=1934 AND ResultTimeStamp='$time_yesterday_30'",$db);
	// Выбираем из БД показания A(-) выбранного терминала за установленное время (вчера)
		$result_rm = mssql_query("SELECT ResultValue FROM dbo.Results WHERE ID_MeasurementPlace=$ID_MeasurementPlace[0] AND ID_ResultType=22 AND ResultTimeStamp='$time_yesterday'",$db);
		$result_rm_15 = mssql_query("SELECT ResultValue FROM dbo.Results WHERE ID_MeasurementPlace=$ID_MeasurementPlace[0] AND ID_ResultType=22 AND ResultTimeStamp='$time_yesterday_15'",$db);
		$result_rm_30 = mssql_query("SELECT ResultValue FROM dbo.Results WHERE ID_MeasurementPlace=$ID_MeasurementPlace[0] AND ID_ResultType=22 AND ResultTimeStamp='$time_yesterday_30'",$db);
		$result_rm_45 = mssql_query("SELECT ResultValue FROM dbo.Results WHERE ID_MeasurementPlace=$ID_MeasurementPlace[0] AND ID_ResultType=22 AND ResultTimeStamp='$time_yesterday_45'",$db);
		// в случае получасовых срезов
		$result_rm_halfhour = mssql_query("SELECT ResultValue FROM dbo.Results WHERE ID_MeasurementPlace=$ID_MeasurementPlace[0] AND ID_ResultType=1938 AND ResultTimeStamp='$time_yesterday'",$db);
		$result_rm_halfhour_30 = mssql_query("SELECT ResultValue FROM dbo.Results WHERE ID_MeasurementPlace=$ID_MeasurementPlace[0] AND ID_ResultType=1938 AND ResultTimeStamp='$time_yesterday_30'",$db);
	// Выводим таблицу
		// из получасовок		
		if (($myrow = mssql_fetch_row($result_halfhour))&&($myrow_ap = mssql_fetch_row($result_ap_halfhour))&&($myrow_am = mssql_fetch_row($result_am_halfhour))&&($myrow_rp = mssql_fetch_row($result_rp_halfhour))&&($myrow_rm = mssql_fetch_row($result_rm_halfhour))&&($myrow_ap_30 = mssql_fetch_row($result_ap_halfhour_30))&&($myrow_am_30 = mssql_fetch_row($result_am_halfhour_30))&&($myrow_rp_30 = mssql_fetch_row($result_rp_halfhour_30))&&($myrow_rm_30 = mssql_fetch_row($result_rm_halfhour_30)))
		{
    			do
    			{
				$my_ap = $myrow_ap[0] + $myrow_ap_30[0]; 
				$my_am = $myrow_am[0] + $myrow_am_30[0]; 
				$my_rp = $myrow_rp[0] + $myrow_rp_30[0]; 
				$my_rm = $myrow_rm[0] + $myrow_rm_30[0]; 
    				printf("<tr><td bgcolor=\"#CCCCFF\">%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td></tr>", $myrow[0], $my_ap, $my_am, $my_rp, $my_rm);
    			// Подсчет суммы за день
				$my_ap_sum = $my_ap_sum + $my_ap;
    				$my_am_sum = $my_am_sum + $my_am;
    				$my_rp_sum = $my_rp_sum + $my_rp;
    				$my_rm_sum = $my_rm_sum + $my_rm;
			}
      		while (($myrow = mssql_fetch_array($result_halfhour))&&($myrow_ap = mssql_fetch_array($result_ap_halfhour))&&($myrow_am = mssql_fetch_array($result_am_halfhour))&&($myrow_rp = mssql_fetch_array($result_rp_halfhour))&&($myrow_rm = mssql_fetch_array($result_rm_halfhour)));
    		}
		// из 15-ти минуток
		else
		{
		if (($myrow = mssql_fetch_row($result))&&($myrow_ap_15 = mssql_fetch_row($result_ap_15))&&($myrow_ap = mssql_fetch_row($result_ap))&&($myrow_am_15 = mssql_fetch_row($result_am_15))&&($myrow_am = mssql_fetch_row($result_am))&&($myrow_rp_15 = mssql_fetch_row($result_rp_15))&&($myrow_rp = mssql_fetch_row($result_rp))&&($myrow_rm_15 = mssql_fetch_row($result_rm_15))&&($myrow_rm = mssql_fetch_row($result_rm))&&($myrow_ap_45 = mssql_fetch_row($result_ap_45))&&($myrow_ap_30 = mssql_fetch_row($result_ap_30))&&($myrow_am_45 = mssql_fetch_row($result_am_45))&&($myrow_am_30 = mssql_fetch_row($result_am_30))&&($myrow_rp_45 = mssql_fetch_row($result_rp_45))&&($myrow_rp_30 = mssql_fetch_row($result_rp_30))&&($myrow_rm_45 = mssql_fetch_row($result_rm_45))&&($myrow_rm_30 = mssql_fetch_row($result_rm_30)))
		{
    			do
    			{
				$my_ap = $myrow_ap[0] + $myrow_ap_15[0] + $myrow_ap_30[0] + $myrow_ap_45[0];
				$my_am = $myrow_am[0] + $myrow_am_15[0] + $myrow_am_30[0] + $myrow_am_45[0];
				$my_rp = $myrow_rp[0] + $myrow_rp_15[0] + $myrow_rp_30[0] + $myrow_rp_45[0];
				$my_rm = $myrow_rm[0] + $myrow_rm_15[0] + $myrow_rm_30[0] + $myrow_rm_45[0];
    				printf("<tr><td bgcolor=\"#CCCCFF\">%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td></tr>", $myrow[0], $my_ap, $my_am, $my_rp, $my_rm);
			// Подсчет суммы за день
    				$my_ap_sum = $my_ap_sum + $my_ap;
    				$my_am_sum = $my_am_sum + $my_am;
    				$my_rp_sum = $my_rp_sum + $my_rp;
    				$my_rm_sum = $my_rm_sum + $my_rm;
			}
    			while (($myrow = mssql_fetch_array($result))&&($myrow_ap_15 = mssql_fetch_array($result_ap_15))&&($myrow_ap = mssql_fetch_array($result_ap))&&($myrow_am_15 = mssql_fetch_array($result_am_15))&&($myrow_am = mssql_fetch_array($result_am))&&($myrow_rp_15 = mssql_fetch_array($result_rp_15))&&($myrow_rp = mssql_fetch_array($result_rp))&&($myrow_rm_15 = mssql_fetch_array($result_rm_15))&&($myrow_rm = mssql_fetch_array($result_rm)));
    		}
		}
     	}
    printf("<tr><td bgcolor=\"#CCCCFF\">%s</td><td bgcolor=\"#00FF66\">%s</td><td bgcolor=\"#00FF66\">%s</td><td bgcolor=\"#00FF66\">%s</td><td bgcolor=\"#00FF66\">%s</td></tr>", 'Сумма за день', $my_ap_sum, $my_am_sum, $my_rp_sum, $my_rm_sum);
    echo "</table>\n";
    printf("<a href=\"%s\"><==Назад</a>", $_SERVER['PHP_SELF']);
 
}
mssql_close($db);
?>
</body>
</html>
