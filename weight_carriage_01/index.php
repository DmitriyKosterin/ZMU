<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Отгрузка аммиака в цистернах</title>
<link rel="stylesheet" href="css/pure-min.css">
<link rel="stylesheet" href="css/style.css">

<script type="text/javascript">
function setVisibleFormat(isVisible)
{
  document.getElementById('format').style.visibility=isVisible ? 'visible':'hidden';
}

//document.oncontextmenu=new Function("return false;");
</script>

</head>
<body>
<!-- <P align=center ><IMG height=55 src="img/logo.gif" width=90 border=0></P> -->

<div class="header">
      <div class="home-menu pure-menu pure-menu-horizontal pure-menu-fixed">
        <a class="pure-menu-heading">Название весов</a>
        <ul class="pure-menu-list">
          <li class="pure-menu-item">
            <a class="pure-menu-link">Телефон </a>
          </li>
        </ul>
      </div>
    </div>
<br>

<? require("func.inc.php");?>
<? require("config.inc.php");?>

<div class="content">  
<form class="pure-form" action="report.php" method="post">
	<fieldset>
	<legend>Начальная дата</legend>
		<div class="pure-g">
			<div class="pure-u-4-24">
				<label for="year1">Год</label>
				<select class="pure-u-23-24" name="year1"><?php print_select_year("2017",20); ?></select>
			</div>
			<div class="pure-u-4-24">
				<label for="month1">Месяц</label>
				<select class="pure-u-23-24" name="month1"><?php print_select_month(); ?></select>
			</div>              
			<div class="pure-u-4-24">
				<label for="day1">День</label>
				<select class="pure-u-23-24" name="day1"><?php print_select_day(); ?></select>
			</div>
		</div>
	</fieldset>
	<br>

	<fieldset>
	<legend>Конечная дата</legend>
		<div class="pure-g">
			<div class="pure-u-4-24">
				<label for="year2">Год</label>
				<select class="pure-u-23-24" name="year2"><?php print_select_year("2017",20); ?></select>
			</div>
			<div class="pure-u-4-24">
				<label for="month2">Месяц</label>
				<select class="pure-u-23-24" name="month2"><?php print_select_month(); ?></select>
			</div>              
			<div class="pure-u-4-24">
				<label for="day2">День</label>
				<select class="pure-u-23-24" name="day2"><?php print_select_day(); ?></select>
			</div>	
		</div>
	</fieldset>
	<br>

    <fieldset>
    <legend>Режим работы</legend>
	<div class="pure-form">
		<label for="mode_weight" class="pure-radio">
			<input type="radio" name="mode_weight" value="shipment" checked="" onClick="setVisibleFormat(1);"> Отгрузка&nbsp
			<input type="radio" name="mode_weight" value="weighing" onClick="setVisibleFormat(0);"> Взвешивание&nbsp
			<input type="radio" name="mode_weight" value="check" onClick="setVisibleFormat(0);"> Поверка&nbsp
		</label>
		<br>
		<input type=text size=8 maxlength=8 name=train class="pure-input-1-5" placeholder="Состав">&nbsp
		<input type=text size=8 maxlength=8 name=vagon class="pure-input-1-5" placeholder="Вагон">&nbsp
	</div>
	<br>
	<div class="pure-form">
		<select id="format" name=format>
			<option value=normal checked>Только отгрузка
			<option value=extended>Отгрузка и тара
			<option value=tare>Только тара
		</select>
    </div>
	</fieldset>
	<br>
	
	<div class="pure-g">
		<button type="submit" class="pure-button pure-button-primary">Выбор</button>
		<button type="reset" class="pure-button">Сброс</button>
	</div>
	<br>

</form>
</div>
</body>
</html>
