<!DOCTYPE html>
<html>
  <head>
    <meta http-equiv=Content-Type content="text/html; charset=utf-8">
    <title>LOG</title>
    <link rel="stylesheet" type="text/css" href="style.css">
    <meta http-equiv="Cache-Control" content="no-cache">
  </head>
  <body>
    <!-- <h2 align=center>Название весов</h2> -->
    <p><a href="#" onclick="history.back();">Назад</a></p> 

    <h4 align=center>Протокол взвешивания автотранспорта</h4>
    <table class="tframe" cellpadding=2 cellspacing=0 align=center border=1>
      <thead style="display:table-header-group">      
        <tr class="titlerow" bgcolor=#EFEBEF>
          <th>Операция</th> 
          <th>Дата,время</th>
          <th>Номер</th>
          <th>Тип</th>
          <th>Тара<br>(т)</th>
          <th>Брутто<br>(т)</th>
		  <th>Нетто<br>(т)</th>
          <th>Продукт</th>
          <th>Водитель</th>
          <th>Цех</th>
          <th>Комментарий</th>
        </tr>
      </thead>
      <tbody>
        <?php
          $id=$_GET["id"];
          require("config.inc.php");
          $connect=odbc_connect($ODBC_DSN,$ODBC_USER,$ODBC_PASSWORD); 
          $query="SELECT log FROM temp WHERE id=$id";
          $res=odbc_exec($connect,$query);
          if(odbc_fetch_row($res)) {
            $log=odbc_result($res,1);
            $arrstr = explode("$", $log);
            foreach($arrstr as $elem) {
              if(strlen($elem)>0) {
                print("<tr>");
                $arr = explode(",", $elem);
                print("<td>"); //операция
                $optype=$arr[2][0];
                switch($optype) {
                  case 'i':
                    print('ввод информации');
                    break;
                  case 't':
                    print($arr[2][2]=='*' ? 'сброс тары':'взвешивание тары');
                    break;
                  case 'b':
                    print($arr[2][2]=='*' ? 'сброс брутто':'взвешивание брутто');
                    break;
                  default:
                    print("???");
                }
                print("</td>");
                print("<td>$arr[0]" . " " . "$arr[1]</td>");    //дата, время
                switch($optype) {
                  case 'i': //info
                    print("<td>".substr($arr[2],2)."</td>");    //номер
                    print("<td>$arr[4]</td>");                  //тип авт.
                    print("<td></td>");                         //тара
                    print("<td></td>");                         //брутто
					print("<td></td>");                         //нетто
                    print("<td>$arr[3]</td>");                  //продукт
                    print("<td>$arr[6]</td>");                  //водитель
                    print("<td>$arr[5]</td>");                  //цех
                    print("<td>$arr[7]</td>");                  //комментарий
                    break;
                  case 't': //tare
                    print("<td></td>");                         //номер
                    print("<td></td>");                         //тип авт.
                    if($arr[2][2]=='*')
                      print("<td>*****</td>");                  //тара,reset
                    else
					{
						print("<td>".substr($arr[2],2)."</td>");  //тара,вес
						$tara = floatval(substr($arr[2],2));
					}
					print("<td></td>");                         //брутто
					print("<td></td>");                         //нетто
                    print("<td></td>");                         //продукт
                    print("<td></td>");                         //водитель
                    print("<td></td>");                         //цех
                    print("<td></td>");                         //комментарий
                    break;
                  case 'b': //brutto
                    print("<td></td>");                         //номер
                    print("<td></td>");                         //тип авт.
                    print("<td></td>");                         //тара
                    if($arr[2][2]=='*')                         
                      print("<td>*****</td>");                  //брутто,reset
                    else
                    {
						print("<td>".substr($arr[2],2)."</td>");  //брутто,вес
						$brutto = floatval(substr($arr[2],2));
					}
				    print("<td></td>");                         //нетто
                    print("<td></td>");                         //продукт
                    print("<td></td>");                         //водитель
                    print("<td></td>");                         //цех
                    print("<td></td>");                         //комментарий
                    break;
                }
                print("</tr>");
              }
            }
          }
		  if(isset($tara) && isset($brutto))
		  {
			  $netto = $brutto - $tara;
				print("<tr>");
					print("<td>подсчет нетто</td>");
					print("<td>$arr[0]" . " " . "$arr[1]</td>");    //дата, время
					print("<td></td>");    						//номер
                    print("<td></td>");                			//тип авт.
                    print("<td></td>");                         //тара
                    print("<td></td>");                         //брутто
					print("<td>$netto</td>");                   //нетто
                    print("<td></td>");                  		//продукт
                    print("<td></td>");                  		//водитель
                    print("<td></td>");                  		//цех
                    print("<td></td>");                  		//комментарий
				print("</tr>");
		  }
        ?>  
      </tbody>
    </table>
  </body>
</html>
  
