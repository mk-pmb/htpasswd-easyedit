#!/bin/sed -urf
# -*- coding: UTF-8, tab-width: 2 -*-
s~( data-voc="([^<>" ]+)"(>| value="))[^<>"]*~\1°\2 ~g

s~°(\S+) ~…~
s~°(\S+) ~~
s~°(\S+) ~«\1»~g
