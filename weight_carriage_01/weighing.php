<?
function weighing($year1,$month1,$day1,$year2,$month2,$day2,$train,$vagon)
{
  require("config.inc.php");

  $datebegin=$year1.".".$month1.".".$day1;
  $dateend=$year2.".".$month2.".".$day2;
  $dbegin=$day1.".".$month1.".".$year1;
  $dend=$day2.".".$month2.".".$year2;
  $strain=trim($train);
  $svagon=trim($vagon);
  echo "<br>";
  echo "<h3>Данные по взвешиванию</h3>";
  echo "<h5>Начальная дата: $dbegin</h5>";
  echo "<h5>Конечная дата: $dend</h5>";
  if($strain!='')
	   echo "<h5>Состав: $strain</h5>";
  if($svagon!='')
	  echo "<h5>№ МПС: $svagon</h5>";

  $query= "SELECT number,brutto1,weighing_mode,speed,status_weight,sostav, ". 
          "DATE_FORMAT(date_time,'%d-%m-%y'),DATE_FORMAT(date_time,'%T') ".
          "FROM w01_raw_weight ".
          "WHERE DATE_FORMAT(date_time,'%Y.%m.%d') >= '$datebegin' AND ".
          "DATE_FORMAT(date_time,'%Y.%m.%d') <= '$dateend' AND ".
          "status_weight=1 ";
   if($strain!='')
        $query.=" AND sostav=$strain ";
   if($svagon!='')
        $query.=" AND number='$svagon' ";
   $query.=" ORDER BY date_time";

  //echo $query;

  mysql_connect($MYSQL_BASE_HOST,$MYSQL_USER_NAME,$MYSQL_USER_PASS)
                or die("Could not connect to MySQL server!!!");
  mysql_select_db($MYSQL_BASE_NAME)
                or die("Could not select database!");

  $query_count="SELECT COUNT(*) ".substr($query,126);
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

  //echo  $query;

  $result=mysql_query($query);

  echo '<p><table class="pure-table pure-table-bordered" cellpadding=8 cellspacing=2 align=center border=1>';
  echo '<thead style="background-color: #00945D; color: #ffffff; border: 1px solid #D3D3D3;">';
  echo '<tr class="titlerow_white"><th>№<th>Дата<th>Время<th>Состав<th>№ МПС<th>Вес,т<th>Режим<th>Скорость</tr>';
  echo '</thead>';
  echo '<tbody>';
  
  $i=1;
  while($d=mysql_fetch_row($result))
  {
    echo "<tr>";
    $dinstat= ($d[2]==1 || $d[2]==3 || $d[2]==5) ? "стат":"дин";
    $weight=sprintf("%.3f",$d[1]);
    $speed=sprintf("%.1f",$d[3]);
    echo "<td>$i<td>$d[6]<td>$d[7]<td>$d[5]<td>$d[0]<td>$weight<td>$dinstat<td>$speed";
    echo "</tr>";
    $i++;
  };


  echo"</tbody>";
  echo"</table>";
}
?>

