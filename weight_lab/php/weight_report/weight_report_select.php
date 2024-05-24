<!DOCTYPE html>
<html lang="ru">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=8" />
    <title>Взвешивание вагонов</title>
    <link rel="stylesheet" href="../../css/pure-min.css">
    <link rel="stylesheet" href="../../css/style.css">
  </head>
  <body>
    <script language="JavaScript">
      document.oncontextmenu=new Function("return false;");
    </script>

    <div class="header">
      <div class="home-menu pure-menu pure-menu-horizontal pure-menu-fixed">
        <a class="pure-menu-heading" href="">Взвешивание на лабораторных весах</a>
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
              <select class="pure-u-23-24" name="year1"><?php print_select_year("2018",20); ?></select>
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
      
        <fieldset>
          <legend>Конечная дата/время</legend>
          <div class="pure-g">
            <div class="pure-u-4-24">
              <label for="year2">Год</label>
              <select class="pure-u-23-24" name="year2"><?php print_select_year("2018",20); ?></select>
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
      
        <fieldset>
          <legend>Смена</legend>
          <div class="pure-g">
            <div class="pure-u-2-24">
              <label for="smena1" class="pure-checkbox">A</label>
              <input type="checkbox" name="smena1">
            </div>
            <div class="pure-u-2-24">
              <label for="smena2" class="pure-checkbox">Б</label>
              <input type="checkbox" name="smena2">
            </div>
            <div class="pure-u-2-24">
              <label for="smena3" class="pure-checkbox">В</label>
              <input type="checkbox" name="smena3">
            </div>
            <div class="pure-u-2-24">
              <label for="smena4" class="pure-checkbox">Г</label>
              <input type="checkbox" name="smena4">
            </div>
          </div>
        </fieldset>
        <fieldset>
          <div class="pure-g">
            <div class="pure-u-12-24">
              <input type="checkbox" name="approximation">
              включить округление до 0.1г
            </div>
          </div>
        </fieldset>
      
        <button type="submit" class="pure-button pure-button-primary">Выбор</button>
        <button type="reset" class="pure-button">Сброс</button>
      </form>
    </div>
  </body>
</html>