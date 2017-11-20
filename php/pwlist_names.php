<?php # -*- coding: utf-8, tab-width: 2 -*-

return function ($cfff, $fn) {
  return array_filter(array_map(function ($ln) {
    $ln = ltrim($ln);
    if (substr($ln, 0, 1) === '#') { return; }
    $ln = explode(':', $ln);
    if (count($ln) < 2) { return; }
    return $ln[0];
  }, (array)@file($fn)));
};
