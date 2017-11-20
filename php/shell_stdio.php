<?php # -*- coding: utf-8, tab-width: 2 -*-

return function ($cfff, $cmd, $stdin = '') {
  $io_spec = [
    0 => array('pipe', 'r'),
    1 => array('pipe', 'w'),
    2 => array('pipe', 'w'),
    ];
  $proc = proc_open($cmd, $io_spec, $pipes);
  if (!is_resource($proc)) { return false; }

  fwrite($pipes[0], $stdin);
  fclose($pipes[0]);

  $result = [];
  $result['stdout'] = stream_get_contents($pipes[1]);
  fclose($pipes[1]);
  $result['stderr'] = stream_get_contents($pipes[2]);
  fclose($pipes[2]);
  $result['retval'] = proc_close($proc);
  return $result;
};
