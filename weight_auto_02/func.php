<?php

function print_select_year($start_year,$count_year) {
  $current_year=strftime("%Y");
  for($i=0;$i<$count_year;$i++)   {
    $y=$start_year+$i;
    if($y==$current_year)
      echo "<option value=\"$y\" selected>$y</option>\n";
    else
      echo "<option value=\"$y\">$y</option>\n";
  }
}

function print_select_month() {
      $m=array('01'=>'январь',
								'02'=>'февраль',
								'03'=>'март',
								'04'=>'апрель',
								'05'=>'май',
								'06'=>'июнь',
								'07'=>'июль',
								'08'=>'август',
								'09'=>'сентябрь',
								'10'=>'октябрь',
								'11'=>'ноябрь',
								'12'=>'декабрь');
  $current_month=strftime("%m");
  foreach($m as $key=>$v) {
    if($current_month==$key)
      echo "<option value=\"$key\" selected>$v</option>\n";
    else
      echo "<option value=\"$key\">$v</option>\n";
  }
}

function print_select_day() {
  $current_day=strftime("%d");
  for($i=1;$i<=31;$i++) {
		$i1=strlen($i)==1 ? '0'.$i : $i;
    if($i==$current_day)
			echo "<option value=\"$i1\" selected>$i</option>\n";
    else
			echo "<option value=\"$i1\">$i</option>\n";
  }
}

