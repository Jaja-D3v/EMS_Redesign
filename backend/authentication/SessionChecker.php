<?php
// $isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');

// session_set_cookie_params([
//     'lifetime' => 3600,
//     'path' => '/',
//     'secure' => $isHttps,
//     'httponly' => true,
//     'samesite' => 'Lax'
// ]);

// session_start();

// // Set to 5 minutes + 10 seconds buffer para hindi mauna ang server kaysa sa UI countdown
// $timeout = 310; 

// if (
//     !isset($_SESSION['id']) ||
//     (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > $timeout))
// ) {
//     session_unset();
//     session_destroy();

//     header('Location: /EMS_home/error-pages/401.html');
//     exit;
// }

// // I-update ang LAST_ACTIVITY sa bawat page refresh/navigation
// $_SESSION['LAST_ACTIVITY'] = time();