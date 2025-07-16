<?php
session_start();
session_destroy();
header("Location: account.php"); // Or wherever you want to redirect
exit();
