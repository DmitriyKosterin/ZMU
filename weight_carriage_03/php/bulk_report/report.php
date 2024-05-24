<!DOCTYPE html>
<html lang="ru">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=8" />
    <title>Насыпь</title>
    <link rel="stylesheet" href="../../css/pure-min.css">
    <link rel="stylesheet" href="../../css/style.css">
  </head>
  <body>
    <?php require("../config.inc.php"); ?>
      <script language="JavaScript">
        document.oncontextmenu=new Function("return true;");
    </script>
     <?php
      $smenas=[];
      $str_smenas='';
      $smenaselect='';
      $year1=$_POST['year1'];
      $month1= substr($_POST['month1'], 0, 2);
      $day1=$_POST['day1'];
      $time1=$_POST['time1'];
      $year2=$_POST['year2'];
      $month2= substr($_POST['month2'], 0, 2);
      $day2=$_POST['day2'];
      $time2=$_POST['time2'];

      if(isset($_POST['in_kg']))
        $in_kg=$_POST['in_kg'];
      else
        $in_kg=false;

      $vagon=$_POST['vagon'];
      if(isset($_POST['smena1']))
        $smenas[]=1;
      if(isset($_POST['smena2']))
        $smenas[]=2;
      if(isset($_POST['smena3']))
        $smenas[]=3;
      if(isset($_POST['smena4']))
        $smenas[]=4;
      if(count($smenas)>0)
      {
        $smenaselect.='(';
        foreach ($smenas as $smena) {
          $smenaselect.="smena=$smena OR ";
          $str_smenas.= ($smena==1)? 'A,': (($smena==2)? 'Б,':(($smena==3)? 'В,':(($smena==4)? 'Г,':'')));
        }
        $str_smenas=substr($str_smenas, 0, -1);
        $smenaselect=substr($smenaselect, 0, -4);
        $smenaselect.=') ';
      }
      else
      {
        echo "<p><h3>Не выбрано ни одной смены</h3></p>";
        exit();
      }
      $datebegin="$year1" . ".$month1" . ".$day1";
      $dateend="$year2" . ".$month2" . ".$day2";
      $str_datebegin="$day1" . ".$month1" . ".$year1";
      $str_dateend="$day2" . ".$month2" . ".$year2";
      $timebegin=($time1=='24:00')? '23:59:59': $time1.':00';
      $timeend=($time2=='24:00')? '23:59:59': $time2.':00';
      //echo "<br>".$timebegin.'  '.$timeend."<br>";
      $query="SELECT * FROM $wdata_table  WHERE ";
      if($vagon!='')
        $query.="vagon='$vagon' AND ";
      $query.=$smenaselect . "AND (";
      if($datebegin!=$dateend) 
      {      
        $query.="(wdate_w = '$datebegin' AND wtime_w >= '$timebegin') OR ";
        $query.="(wdate_w = '$dateend' AND wtime_w <= '$timeend') OR ";
        $query.="(wdate_w > '$datebegin' AND wdate_w <'$dateend'))";
      }
      else
        $query.="(wdate_w = '$datebegin' AND wtime_w >= '$timebegin' AND wtime_w <='$timeend'))";
      $part_qwery=substr($query,9);
      //echo $part_qwery;
      $query.="ORDER BY wdate_w,wtime_w";
      //echo $query;
      $mysqli=mysqli_connect($MYSQL_BASE_HOST,$MYSQL_USER_NAME,$MYSQL_USER_PASS, $MYSQL_BASE_NAME);
      if(mysqli_connect_errno()) {
        echo("<h3>Ошибка соединения c базой данных</h3>");
        exit();
      }
	 // echo $query;
      $result=mysqli_query($mysqli,$query);
      $result_count=mysqli_num_rows($result);
      if($result_count>1000)
      {
          echo "<p><h3>Полученные данные содержат $result_count записей. Допускается делать выборки, содержащие не более 1000 записей.<h3></p>";
          exit();
      }
      $cur_date_time=strftime("%d.%m.%Y / %H:%M:%S");
    ?>
    <div>
      <h3>Отгрузка насыпью в ж/д вагоны</h3>
      <h4>Дата/время формирования отчёта: <?=$cur_date_time?></h4>
      <h5>Начальная дата/время: <?= $str_datebegin . ' / ' .$time1 ?></h5>
      <h5>Конечная дата/время:  <?= $str_dateend . ' / ' .$time2 ?></h5>
      <h5>Смена: <?=$str_smenas?></h5>
      <?php 
        if(strlen($vagon)>0)
          echo "<h5>Вагон: $vagon</h5>";
      ?>
    </div>
    <p>
      <table class="tabledata">
        <thead style="display:table-header-group">      
          <tr>
            <th>Дата</th>
            <th>Весы</th>
            <th>Начало</th>
            <th>Оконч</th>
            <th>Взвеш</th>
            <th>№ вагона</th>
            <th>Трафарет</th>
            <th>Тара</th>
			<th>Ось1(Тара)</th>
			<th>Ось2(Тара)</th>
            <th>Нетто</th>
            <th>Брутто</th>
            <th>Ось1(Брутто)</th>
            <th>Ось2(Брутто)</th>
            <th>Дисб.</th>
            <th>Продукт</th>
            <th>Таб. №</th>
            <th>Смена</th>
          </tr>
        </thead>
        <tbody>
          <?php
            $ct=0;
            while($row=mysqli_fetch_assoc($result))
            {
              $date=explode("-",$row['wdate_w']); //дата: 2017-10-30
              $year=substr($date[0],2);   //последние 2 цифры года: 17
              $nettow=sprintf("%.2f",$row['brutto_w']-$row['taraf']);
              if($in_kg) $nettow*=1000;
              $tara=sprintf("%.2f",$row['taraf']);
			  if($in_kg) $tara*=1000;
			  $front_tara=sprintf("%.2f",$row['front_tara']);
              if($in_kg) $front_tara*=1000;
              $brutto=sprintf("%.2f",$row['brutto_w']);
              if($in_kg) $brutto*=1000;
              //$brutto_d=sprintf("%.2f",$row['brutto_d']);
              $nweight=$row['nweight']+1;
              $car1=sprintf("%.2f",$row['brutto_w_car1']);
              if($in_kg) $car1*=1000;
              $smena_code=$row['smena'];
              $start_wtime_d=$row['start_wtime_d'];
              $wtime_d=$row['wtime_d'];
              $wtime_w=$row['wtime_w'];
              $vagon=$row['vagon'];
              $tarar=sprintf("%.2f",$row['tarar']);
              if($in_kg) $tarar*=1000;
              $product=$row['product'];
              $tabn=$row['tabn'];
              $gu1=$gu2=$front_tara1=$front_tara2=0.0;
              if($car1)
              {
                $disbalans=$in_kg ? sprintf("%.0f",2*$car1-$brutto) : sprintf("%.2f",2*$car1-$brutto);
                $gu1=$in_kg ? sprintf("%.0f",$car1) : sprintf("%.2f",$car1);
                $gu2=$in_kg ? sprintf("%.0f",$brutto-$car1) : sprintf("%.2f",$brutto-$car1);
              }
              else
                $disbalans="";
			if($front_tara > 0)
			  {
				$front_tara1=$in_kg ? sprintf("%.0f",$front_tara) : sprintf("%.2f",$front_tara);
				$front_tara2=$in_kg ? sprintf("%.0f",$tara-$front_tara) : sprintf("%.2f",$tara-$front_tara);
			  }
              $smena_liter= ($smena_code==1)? 'A': (($smena_code==2)? 'Б':(($smena_code==3)? 'В':(($smena_code==4)? 'Г':'')));
              $ct++;
              echo  "<tr>".
                    "<td>$date[2].$date[1].$year</td>".   //дата: 30.10.17
                    "<td>$nweight</td>".                  //номер весов
                    "<td>$start_wtime_d</td>".            //начало
                    "<td>$wtime_d</td>".                  //окончание
                    "<td>$wtime_w</td>".                  //взвешивание
                    "<td>$vagon</td>".                    //вагон
                    "<td>$tarar</td>".                    //трафарет
                    "<td>$tara</td>".                     //тара фактическая
					"<td>$front_tara1</td>".              //ось1 тары
					"<td>$front_tara2</td>".              //ось2 тары
                    "<td>$nettow</td>".                   //нетто (по результату контрольного взвешивания)
                    "<td>$brutto</td>".                   //брутто (по результату контрольного взвешивания)
                    //"<td>$brutto_d</td>".               //брутто (поcле дозирования)
                    "<td>$gu1</td>".                      //ось1 брутто
                    "<td>$gu2</td>".                      //ось2 брутто
                    "<td>$disbalans</td>".                //дисбаланс
                    "<td>$product</td>".                  //продукт
                    "<td>$tabn</td>".                     //таб. №
                    "<td>$smena_liter".                   //литер смены
                    "</tr>";
            }
            mysqli_free_result($result);
          ?>
        </tbody>        
      </table>
    </p>
    <?php
      $query_product="SELECT product,SUM(brutto_w-taraf) AS sum_product ". $part_qwery . " GROUP BY product";
    ?>
    <div class="txt-centered">
      <p>Выбрано записей: <?=$ct ?></p>
      <!-- <table class='tabledata'> -->
      <table class="tabledata">
        <?php 
          $tabletitle=$in_kg ? "Сумма,кг" : "Сумма,т";
        ?>
        <thead>
          <tr><th>Продукт</th><th><?= $tabletitle ?></th></tr>
        </thead>
        <tbody>
          <?php
            $result=mysqli_query($mysqli,$query_product);
            while($row=mysqli_fetch_assoc($result))
            {
              $sum_product = $in_kg ? sprintf("%.0f",1000*$row['sum_product']) : sprintf("%.2f",$row['sum_product']);
              echo  "<tr>" .
                      "<td>{$row['product']}</td>" . 
                      "<td>" . $sum_product . "</td>" .
                    "</tr>";
            }
            mysqli_free_result($result);
          ?>
        </tbody>
      </table>
    </div>
  </body>
</html>
