<html>
<head>
<meta http-equiv=Content-Type content="text/html; charset=windows-1251">
</head>
<body>
<?php
$db = mssql_connect("SERVKALMENERGO,1433", "web_user", "web_user");
mssql_select_db("KalmEnerg",$db);
$result = mssql_query("SELECT * FROM dbo.SubstationDetails WHERE PropertyName='DeviceID'",$db);
if ($myrow = mssql_fetch_row($result))
{
    echo "<table border=1>\n";
    echo "<tr><td bgcolor=\"#CCCCFF\">Name</td><td bgcolor=\"#CCCCFF\">DeviceID</td></tr>\n";
    do
    {
    $result_name = mssql_query("SELECT Name FROM dbo.Substations WHERE ID_Substation=$myrow[0] AND Type='MT851'",$db);
    $name = mssql_fetch_row($result_name);
    printf("<tr><td bgcolor=\"#CCCCFF\">%s</td><td>%s</td></tr>", $name[0], $myrow[2]);
    }
    while ($myrow = mssql_fetch_array($result));
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
