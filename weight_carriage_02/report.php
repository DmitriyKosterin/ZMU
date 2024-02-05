<!DOCTYPE html>
<html lang="ru">
  <head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=8" />
    <title>Насыпь</title>
    <link rel="stylesheet" href="css/pure-min.css">
    <link rel="stylesheet" href="css/style.css">
  </head>
  <body>
    <?php require("config.inc.php"); ?>
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
      $vagon=$_POST['vagon'];
      $part=$_POST['part'];
      if(isset($_POST['smena1']))
        $smenas[]="'А'";
      if(isset($_POST['smena2']))
        $smenas[]="'Б'";
      if(isset($_POST['smena3']))
        $smenas[]="'В'";
      if(isset($_POST['smena4']))
        $smenas[]="'Г'";
      if(count($smenas)>0)
      {
        $smenaselect.='(';
        foreach ($smenas as $smena) {
          $smenaselect.="tour=$smena OR ";
          $str_smenas.= ($smena=='А')? 'A,': (($smena=='Б')? 'Б,':(($smena=='В')? 'В,':(($smena=='Г')? 'Г,':'')));
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

      $query="SELECT distinct party_number,invent_number,way,username,tour,prod_name,date_time_start,date_time_fin,traf_mass,tare_mass,tare_left_mass,tare_gu1_mass,net_mass,gross_mass,gross_left_mass,gu1_mass FROM $wdata_table  WHERE ";
      if($vagon!='')
        $query.="invent_number='$vagon' AND ";
      if($part!='')
        $query.="party_number='$part' AND ";

      //$query.=$smenaselect . "AND ";
      $query.="date(date_time_start) >= '$datebegin' AND date(date_time_start) <= '$dateend' ";
      $part_qwery=substr($query,9);
      //$query.="ORDER BY date_for_search";
        
      //echo $query;

      $mysqli=mysqli_connect($MYSQL_BASE_HOST,$MYSQL_USER_NAME,$MYSQL_USER_PASS, $MYSQL_BASE_NAME);
	  echo mysqli_connect_error();
      if(mysqli_connect_errno()) {
        echo("<h3>Ошибка соединения c базой данных</h3>");
        exit();
      }
      $result=mysqli_query($mysqli,$query);
	  //echo $result;
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
      <h4>Дата/время формирования отчёта: <?= $cur_date_time ?></h4>
      <h5><?="Смена: $str_smenas"?></h5>
      <h5><?="Начальная дата: $str_datebegin"?></h5>
      <h5><?="Конечная дата: $str_dateend"?></<h5>
      <?php 
        if(strlen($vagon)>0)
          echo "<h5>Вагон: $vagon</h5>";
        if(strlen($part)>0)
          echo "<h5>Партия: $part</h5>";
      ?>
    </div>

    <p>
      <table class="tabledata">
        <thead style="display:table-header-group">      
          <tr>
			<th>№</th>
			<th>Путь</th>
			<th>Партия</th>
            <th>Вагон</th>
            <th>Смена</th>
            <th>Оператор</th>
            <th>Дата/время взвешивания тары</th>
            <th>Дата/время взвешивания брутто</th>
			<th>Продукт</th>
            <th>Тара,т</th>
            <th>Трафарет,т</th>
            <th>Брутто,т</th>
            <th>Нетто,т</th>
            <th>ГУ1,т</th>
            <th>ГУ2,т</th>
            <th>Дисбаланс,т</th>
          </tr>
        </thead>
        <tbody>
          <?php
            $ct=0;
            $sum=0;
			//echo 'test1';
            while($row=mysqli_fetch_assoc($result))
            {
				//echo 'test';
				$way=$row['way'] == 1 ? 32 : 27;
				$vagon=$row['invent_number'] ? $row['invent_number']:'';
				$smena_code=$row['tour'];
				$username=$row['username'] ?  iconv("CP1251", "UTF-8", $row['username']):'';
				$party_number=$row['party_number'] ?  $row['party_number']:'';
				//$smena_liter= ($smena_code==1)? 'A': (($smena_code==2)? 'Б':(($smena_code==3)? 'В':(($smena_code==4)? 'Г':'')));
				$date_time_start=$row['date_time_start'] ? $row['date_time_start']:'';
				$date_time_fin=$row['date_time_fin'] ? $row['date_time_fin']:'';
				$prod_name=$row['prod_name'] ?  iconv("CP1251", "UTF-8", $row['prod_name']):'';
				$tare_mass=sprintf("%.2f",$row['tare_mass'] ? $row['tare_mass']:0);
				$traf_mass=sprintf("%.2f",$row['traf_mass'] ? $row['traf_mass']:0);
				$gross_mass=sprintf("%.2f",$row['gross_mass'] ? $row['gross_mass']:0);
				$net_mass=sprintf("%.2f",$row['net_mass'] ? $row['net_mass']:0);
				$gu1_mass=sprintf("%.2f",$row['gu1_mass'] ? $row['gu1_mass']:0);
				$gu2_mass=sprintf("%.2f",$gross_mass-$gu1_mass);
				$disbalans=sprintf("%.2f",$gu1_mass-$gu2_mass);
				$ct++;
				$sum+=$net_mass;
			  if($date_time_fin != '')
			  {
				  echo  "<tr>".
							"<td>$ct</td>".     			//номер строки
							"<td>$way</td>".     			//путь
							"<td>$party_number</td>".     	//партия
							"<td>$vagon</td>".            	//вагон
							"<td>$smena_code</td>".      	//смена
							"<td>$username</td>".     		//оператор
							"<td>$date_time_start</td>".  	//начало загрузки
							"<td>$date_time_fin</td>".    	//окончание загрузки
							"<td>$prod_name</td>".    		//продукт
							"<td>$tare_mass</td>".        	//тара факт
							"<td>$traf_mass</td>".        	//тара траф
							"<td>$gross_mass</td>".       	//брутто
							"<td>$net_mass</td>".         	//нетто
							"<td>$gu1_mass</td>".         	//ГУ1
							"<td>$gu2_mass</td>".         	//ГУ2
							"<td>$disbalans</td>".        	//дисбаланс
						"</tr>";
			  }
            }
            mysqli_free_result($result);
          ?>
        </tbody>        
      </table>
    </p>
    <div class="txt-centered">
      <p>Выбрано записей: <?=$ct ?></p>
      <table class="tabledata">
        <thead>
          <tr><th>Всего загружено, т</th></tr>
        </thead>
        <tbody>
          <?php 
            $sum_t=sprintf("%.2f",$sum);
          ?>
          <tr><td><?= $sum_t ?></td></tr>
        </tbody>
      </table>
    </div>
  </body>
</html>
