<?php

// Connects to the XE service (i.e. database) on the "localhost" machine
$conn = oci_connect('EXERCISE3', 'exe08120626', 'testdb.htenc.com:1521/ORCL');
if (!$conn) {
    $e = oci_error();
    trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
}
phpinfo();
?>
