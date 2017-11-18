#!/bin/bash
# -*- coding: utf-8, tab-width: 2 -*-


function static_uis () {
  local VOC_JSON=
  local BFN=
  local VOC_SED=
  local HTML='ui.html'
  for VOC_JSON in [a-z][a-z]_*.json; do
    [ -f "$VOC_JSON" ] || continue
    BFN="${VOC_JSON%.json}"
    VOC_SED="$BFN".trans.sed
    voc_json2sed "$VOC_JSON" >"$VOC_SED" || return $?
    csed -rf "$VOC_SED" -- ../"$HTML" >"$BFN.$HTML" || return $?
  done
  return 0
}


function csed () { LANG=C sed "$@"; }

function voc_json2sed () {
  csed -nure '
    1s~^[~-\xFF]*\{~~
    s~",?\s*$~~
    s~^\s*"([^"<> ]+)":\s*"~\1 ~p
    ' -- "$@" | csed -re '
    s!~|\&!\\&!g
    s!$!~g!
    s!^(\S+) !s~%\\[\1\\]~!
    '
  return 0
}







static_uis "$@"; exit $?
