<!DOCTYPE html>
<html lang="ru">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=8" />
    <title>Взвешивание вагонов</title>
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

      // $query="SELECT * FROM $weight_table  WHERE ";
      // if($vagon!='')
      //   $query.="vagon='$vagon' AND ";
      // $query.=$smenaselect . "AND ";
      // $query.="wdate >= '$datebegin' AND wdate<='$dateend' ";
      // $query.="ORDER BY wdate,wtime";

      $query="SELECT * FROM $weight_table  WHERE ";
      if($vagon!='')
        $query.="vagon='$vagon' AND ";
      $query.=$smenaselect . "AND (";
      if($datebegin!=$dateend)
      {
        $query.="(wdate = '$datebegin' AND wtime >= '$timebegin') OR ";
        $query.="(wdate = '$dateend' AND wtime <= '$timeend') OR ";
        $query.="(wdate > '$datebegin' AND wdate <'$dateend'))";
      }
      else
        $query.="(wdate = '$datebegin' AND wtime >= '$timebegin' AND wtime<='$timeend'))";
      $query.="ORDER BY wdate,wtime";
      //echo $query;
      $mysqli = mysqli_connect($MYSQL_BASE_HOST,$MYSQL_USER_NAME,$MYSQL_USER_PASS, $MYSQL_BASE_NAME);
      if(mysqli_connect_errno()) {
        echo("Ошибка соединения c базой данных" . "<br>");
        exit();
      }
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
      <h3>Взвешивание вагонов</h3>
      <h4>Дата/время формирования отчёта: <?= $cur_date_time ?></h4>
      <h5>Начальная дата/время: <?= $str_datebegin . ' / ' .$time1 ?></h5>
      <h5>Конечная дата/время:  <?= $str_dateend . ' / ' .$time2 ?></h5>
      <h5><?="Смена: $str_smenas"?></h5>
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
            <th>Время</th>
            <th>№ вагона</th>
			<th>Тара,т</th>
            <th>Ось1(Тара)</th>
            <th>Ось2(Тара)</th>
            <th>Брутто,т</th>
            <th>Ось1(Брутто)</th>
            <th>Ось2(Брутто)</th>
            <th>Дисбаланс</th>
            <th>Номер весов</th>
            <th>Таб. №</th>
            <th>Смена</th>
          </tr>
        </thead>
        <tbody>
          <?php
            $ct=0;
            while($row=mysqli_fetch_assoc($result))
            {
              $date=explode("-",$row['wdate']); //дата: 2017-10-30
              $year=substr($date[0],2);         //последние 2 цифры года: 17
              $wtime=$row['wtime'];
              $vagon=$row['vagon'];
              $brutto=sprintf("%.2f",$row['brutto']);
              $car1=sprintf("%.2f",$row['brutto_car1']);
			  $front_tara=sprintf("%.2f",$row['front_tara']);
			  $tara=sprintf("%.2f",$row['tara']);
              $nweight=$row['nweight']+1;
              $tabn=$row['tabn'];
              $smena_code=$row['smena'];
              $gu1=$gu2=$front_tara1=$front_tara2=0.0;
              if($car1)
              {
                $disbalans=sprintf("%.2f",2*$car1-$brutto);
                $gu1=sprintf("%.2f",$car1);
                $gu2=sprintf("%.2f",$brutto-$car1);
              }
              else
                $disbalans="";
			  if($front_tara > 0)
			  {
				$front_tara1= sprintf("%.2f",$front_tara);
				$front_tara2= sprintf("%.2f",$tara-$front_tara);
			  }
              $smena_liter= ($smena_code==1)? 'A': (($smena_code==2)? 'Б':(($smena_code==3)? 'В':(($smena_code==4)? 'Г':'')));
              $ct++;
              echo  "<tr>".
                      "<td>$date[2].$date[1].$year</td>".   //дата: 30.10.17
                      "<td>$wtime</td>".                    //время
                      "<td>$vagon</td>".                    //вагон
					  "<td>$tara</td>".                   	//тара
                      "<td>$front_tara1</td>".           	//ось1 тары
                      "<td>$front_tara2</td>".        		//ось2 тары
                      "<td>$brutto</td>".                   //брутто
                      "<td>$gu1</td>".                      //ось1
                      "<td>$gu2</td>".                      //ось2
                      "<td>$disbalans</td>".                //дисбаланс
                      "<td>$nweight</td>".                  //номер весов
                      "<td>$tabn</td>".                     //таб. №
                      "<td>$smena_liter".                   //литер смены
                    "</tr>";
            }
            mysqli_free_result($result);
          ?>
        </tbody>        
      </table>
      <p align=center>Выбрано записей: <?=$ct ?></p>
    <p>
  </body>
</html>
