<?php # -*- coding: utf-8, tab-width: 2 -*-

return function ($cfff, $cfg, $fn = NULL) {
  static $fefile = NULL;
  if (!$fefile) {
    $fepath = $cfg['frontend_dir'];
    $fefile = function ($fn) use ($fepath) {
      return ltrim((string)@file_get_contents($fepath . $fn),
        "\xEF\xBB\xBF");  # UTF-8 BOM
    };
  }
  if ($fn) { return $fefile($fn); }
  return $fefile;
};
