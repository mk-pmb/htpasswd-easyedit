
<!--#echo json="package.json" key="name" underline="=" -->
htpasswd-easyedit
=================
<!--/#echo -->

<!--#echo json="package.json" key="description" -->
Easy web editor for Apache password lists.
<!--/#echo -->


Usage
-----

On PHP webspace:

```php
<?php
$htpw_main = require(__DIR__ . '/../../util/htpasswd-easyedit/php/main.php');
$htpw_main(NULL, [
  'i18n' => 'en_EN',
  'xsrf_salt' => 'put random data here',
  'data_dir' => __DIR__,
  'list_suffix' => '.pwl',    # your file extension for password lists
  ]);
```



<!--#toc stop="scan" -->



Known issues
------------

* Needs more/better tests and docs.




&nbsp;


License
-------
<!--#echo json="package.json" key=".license" -->
GPL-2
<!--/#echo -->
