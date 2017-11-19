<?php # -*- coding: utf-8, tab-width: 2 -*-

return function ($cfff, $cfg) {
  $prfx = (string)@$cfg['list_prefix'];
  $sufx = (string)@$cfg['list_suffix'];
  $dd = $cfg['data_dir'];
  $prfx_len = strlen($prfx);
  $sufx_len = strlen($sufx);

  $prios = (array)@$cfg['listfiles_prio'];
  $files = [];

  foreach ((array)@scandir($dd) as $fn) {
    if (substr($fn, 0, $prfx_len) !== $prfx) { continue; }
    if (substr($fn, -$sufx_len) !== $sufx) { continue; }
    if (!is_file($dd . $fn)) { continue; }
    $fn = substr($fn, $prfx_len, -$sufx_len);
    if (!$fn) { continue; }
    if (in_array($fn, $prios, true)) { continue; }
    $files[] = $fn;
  }

  $ins = array_search('', $prios, true);
  if ($ins === false) { $ins = count($prios); }
  array_splice($prios, $ins, 1, $files);
  return $prios;
};
