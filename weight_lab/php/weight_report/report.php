<!DOCTYPE html>
<html lang="ru">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=8" />
    <title>Взвешивание на лабораторных весах</title>
    <link rel="stylesheet" href="../../css/pure-min.css">
    <link rel="stylesheet" href="../../css/style.css">
  </head>
  <body>
    <?php require("../config.inc.php"); ?>

    <script language="JavaScript">
        document.oncontextmenu=new Function("return false;");
    </script>

    <?php
      $smenas=[];
      $str_smenas='';
      $smenaselect='';
      $year1=$_POST['year1'];
      $month1= substr($_POST['month1'], 0, 2);
      $day1=$_POST['day1'];
      $year2=$_POST['year2'];
      $month2= substr($_POST['month2'], 0, 2);
      $day2=$_POST['day2'];

      $date_time_begin=$year1.$month1.$day1."000000";
      $date_time_end=$year2.$month2.$day2."235959";
      $str_datebegin="$day1" . ".$month1" . ".$year1";
      $str_dateend="$day2" . ".$month2" . ".$year2";
      if(isset($_POST['approximation']))
        $approximation=$_POST['approximation'];
      else
        $approximation=false;

      $query="SELECT * FROM $weight_table  WHERE ";
      $query.="(date_time_brutto >= '$date_time_begin' AND date_time_tare <= '$date_time_end') ";
      $query.="ORDER BY date_time_brutto";
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
      <h3>Взвешивание на лабораторных весах</h3>
      <h4>Дата/время формирования отчёта: <?= $cur_date_time ?></h4>
      <h5>Начальная дата: <?= $str_datebegin ?></h5>
      <h5>Конечная дата:  <?= $str_dateend  ?></h5>
    </div>

    <p>
      <table class="tabledata">
        <thead style="display:table-header-group">      
          <tr>
            <th>Дата,время<br>тары</th>
            <th>Дата,время<br>брутто</th>
            <th>Тара</th>
            <th>Брутто</th>
            <th>Нетто</th>
            <th>Продукт</th>
            <th>Оператор</th>
            <th>Документ</th>
            <th>Комментарий</th>
          </tr>
        </thead>
        <tbody>
          <?php
            $ct=0;
            while($row=mysqli_fetch_assoc($result))
            {
              $datetime_tare = strtotime($row["date_time_tare"]); 
              $day   = date('d',$datetime_tare); 
              $month = date('m',$datetime_tare); 
              $year  = date('y',$datetime_tare); 
              $hour  = date('H',$datetime_tare);
              $min  = date( 'i',$datetime_tare);
              $sec  = date( 's',$datetime_tare);
              $sdatetime_tare="$day.$month.$year $hour:$min:$sec";

              $datetime_brutto = strtotime($row["date_time_brutto"]); 
              $day   = date('d',$datetime_brutto); 
              $month = date('m',$datetime_brutto); 
              $year  = date('y',$datetime_brutto); 
              $hour  = date('H',$datetime_brutto);
              $min  = date( 'i',$datetime_brutto);
              $sec  = date( 's',$datetime_brutto);
              $sdatetime_brutto="$day.$month.$year $hour:$min:$sec";

              if(!$approximation)
							{
              	$tare=sprintf("%.2f",$row['tare']);
              	$brutto=sprintf("%.2f",$row['brutto']);
              	$delta=sprintf("%.2f",$row['delta']);
              }
              else
              {
                $tare=sprintf("%.1f",  round($row['tare'],1));
                $brutto=sprintf("%.1f",round($row['brutto'],1));
                $delta=sprintf("%.1f",$brutto-$tare);
              }
              $goods=$row['goods'];
              $operator=$row['operator'];
              $document=$row['document'];
              $comment=$row['comment'];

              $ct++;
              echo  "<tr>".
                      "<td>$sdatetime_tare</td>".   //дата/время тары:   08.03.18 11:34:57
                      "<td>$sdatetime_brutto</td>". //дата/время брутто: 08.03.18 11:35:09
                      "<td>$tare</td>".             //тара
                      "<td>$brutto</td>".           //брутто
                      "<td>$delta</td>".            //брутто-тара
                      "<td>$goods</td>".            //продукт
                      "<td>$operator</td>".         //оператор
                      "<td>$document</td>".         //документ
                      "<td>$comment</td>".          //комментарий
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
