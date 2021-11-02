<?php 
/** 
 * Database_Placeholder: placeholder support for most SQL interfaces. 
 * (C) 2005 Dmitry Koterov, http://forum.dklab.ru/users/DmitryKoterov/ 
 * 
 * This library is free software; you can redistribute it and/or 
 * modify it under the terms of the GNU Lesser General Public 
 * License as published by the Free Software Foundation; either 
 * version 2.1 of the License, or (at your option) any later version. 
 * See http://www.gnu.org/copyleft/lesser.html 
 * 
 *
 * @version 2.20; 
 */ 

@define("PLACEHOLDER_ERROR_PREFIX", "ERROR: ");

function sql_compile_placeholder($tmpl) {
  $compiled  = array(); 
  $p         = 0;
  $i         = 0;
  $has_named = false; 
  while (false !== ($start = $p = strpos($tmpl, "?", $p))) { 
    switch ($c = substr($tmpl, ++$p, 1)) {
      case '%': case '@': case '#': 
        $type = $c; ++$p; break; 
      default: 
        $type = ''; break; 
    } 
    if (preg_match('/^((?:[^\s[:punct:]]|_)+)/', substr($tmpl, $p), $pock)) {
      $key = $pock[1]; 
      if ($type != '#') $has_named = true; 
      $p += strlen($key); 
    } else { 
      $key = $i; 
      if ($type != '#') $i++; 
    } 
    $compiled[] = array($key, $type, $start, $p - $start);
  } 
  return array($compiled, $tmpl, $has_named); 
} 

function sql_placeholder_ex($tmpl, $args, &$errormsg) {
  if (is_array($tmpl)) {
    $compiled = $tmpl; 
  } else { 
    $compiled  = sql_compile_placeholder($tmpl); 
  } 

  list ($compiled, $tmpl, $has_named) = $compiled; 

  if ($has_named) $args = @$args[0];

  $p   = 0;
  $out = '';
  $error = false;

  foreach ($compiled as $num=>$e) { 
    list ($key, $type, $start, $length) = $e; 

    $out .= substr($tmpl, $p, $start - $p);
    $p = $start + $length; 

    $repl = '';
    $errmsg = '';
    do { 
      if ($type === '#') {
        $repl = @constant($key); 
        if (NULL === $repl)  
          $error = $errmsg = "UNKNOWN_CONSTANT_$key"; 
        break; 
      } 
      if (!isset($args[$key])) {
        $error = $errmsg = "UNKNOWN_PLACEHOLDER_$key"; 
        break; 
      } 
      $a = $args[$key];
      if ($type === '') { 
        if (is_array($a)) {
          $error = $errmsg = "NOT_A_SCALAR_PLACEHOLDER_$key"; 
          break; 
        } 
        if (strlen($a)<10)
        {
        	$temp_my=intval($a);
        	if ($temp_my===$a)
        	{
        	    $repl=$a;
        	} else {
        	    $repl = "'".sql_escape($a)."'";
        	}
        } else {
        $repl = "'".sql_escape($a)."'";
        }
        break; 
      } 
      if (!is_array($a)) {
        $error = $errmsg = "NOT_AN_ARRAY_PLACEHOLDER_$key"; 
        break; 
      } 
      if ($type === '@') { 
        foreach ($a as $v)
          $repl .= ($repl===''? "" : ",")."'".sql_escape($v)."'";
      } elseif ($type === '%') { 
        $lerror = array();
        foreach ($a as $k=>$v) { 
          if (!is_string($k)) { 
            $lerror[$k] = "NOT_A_STRING_KEY_{$k}_FOR_PLACEHOLDER_$key"; 
          } else { 
            $k = preg_replace('/[^a-zA-Z0-9_]/', '_', $k); 
          } 
          $repl .= ($repl===''? "" : ", ").$k."='".sql_escape($v)."'";
        } 
        if (count($lerror)) {
          $repl = ''; 
          foreach ($a as $k=>$v) { 
            if (isset($lerror[$k])) { 
              $repl .= ($repl===''? "" : ", ").$lerror[$k]; 
            } else { 
              $k = preg_replace('/[^a-zA-Z0-9_-]/', '_', $k); 
              $repl .= ($repl===''? "" : ", ").$k."=?"; 
            } 
          } 
          $error = $errmsg = $repl; 
        } 
      } 
    } while (false); 
    if ($errmsg) $compiled[$num]['error'] = $errmsg; 
    if (!$error) $out .= $repl; 
  } 
  $out .= substr($tmpl, $p); 

  if ($error) {
    $out = ''; 
    $p   = 0;
    foreach ($compiled as $num=>$e) { 
      list ($key, $type, $start, $length) = $e; 
      $out .= substr($tmpl, $p, $start - $p); 
      $p = $start + $length; 
      if (isset($e['error'])) { 
        $out .= $e['error']; 
      } else { 
        $out .= substr($tmpl, $start, $length); 
      } 
    } 
    $out .= substr($tmpl, $p);
    $errormsg = $out; 
    return false; 
  } else { 
    $errormsg = false; 
    return $out; 
  } 
} 

function sql_placeholder() {
  $args = func_get_args();
  if (is_array($args[1]))  
  {
  	$result = sql_placeholder_ex($args[0], $args[1], $error);
  } else {
  	$tmpl = array_shift($args); 
  	$result = sql_placeholder_ex($tmpl, $args, $error); 
  }
  
  if ($result === false) return PLACEHOLDER_ERROR_PREFIX.$error; 
  else return $result; 
} 

function sql_pholder() { 
  $args = func_get_args();  
  $tmpl = array_shift($args); 
  $result = sql_placeholder_ex($tmpl, $args, $error); 
  if ($result === false) { 
    $error = "Placeholder substitution error. Diagnostics: \"$error\""; 
    if (function_exists("debug_backtrace")) { 
      $bt = debug_backtrace(); 
      $error .= " in ".@$bt[0]['file']." on line ".@$bt[0]['line']; 
    } 
    trigger_error($error, E_USER_WARNING); 
    return false; 
  } 
  return $result; 
}