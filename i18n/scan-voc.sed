#!/bin/sed -nurf
# -*- coding: UTF-8, tab-width: 2 -*-

s~\s~ ~g
s~ data-voc=~\t~g
1s~^[^\t]*$~\xEF\xBB\xBF{~p
$s~^[^\t]*$~  "": null }~p
/\t/!b skip
s~^[^\t]*\t("[^<>" ]+")(>([^<>"]*)<| value="([^<>"]*)"|\
  )[^\t]*$~  \1: "\3\4",~
p

: skip
