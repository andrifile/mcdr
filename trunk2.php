<?php
include_once("mysql.php"); include_once("header.php");

set_time_limit(0);

if (!isset($_GET['print']))
	print '<p><a href="index.php">Home</a></p>';

if (isset($_GET['trunk'])) {
	if ($_GET['trunk']=='telekom') {
		$_GET['trunk'] = 'DAHDI/G1';
	}
}
?>
<?php if(!isset($_GET['print'])): ?>
<form name="date" method="get" action="<?php print $_SERVER['REQUEST_URI']; ?>">
  <select name="year" id="year">
    <option value="2012">2012</option>
    <option value="2013">2013</option>
    <option value="2014">2014</option>
    <option value="2015">2015</option>
    <option value="2016">2016</option>
    <option value="2017">2017</option>
    <option value="2018">2018</option>
    <option value="2019">2019</option>
    <option value="2020">2020</option>
  </select>
  <select name="month" id="month">
    <option value="01">Janar</option>
    <option value="02">Shkurt</option>
    <option value="03">Mars</option>
    <option value="04">Prill</option>
    <option value="05">Maj</option>
    <option value="06">Qershor</option>
    <option value="07">Korrik</option>
    <option value="08">Gusht</option>
    <option value="09">Shtator</option>
    <option value="10">Tetor</option>
    <option value="11">Nentor</option>
    <option value="12">Dhjetor</option>
  </select>
  <?php if(isset($_GET['trunk'])): ?>
  <input type="hidden" name="trunk" id="trunk" value="<?php print $_GET['trunk']; ?>">
  <?php endif; ?>
  <input type="submit" name="filter" id="filter" value="Shfaq">
</form>
<div class="hrule"></div>
<?php
endif;

if (isset($_GET['filter'])) {
	$datetime = " and (`calldate` between '".$_GET['year']."-".$_GET['month']."-01' and '".$_GET['year']."-".$_GET['month']."-31')";
} else {
	$datetime = "";
}

//// dalje
$cmim = mysql_query("select * from cmimet");
while($row=mysql_fetch_row($cmim))
	$operator[]=array($row[1], $row[2], $row[3], $row[4], $row[5], $row[6]);

if (isset($_GET['trunk'])) {
	$query_src = "select distinct(lastdata) from cdr where  lastdata like '".$_GET['trunk']."%'";
	$query_dst = "select distinct(lastdata) from cdr where lastdata like 'SIP/4535%' and src not like '4535%' and channel like 'DAHDI/%-1'";

	$query_src_list = "select calldate, dst, billsec, src from cdr where disposition='ANSWERED' and lastdata like '%".$_GET['trunk']."%'".$datetime;

	$query_dst_list = "SELECT calldate, src, billsec, dst FROM `cdr` where (src not like '04535%' and src not like '4535%') and (dst like '04535%' and dst like '%4535%') and disposition='ANSWERED' and (channel like 'DAHDI/%-1' or dstchannel like 'DAHDI/%-1')".$datetime;
	
	//	$query_dst_list = "select calldate, src, billsec, dst from cdr where disposition='ANSWERED' and lastdata like 'SIP/4535%' and src not like '%4535%' and channel like 'DAHDI/%-1'".$datetime;
}
$result = mysql_query($query_dst);
$tnr = 0;
//while ($row = mysql_fetch_row($result)) {
	$gtotal = 0;
	$totaltime = 0;
	$nrrend = 0;
	$result2=mysql_query($query_dst_list);
?>

<div class="info">
<table><tr><td><img src="mc.jpg" border="0" alt="" /></td><td>
<table border="0" cellpadding="2" cellspacing="2">
  <tr>
    <td width="200" align="right"><b>Operatori:</b></td>
    <td><?php print strtoupper(provider($_GET['trunk'])); ?></td>
  </tr>
  <tr>
    <td width="200" align="right"><b>Periudha e faturimit:</b></td>
    <td><?php if (isset($_GET['year'])) print '01-'.$_GET['month'].'-'.$_GET['year'].' deri m&euml; '.date('t', mktime(0,0,0, $_GET['month'], 1, $_GET['year'])).'-'.$_GET['month'].'-'.$_GET['year']; else print "Te gjitha"; ?></td>
  </tr>
</table>
</td></tr></table>
</div>

<?php
	print "<table width=\"750px\" cellpadding=\"2\" cellspacing=\"2\">";
	print "<tr class=\"theader\">
	<td><b>Nr.</b></td>
	<td><b>Koha e thirrjes</b></td>
	<td><b>Source</b></td>
	<td><b>Destination</b></td>
	<td><b>Kohezgjatja</b></td>
	<td align=right><b>Vlera</b></td>
	</tr>";

	while($row = mysql_fetch_row($result2)) {
		$op = cmimi($row[1], $operator);
		$total = round($row[2]*($op[3]/60),2)+0.83;

		$date = date_create($row[0]);
		$nrrend++;
		print "<tr";
		if ($nrrend % 2 == 0)
			print ' class="even"';
		else
			print ' class="odd"';
			print "><td>$nrrend</td>
			<td>".date_format($date, "d.m.Y H:i:s")."</td>
			<td>{$row[1]}</td>
			<td>{$row[3]}</td>
			<td>".format_time($row[2])."</td>
			<td align=right>$total Leke</td>
			</tr>";
		$totaltime += $row[2];
		$gtotal += $total;
	}
	$tnr += $nrrend;
	print "</table>";
	print "<b>Koha totale e bisedave: ".format_time($totaltime)."</b><br />";


