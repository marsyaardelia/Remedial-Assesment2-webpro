<?php

$page = $_GET['page'] ?? 'login';

switch($page){

    case 'dashboard':
        include 'dashboard.php';
        break;

    case 'laporan':
        include 'laporan.php';
        break;

    case 'tambah':
        include 'tambah_laporan.php';
        break;

    default:
        include 'login.php';
}