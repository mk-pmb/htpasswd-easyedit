#!/bin/bash
# -*- coding: utf-8, tab-width: 2 -*-


function scan_voc () {
  local SELFPATH="$(readlink -m "$BASH_SOURCE"/..)"
  local PROJ_DIR="${SELFPATH%/*/*}"
  cd "$PROJ_DIR" || return $?

  export LANG=C
  local FILES=(
    -type f
    '(' -name '*.html'
      -o -name '*.css'
      -o -name '*.php'
      ')'
    )
  readarray -t FILES < <(find [a-z]*/ "${FILES[@]}")
  local VOC_RX='[\x22\x27]?[\w:\-]+[\x22\x27]?'
  local VOC_USED=()
  readarray -t VOC_USED < <(
    ( grep -hoPe '%\['"$VOC_RX"'\]' -- "${FILES[@]}" | sed -re '
        s~^%\[~~;s~\]$~~'
      grep -hoPe '/\*i18n:\*/'"$VOC_RX" -- "${FILES[@]}" | sed -re '
        s~[\x22\x27]~ ~
        s~^\S+ ~~
        s~[\x22\x27]~~g'
    ) | sort -u )

  cd "$SELFPATH" || return $?

  local VOC_JSON=
  local VOC_HAVE=()
  local VOC_MISS=()
  local VOC_XTRA=()
  for VOC_JSON in [a-z][a-z]_*.json; do
    echo -n "$VOC_JSON"$'\t'
    readarray -t VOC_HAVE < <(sed -nre '
      s~^[^"]*"([^"]+)":.*$~\1~p
      ' -- "$VOC_JSON")
    echo -n "${#VOC_HAVE[@]}"$'\t'
    readarray -t VOC_MISS < <(printf '%s\n' "${VOC_USED[@]}" \
      | grep -xvFe "$(printf '%s\n' "${VOC_HAVE[@]}")" | sort -u)
    echo -n "-${#VOC_MISS[@]}"$'\t'
    readarray -t VOC_XTRA < <(printf '%s\n' "${VOC_HAVE[@]}" \
      | grep -xvFe "$(printf '%s\n' "${VOC_USED[@]}")" | sort -u)
    echo "+${#VOC_XTRA[@]}"
    [ -z "${VOC_MISS[*]}" ] || echo "  - ${VOC_MISS[*]}"
    [ -z "${VOC_XTRA[*]}" ] || echo "  + ${VOC_XTRA[*]}"
  done

  return 0
}










[ "$1" == --lib ] && return 0; scan_voc "$@"; exit $?
