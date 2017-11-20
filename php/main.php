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
    'pwlist' => (string)@$_REQUEST['file'],

    'htpasswd_stdin_sep' => "\x00",
    'htpasswd_set_cmd' => 'xargs --null htpasswd -b',
    'htpasswd_del_cmd' => 'xargs --null htpasswd -D',

    'xsrf_token_algo' => 'md5',
    'xsrf_token_length' => 6,
    ];
  $cfg = $cfff('cfg_resolve_paths', $cfg);
  if (!is_array(@$cfg['listfiles'])) {
    $cfg['listfiles'] = $cfff('cfg_scan_listfiles', $cfg);
  }

  $rqmthd = (string)@$_SERVER['REQUEST_METHOD'];
  if ($rqmthd === 'GET') { return $cfff('render_ui', $cfg); }
  if ($rqmthd !== 'POST') { die("Unsupported HTTP method.\n"); }

  $result = $cfff('setpw', $cfg);
  $cfff('human_json_combo_reply', $cfg, $result);
};
