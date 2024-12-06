<?php
// File: templates/nav.php
?>
<nav class="main-header navbar navbar-expand navbar-white navbar-light">
  <!-- Left navbar links -->
  <ul class="navbar-nav">
    <li class="nav-item">
      <a class="nav-link" href="/admin/dashboard" role="button"><i class="fas fa-home"></i> Dashboard</a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="/admin/pages" role="button"><i class="fas fa-file"></i> Pages</a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="/admin/media" role="button"><i class="fas fa-photo-video"></i> Media</a>
    </li>
    <!-- Add to nav.php and sidebar.php -->
    <li class="nav-item">
        <a href="/admin/page-views" class="nav-link">
            <i class="nav-icon fas fa-chart-line"></i>
            <p>Page Views</p>
        </a>
    </li>
  </ul>

  <!-- Right navbar links -->
  <ul class="navbar-nav ml-auto">
    <li class="nav-item">
      <a class="nav-link" href="/admin/logout" role="button"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </li>
  </ul>
</nav>
