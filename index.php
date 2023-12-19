<?php

include_once("mysql.php"); include_once("header.php"); include_once("sugarsoap.php");

$result = mysql_query("select distinct(src) from cdr where src like '4535%'");
$trunks = mysql_query("select distinct(dcontext) from cdr");


print '<div class="indexlinks">';
print '<table border=0 width=800><tr><td valign=top>';
while ($row = mysql_fetch_row($result)) {
	//print '<a href="cdr.php?ext='.$row[0].'">'.get_sugar_name($row[0]).' ('.$row[0].')'.'</a><br />';
	print '<a href="cdr.php?ext='.$row[0].'">'.$row[0].'</a><br />';
}
print '<a href="cdr.php"><br />Shikoji te gjitha</a></td><td valign=top>';

print "<div style=\"margin-left:100px;\"><table width=300 cellpadding=2 cellspacing=2 style=\"border:1px solid #000;\"><tr><td>Thirje nga MC</td><td>Thirrje drejt MC</td></tr>";
while ($row = mysql_fetch_row($trunks)) {
	print '<tr><td><a href="trunk.php?trunk='.$row[0].'">'.$row[0].'</a></td>';
	print '<td><a href="trunk2.php?trunk='.$row[0].'">'.$row[0].'</a></td></tr>';
}
print "</table></div>";
print '</td></tr></table>';
print '</div>';
mysql_close($link);
?>
</body>
</html>