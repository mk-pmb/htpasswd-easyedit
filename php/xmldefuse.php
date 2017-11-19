<?php # -*- coding: utf-8, tab-width: 2 -*-

return function ($cfff, $x = NULL) {
  static $d = NULL;
  if (!$d) {
    $d = function ($t) {
      return htmlspecialchars((string)@$t, ENT_COMPAT, 'ISO-8859-1');
    };
  }
  if ($x === NULL) { return $d; }
  return $d($x);
};
