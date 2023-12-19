<?php
include_once("mysql.php"); 
include_once("header.php"); 
include_once("sugarsoap.php");

set_time_limit(0);

if (!isset($_GET['print']))
	print '<p><a href="index.php">Home</a></p>';

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
  <?php if(isset($_GET['ext'])): ?>
  <input type="hidden" name="ext" id="ext" value="<?php print $_GET['ext']; ?>">
  <?php endif; ?>
  <input type="submit" name="filter" id="filter" value="Shfaq">
</form>
<div class="hrule"></div>
<?php
endif;

if (isset($_GET['filter'])) {
	$datetime = " and `calldate`<='".$_GET['year']."-".$_GET['month']."-31' and `calldate`>='".$_GET['year']."-".$_GET['month']."-01'";
} else {
	$datetime = "";
}

//tarifat
$cmim = mysql_query("select * from cmimet");
while($row=mysql_fetch_row($cmim))
	$operator[]=array($row[1], $row[2], $row[5]);

if (!isset($_GET['ext']))
	$query = "select distinct(src) from cdr where src like '%4535%'";
else
	$query = "select distinct(src) from cdr where src like '".$_GET['ext']."%'";
$result = mysql_query($query);

$tnr = 0;
while ($row = mysql_fetch_row($result)) {
	$extension=$row[0];
	$gtotal = 0;
	$totaltime = 0;
	$nrrend = 0;
	$query = "select calldate, dst, billsec from cdr where disposition='ANSWERED' and src='$extension'".$datetime;
	$result2=mysql_query($query);
	
?>

<div class="info">
<table><tr><td width="400"><img src="mc.jpg" border="0" alt="" /></td><td>
<table border="0" cellpadding="2" cellspacing="2">
  <tr>
    <td width="150" align="right"><b>Emri i abonentit:</b></td>
    <td><?php print get_sugar_name($_GET['ext']); ?></td>
  </tr>
  <tr>
    <td width="150" align="right"><b>Numri i telefonit:</b></td>
    <td><?php print "0".$extension; ?></td>
  </tr>
  <tr>
    <td width="150" align="right"><b>Adresa:</b></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td width="150" align="right"><b>Periudha e faturimit:</b></td>
    <td><?php if (isset($_GET['year'])) print '01-'.$_GET['month'].'-'.$_GET['year'].' deri '.date('t', mktime(0,0,0, $_GET['month'], 1, $_GET['year'])).'-'.$_GET['month'].'-'.$_GET['year']; else print "Te gjitha"; ?></td>
  </tr>
  <tr>
    <td width="150" align="right"><b>Plani i tarifimit:</b></td>
    <td>&nbsp;</td>
  </tr>
</table>
</td></tr></table>
</div>

<?php

	

	print "<table width=\"750px\" cellpadding=\"2\" cellspacing=\"2\">";
	print "<tr class=\"theader\">
	<td><b>Nr.</b></td>
	<td><b>Koha e thirrjes</b></td>
	<td><b>Nr. i thirrur</b></td>
	<td><b>Destinacioni</b></td>
	<td><b>Kohezgjatja</b></td>
	<td align=right><b>Cmimi</b></td>
	</tr>";

	while($row = mysql_fetch_row($result2)) {
	
	
		$op = cmimi($row[1], $operator);
		echo "<pre>";
	//var_dump($row);
	echo "</pre>";
		if ($op[2]!="0") {
			if ($row[2] <= 30) {
				$total = round($op[2]/2,2);
			} else {
				$total = round(($row[2]/60)*$op[2],2);
			}
		} else {
			$total = 0;
		}
		
		$date = date_create($row[0]);
		$nrrend++;
		print "<tr"; 
		
		$qq = substr($row[1], 0, 5);
		if($qq == "04535"){
			print ' class="error"';
			$total = 0;
			$op[0] = "Internal";
		}
		if (strlen($row[1])<4)
			print ' class="error"';
		if ($nrrend % 2 == 0)
			print ' class="even"';
		
		else
			print ' class="odd"';
			print "><td>$nrrend</td><td>".date_format($date, "d.m.Y H:i:s")."</td><td>{$row[1]}</td><td>{$op[0]}</td><td>".format_time($row[2])."</td><td align=right>$total Leke</td></tr>";
		$totaltime += $row[2];
		$gtotal += $total;
	}
	$tnr += $nrrend;
	print "</table>";
	print "<table width=\"750px\"><tr><td><b>Koha totale e bisedave: ".format_time($totaltime)."</b></td><td align=right><h4>Totali pa tvsh: ".$gtotal." Leke<br />Tvsh: ".($gtotal*0.2)." Leke<br />Totali: ".($gtotal+($gtotal*0.2))." Leke</h4></td></tr></table>";

	if (isset($_GET['print'])) {
		print '<barcode type="EAN13" value="45677654" label="" style="width:30mm; height:5mm; color: #222; font-size: 4mm"></barcode>';
		//print '<qrcode value="http://google.com" ec="H" style="width: 50mm; background-color: white; color: black;"></qrcode>';
	}

	if (!isset($_GET['print'])) {
		if (isset($_GET['year'])) {
			print '<p><a href="print.php?year='.$_GET['year'].'&month='.$_GET['month'].'&ext='.$_GET['ext'].'&print=1" target="_new">PDF</a>';
			print ' | ';
			print '<a href="print.php?year='.$_GET['year'].'&month='.$_GET['month'].'&ext='.$_GET['ext'].'" target="_new">HTML</a>';
			print ' | ';
			print '<a href="print.php?year='.$_GET['year'].'&month='.$_GET['month'].'&ext='.$_GET['ext'].'&file=1" target="_new">Ruaje ne server</a></p>';
		} else {
			print '<p><a href="print.php?ext='.$_GET['ext'].'&print=1" target="_new">PDF</a>';
			print ' | ';
			print '<a href="print.php?ext='.$_GET['ext'].'" target="_new">HTML</a>';
			print ' | ';
			print '<a href="print.php?ext='.$_GET['ext'].'&file=1" target="_new">Ruaje ne server</a></p>';
		}
	}
}
if (!isset($_GET['ext'])) print "Nr. Total i thirrjeve: ".$tnr;
mysql_close($link);
?>
</body>
</html>