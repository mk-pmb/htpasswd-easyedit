<?php # -*- coding: utf-8, tab-width: 2 -*-

return function ($cfff, $cfg) {
  $pwfile_bfn = $cfg['pwlist'];
  if (!in_array($pwfile_bfn, $cfg['listfiles'])) {
    return [ 403, /*i18n:*/'file:invalid' ];
  }

  function rqv($n, $d='') {
    $v = @$_REQUEST[$n];
    if (is_string($v)) { return $v; }
    return $d;
  }
  function trqv($n, $d = '') { return trim(rqv($n, $d)); }

  $pw_trim = "\r\n";
  if (rqv('trim_sp')) { $pw_trim .= " \t\v"; }
  if (rqv('trim_qb')) { $pw_trim .= "'\"()[]"; }

  function check_badchars($val, $errvoc, $extra_bad = '') {
    $rgx = "|[\\x00-\\x1F\\x7F$extra_bad]|";
    $found = preg_match($rgx, $val, $match, PREG_OFFSET_CAPTURE);
    if ($found === 0) { return false; }
    $pos = ($found === 1 ? $match[0][1] + 1 : -1);
    return [ 400, $errvoc, 'badchar' => $pos ];
  }

  $subj_user = trqv('user_orig') . trqv('user_sorted') . trqv('user_new');
  $badchar = check_badchars($subj_user, /*i18n:*/'badchar:user', "\"\'%:#");
  if ($badchar) { return $badchar; }
  $new_pswd = trim(rqv('pass'), $pw_trim);
  $badchar = check_badchars($new_pswd, /*i18n:*/'badchar:pass');
  if ($badchar) { return $badchar; }

  $typo = trim(rqv('typo'), $pw_trim);
  if ($typo && ($typo !== $new_pswd)) {
    return [ 400, /*i18n:*/'typo:diff' ];
  }

  $pwfile_abs = $cfg['data_dir'] . $cfg['list_prefix'] .
    $pwfile_bfn . $cfg['list_suffix'];

  $cmd = $cfg['htpasswd_del_cmd'];
  $stdin_sep = $cfg['htpasswd_stdin_sep'];
  $stdin_data = $pwfile_abs . $stdin_sep . $subj_user . $stdin_sep;
  $done_voc = /*i18n:*/'shell:del_done';
  $xsrf_voc = /*i18n:*/'xsrf:code4del';
  if ($new_pswd !== '') {
    $cmd = $cfg['htpasswd_set_cmd'];
    $stdin_data .= $new_pswd . $stdin_sep;
    $done_voc = /*i18n:*/'shell:set_done';
    $xsrf_voc = /*i18n:*/'xsrf:code4set';
  }

  function srvv($n) { return (string)@$_SERVER[$n]; }
  $xsrf_data = implode("\a", [$cmd, $stdin_data,
    srvv('REMOTE_USER'),
    srvv('REMOTE_ADDR'),
    srvv('HTTP_USER_AGENT'),
    date('ymdH') ]);
  $xsrf_auth = hash_hmac($cfg['xsrf_token_algo'], $xsrf_data,
    $cfg['xsrf_salt']);
  if (!$xsrf_auth) { return [ 500, /*i18n:*/'xsrf:unavail' ]; }
  $xsrf_len = (int)$cfg['xsrf_token_length'];
  if ($xsrf_len >= 1) { $xsrf_auth = substr($xsrf_auth, 0, $xsrf_len); }

  if (trqv('xsrf') !== $xsrf_auth) {
    return [ false, $xsrf_voc, 'user' => $subj_user, 'xsrf' => $xsrf_auth ];
  }


  $result = $cfff('shell_stdio', $cmd, $stdin_data);
  if (!$result) { return [ 500, /*i18n:*/'shell:nospawn' ]; }

  $output = trim($result['stderr'] . "\n" . $result['stdout']);
  unset($result['stdout']);
  unset($result['stderr']);
  $output = str_replace($cfg['data_dir'], '…data…/', $output);
  $result['output'] = $output;

  return ($result['retval'] === 0
    ? [ false, $done_voc ]
    : [ 500, /*i18n:*/'shell:failed' ]
    ) + $result;
};
