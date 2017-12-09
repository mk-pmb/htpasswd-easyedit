<?php # -*- coding: utf-8, tab-width: 2 -*-

return function ($cfff, $cfg) {
  $fefile = $cfff('frontend_readfile', $cfg);

  if (@$cfg['style_html'] === NULL) {
    $cfg['style_html'] = "<style>\n" . str_replace("\n", "\n    ",
      trim($fefile('style.css'))) . "\n  </style>";
  }

  $xmldefuse = $cfff('xmldefuse');
  $optlist = function ($v) use (&$xmldefuse) {
    return "<option>" . $xmldefuse($v) . "</option>";
  };

  if (@$cfg['listfiles_html'] === NULL) {
    $cfg['pwlists_html'] = array_map($optlist, $cfg['listfiles']);
  }

  $ui_html = $fefile('ui.html');

  $ins_html = function ($match) use (&$cfg) {
    list ($orig, $nl, $indent, $slot) = $match;
    $ins = @$cfg[$slot . '_html'];
    if (is_callable($ins)) { $ins = $ins($cfg, $match); }
    if (!$ins) { return ''; }
    if (is_array($ins)) { return $nl . $indent . implode("\n$indent", $ins); }
    return $nl . $indent . (string)@$ins;
  };
  $ui_html = preg_replace_callback(":(\n?)( *)<!-- @([A-Za-z0-9_]+) -->:",
    $ins_html, $ui_html);
  $ui_html = $cfff('i18n', $cfg, $ui_html);

  $cfff('http_headers', 200, 'html');
  echo $ui_html;
};
