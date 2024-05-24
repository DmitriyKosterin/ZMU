<!DOCTYPE html>
<html lang="ru">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=8" />
    <title>Отчёт за месяц</title>
    <link rel="stylesheet" href="../../css/pure-min.css">
    <link rel="stylesheet" href="../../css/style.css">
  </head>
  <body>
    <script language="JavaScript">
        document.oncontextmenu=new Function("return false;");
    </script>

    <?php
      require("../config.inc.php");
      require("../func.php");

      $str_curdatetime=strftime("%d.%m.%Y / %H:%M:%S");
      $curdate=strftime("%Y.%m.%d");
      $year1=$_POST['year1'];
      $month1= substr($_POST['month1'], 0, 2);
      $month_name= substr($_POST['month1'], 3);

      $datefirst=$year1 . ".$month1" . ".01";                                //дата начала мес¤ца (первое число мес¤ца)
      $last_day_month=cal_days_in_month(CAL_GREGORIAN, $month1, $year1);     //число дней в мес¤це
      $datelast=$year1 . ".$month1" . ".$last_day_month";                    //дата конца мес¤ца  (последнее число мес¤ца)

      $mysqli = mysqli_connect($MYSQL_BASE_HOST,$MYSQL_USER_NAME,$MYSQL_USER_PASS, $MYSQL_BASE_NAME);
      if(mysqli_connect_errno()) {
        echo("<h3>ќшибка соединени¤ c базой данных</h3>");
        exit();
      }
      $query_prod1="SELECT product FROM $input_manual_table WHERE wdate>='$datefirst' AND wdate<='$datelast' GROUP BY product";
      $query_prod2="SELECT product FROM $wdata_table WHERE wdate_w>='$datefirst' AND wdate_w<='$datelast' GROUP BY product";

      $list_products1=[];
      $list_products2=[];
      $list_products=[];

      $result1=mysqli_query($mysqli,$query_prod1);
      $num_products1=mysqli_num_rows($result1);
      while($row=mysqli_fetch_row($result1))
        $list_products1[]=$row[0];
      mysqli_free_result($result1);

      $result2=mysqli_query($mysqli,$query_prod2);
      $num_products2=mysqli_num_rows($result2);
      while($row=mysqli_fetch_row($result2))
        $list_products2[]=$row[0];
      mysqli_free_result($result2);

      $list_products=array_unique(array_merge($list_products1,$list_products2));
      $num_products=count($list_products);

      //array create 
      $days_prod_array=[];
      for($day=1;$day<=$last_day_month;$day++) {
        $sday= $day<10 ? "0".$day : $day;
        $str_day_date=$year1 . ".$month1" . ".$sday";
        foreach ($list_products as $product) {
          $query =  "SELECT SUM(sum_weight) FROM $input_manual_table WHERE " .  //bags (day)
                    "product='$product' AND wdate='$str_day_date'";
          $result=mysqli_query($mysqli,$query);
          $row=mysqli_fetch_row($result);
          $bags_day=$row[0] ? $row[0]:0.0;
          mysqli_free_result($result);

          $query =  "SELECT SUM(sum_weight) FROM $input_manual_table WHERE " .  //bags (month)
                    "product='$product' AND wdate>='$datefirst' AND wdate<='$str_day_date'";
          $result=mysqli_query($mysqli,$query);
          $row=mysqli_fetch_row($result);
          $bags_month=$row[0] ? $row[0]:0.0;
          mysqli_free_result($result);

          $query =  "SELECT SUM(brutto_w-taraf) FROM $wdata_table WHERE " .    //bulk (day)
                    "product='$product' AND wdate_w='$str_day_date'";
          $result=mysqli_query($mysqli,$query);
          $row=mysqli_fetch_row($result);
          $bulk_day=$row[0] ? $row[0]:0.0;
          mysqli_free_result($result);

          $query =  "SELECT SUM(brutto_w-taraf) FROM $wdata_table WHERE " .    //bulk (month)
                    "product='$product' AND wdate_w>='$datefirst' AND wdate_w<='$str_day_date'";
          $result=mysqli_query($mysqli,$query);
          $row=mysqli_fetch_row($result);
          $bulk_month=$row[0] ? $row[0]:0.0;
          mysqli_free_result($result);

          $days_prod_array[$day][$product]=array(
            'bags'=> [ //bags
                      'weight_day'=>$bags_day,      //day
                      'weight_month'=>$bags_month   //month
                    ],
            'bulk'=>[ //bulk
                      'weight_day'=>$bulk_day,      //day
                      'weight_month'=>$bulk_month   //month
                    ]
          );
        }
        if(!strcmp($str_day_date, $curdate))
          break;
      }
      //debug ($days_prod_array);
    ?>
    <div>
      <h3>Отчёт по отгрузке за <?="$month_name" . " $year1 г."?> </h3>
      <?= "<h4>ƒата/врем¤ формировани¤ отчЄта: " . "$str_curdatetime</h4>" ?>
    </div>

    <?php if(count($days_prod_array)==0): ?>
      <p>
        <h3><?= "--- Нет данных ---" ?></h3>
      </p>
      <?php exit(); ?>
    <?php endif; ?>

    <p>
      <table class="tabledata">
        <thead style=display:table-header-group>
          <tr>
            <th rowspan=2>Число</th>
            <th rowspan=2>Вид<br>отгрузки</th>
            <?php foreach($days_prod_array[1] as $product => $product_data): ?>
              <th colspan=2><?= $product ?></th>
            <?php endforeach ?>
          </tr>
          <tr>
            <?php for($i=0; $i<$num_products; $i++): ?> 
              <th>за cутки</th><th>с начала<br>месяца</th>
            <?php endfor ?>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($days_prod_array as $day => $day_data): ?>
            <tr>
              <td rowspan=2><?= $day ?></td>
              <td>мешки</td>
              <?php foreach ($day_data as $product_data): ?>
                <?php 
                  $bags_weight_day=number_format($product_data['bags']['weight_day'],2,'.','');
                  $bags_weight_month=number_format($product_data['bags']['weight_month'],2,'.','');
                ?>
                <td><?= $bags_weight_day ?></td>
                <td><?= $bags_weight_month ?></td>
              <?php endforeach ?>
            </tr>
            <tr>
              <td>насыпь</td>
              <?php foreach ($day_data as $product => $product_data): ?>
                <?php 
                  $bulk_weight_day=number_format($product_data['bulk']['weight_day'],2,'.','');
                  $bulk_weight_month=number_format($product_data['bulk']['weight_month'],2,'.','');
                ?>
                <td><?= $bulk_weight_day ?></td>
                <td><?= $bulk_weight_month ?></td>
              <?php endforeach ?>
            </tr>
          <?php endforeach ?>
        </tbody>  
      </table>
    </p>
  </body>
</html>
