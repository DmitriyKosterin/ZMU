<!DOCTYPE html>
<html lang="ru">
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>Результаты взешивания</title>
		<link rel="stylesheet" type="text/css" href="style.css" />
		<meta http-equiv="Cache-Control" content="no-cache">
	</head>
<body>
	<?php
		require_once("config.php"); 
		$connect=odbc_connect($ODBC_DSN,$ODBC_USER,$ODBC_PASSWORD); 
		if(!$connect)	{
			print "odbc_connect";
			print odbc_errormsg();
		}
		$year1=$_POST["year1"];
		$month1=$_POST["month1"];
		$day1=$_POST["day1"];
		$year2=$_POST["year2"];
		$month2=$_POST["month2"];
		$day2=$_POST["day2"];
		$ncar=trim($_POST["ncar"]);
		$datebegin=$year1.$month1.$day1."000000";
		$dateend=$year2.$month2.$day2."235959";
		$dbegin=$day1.".".$month1.".".$year1;
		$dend=$day2.".".$month2.".".$year2;

		$query= <<< SELECT_QUERY
		SELECT * FROM weight WHERE 
		date_time_tare >= $datebegin AND
		date_time_tare <=$dateend
SELECT_QUERY;
		// if($ncar!="")
			// $query.=" AND ntransport='$ncar'";  
		$query.=" ORDER BY date_time_tare";
		$res=odbc_exec($connect,$query);
	?>
	<a href="index.html">На главную</a> 
	<h4 align=center>Результаты взвешивания</h4>
	<h4 align=center><?= strftime("%d.%m.%Y&nbsp%H:%M:%S"); ?></h4>
	<table cellpadding=2 cellspacing=0 align=center border=0>
		<tr>
			<td>&#8226 Начальная дата:</td>
			<td><?=$dbegin;?></td>
		</tr>
		<tr>
			<td>&#8226 Конечная дата:</td>
			<td><?=$dend;?></td>
		</tr>
		<tr>
			<td><?php if($ncar!='') echo '&#8226 Номер:'?></td>
			<td><?=$ncar?></td>
		</tr>
	</table>

	<table class="tframe" cellpadding=2 cellspacing=0 align=center border=1>
		<thead style="display:table-header-group">      
			<tr class="titlerow">
				<th>#</th>
				<th>Номер</th>
				<th>Тип</th>
				<th>Дата,время<br>тара</th>
				<th>Дата,время<br>брутто</th>
				<th>Тара<br>(т)</th>
				<th>Брутто<br>(т)</th>
				<th>Нетто<br>(т)</th>
				<th>Ось<br>макс<br>(т)</th>
				<th>Продукт</th>
				<th>Водитель</th>
				<th>Оператор</th>
				<th>Цех</th>
				<th>Пропуск</th>
				<th>Комментарий</th>
			</tr>
		</thead>
		<?php
			$num=0;
			while($d=odbc_fetch_row($res))
			{
				$nauto=odbc_result($res,"ntransport");
				if($ncar == "" || $ncar == $nauto)
				{
					$num++;
					$tauto=odbc_result($res,"ttransport");
					$datetime_tare=odbc_result($res,"date_time_tare");
					$sdatetime=explode(" ",$datetime_tare);
					$sdate=explode("-",$sdatetime[0]);
					$year=substr($sdate[0],2);   //последние 2 цифры года
					$stime=$sdatetime[1];
					$datetime_brutto=odbc_result($res,"date_time_brutto");
					$sdatetime=explode(" ",$datetime_brutto);
					$sdate1=explode("-",$sdatetime[0]);
					$year1=substr($sdate1[0],2);   //последние 2 цифры года
					$stime1=$sdatetime[1];
					$tare=odbc_result($res,"tare");
					$brutto=odbc_result($res,"brutto");
					$netto=$brutto-$tare;
					if($tare==-9999 || $brutto==-9999) 
						$tare=$brutto=$netto="";
					$goods=odbc_result($res,"goods");
					$trucker=odbc_result($res,"trucker");
					$operator=odbc_result($res,"operator");
					$department=odbc_result($res,"department");
					$document=odbc_result($res,"document");
					$comment=odbc_result($res,"comment");
					$id=odbc_result($res,"id");
					$axes=odbc_result($res,"axis");
					$axis_arr = explode(";", $axes);
					$axis_max=-10.0;
					if(count($axis_arr)>=3) {
						for($i=0;$i<$axis_arr[0];$i++)
							if($axis_arr[$i+2]>$axis_max) $axis_max=$axis_arr[$i+2];
					}
					if($axis_max<0) $axis_max="";
					$rowstr= <<< ROWSTR
					<tr>
						<td>$num</td>
						<td><a href="log.php?id=$id&tablename=weight">$nauto</a>
						<td>$tauto</td>
						<td>$sdate[2].$sdate[1].$year $stime</td>
						<td>$sdate1[2].$sdate1[1].$year1 $stime1</td>
						<td>$tare</td>
						<td>$brutto</td>
						<td>$netto</td>
						<td><a href="axes.php?id=$id&tablename=weight">$axis_max</a>
						<td>$goods</td>
						<td>$trucker</td>
						<td>$operator</td>
						<td>$department</td>
						<td>$document</td>
						<td>$comment</td>
					</tr>
	ROWSTR;
					echo($rowstr);
				}
			}
			echo "</table>";
			odbc_free_result($res);
		?>
	<h4 align=center> Итоговая таблица по грузам</h4>
	<table class="tframe" cellpadding=2 cellspacing=0 align=center border=1>
		<thead style="display:table-header-group">      
			<tr class="titlerow">
				<th>Груз</th>
				<th>Вес(т)</th>
			</tr>
		</thead>	
	<?php
		if($ncar == "")
		{
			$query_goods= <<< QUERY_QOODS
			SELECT goods,
			SUM(IF(brutto=-9999 || tare=-9999,0,brutto-tare)) AS sum
			FROM weight WHERE date_time_tare >= $datebegin AND date_time_tare <=$dateend
	QUERY_QOODS;
			// if($ncar!="")
				// $query_goods.=" AND ntransport='$ncar'";  
			$query_goods.=' GROUP BY goods';
			$res=odbc_exec($connect,$query_goods);
			while($d=odbc_fetch_row($res)) {
				$goods=odbc_result($res,"goods");
				$sum=odbc_result($res,"sum");
				$str_sum=sprintf("%.3f",$sum);
				$row_goods= <<< ROW_GOODS
				<tr>
					<td>$goods</td>
					<td>$str_sum</td>
				</tr>
	ROW_GOODS;
				echo($row_goods);
			}
		}
		odbc_close($connect);
	?>
</body>
</html>
