<!DOCTYPE html>
<html lang="ru">
  <head>
    <meta charset="windows-1251">
    <meta http-equiv="X-UA-Compatible" content="IE=8" />
    <title>Насыпь</title>
    <link rel="stylesheet" href="../../css/pure-min.css">
    <link rel="stylesheet" href="../../css/style.css">
  </head>
  <body>
    <script language="JavaScript">
      document.oncontextmenu=new Function("return false;");
    </script>

    <div class="header">
      <div class="home-menu pure-menu pure-menu-horizontal pure-menu-fixed">
        <a class="pure-menu-heading" href="">Отчёт сменный</a>
      </div>
    </div>
    <?php require("../func.inc.php"); ?>
    <?php require("../config.inc.php"); ?>

    <div class="content">  
      <form class="pure-form" action="report.php" method="post">
        <fieldset>
          <legend>Дата</legend>
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
          </div>
        </fieldset>
      
        <button type="submit" class="pure-button pure-button-primary">Выбор</button>
        <button type="reset" class="pure-button">Сброс</button>
      </form>
    </div>
  </body>
</html>