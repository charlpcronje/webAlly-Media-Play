<!-- File: templates/pages_edit.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Page</title>
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
        <h1>Edit Page</h1>

        <form action="/admin/pages/edit/<?= htmlspecialchars($page->id) ?>" method="post">
            <div class="form-group">
                <label for="name">Page Name</label>
                <input type="text" name="name" id="name" class="form-control" value="<?= htmlspecialchars($page->name) ?>" required>
            </div>
            <div class="form-group">
                <label for="slug">Page Slug</label>
                <input type="text" name="slug" id="slug" class="form-control" value="<?= htmlspecialchars($page->slug) ?>" required>
            </div>
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Update Page</button>
        </form>
    </div>
    <!-- /.content-wrapper -->
</div>
<!-- ./wrapper -->
<!-- AdminLTE & Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3/dist/js/adminlte.min.js"></script>
</body>
</html>
