   <?php
    helper('menu');
    $arr_menu = getMenu();
    ?>
   <!-- ========== Left Sidebar Start ========== -->
   <div class="vertical-menu">
       <div data-simplebar class="h-100">
           <!--- Sidemenu -->
           <div id="sidebar-menu">
               <!-- Left Menu Start -->
               <ul class="metismenu list-unstyled" id="side-menu">
                   <li class="menu-title" data-key="t-menu">Menu</li>
                   <?php foreach ($arr_menu as $key_section => $section) : ?>
                       <li>
                           <a href="javascript: void(0);" class="has-arrow menu-hover"><i data-feather="<?= $section[0]->icone_section ?>"></i><span><?= $key_section ?></span></a>
                           <ul class=" sub-menu menu-hover" aria-expanded="false">
                               <?php foreach ($section as $key_page => $value_page) : ?>
                                   <li>
                                       <a href="<?= base_url("$value_page->lien") ?>" class="menu-hover nav-link" key="t-products"><?= $value_page->page ?></a>
                                   </li>
                               <?php endforeach; ?>
                           </ul>
                       </li>
                   <?php endforeach; ?>
               </ul>
           </div>
       </div>
   </div>
   <!-- Left Sidebar End -->