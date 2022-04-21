<?php

session_unset();
unset($_COOKIE['username']);
setcookie('username', null, time() - 1);

header('location: login');