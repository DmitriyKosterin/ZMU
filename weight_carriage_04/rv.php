<!DOCTYPE html>
<HTML>
<HEAD>
<META http-equiv=Content-Type content="text/html; charset=windows-1251">
<TITLE>Результаты взвешивания</TITLE>
<link rel="stylesheet" type="text/css" href="style.css">
<meta http-equiv="Cache-Control" content="no-cache">
</HEAD>


<BODY>
<?
ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(-1);


$year1=$_POST["year1"];
$month1=$_POST["month1"];
$day1=$_POST["day1"];
$year2=$_POST["year2"];
$month2=$_POST["month2"];
$day2=$_POST["day2"];
$vagon=$_POST["vagon"];
$poezd=$_POST["poezd"];
$ceh=$_POST["ceh"];
$smena_a=$_POST["smena_a"];
$smena_b=$_POST["smena_b"];
$smena_w=$_POST["smena_w"];
$smena_g=$_POST["smena_g"];



//$dsn="Driver={Microsoft Visual FoxPro Driver};SourceType=DBF;SourceDB=d:\RW;Exclusive=No;Collate=Machine;NULL=NO;DELETED=NO;BACKGROUNDFETCH=NO";
//$connect=odbc_connect($dsn,"","");

$connect=odbc_connect("RW","","");


if(!$connect)
{
	print "odbc_connect";
	print odbc_errormsg();
}


$datebegin=$year1.".".$month1.".".$day1;
$dateend=$year2.".".$month2.".".$day2;
$dbegin=$day1.".".$month1.".".$year1;
$dend=$day2.".".$month2.".".$year2;
$query="SELECT * FROM table WHERE wdate>="."{^".$datebegin."}"." AND wdate <="."{^".$dateend."}";


if($poezd!="")
	$query.=" AND trnum=".$poezd;	
if($vagon!="")
	$query.=" AND nv="."'".$vagon."'";	
if($ceh!="")
	$query.=" AND ts=".$ceh;	
$strsmena=" AND (";
$smenas="";
if(isset($smena_a))
{
		$strsmena.="nsm='1' OR ";
		$smenas="А";
}
else
		$strsmena.="nsm='0' OR ";
if(isset($smena_b))
{		
		if(strlen($smenas)>0) 
				$smenas.=",";
		$smenas.="Б";
		$strsmena.="nsm='2' OR ";
}
else
		$strsmena.="nsm='0' OR ";
if(isset($smena_w))
{
		if(strlen($smenas)>0) 
				$smenas.=",";
		$smenas.="В";
		$strsmena.="nsm='3' OR ";
}
else
		$strsmena.="nsm='0' OR ";
if(isset($smena_g))
{
		if(strlen($smenas)>0) 
				$smenas.=",";
		$smenas.="Г";
		$strsmena.="nsm='4') ";
}
else
		$strsmena.="nsm='0') ";
$query.=$strsmena;
$query.=" ORDER BY systime,wtime";

//$query="select * from resvans";
//echo $query;


$res=@odbc_exec($connect,$query);


if(!$res)
{
	print "odbc_execute:";
	print odbc_errormsg();
}


?>
<h3 align=center>Взвешивание входящих и исходящих грузов на железнодорожных весах</h3>
<h4 align=center>Название весов</h3>
<h4 align=center><? echo strftime("Дата: %d.%m.%Y,&nbsp&nbsp&nbsp&nbspВремя: %H:%M:%S"); ?></h4>

<table cellpadding=2 cellspacing=0 align=center border=0>
<tr><td>&#8226 Начальная дата:<td><?echo $dbegin;?></tr>
<tr><td>&#8226 Конечная дата:<td><?echo $dend;?></tr>
<tr><td>&#8226 Смена:<td><?echo $smenas;?></td></tr>
<?
if($poezd!="")
	print "<tr><td><ul><li>Поезд:<ul></td><td>$poezd</td></tr>";
if($vagon!="")
	print "<tr><td><ul><li>Вагон:<ul></td><td>$vagon</td></tr>";
if($ceh!="")
	print "<tr><td><ul><li>Цех:<ul></td><td>$ceh</td></tr>";
?>
</table>





<br>
<p><table class="tframe" cellpadding=2 cellspacing=0 align=center border=0>
<thead style="display:table-header-group">      
<tr class="titlerow" bgcolor=#EFEBEF><th>№ поезда</th><th>№ весов</th><th>Дата</th><th>Время</th><th>Вагон</th><th>Брутто</th><th>Тара</th><th>Нетто</th><th>Цех</th><th>Смена</th><th>Таб.№</th><th>Груз</th><th>Станция</th><th>Направление</th></tr>
</thead>

<?
while($d=odbc_fetch_row($res))
{
  print "<tr>";
  $trnum=odbc_result($res,"trnum");
  $scales=odbc_result($res,"scales");
  $wdate=odbc_result($res,"wdate");
  $s=explode("-",$wdate);
  $s1=substr($s[0],2);   //последние 2 цифры года
  $wtime=odbc_result($res,"wtime");
  $nv=odbc_result($res,"nv");
  $brutto=odbc_result($res,"brutto");
  $tare=odbc_result($res,"tare");
  $netto=$brutto-$tare;
  $ts=odbc_result($res,"ts");
  $litsm=odbc_result($res,"litsm");
  $tabn=odbc_result($res,"tabn");
  $cargotype=odbc_result($res,"cargotype");
  $station=odbc_result($res,"station");
	$leave=odbc_result($res,"leave");
  if($leave) $leave="исходящий";
  else $leave="входящий";
  if($station[0]==' ') $station="&nbsp";
  print "<td>$trnum<td>$scales<td>$s[2].$s[1].$s1<td>$wtime<td>$nv<td>$brutto<td>$tare<td>$netto<td>$ts<td>$litsm<td>$tabn<td>$cargotype<td>$station<td>$leave";
  print "</tr>";
}
print "</table>";
odbc_free_result($res);
odbc_close($connect);

?>
</BODY>
</HTML>