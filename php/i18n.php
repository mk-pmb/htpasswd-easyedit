<?php # -*- coding: utf-8, tab-width: 2 -*-

return function ($cfff, $cfg, $text = NULL) {
  static $i18n = NULL;
  if ($i18n) { return $i18n($text); }

  $dict = 'i18n/' . strtolower($cfg['i18n']) . '.json';
  $dict = $cfff('frontend_readfile', $cfg, $dict);
  $dict = json_decode($dict, true);

  $i18n = function ($text) use (&$dict, &$i18n) {
    if ($text === NULL) { return $i18n; }
    if (is_string($text)) {
      return preg_replace_callback("!%\\[([\\w:\\-]+)\\]!", $i18n, $text);
    }
    if (is_array($text)) { $text = $text[1]; }
    $voc = (string)@$dict[$text];
    if ($voc) { return $voc; }
    return "%[$text]";
  };
  return $i18n($text);
};
