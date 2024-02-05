<?
function check($year1,$month1,$day1,$year2,$month2,$day2,$train,$vagon)
{
  require("config.inc.php");

  $datebegin=$year1.".".$month1.".".$day1;
  $dateend=$year2.".".$month2.".".$day2;
  $dbegin=$day1.".".$month1.".".$year1;
  $dend=$day2.".".$month2.".".$year2;
  $strain=trim($train);
  $svagon=trim($vagon);
  $curdate=strftime("Дата: %d.%m.%Y");
  
  echo "<br>";
  echo "<h3>Протокол поверки весов железнодорожных типа 7260RSM</h3>";
  echo"<h3 align=center>$curdate</h3>";
  if($strain!='')
	   echo "<h5>Состав: $strain</h5>";
  if($svagon!='')
	  echo "<h5>№ МПС: $svagon</h5>";

	$query = "SELECT number,brutto1,check_weight,weighing_mode,speed,status_weight,sostav, ". 
          "DATE_FORMAT(date_time,'%d-%m-%y'),DATE_FORMAT(date_time,'%T') ".
          "FROM dn_name.w01_raw_weight ".
          "WHERE DATE_FORMAT(date_time,'%Y.%m.%d') >= '$datebegin' AND ".
          "DATE_FORMAT(date_time,'%Y.%m.%d') <= '$dateend' AND ".
          "status_weight=2 ";
	if($strain!='')
        $query.=" AND sostav=$strain ";
	if($svagon!='')
        $query.=" AND number='$svagon' ";
	
	$query .= "union SELECT number,brutto1,check_weight,weighing_mode,speed,status_weight,sostav, ". 
          "DATE_FORMAT(date_time,'%d-%m-%y'),DATE_FORMAT(date_time,'%T') ".
          "FROM dn_name_archive.w01_raw_weight ".
          "WHERE DATE_FORMAT(date_time,'%Y.%m.%d') >= '$datebegin' AND ".
          "DATE_FORMAT(date_time,'%Y.%m.%d') <= '$dateend' AND ".
          "status_weight=2 ";
	if($strain!='')
        $query.=" AND sostav=$strain ";
	if($svagon!='')
        $query.=" AND number='$svagon' ";
	
	$query.=" ORDER BY 8,9";

  //echo $query;

  mysql_connect($MYSQL_BASE_HOST,$MYSQL_USER_NAME,$MYSQL_USER_PASS)
                or die("Could not connect to MySQL server!!!");
  //mysql_select_db($MYSQL_BASE_NAME)
  //              or die("Could not select database!");

  $query_count="SELECT COUNT(*) ".substr($query,138);
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

  //echo  $query_count;

  $result=mysql_query($query);

  echo '<p><table class="pure-table pure-table-bordered" cellpadding=8 cellspacing=2 align=center border=1>';
  echo '<thead style="background-color: #00945D; color: #ffffff; border: 1px solid #D3D3D3;">';
  echo '<tr class="titlerow_white"><th>№<th>Дата<th>Время<th>Состав<th>№ МПС<th>Вес,т<th>Поверочный груз,т<th>Отклонение,т<th>Режим<th>Скорость</tr>';
  echo '</thead>';
  echo '<tbody>';
  
  $i=1;
  while($d=mysql_fetch_row($result))
  {
    echo "<tr>";
    $din_stat= ($d[3]==1 || $d[3]==3 || $d[3]==5) ? "стат":"дин";
    $weight=sprintf("%.3f",$d[1]);
    $check_weight=sprintf("%.3f",$d[2]);
    $diff=sprintf("%+.3f",$weight-$check_weight);
    $speed=sprintf("%.1f",$d[4]);
    echo "<td>$i<td>$d[7]<td>$d[8]<td>$d[6]<td>$d[0]<td>$weight<td>$check_weight<td>$diff<td>$din_stat<td>$speed";
    echo "</tr>";
    $i++;
  };
  echo"</tbody>";
  echo"</table>";

}
?>

