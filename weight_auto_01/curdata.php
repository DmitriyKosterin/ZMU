﻿<?php 
  require("config.inc.php");
  header('Content-type: text/html; charset=utf-8');
  error_reporting(0);
  $connect=odbc_connect($ODBC_DSN,$ODBC_USER,$ODBC_PASSWORD); 
  odbc_exec($connect,"SET NAMES utf8");
  if(!$connect)
    exit();
  $query="SELECT * FROM memory";
  $res=odbc_exec($connect,$query);
  $d=odbc_fetch_array($res);
  echo json_encode($d);
