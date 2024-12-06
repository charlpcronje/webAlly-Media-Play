<!-- File: templates/pages_create.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create Page</title>
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
        <h1>Create New Page</h1>

        <form action="/admin/pages/create" method="post">
            <div class="form-group">
                <label for="name">Page Name</label>
                <input type="text" name="name" id="name" class="form-control" placeholder="Enter page name" required>
            </div>
            <div class="form-group">
                <label for="slug">Page Slug</label>
                <input type="text" name="slug" id="slug" class="form-control" placeholder="Enter page slug (unique)" required>
            </div>
            <button type="submit" class="btn btn-success"><i class="fas fa-save"></i> Create Page</button>
        </form>
    </div>
    <!-- /.content-wrapper -->
</div>
<!-- ./wrapper -->
<!-- AdminLTE & Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3/dist/js/adminlte.min.js"></script>
</body>
</html>
