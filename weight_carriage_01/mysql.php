<?php
/** Simple layer for supporting most common deprecated mysql_* functions in PHP 7.
 *  It replaces their calls to MySQLi function calls.
 *  Just include this file in start of your index.php.
 *
 *  Written by 4X_Pro, http://4xpro.ru
 *  Distributed under MIT license terms.
 * **/
//echo "...";
if (version_compare(PHP_VERSION, '7.0.0','>=')) {
if ( !defined( 'OLD_MYSQL_FUNCTIONS' ) ) {

define ('OLD_MYSQL_FUNCTIONS',TRUE); // антихак для include-ов

function mysql_connect($server,$username,$password,$new_link=false,$client_flags=0) {
  if ($GLOBALS['mysql_oldstyle_link']!=NULL) mysqli_close($GLOBALS['mysql_oldstyle_link']);
  $GLOBALS['mysql_oldstyle_link']=mysqli_connect($server,$username,$password);
  return $GLOBALS['mysql_oldstyle_link'];
}

function mysql_pconnect($server,$username,$password,$new_link=false,$client_flags=0) {
  if ($GLOBALS['mysql_oldstyle_link']!=NULL) mysqli_close($GLOBALS['mysql_oldstyle_link']);
  $GLOBALS['mysql_oldstyle_link']=mysqli_connect($server,$username,$password);
  return $GLOBALS['mysql_oldstyle_link'];
}

function mysql_query($sql,$link=NULL) {
  if ($link==NULL) $link=$GLOBALS['mysql_oldstyle_link'];
  return mysqli_query($link,$sql);
}

function mysql_fetch_row($res) {
  return mysqli_fetch_row($res);
}

function mysql_fetch_assoc($res) {
  return mysqli_fetch_assoc($res);
}

function mysql_fetch_array($res) {
  return mysqli_fetch_array($res);
}

function mysql_fetch_object($res,$classname='stdClass',$params=array()) {
  return mysqli_fetch_object($res,$classname,$params);
}

function mysql_affected_rows($link=NULL) {
  if ($link===NULL) $link=$GLOBALS['mysql_oldstyle_link'];
  return mysqli_affected_rows($link);
}

function mysql_insert_id($link=NULL) {
  if ($link===NULL) $link=$GLOBALS['mysql_oldstyle_link'];
  return mysqli_insert_id($link);
}

function mysql_select_db($database_name,$link=NULL) {
  if ($link==NULL) $link=$GLOBALS['mysql_oldstyle_link'];
  return mysqli_select_db($link,$database_name);
}

function mysql_errno($link=NULL) {
  if ($link===NULL) $link=$GLOBALS['mysql_oldstyle_link'];
  return mysqli_errno($link);
}

function mysql_error($link=NULL) {
  if ($link===NULL) $link=$GLOBALS['mysql_oldstyle_link'];
  return mysqli_error($link);
}

function mysql_num_rows($res) {
  return mysqli_num_rows($res);
}

function mysql_free_result($res) {
  return mysqli_free_result($res);
}

function mysql_close($link) {
  if ( $GLOBALS['mysql_oldstyle_link']==$link )
    $GLOBALS['mysql_oldstyle_link']=NULL;
  return mysqli_close($link);
}

function mysql_real_escape_string($sql,$link=NULL) {
  if ($link===NULL) $link=$GLOBALS['mysql_oldstyle_link'];
  return mysqli_real_escape_string($link,$sql);
}

function mysql_get_server_info($link=NULL) {
  if ($link===NULL) $link=$GLOBALS['mysql_oldstyle_link'];
  return mysqli_get_server_info($link);
}

function mysql_set_charset ($charset, $link_identifier = NULL) {
  if ($link===NULL) $link=$GLOBALS['mysql_oldstyle_link'];
  return mysqli_set_charset($link, $charset);
}

function mysql_num_fields($res) {
  return mysqli_num_fields($res);
}

function mysql_client_encoding($link=NULL) {
  if ($link===NULL) $link=$GLOBALS['mysql_oldstyle_link'];
  return mysqli_character_set_name($link);
}

}
}