$queries = array(
	'albtelekom' => "select count(*), sum(billsec) from cdr where (src not like '04535%' and src not like '4535%') and (dst like '04535%' and dst like '%4535%') and disposition='ANSWERED' and (channel like 'DAHDI/%-1' or dstchannel like 'DAHDI/%-1') and (src like '04%' or src like '003554%')".$datetime,
	'amc'        => "select count(*), sum(billsec) from cdr where (src not like '04535%' and src not like '4535%') and (dst like '04535%' and dst like '%4535%') and disposition='ANSWERED' and (channel like 'DAHDI/%-1' or dstchannel like 'DAHDI/%-1') and (src like '068%' or src like '0035568%')".$datetime,
	'vodafone'   => "select count(*), sum(billsec) from cdr where (src not like '04535%' and src not like '4535%') and (dst like '04535%' and dst like '%4535%') and disposition='ANSWERED' and (channel like 'DAHDI/%-1' or dstchannel like 'DAHDI/%-1') and (src like '069%' or src like '0035569%')".$datetime,
	'plus'       => "select count(*), sum(billsec) from cdr where (src not like '04535%' and src not like '4535%') and (dst like '04535%' and dst like '%4535%') and disposition='ANSWERED' and (channel like 'DAHDI/%-1' or dstchannel like 'DAHDI/%-1') and (src like '066%' or src like '0035566%')".$datetime,
	'eagle'      => "select count(*), sum(billsec) from cdr where (src not like '04535%' and src not like '4535%') and (dst like '04535%' and dst like '%4535%') and disposition='ANSWERED' and (channel like 'DAHDI/%-1' or dstchannel like 'DAHDI/%-1') and (src like '067%' or src like '0035567%')".$datetime,
	'other'      => "select count(*), sum(billsec) from cdr where (src not like '04535%' and src not like '4535%') and (dst like '04535%' and dst like '%4535%') and disposition='ANSWERED' and (channel like 'DAHDI/%-1' or dstchannel like 'DAHDI/%-1') and (src not like '04%' and src not like '%3554%') and (src not like '068%' and src not like '%35568%') and (src not like '069%' and src not like '%35569%') and (src not like '066%' and src not like '%35566%') and (src not like '067%' and src not like '%35567%')".$datetime
);

print "<div align=right><br />Thirrjet sipas operatoreve:<br /><table width=\"500\" border=0 cellpadding=2 cellspacing=2 style=\"border-top:1px solid #000;\"><tr><td><b>Operatori</b></td><td align=right><b>Nr. thirrjeve</b></td><td align=right><b>Minuta total</b></td><td align=right><b>Lek total</b></td></tr>";
$total = 0;
foreach ($queries as $k=>$v) {
	$result = mysql_query($v);
	while ($row = mysql_fetch_row($result)) {
		print "<tr>";
		print "<td>".strtoupper($k)."</td>";
		print "<td align=right>{$row[0]}</td>";
		print "<td align=right>".round($row[1]/60,2)."</td>";
		$a = cmimi("", $operator, strtoupper($k));
		if ($a[5]!=1.95)
			$a[5]=1.95;
		$total += ($row[0]*0.83) + ($row[1]/60)*$a[5];
		print "<td align=right><b>".(($row[1]/60)*$a[5]+($row[0]*0.83))."</b></td>";
		print "</tr>";
		}
}
print "</table><table width=\"500\" border=0 cellpadding=2 cellspacing=2 style=\"border-top:1px solid #000;\">";
print "<tr><td></td><td></td><td align=right><b>Total :</b></td><td align=right><b>".round($total,2)."</b></td></tr>";
print "<tr><td></td><td></td><td align=right><b>TVSH  :</b></td><td align=right><b>".round($total*0.2,2)."</b></td></tr>";
print "<tr><td></td><td></td><td align=right><b>Totali me TVSH :</b></td><td align=right><b>".round($total*1.2,2)."</b></td></tr>";
print "</table></div>";

	if (!isset($_GET['print'])) {
		if (isset($_GET['year'])) {
			print '<p><a href="print.php?year='.$_GET['year'].'&month='.$_GET['month'].'&trunk='.$_GET['trunk'].'&print=1" target="_new">PDF</a>';
			print ' | ';
			print '<a href="print.php?year='.$_GET['year'].'&month='.$_GET['month'].'&trunk='.$_GET['trunk'].'" target="_new">HTML</a>';
			print ' | ';
			print '<a href="print.php?year='.$_GET['year'].'&month='.$_GET['month'].'&trunk='.$_GET['trunk'].'&file=1" target="_new">Ruaje ne server</a></p>';
		} else {
			print '<p><a href="print.php?trunk='.$_GET['trunk'].'&print=1" target="_new">PDF</a>';
			print ' | ';
			print '<a href="print.php?trunk='.$_GET['trunk'].'" target="_new">HTML</a>';
			print ' | ';
			print '<a href="print.php?trunk='.$_GET['trunk'].'&file=1" target="_new">Ruaje ne server</a></p>';
		}
	}

mysql_close($link);
?>
</body>
</html>