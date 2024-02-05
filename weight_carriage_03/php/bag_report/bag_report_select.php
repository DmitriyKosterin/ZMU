<!DOCTYPE html>
<html lang="ru">
  <head>
    <meta charset="windows-1251">
    <meta http-equiv="X-UA-Compatible" content="IE=8" />
    <title>Насыпь</title>
    <link rel="stylesheet" href="../../css/pure-min.css">
    <link rel="stylesheet" href="../../css/style.css">

    <script>
      function check_uncheck_All() {
        isChecked=document.getElementById('all').checked
        document.getElementById('smena1').checked=isChecked
        document.getElementById('smena2').checked=isChecked
        document.getElementById('smena3').checked=isChecked
        document.getElementById('smena4').checked=isChecked
      }
    </script>

  </head>
  <body>
    <script language="JavaScript">
      document.oncontextmenu=new Function("return false;");
    </script>

    <div class="header">
      <div class="home-menu pure-menu pure-menu-horizontal pure-menu-fixed">
        <a class="pure-menu-heading" href="">Отгрузка в мешках и биг-бегах в ж/д вагоны и автотранспорт</a>
      </div>
    </div>
    <?php require("../func.inc.php"); ?>
    <?php require("../config.inc.php"); ?>

    <div class="content">  
      <form class="pure-form" action="report.php" method="post">
        <fieldset>
          <legend>Начальная дата/время</legend>
          <div class="pure-g">
            <div class="pure-u-4-24">
              <label for="year1">Год</label>
              <select class="pure-u-23-24" name="year1"><?php print_select_year("2016",20); ?></select>
            </div>
            <div class="pure-u-4-24">
              <label for="month1">Месяц</label>
              <select class="pure-u-23-24" name="month1"><?php print_select_month(); ?></select>
            </div>              
            <div class="pure-u-4-24">
              <label for="day1">День</label>
              <select class="pure-u-23-24" name="day1"><?php print_select_day(); ?></select>
            </div>
            <div class="pure-u-4-24">
              <label for="time1">Время</label>
              <select class="pure-u-23-24" name="time1">
                <option>00:00</option>
                <option>08:00</option>
                <option>20:00</option>
                <option>24:00</option>
              </select>
            </div>
          </div>
        </fieldset>
      
        <fieldset>
          <legend>Конечная дата/время</legend>
          <div class="pure-g">
            <div class="pure-u-4-24">
              <label for="year2">Год</label>
              <select class="pure-u-23-24" name="year2"><?php print_select_year("2016",20); ?></select>
            </div>
            <div class="pure-u-4-24">
              <label for="month2">Месяц</label>
              <select class="pure-u-23-24" name="month2"><?php print_select_month(); ?></select>
            </div>              
            <div class="pure-u-4-24">
              <label for="day2">День</label>
              <select class="pure-u-23-24" name="day2"><?php print_select_day(); ?></select>
            </div>
            <div class="pure-u-4-24">
              <label for="time2">Время</label>
              <select class="pure-u-23-24" name="time2">
                <option>00:00</option>
                <option>08:00</option>
                <option>20:00</option>
                <option selected>24:00</option>
              </select>
            </div>
          </div>
        </fieldset>
      
        <fieldset>
          <legend>Смена</legend>
          <div class="pure-g">
            <div class="pure-u-2-24">
              <label for="smena1" class="pure-checkbox">A</label>
              <input type="checkbox" id="smena1" name="smena1">
            </div>
            <div class="pure-u-2-24">
              <label for="smena2" class="pure-checkbox">Б</label>
              <input type="checkbox" id="smena2" name="smena2">
            </div>
            <div class="pure-u-2-24">
              <label for="smena3" class="pure-checkbox">В</label>
              <input type="checkbox" id="smena3" name="smena3">
            </div>
            <div class="pure-u-2-24">
              <label for="smena4" class="pure-checkbox">Г</label>
              <input type="checkbox" id="smena4" name="smena4">
            </div>
            <div class="pure-u-2-24">
              <label for="all" class="pure-checkbox">все смены</label>
              <input type="checkbox" id="all" onClick="check_uncheck_All()">
            </div>
          </div>
        </fieldset>
      
        <fieldset>
          <input type="text" name="ntransport" maxlength=16 class="pure-input-1-4" placeholder="Номер вагона или автомобиля">
        </fieldset>
        <button type="submit" class="pure-button pure-button-primary">Выбор</button>
        <button type="reset" class="pure-button">Сброс</button>
      </form>
    </div>
  </body>
</html>