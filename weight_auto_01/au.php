<!DOCTYPE html>
<html>
  <head>
    <meta http-equiv=Content-Type content="text/html; charset=utf-8">
    <title>Результаты</title>
    <link rel="stylesheet" type="text/css" href="style.css">
    <meta http-equiv="Cache-Control" content="no-cache">
  </head>

  <body>
    <?php
      require("config.inc.php");
      $connect=odbc_connect($ODBC_DSN,$ODBC_USER,$ODBC_PASSWORD); 
      if(!$connect) {
        print "odbc_connect error:";
        print iconv("windows-1251","utf-8",odbc_errormsg());
      }
      $year1=$_POST["year1"];
      $month1=$_POST["month1"];
      $day1=$_POST["day1"];
      $year2=$_POST["year2"];
      $month2=$_POST["month2"];
      $day2=$_POST["day2"];
      $ncar=$_POST["ncar"];
      $datebegin=$year1.$month1.$day1."000000";
      $dateend=$year2.$month2.$day2."235959";
      $dbegin=$day1.".".$month1.".".$year1;
      $dend=$day2.".".$month2.".".$year2;
      $query= "SELECT * FROM weight WHERE ". 
              "(date_time_tare>='$datebegin' AND " . 
              "date_time_tare <='$dateend')";
	  // print $ncar;
      $part_qwery=substr($query,9);
      $query.=" ORDER BY date_time_tare";
      // print $query; 
      $res=odbc_exec($connect,$query);
	  // print $res;
	  print odbc_errormsg($connect);
    ?>
    <h2 align=center>Весы №5</h2>
    <p><a href="index.html">На главную</a></p> 
    <h4 align=center><?php echo strftime("Дата: %d.%m.%Y,&nbsp&nbsp&nbsp&nbspВремя: %H:%M:%S"); ?></h4>

    <table cellpadding=2 cellspacing=0 align=center border=0>
      <tr><td>&#8226 Начальная дата:<td><?echo $dbegin;?></tr>
      <tr><td>&#8226 Конечная дата:<td><?echo $dend;?></tr>
      <?php
        if($ncar!="")
          print "<tr><td><ul><li>Номер:<ul></td><td>$ncar</td></tr>";
      ?>
    </table><br>
    <p>
      <table class="tframe" cellpadding=2 cellspacing=0 align=center border=1>
        <thead style="display:table-header-group">      
          <tr class="titlerow" bgcolor=#EFEBEF><th>Номер<br>автомобиля</th><th>Тип<br>автомобиля</th><th>Дата,время<br>тара</th><th>Дата,время<br>брутто</th><th>Тара<br>(т)</th><th>Брутто<br>(т)</th><th>Нетто<br>(т)</th><th>Продукт</th><th>Водитель</th><th>Оператор</th><th>Цех</th><th>Пропуск</th><th>Комментарий</th></tr>
        </thead>
        <?php
		// print odbc_num_rows($res);
          while($d=odbc_fetch_row($res))
		  {
			  $nauto=odbc_result($res,"ntransport");
			  if($ncar == "" || $ncar == $nauto)
			  {
				print "<tr>";
				// print $nauto;
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
				  $snetto="";
				else
				  $snetto=sprintf("%.3f",$netto);
				if($tare==-9999) 
				  $stare="";
				else
				  $stare=sprintf("%.3f",$tare);
				if($brutto==-9999) 
				  $sbrutto="";
				else
				  $sbrutto=sprintf("%.3f",$brutto);
				$goods=odbc_result($res,"goods");
				$trucker=odbc_result($res,"trucker");
				$operator=odbc_result($res,"operator");
				$department=odbc_result($res,"department");
				$document=odbc_result($res,"document");
				$comment=odbc_result($res,"comment");
				$id=odbc_result($res,"id");
				print "<td><a href='log.php?id=$id'>$nauto</a><td>$tauto<td>$sdate[2].$sdate[1].$year $stime<td>$sdate1[2].$sdate1[1].$year1 $stime1<td>$stare<td>$sbrutto<td>$snetto<td>$goods<td>$trucker<td>$operator<td>$department<td>$document<td>$comment";
				print "</tr>";
			  }
          }
          odbc_free_result($res);
        ?>
      </table>
    </p>
    <h4 align=center> Итоговая таблица по грузам</h4>
    <table class="tframe" cellpadding=2 cellspacing=0 align=center border=1>
      <thead style="display:table-header-group">      
        <tr class="titlerow" bgcolor=#EFEBEF><th>Груз</th><th>Вес(т)</th></tr>
      </thead>
      <tbody>
        <?php
		if($ncar == "")
		{
		  $query_goods="SELECT goods,SUM(IF(brutto=-9999 || tare=-9999,0,brutto-tare)) AS sum " . 
                        $part_qwery . " GROUP BY goods";
          $res=odbc_exec($connect,$query_goods);
          while($d=odbc_fetch_row($res)) {
            echo "<tr>";
            $goods=odbc_result($res,"goods");
            $sum=odbc_result($res,"sum");
            echo "<td align=left>$goods<td>".sprintf("%.3f",$sum);
            echo "</tr>";
          }
		}
          odbc_close($connect);
        ?>
      </tbody>
    </table>
  </body>
</html>