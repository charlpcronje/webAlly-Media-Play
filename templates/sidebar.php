<!-- File: templates/sidebar.php -->
<?php
use ..\Models\Page;

$pages = Page::all(true); // Include archived
?>
<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="/admin/dashboard" class="brand-link">
        <span class="brand-text font-weight-light">Media Tracker</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar Menu -->
        <nav class="mt-2">
          <ul class="nav nav-pills nav-sidebar flex-column">
              <li class="nav-item">
                  <a href="/admin/pages" class="nav-link">
                      <i class="nav-icon fas fa-file"></i>
                      <p>Pages</p>
                  </a>
              </li>
              <li class="nav-item">
                  <a href="/admin/media" class="nav-link">
                      <i class="nav-icon fas fa-photo-video"></i>
                      <p>Media</p>
                  </a>
              </li>
              <li class="nav-item">
                  <a href="#" class="nav-link">
                      <i class="nav-icon fas fa-cog"></i>
                      <p>
                          Tracking Pixels
                          <i class="right fas fa-angle-left"></i>
                      </p>
                  </a>
                  <ul class="nav nav-treeview">
                      <?php foreach ($pages as $p): ?>
                      <li class="nav-item">
                          <a href="/admin/tracking-pixel-config?page_id=<?= htmlspecialchars($p->id) ?>" class="nav-link">
                              <i class="far fa-circle nav-icon"></i>
                              <p><?= htmlspecialchars($p->name) ?></p>
                          </a>
                      </li>
                      <?php endforeach; ?>
                  </ul>
              </li>
              <!-- Add more navigation items here -->
          </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
</aside>
