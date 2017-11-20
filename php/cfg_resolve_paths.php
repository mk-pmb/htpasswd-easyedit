<?php # -*- coding: utf-8, tab-width: 2 -*-

return function ($cfff, $cfg) {
  $resolve = function ($key) use (&$cfg, &$resolve) {
    $isdir = (substr($key, -4) === '_dir');
    if ((!$isdir) && (substr($key, -5) !== '_file')) { return; }
    $val = (string)@$cfg[$key];
    //echo "reso: [$key] ? '$val'\n";
    if (substr($val, 0, 1) === '<') {
      $val = explode('>', substr($val, 1), 2);
      if (count($val) === 2) {
        $dir = $val[0] . '_dir';
        //echo "reso: [$key] <$dir> ";
        $dir = $resolve($dir);
        //echo "'$dir'\n";
        $val = $dir . ltrim($val[1], '/');
      }
    }
    if (substr($val, 0, 1) !== '/') { die("Need absolute path for $key\n"); }
    if ($isdir && (substr($val, -1) !== '/')) { $val .= '/'; }
    //echo "reso: [$key] = '$val'\n";
    $cfg[$key] = $val;
    return $val;
  };
  array_map($resolve, array_keys($cfg));
  return $cfg;
};
