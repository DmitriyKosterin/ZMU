<?
function shipment_normal($year1,$month1,$day1,$year2,$month2,$day2,$train,$vagon)
{
  require("config.inc.php");
  
  $datebegin=$year1.".".$month1.".".$day1;
  $dateend=$year2.".".$month2.".".$day2;
  $dbegin=$day1.".".$month1.".".$year1;
  $dend=$day2.".".$month2.".".$year2;
  $strain=trim($train);
  $svagon=trim($vagon);
  echo "<br>";
  echo "<h3>Данные по отгрузке</h3>";
  echo "<h5>Начальная дата: $dbegin</h5>";
  echo "<h5>Конечная дата: $dend</h5>";
  if($strain!='')
	   echo "<h5>Состав: $strain</h5>";
  if($svagon!='')
	  echo "<h5>№ МПС: $svagon</h5>";

	$query = "SELECT w1.number,w1.brutto1,w1.weighing_mode,w1.speed,w1.status_weight,w1.sostav,w2.tare2,w2.is_manual_tare,
          DATE_FORMAT(w1.date_time,'%d-%m-%y'),DATE_FORMAT(w1.date_time,'%T') 
          FROM db_name.w01_raw_weight w1,db_name.w01_raw_tare w2
          WHERE DATE_FORMAT(w1.date_time,'%Y.%m.%d') >= '$datebegin' AND 
          DATE_FORMAT(w1.date_time,'%Y.%m.%d') <= '$dateend' AND 
          w1.id_tare=w2.id AND 
          w1.status_weight=0 ";

   if($strain!='')
        $query.=" AND w1.sostav=$strain ";
   if($svagon!='')
        $query.=" AND w1.number='$svagon' ";
	
	$query .= "union SELECT w1.number,w1.brutto1,w1.weighing_mode,w1.speed,w1.status_weight,w1.sostav,w2.tare2,w2.is_manual_tare,
          DATE_FORMAT(w1.date_time,'%d-%m-%y'),DATE_FORMAT(w1.date_time,'%T') 
          FROM db_name_archive.w01_raw_weight w1,db_name.w01_raw_tare w2
          WHERE DATE_FORMAT(w1.date_time,'%Y.%m.%d') >= '$datebegin' AND 
          DATE_FORMAT(w1.date_time,'%Y.%m.%d') <= '$dateend' AND 
          w1.id_tare=w2.id AND 
          w1.status_weight=0 ";

   if($strain!='')
        $query.=" AND w1.sostav=$strain ";
   if($svagon!='')
        $query.=" AND w1.number='$svagon' ";
	
   $query.=" ORDER BY 9,10";

   //echo $query;

  mysql_connect($MYSQL_BASE_HOST,$MYSQL_USER_NAME,$MYSQL_USER_PASS)
                or die("Could not connect to MySQL server!!!");
  //mysql_select_db($MYSQL_BASE_NAME)
  //              or die("Could not select database!");

  $query_count="SELECT COUNT(*) ".substr($query,212);

  //echo $query_count;

  $result_count=mysql_query($query_count);
  $ss=mysql_fetch_row($result_count);
  if($ss)
  {
    if($ss[0]>1000)
    {
      echo "<p align=center>Полученные данные содержат $ss[0] строк. Допускается формировать отчеты, содержащие не более 1000 строк.</p>";
      return;
    }
  }
  $result=mysql_query($query);
  echo '<p><table class="pure-table pure-table-bordered" cellpadding=8 cellspacing=2 align=center border=1>';
  echo '<thead style="background-color: #00945D; color: #ffffff;">';
  echo '<tr><th>№<th>Дата<th>Время<th>Состав<th>№ МПС<th>Тара,т<th>Брутто,т<th>Нетто,т<th>Режим<th>Скорость</tr>';
  echo '</thead>';
  echo '<tbody>';
  $i=1;
  $sum_netto=0;
  while($d=mysql_fetch_row($result))
  {
    echo "<tr>";
    $din_stat= ($d[2]==1 || $d[2]==3 || $d[2]==5) ? "стат":"дин";
    $tare=sprintf("%.3f",$d[6]);
    $brutto=sprintf("%.3f",$d[1]);
    $netto=sprintf("%.3f",$brutto-$tare);
    $sum_netto+=$netto;
    $speed=sprintf("%.1f",$d[3]);
    $str_is_manual=$d[7] ? "manual_tare":"weight_tare";
    echo "<td>$i<td>$d[8]<td>$d[9]<td>$d[5]<td class=$str_is_manual>$d[0]<td>$tare<td>$brutto<td>$netto<td>$din_stat<td>$speed";
    echo "</tr>";
    $i++;
  };
  echo "<tr>";
  echo "<td colspan=3><b>Всего отгружено</b><td colspan=4><td><b>$sum_netto</b><td colspan=2>&nbsp";
  echo "</tr>";
  echo"</tbody>";
  echo"</table>";
  //echo"<p align=center>Примечание. Красным цветом отмечены номера вагонов с ручным вводом веса тары.";
}
?>

