<?php # -*- coding: utf-8, tab-width: 2 -*-

return function ($cfff, $cfg, $result) {
  $http_err = $result[0];
  unset($result[0]);
  $msg_voc = $result[1];
  unset($result[1]);
  $cfff('http_headers', ($http_err ? $http_err : 200), 'plain');

  if ($http_err) { echo $cfff('i18n', $cfg, '%[err:] '); }

  $human = $result;
  if (@$result['retval'] === 0) { unset($human['retval']); }
  echo $cfff('i18n', $cfg, "%[$msg_voc]"), "\n\n",
    implode("\n", $human), "\n\n\n";

  $result = [ "error" => $http_err, "time" => time(),
    "status" => $msg_voc ] + $result;
  echo json_encode($result,
    + JSON_UNESCAPED_SLASHES
    + JSON_FORCE_OBJECT
    + JSON_PARTIAL_OUTPUT_ON_ERROR
    ), "\n";
};
