<!-- File: templates/dashboard.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <!-- AdminLTE CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3/dist/css/adminlte.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free/css/all.min.css">
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">
    <?php include __DIR__ . '/nav.php'; ?>
    <?php include __DIR__ . '/sidebar.php'; ?>

    <!-- Content Wrapper -->
    <div class="content-wrapper p-3">
        <h1>Dashboard</h1>
        <p>Welcome to the Media Tracker Admin Dashboard.</p>

        <!-- Dashboard content here -->
        <div class="row">
            <div class="col-lg-3 col-6">
                <!-- small box -->
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3><?= count($pages) ?></h3>
                        <p>Pages</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-file"></i>
                    </div>
                    <a href="/admin/pages" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
            <!-- ./col -->
            <div class="col-lg-3 col-6">
                <!-- small box -->
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3><?= count($media) ?></h3>
                        <p>Media</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-photo-video"></i>
                    </div>
                    <a href="/admin/media" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
            <!-- ./col -->
            <!-- Add more boxes as needed -->
        </div>

    </div>
    <!-- /.content-wrapper -->
</div>
<!-- ./wrapper -->
<!-- AdminLTE & Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3/dist/js/adminlte.min.js"></script>
</body>
</html>
