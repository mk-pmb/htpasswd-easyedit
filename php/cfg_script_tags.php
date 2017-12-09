<?php # -*- coding: utf-8, tab-width: 2 -*-

return function ($cfff, $cfg) {
  $burl = (string)@$cfg['scripts_baseurl'];
  $stag = function ($src) use ($burl) {
    return '<script src="' . $burl . $src . '"></script>';
  };
  $render_stags = function ($slot) use (&$cfg, $stag) {
    $srcs = (array)@$cfg[$slot];
    if (count($srcs) < 1) { return; }
    $cfg[$slot . '_html'] = array_map($stag, $srcs);
  };
  array_map($render_stags, [ 'early_scripts', 'late_scripts' ]);
  return $cfg;
};
