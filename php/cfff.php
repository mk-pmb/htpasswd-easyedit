<?php # -*- coding: utf-8, tab-width: 2 -*-

return call_user_func(function () {
  static $cache = NULL, $r = NULL;
  if ($r) { return $r; }
  $r = function ($n) using (&$cache) {
    $f = @$cache[$n];
    if (!$f) { $f = $cache[$n] = require(__DIR__ . '/' . $n . '.php'); }
    $a = func_get_args();
    $a[0] = $r;
    return call_user_func_array($f, $a);
  };
  $cache = [ 'cfff' => $r ];  # call function from file
  return $r;
});
