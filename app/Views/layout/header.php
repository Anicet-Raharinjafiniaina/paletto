<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title><?= esc($title ?? 'Paletto') ?></title>
    <link rel="shortcut icon" href="<?= base_url('assets/images/logoBoost.png') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/bootstrap.min.css') ?>">
    <link href="<?= base_url('assets/css/icons.min.css') ?>" rel="stylesheet" type="text/css" />
    <link href='<?= base_url("assets/libs/sweetalert2/sweetalert2.min.css") ?>' rel="stylesheet" type="text/css" />
    <link href='<?= base_url("assets/libs/duallistbox/duallistbox.min.css") ?>' id="bootstrap-style" rel="stylesheet" type="text/css" />
    <link href='<?= base_url("assets/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css") ?>' rel="stylesheet" type="text/css" />
    <link href='<?= base_url("assets/libs/datatables.net-responsive-bs4/css/responsive.bootstrap4.min.css") ?>' rel="stylesheet" type="text/css" />
    <link href='<?= base_url("assets/libs/select2/select2.min.css") ?>' rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="<?= base_url('assets/css/preloader.min.css') ?>">
    <link href="<?= base_url('assets/css/app.min.css') ?>" id="app-style" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="<?= base_url('assets/css/style.css') ?>">
    <?= $this->renderSection('link') ?>
</head>

<body data-topbar="dark" data-sidebar-size="sm">