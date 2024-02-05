<html>
<head>
<meta http-equiv=Content-Type content="text/html; charset=utf-8">
<title>Взвешивание вагонов</title>
<link rel="stylesheet" href="css/pure-min.css">
<link rel="stylesheet" href="css/style.css">
</head>

<body>
<script language="JavaScript">
    document.oncontextmenu=new Function("return false;");
</script>

<?
//require("config.inc");
require("shipment_normal.php");
require("shipment_extended.php");
require("shipment_tare.php");
require("weighing.php");
require("check.php");

$year1=$_POST['year1'];
$month1=substr($_POST['month1'], 0, 2);
$day1=$_POST['day1'];
$year2=$_POST['year2'];
$month2=substr($_POST['month2'], 0, 2);
$day2=$_POST['day2'];
$mode_weight=$_POST['mode_weight'];
$train=$_POST['train'];
$vagon=$_POST['vagon'];
$format=$_POST['format'];

switch($mode_weight)
{
	case 'check':	//поверка
		check($year1,$month1,$day1,$year2,$month2,$day2,$train,$vagon);		
		break;
	case 'shipment':	//отгрузка
	  switch($format)
		{
			case 'normal':
				shipment_normal($year1,$month1,$day1,$year2,$month2,$day2,$train,$vagon);
				break;
			case 'extended':
				shipment_extended($year1,$month1,$day1,$year2,$month2,$day2,$train,$vagon);
				break;
			case 'tare':
				shipment_tare($year1,$month1,$day1,$year2,$month2,$day2,$train,$vagon);
				break;
		 }
		 break;
	case 'weighing':	//взвешивание
		weighing($year1,$month1,$day1,$year2,$month2,$day2,$train,$vagon);
		break;
}
?>
</body>
</html>





