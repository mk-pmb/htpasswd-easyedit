<?php # -*- coding: utf-8, tab-width: 2 -*-

return function ($cfff, $cfg) {
  if (!$cfff) { $cfff = require(__DIR__ . '/cfff.php'); }
  $cfff('http_headers');

  $check_mandatory_config = function ($slot) use ($cfg) {
    if (!@$cfg[$slot]) { die("Missing config opt: $slot\n"); }
  };
  array_map($check_mandatory_config, [ 'xsrf_salt', 'data_dir' ]);
  $cfg += [
    'i18n' => 'en',
    'list_prefix' => '',
    'list_suffix' => '.htpw',
    'frontend_dir' => __DIR__ . '/../frontend',
    'backup_dir' => '<data>/bak',
    'xsrf_memo_file' => '<backup>/.xsrf-memo.txt',
    ];
  $cfff('cfg_resolve_paths', [ &$cfg ]);
  if (!is_array(@$cfg['listfiles'])) {
    $cfg['listfiles'] = $cfff('cfg_scan_listfiles', $cfg);
  }

  $rqmthd = (string)@$_SERVER['REQUEST_METHOD'];

  if ($rqmthd === 'GET') {
    $cfff('render_ui', $cfg);
    return;
  }

  if ($rqmthd !== 'POST') {
    die("Unsupported HTTP method.\n");
  }

  print_r($_SERVER);
};
