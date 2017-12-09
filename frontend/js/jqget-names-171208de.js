/*jslint indent: 2, maxlen: 80, browser: true */
/* -*- tab-width: 2 -*- */
(function () {
  'use strict';
  var fel = document.forms.htpw.elements, jq = window.jQuery,
    allUsers = '… initialisiere …', rqCnt = 0, filterField = false;

  function strCmpNocase(a, b) {
    a = a.toLowerCase();
    b = b.toLowerCase();
    if (a < b) { return -1; }
    if (a > b) { return 1; }
    return 0;
  }

  function updateOneNamesList(sel, names) {
    var jqSel = jq(sel);
    jqSel.children().slice(1).remove();
    jqSel.append(names.map(function (n) { return jq('<option>').text(n); }));
    sel.size = names.length + 3;
    sel.selectedIndex = 0;
  }

  function str_isin(needle, hay) { return (hay.indexOf(needle) >= 0); }

  function makeFilterFunc() {
    var kws = String(filterField.value || '').toLowerCase().match(/\S+/g);
    if (!kws) { return; }
    if (!kws.length) { return; }
    return function (name) {
      var lcName = name.toLowerCase();
      return kws.reduce(function (prev, kw) {
        return (prev && str_isin(kw, lcName));
      }, true);
    };
  }

  function updateNamesLists() {
    if (allUsers.substr) {
      updateOneNamesList(fel.user_orig, [allUsers]);
      updateOneNamesList(fel.user_sorted, [allUsers]);
      return;
    }
    var filter = makeFilterFunc(), matches;
    matches = (filter ? allUsers.filter(filter) : allUsers.slice());
    updateOneNamesList(fel.user_orig, matches);
    matches.sort(strCmpNocase);
    updateOneNamesList(fel.user_sorted, matches);
    if (filterField) {
      jq(filterField.row).attr({ 'data-n-total': allUsers.length,
        'data-n-match': (filter ? matches.length : null) });
      jq('#user-filter-stats').text('zeige ' +
        matches.length + ' von ' + allUsers.length);
    }
  }

  function scanUsers() {
    rqCnt += 1;
    allUsers = '… lade …';
    updateNamesLists();
    var pwlUrl = jq(fel.file).val() + '.txt', rqId = rqCnt;
    jq.ajax({ method: 'GET', url: pwlUrl,
      cache: false,
      dataType: 'text',
      mimeType: 'text/plain; charset=UTF-8',
      timeout: 10e3,
      success: function (data) {
        if (rqId !== rqCnt) { return; }
        allUsers = [];
        String(data).replace(/(?:^|\n)([ -"\$-9;-\uFFFF]*):/g,
          function (m, n) { allUsers.push(m && n); });
        updateNamesLists();
      },
      error: function (jqXHR, textStatus, errorThrown) {
        if (rqId !== rqCnt) { return; }
        console.error('scanUsers ajax fail', jqXHR, errorThrown);
        window.alert(['Konnte Benutzerliste nicht laden:', pwlUrl,
          textStatus, errorThrown].join('\n'));
      },
      });
  }
  jq(fel.scan_users).attr({ disabled: false }).on('click', scanUsers);


  function debounce(f, delay_msec) {
    var timer;
    delay_msec = (+delay_msec || 50);
    function fire() {
      timer = null;
      f();
    }
    return function () {
      if (timer) { clearTimeout(timer); }
      timer = setTimeout(fire, delay_msec);
    };
  }


  (function () {
    var row = jq('#user-filter-row').first(),
      txf = row.find('input[type=text]').first();
    if (!txf[0]) { return; }
    filterField = txf[0];
    filterField.row = row;
    filterField.filterSoon = debounce(updateNamesLists);
    txf.on('keyup change', filterField.filterSoon);
    row.removeClass('not-ready');
  }());





  jq(window).ready(scanUsers);
}());
