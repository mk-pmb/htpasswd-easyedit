<?php # -*- coding: utf-8, tab-width: 2 -*-

return function ($cfff, $status = 500, $textfmt = 'plain') {
  header("Content-Type: text/$textfmt; charset=UTF-8");
  http_response_code($status);
};
