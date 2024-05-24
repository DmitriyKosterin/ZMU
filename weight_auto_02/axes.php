<!DOCTYPE html>
<html>
  <head>
    <meta http-equiv=Content-Type content="text/html; charset=utf-8">
    <title>LOG</title>
    <link rel="stylesheet" type="text/css" href="style.css">
    <meta http-equiv="Cache-Control" content="no-cache">
  </head>
  <body>
    <a href="#" onclick="history.back();">Назад</a>
    <?php
      $tablename=$_GET["tablename"];
      $id=$_GET["id"];
      require("config.php");
      $connect=odbc_connect($ODBC_DSN,$ODBC_USER,$ODBC_PASSWORD); 
      $res=0;
      $query="SELECT ntransport, goods, date_time_brutto, axis FROM `$tablename` WHERE id=$id";
      $res=odbc_exec($connect,$query);
      if(odbc_fetch_row($res)) {
        $axes=odbc_result($res,"axis");
        $axis_arr = explode(";", $axes);
        $ntransport=odbc_result($res,"ntransport");
        $goods=odbc_result($res,"goods");
        $datetime_brutto=odbc_result($res,"date_time_brutto");
      }
      odbc_close($connect);
      $weight_dynamic=$n_axis="";
      if(count($axis_arr)>=3) {
        $weight_dynamic=$axis_arr[1];
        $weight_static=$axis_arr[0];
        $n_axis=$axis_arr[0];
        $sdatetime=explode(" ",$datetime_brutto);
        if($tablename!='temp') { //yyyy-mm-dd hh:mm:ss
          $sdate1=explode("-",$sdatetime[0]);
          $year1=substr($sdate1[0],2);   //последние 2 цифры года
        }
        else {  //dd.mm.yy hh:mm:ss
          $sdate1=explode(".",$sdatetime[0]);
          $year1=$sdate1[2];   //последние 2 цифры года
          $sdate1[2]=$sdate1[0];
        }
        $stime1=$sdatetime[1];
      }
    ?>
    <div id="axes_summary"> 
      <h4 align=left>Результаты взвешивания</h4>
      <ul>
        <li>№: <?=$ntransport?></li>
        <li>Груз: <?=$goods?></li>
        <li><?=$sdate1[2].'.'.$sdate1[1].'.'.$year1.' '.$stime1?></li>
        <!--<li>Вес статический(т): <?=$weight_static?></li>-->
        <li>Вес в движении(т): <?=$weight_dynamic?></li>
        <li>Число осей: <?=$n_axis?></li>
      </ul>
      <h5 align=left>Вес по осям(т):</h5>
      <table class="tframe t_axis" cellpadding=2 cellspacing=0 border=1>
        <tr class="tr_ax">
          <?php
            for($i=1;$i<=8;$i++)
              echo("<td class='nnn'>$i</td>");
          ?>  
        </tr> 
        <tr class="tr_ax">
          <?php
            for($i=0;$i<8;$i++) {
              $el=$i<$axis_arr[0] ? $axis_arr[$i+2]:"---";
              echo("<td>$el</td>");
            }
          ?>  
        </tr> 
      </table>
    </div>
  </body>
</html>
  
