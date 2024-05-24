<!DOCTYPE html>
<html>
  <head>
    <meta http-equiv=Content-Type content="text/html; charset=utf-8">
    <title>Результаты</title>
    <link rel="stylesheet" type="text/css" href="style.css">
    <meta http-equiv="Cache-Control" content="no-cache">
  </head>

  <body>
    <script src="../js/jquery.js"></script>
    <?php
      require("config.inc.php");
      $connect=odbc_connect($ODBC_DSN,$ODBC_USER,$ODBC_PASSWORD); 
      $query="SELECT * FROM temp ORDER BY id";
      //print $query; 
      $res=odbc_exec($connect,$query);
    ?>
    <h2 align=center>Название весов</h2>
    <h4 align=center>Рабочая таблица</h4>
    <p><a href="index.html">На главную</a></p> 
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
    <br>
    <p><table class="tframe" cellpadding=2 cellspacing=0 align=center border=1>
    <thead style="display:table-header-group">      
      <tr class="titlerow" bgcolor=#EFEBEF><th>№ п/п</th><th>Номер<br>автотранспорта</th><th>Тип<br>автотранспорта<th>Дата,время<br>тары</th><th>Дата,время<br>брутто</th><th>Тара<br>(т)</th><th>Брутто<br>(т)</th><th>Нетто<br>(т)</th><th>Груз</th><th>Водитель</th><th>Оператор</th><th>Цех</th><th>Пропуск</th><th>Комментарий</th><th>Т</th><th>Б</th><th>И</th></tr>
    </thead>
    <?php
      $num=0;
      while($d=odbc_fetch_row($res)) {
        print "<tr>";
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
        print "<td>$num<td><a href='log-work.php?id=$id'>$nauto<td>$tauto<td state=$attr_datetime_tare>$datetime_tare<td state=$attr_datetime_brutto>$datetime_brutto<td state=$attr_tare>$tare<td state=$attr_brutto>$brutto<td state=$attr_netto>$netto<td>$goods<td>$trucker<td>$operator<td>$department<td>$document<td>$comment<td stateButton=$attr_taraTableButton>Т<td stateButton=$attr_bruttoTableButton>Б<td stateButton=$attr_infoTableButton>И";
        print "</tr>";
      }
      print "</table>";
      odbc_free_result($res);
      odbc_close($connect);
    ?>
  </body>
</html>
