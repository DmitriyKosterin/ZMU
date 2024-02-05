<!DOCTYPE html>
<html lang="ru">
  <head>
    <meta charset="windows-1251">
    <meta http-equiv="X-UA-Compatible" content="IE=8" />
    <title>ЋтчЮт сменный</title>
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

      $datefirst=$year1.".".$month1.".01";                    //дата начала месяца (1-е число месяца)
      $dateselect=$year1.".".$month1.".$day1";                //дата для выборки данных (из формы)
      $timenext=mktime(1,0,0,$month1,$day1,$year1)+3600*24;   //метка времени для 01:00:00 следующих суток
      $datenext=date("Y.m.d",$timenext);                      //дата, следующая за $dateselect
      $smena0_start_time= $smena_start_0.":00:00";            //8:00:00
      $smena0_end_time = $smena_end_0.":00:00";               //20:00:00
      $smena1_start_time = $smena_start_1.":00:00";           //20:00:00
      $smena1_end_time="$smena_end_1:00:00";                  //8:00:00

      $mysqli = mysqli_connect($MYSQL_BASE_HOST,$MYSQL_USER_NAME,$MYSQL_USER_PASS, $MYSQL_BASE_NAME);
      if(mysqli_connect_errno()) {
        echo("<h3>Ошибка соединения c базой данных</h3>");
        exit();
      }
      $query1="SELECT product FROM $input_manual_table WHERE " .
              "(wdate='$dateselect' AND wtime>='$smena0_start_time') OR " .
              "(wdate='$datenext' AND wtime < '$smena0_start_time') GROUP BY product";
      $query2="SELECT product FROM $wdata_table WHERE " .
              "(wdate_w='$dateselect' AND wtime_w>='$smena0_start_time') OR " .
              "(wdate_w='$datenext' AND wtime_w < '$smena0_start_time') GROUP BY product";

      //echo $query1 . '<br>';
      //echo $query2 . '<br>';

      $list_products1=[];
      $list_products2=[];
      $list_products=[];

      $result1=mysqli_query($mysqli,$query1);
      $num_products1=mysqli_num_rows($result1);
      while($d=mysqli_fetch_row($result1))
        $list_products1[]=$d[0];
      mysqli_free_result($result1);

      $result2=mysqli_query($mysqli,$query2);
      $num_products2=mysqli_num_rows($result2);
      while($d=mysqli_fetch_row($result2))
        $list_products2[]=$d[0];
      mysqli_free_result($result2);
      $list_products=array_unique(array_merge($list_products1,$list_products2));
      $num_products=count($list_products);
      //array create
      $prod_array=[];
      foreach ($list_products as $product) {
        $prod_array[$product]=array(
          'bags'=>[     //bags
                    'weight_shift0'=>0.0,   //8-20
                    'weight_shift1'=>0.0    //20-8
                  ],
          'bulk'=>[     //bulk
                    'weight_shift0'=>0.0,   //8-20
                    'weight_shift1'=>0.0    //20-8
                  ]
        );
        $query =  "SELECT SUM(sum_weight) FROM $input_manual_table WHERE " .  //bags, 8-20
                  "product='$product' AND " .
                  "wdate='$dateselect' AND " .
                  "wtime>='$smena0_start_time' AND " .
                  "wtime<'$smena0_end_time'";
        $result=mysqli_query($mysqli,$query);
        $row=mysqli_fetch_row($result);
        //echo $query.'<br>';
        //echo("shift0=".$row[0])."<br>";
        $prod_array[$product]['bags']['weight_shift0']=$row[0] ? $row[0]:0.0;
        mysqli_free_result($result);

        $query =  "SELECT SUM(sum_weight) FROM $input_manual_table WHERE " .   //bags, 20-8
                  "product='$product' AND " .
                  "( (wdate='$dateselect' AND wtime>='$smena1_start_time' AND wtime<='23:59:59') OR " .
                  "  (wdate='$datenext'   AND wtime>='00:00:00' AND wtime<'$smena1_end_time') " .
                  ")";  
        $result=mysqli_query($mysqli,$query);
        $row=mysqli_fetch_row($result);
        //echo $query.'<br>';
        //echo("shift1=".$row[0])."<br>";
        $prod_array[$product]['bags']['weight_shift1']=$row[0] ? $row[0]:0.0;
        mysqli_free_result($result);

        $query =  "SELECT SUM(brutto_w-taraf) FROM $wdata_table WHERE " .  //bulk, 8-20
                  "product='$product' AND " .
                  "wdate_w='$dateselect' AND " .
                  "wtime_w>='$smena0_start_time' AND " .
                  "wtime_w<'$smena0_end_time'";
        $result=mysqli_query($mysqli,$query);
        $row=mysqli_fetch_row($result);
        //echo $query.'<br>';
        //echo("shift0=".$row[0])."<br>";
        $prod_array[$product]['bulk']['weight_shift0']=$row[0] ? $row[0]:0.0;
        mysqli_free_result($result);

        $query =  "SELECT SUM(brutto_w-taraf) FROM $wdata_table WHERE " .   //bulk, 20-8
                  "product='$product' AND " .
                  "( (wdate_w='$dateselect' AND wtime_w>='$smena1_start_time' AND wtime_w<='23:59:59') OR " .
                  "  (wdate_w='$datenext'   AND wtime_w>='00:00:00' AND wtime_w<'$smena1_end_time') " .
                  ")";  
        $result=mysqli_query($mysqli,$query);
        $row=mysqli_fetch_row($result);
        //echo $query.'<br>';
        //echo("shift1=".$row[0])."<br>";
        $prod_array[$product]['bulk']['weight_shift1']=$row[0] ? $row[0]:0.0;
        mysqli_free_result($result);
      }
      //debug($prod_array);
    ?>
    <div>
      <h3>ЋтчЮт сменный по отгрузке за <?="$day1" . ".$month1" . ".$year1"?> </h3>
      <?= "<h4>Дата/время формирования отчёта: " . "$cur_date_time</h4>" ?>
    </div>

    <p>
      <!-- <table class="pure-table pure-table-bordered tabledata"> -->
        <table class="tabledata">
        <thead style=display:table-header-group>
          <tr>
           <th>Џродукт</th>
           <th>‚ид отгрузки</th>
           <th>‘мена</th>
           <th>‘уммарный вес, т</th>
          </tr>
        </thead>
        <tbody>
          <?php 
            if($num_products > 0) {
              foreach ($list_products as $product) { 
                $bags_shift0=number_format($prod_array[$product]['bags']['weight_shift0'],2,'.','');
                $bags_shift1=number_format($prod_array[$product]['bags']['weight_shift1'],2,'.','');
                $bulk_shift0=number_format($prod_array[$product]['bulk']['weight_shift0'],2,'.','');
                $bulk_shift1=number_format($prod_array[$product]['bulk']['weight_shift1'],2,'.','');
                echo "<tr>" .
                        "<td rowspan=4> $product </td>".
                        "<td rowspan=2>мешки и биг-беги</td>".
                        "<td>8-20</td>".
                        "<td>$bags_shift0</td>".
                      "</tr>";
                echo "<tr>" .
                        "<td>20-8</td>".
                        "<td>$bags_shift1</td>".
                      "</tr>";
                echo "<tr>" .
                        "<td rowspan=2>насыпь</td>".
                        "<td>8-20</td>".
                        "<td>$bulk_shift0</td>".
                      "</tr>";
                echo "<tr>" .
                        "<td>20-8</td>".
                        "<td>$bulk_shift1</td>".
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

