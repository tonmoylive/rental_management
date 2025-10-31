<?php
// admin/logout.php
require_once '../config.php';
session_unset();
session_destroy();
redirect('../index.php');

// owner/logout.php
require_once '../config.php';
session_unset();
session_destroy();
redirect('../index.php');

// tenant/logout.php
require_once '../config.php';
session_unset();
session_destroy();
redirect('../index.php');
?>