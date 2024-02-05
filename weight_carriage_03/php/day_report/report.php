<!DOCTYPE html>
<html lang="ru">
  <head>
    <meta charset="windows-1251">
    <meta http-equiv="X-UA-Compatible" content="IE=8" />
    <title>ЋтчЮт суточный</title>
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

      $cur_date_time=strftime("%d.%m.%Y / %H:%M:%S");
      $year1=$_POST['year1'];
      $month1= substr($_POST['month1'], 0, 2);
      $day1=$_POST['day1'];

      $datefirst=$year1 . ".$month1" . ".01";                    //дата начала месяца (1-е число месяца)
      $dateselect=$year1 . ".$month1" . ".$day1";                //дата для выборки данных (из формы)
      //$timenext=mktime(1,0,0,$month1,$day1,$year1)+3600*24;      //метка времени для 01:00:00 следующих суток
      //$datenext=date("Y.m.d",$timenext);                         //дата, следующая за $dateselect


      $mysqli = mysqli_connect($MYSQL_BASE_HOST,$MYSQL_USER_NAME,$MYSQL_USER_PASS, $MYSQL_BASE_NAME);
      if(mysqli_connect_errno()) {
        echo("<h3>Ошибка соединения c базой данных</h3>");
        exit();
      }
      $query_prod1="SELECT product FROM $input_manual_table WHERE wdate='$dateselect' GROUP BY product";
      $query_prod2="SELECT product FROM $wdata_table WHERE wdate_w='$dateselect' GROUP BY product";

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
      $prod_array=[];
      foreach ($list_products as $product) {
        $prod_array[$product]=array(
          'bags'=>[     //bags
                    'weight_day'=>0.0,    //day
                    'weight_month'=>0.0   //month
                  ],
          'bulk'=>[     //bulk
                    'weight_day'=>0.0,    //day
                    'weight_month'=>0.0   //month
                  ]
        );
        
        $query =  "SELECT SUM(sum_weight) FROM $input_manual_table WHERE " .  //bags (day)
                  "product='$product' AND wdate='$dateselect'";
        $result=mysqli_query($mysqli,$query);
        $row=mysqli_fetch_row($result);
        //echo $query.'<br>';
        $prod_array[$product]['bags']['weight_day']=$row[0] ? $row[0]:0.0;
        mysqli_free_result($result);

        $query =  "SELECT SUM(sum_weight) FROM $input_manual_table WHERE " .  //bags  (month)
                  "product='$product' AND wdate>='$datefirst' AND wdate<='$dateselect'";
        $result=mysqli_query($mysqli,$query);
        $row=mysqli_fetch_row($result);
        //echo $query.'<br>';
        $prod_array[$product]['bags']['weight_month']=$row[0] ? $row[0]:0.0;
        mysqli_free_result($result);

        $query =  "SELECT SUM(brutto_w-taraf) FROM $wdata_table WHERE " .    //bulk (day)
                  "product='$product' AND wdate_w='$dateselect'";
        $result=mysqli_query($mysqli,$query);
        $row=mysqli_fetch_row($result);
        //echo $query.'<br>';
        $prod_array[$product]['bulk']['weight_day']=$row[0] ? $row[0]:0.0;
        mysqli_free_result($result);

        $query =  "SELECT SUM(brutto_w-taraf) FROM $wdata_table WHERE " .    //bulk (month)
                  "product='$product' AND wdate_w>='$datefirst' AND wdate_w<='$dateselect'";
        $result=mysqli_query($mysqli,$query);
        $row=mysqli_fetch_row($result);
        //echo $query.'<br>';
        $prod_array[$product]['bulk']['weight_month']=$row[0] ? $row[0]:0.0;
        mysqli_free_result($result);
      }
      //debug($prod_array);
    ?>
    <div>
      <h3>Отчёт суточный по отгрузке за <?="$day1" . ".$month1" . ".$year1"?> </h3>
      <?= "<h4>Дата/время формирования отчёта: " . "$cur_date_time</h4>" ?>
    </div>

    <p>
      <table class="tabledata">
        <thead style=display:table-header-group>
          <tr>
           <th>Џродукт</th>
           <th>‚ид отгрузки</th>
           <th>‘умма за сутки, т</th>
           <th>‘умма с начала месЯца, т</th>
          </tr>
        </thead>
        <tbody>
          <?php 
            if($num_products > 0) {
              foreach ($list_products as $product) { 
                $bags_day=number_format($prod_array[$product]['bags']['weight_day'],2,'.','');
                $bags_month=number_format($prod_array[$product]['bags']['weight_month'],2,'.','');
                $bulk_day=number_format($prod_array[$product]['bulk']['weight_day'],2,'.','');
                $bulk_month=number_format($prod_array[$product]['bulk']['weight_month'],2,'.','');
                echo "<tr>" .
                        "<td rowspan=2> $product </td>".
                        "<td>мешки и биг-беги</td>".
                        "<td>$bags_day</td>".
                        "<td>$bags_month</td>".
                      "</tr>";
                echo "<tr>" .
                        "<td>насыпь</td>".
                        "<td>$bulk_day</td>".
                        "<td>$bulk_month</td>".
                      "</tr>";
              }
            }
            else
              echo "<tr><td align=center colspan=4>Нет данных за выбранную дату</td></tr>";
          ?>
        </tbody>  
      </table>
    </p>
  </body>
</html>

