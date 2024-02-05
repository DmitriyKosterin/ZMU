<?php
function print_select_year($start_year,$count_year)
{
  $current_year=strftime("%Y");
  for($i=0;$i<$count_year;$i++)
  {
    $y=$start_year+$i;
    if($y==$current_year)
    {
      echo "<option value=\"$y\" selected>$y</option>\n";
      break;
    }
    else
      echo "<option value=\"$y\">$y</option>\n";
  }
}


function print_select_month()
{
      $m=array('01'=>'€нварь',
               '02'=>'февраль',
               '03'=>'март',
               '04'=>'апрель',
               '05'=>'май',
               '06'=>'июнь',
               '07'=>'июль',
               '08'=>'август',
               '09'=>'сент€брь',
               '10'=>'окт€брь',
               '11'=>'но€брь',
               '12'=>'декабрь');
  $current_month=strftime("%m");
  foreach($m as $month_num=>$month_name)
  {
    if($current_month==$month_num)
      echo "<option value=\"$month_num;$month_name\" selected> $month_name </option>\n";
    else
      echo "<option value=\"$month_num;$month_name\"> $month_name </option>\n";
  }
}


function print_select_day()
{
  $current_day=strftime("%d");
  for($nday=1;$nday<=31;$nday++)
  {
      $day=strlen($nday)==1 ? '0'. $nday : $nday;
      if($day==$current_day)
        echo "<option value=\"$day\" selected>$nday</option>\n";
      else
        echo "<option value=\"$day\">$nday</option>\n";
  }
}

