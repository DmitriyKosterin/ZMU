<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" type="text/css" href="style.css" />
	<script src="jquery.js"></script>
	<title>Рабочая таблица</title>
</head>
<body>
	<?php
		require_once("config.php"); 
		$connect=odbc_connect($ODBC_DSN,$ODBC_USER,$ODBC_PASSWORD);  
		$query="SELECT * FROM temp ORDER BY id";
		$res=odbc_exec($connect,$query);
	?>
	<a href="index.html">На главную</a> 
		<div id="curdata">
      <div id="curweight"></div>
      <div id="operator"></div>
		</div>
		
    <script>
      function showCurdata() {
        $.ajax({
          dataType: "json",
          url: "curdata.php",  
          cache: false,  
          success: function(jsonObj){
            var d=Number(jsonObj.curw).toFixed(3);
            $("#curweight").text(d==-999999.0 ? "?????":d);
            $("#operator").text(jsonObj.operator);
          }          
        });  
      };  
      $(document).ready(function(){  
        setInterval('showCurdata()',500);  
      });  
    </script>

	<h4 align=center>Рабочая таблица</h4>
	<table class="tframe" cellpadding=2 cellspacing=0 align=center border=1>
		<thead style="display:table-header-group">      
			<tr class="titlerow">
				<th>#</th>
				<th>Номер</th>
				<th>Тип</th>
				<th>Дата,время<br>тары</th>
				<th>Дата,время<br>брутто</th>
				<th>Тара<br>(т)</th>
				<th>Брутто<br>(т)</th>
				<th>Нетто<br>(т)</th>
				<th>Ось<br>макс<br>(т)</th>
				<th>Груз</th>
				<th>Водитель</th>
				<th>Оператор</th>
				<th>Цех</th>
				<th>Пропуск</th>
				<th>Комментарий</th>
				<th>Т</th>
				<th>Б</th>
				<th>И</th>
			</tr>
		</thead>
	<?php
		$num=0;
		while($d=odbc_fetch_row($res)) {
			$num++;
			$nauto=odbc_result($res,"ntransport");
			$tauto=odbc_result($res,"ttransport");
			$datetime_tare=odbc_result($res,"date_time_tare");
			$attr_datetime_tare=odbc_result($res,"attr_date_time_tare");
			$datetime_brutto=odbc_result($res,"date_time_brutto");
			$attr_datetime_brutto=odbc_result($res,"attr_date_time_brutto");
			$tare=odbc_result($res,"tare");
			$attr_tare=odbc_result($res,"attr_tare");
			$brutto=odbc_result($res,"brutto");
			$attr_brutto=odbc_result($res,"attr_brutto");
			$netto=odbc_result($res,"netto");
			$attr_netto=odbc_result($res,"attr_netto");
			$goods=odbc_result($res,"goods");
			$trucker=odbc_result($res,"trucker");
			$operator=odbc_result($res,"operator");
			$department=odbc_result($res,"department");
			$document=odbc_result($res,"document");
			$comment=odbc_result($res,"comment");
			$attr_taraTableButton=odbc_result($res,"attr_taraTableButton");
			$attr_bruttoTableButton=odbc_result($res,"attr_bruttoTableButton");
			$attr_infoTableButton=odbc_result($res,"attr_infoTableButton");
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
					<td><a href="log.php?id=$id&tablename=temp">$nauto</a>
					<td>$tauto</td>
					<td state=$attr_datetime_tare>$datetime_tare</td>
					<td state=$attr_datetime_brutto>$datetime_brutto</td>
					<td state=$attr_tare>$tare</td>
					<td state=$attr_brutto>$brutto</td>
					<td state=$attr_netto>$netto</td>
					<td><a href="axes.php?id=$id&tablename=temp">$axis_max</a>
					<td>$goods</td>
					<td>$trucker</td>
					<td>$operator</td>
					<td>$department</td>
					<td>$document</td>
					<td>$comment</td>
					<td stateButton=$attr_taraTableButton>Т</td>
					<td stateButton=$attr_bruttoTableButton>Б</td>
					<td stateButton=$attr_infoTableButton>И</td>
				</tr>
ROWSTR;
			echo($rowstr);
		}
		echo("</table>");
		odbc_free_result($res);
		odbc_close($connect);
	?>
</body>
</html>
