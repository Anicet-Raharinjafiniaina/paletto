     <?php
        helper('user');
        ?>
     <style>
         @media (max-width: 768px) {
             .navbar-header .navbar-brand-box {
                 order: 2;
             }

             .navbar-header #vertical-menu-btn {
                 order: 1;
             }
         }
     </style>
     <header id="page-topbar">
         <div class="navbar-header">
             <div class="d-flex">
                 <!-- LOGO -->
                 <div class="navbar-brand-box">
                     <a href="index.html" class="logo logo-light">
                         <span class="logo-sm">
                             <img src="assets/images/logoBoost.png" alt="" height="30">
                         </span>
                         <span class="logo-lg">
                             <img src="assets/images/logoBoost.png" alt="" height="24">
                         </span>
                     </a>
                 </div>

                 <button type="button" class="btn btn-sm px-3 font-size-16 header-item d-lg-none" id="vertical-menu-btn">
                     <i class="fa fa-fw fa-bars"></i>
                 </button>
             </div>

             <div class="d-flex">
                 <div class="dropdown d-inline-block">
                     <button type="button" class="btn header-item bg-soft-light border-start border-end" id="page-header-user-dropdown"
                         data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">

                         <img class="rounded-circle header-profile-user" src="<?= get_user_photo() ?>"" alt="">
                         <span class=" d-none d-xl-inline-block ms-1 fw-medium"><?= session()->get('utilisateur')['login'] . " - " . session()->get('utilisateur')['prenom'] . " " . session()->get('utilisateur')['nom'] ?? "" ?></span>
                         <i class="mdi mdi-chevron-down d-none d-xl-inline-block"></i>
                     </button>
                     <div class="dropdown-menu dropdown-menu-end">
                         <div class="d-flex align-items-center px-3 py-2 d-xl-none">
                             <i class="mdi mdi-account font-size-16 me-2"></i>
                             <span class="fw-medium"><i><u>
                                         <?= session()->get('utilisateur')['login'] . " - " . session()->get('utilisateur')['prenom'] . " " . session()->get('utilisateur')['nom'] ?? "" ?>
                                 </i></u> </span>
                         </div>
                         <div class="dropdown-divider"></div>
                         <a class="dropdown-item" href="<?= base_url("/Login/logout") ?>"><i class="mdi mdi-logout font-size-16 align-middle me-1"></i> Se déconnecter</a>
                     </div>
                 </div>
             </div>
         </div>
     </header>

     <input type="checkbox" id="layout-horizontal" hidden>
     <input type="checkbox" id="layout-vertical" hidden>
     <input type="checkbox" id="sidebar-size-compact" hidden>
     <input type="checkbox" id="sidebar-color-brand" hidden>

     <?= $this->include('layout/menu'); ?>