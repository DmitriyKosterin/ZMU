<!DOCTYPE html>
<html lang="ru">
  <head>
    <meta charset="windows-1251">
    <meta http-equiv="X-UA-Compatible" content="IE=8" />
    <title>Мешки и биг-беги</title>
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
      $ntransport=$_POST['ntransport'];
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

      $query="SELECT * FROM $input_manual_table  WHERE ";
      if($ntransport!='')
        $query.="ntransport='$ntransport' AND ";
      $query.=$smenaselect . "AND (";
      if($datebegin!=$dateend)
      {
        $query.="(wdate = '$datebegin' AND wtime >= '$timebegin') OR ";
        $query.="(wdate = '$dateend' AND wtime <= '$timeend') OR ";
        $query.="(wdate > '$datebegin' AND wdate <'$dateend')) ";
      }
      else
        $query.="(wdate = '$datebegin' AND wtime >= '$timebegin' AND wtime <= '$timeend'))";
      $part_qwery=substr($query,9);
      $query.="ORDER BY wdate,wtime";
      //echo $query;
      $mysqli=mysqli_connect($MYSQL_BASE_HOST,$MYSQL_USER_NAME,$MYSQL_USER_PASS, $MYSQL_BASE_NAME);
      if(mysqli_connect_errno()) {
        echo("<h3>Ошибка соединения c базой данных</h3>");
        exit();
      }
      $result=mysqli_query($mysqli,$query);
      $result_count=mysqli_num_rows($result);
      if($result_count>1000)
      {
          echo "<p><h3>Полученные данные содержат $result_count записей. Допускается делать выборки, содержащие не более 1000 записей.</h3></p>";
          exit();
      }
      $cur_date_time=strftime("%d.%m.%Y / %H:%M:%S");
    ?>
    <div>
      <h3>Отгрузка в мешках и биг-бегах в ж/д вагоны и автотранспорт</h3>
      <h4>Дата/время формирования отчёта: <?= $cur_date_time ?></h4>
      <h5>Начальная дата/время: <?= $str_datebegin . ' / ' .$time1 ?></h5>
      <h5>Конечная дата/время:  <?= $str_dateend . ' / ' .$time2 ?></h5>
      <h5>Смена: <?=$str_smenas?></h5>
      <?php 
        if(strlen($ntransport)>0)
          echo "<h5>Номер: $ntransport</h5>";
      ?>
    </div>
    <p>
      <table class="tabledata">
        <thead style="display:table-header-group">      
          <tr>
            <th>Дата</th>
            <th>Время</th>
            <th>№ вагона или<br>автотранспорта</th>
            <th>Вес,т</th>
            <th>Количество мешков<br>или биг-бегов</th>
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
              $date=explode("-",$row['wdate']); //дата: 2017-10-30
              $year=substr($date[0],2);         //последние 2 цифры года: 17
              $wtime=$row['wtime'];
              $ntransport=$row['ntransport'];
              $sum_weight=sprintf("%.2f",$row['sum_weight']);
              $nbags=$row['nbags'];
              $product=$row['product'];
              $tabn=$row['tabn'];
              $smena_code=$row['smena'];
              $smena_liter= ($smena_code==1)? 'A': (($smena_code==2)? 'Б':(($smena_code==3)? 'В':(($smena_code==4)? 'Г':'')));
              $ct++;
              echo  "<tr>".
                      "<td>$date[2].$date[1].$year</td>".   //дата: 30.10.17
                      "<td>$wtime</td>".                    //время
                      "<td>$ntransport</td>".               //№ вагона или автотранспорта
                      "<td>$sum_weight</td>".               //вес отгруженной продукции,т
                      "<td>$nbags</td>".                    //количество мешков<br>или биг-бегов
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
      $query_product="SELECT product,SUM(sum_weight) AS sum_weight,SUM(nbags) AS sum_nbags ". $part_qwery . " GROUP BY product";
    ?>
    <div class="txt-centered">
      <p>Выбрано записей: <?=$ct ?></p><p>
      <table class="tabledata">
        <thead>
          <tr><th>Продукт</th><th>Суммарный вес,т</th><th>Количество</th></tr>
        </thead>
        <tbody>
          <?php
            $result=mysqli_query($mysqli,$query_product);
            while($row=mysqli_fetch_assoc($result))
            {
              echo  "<tr>" .
                      "<td>{$row['product']}</td>" . 
                      "<td>" . sprintf("%.2f",$row['sum_weight']) . "</td>" .
                      "<td>" . $row['sum_nbags'] . "</td>" .  
                    "</tr>";
            };
            mysqli_free_result($result);
          ?>
        </tbody>
      </table>
    </div>
  </body>
</html>
