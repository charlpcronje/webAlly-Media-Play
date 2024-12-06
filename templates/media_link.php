<!-- File: templates/media_link.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Link Media to Page</title>
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
        <h1>Link Media to Page</h1>

        <form action="/admin/media/link" method="post">
            <div class="form-group">
                <label for="media_id">Select Media</label>
                <select name="media_id" id="media_id" class="form-control" required>
                    <option value="">-- Select Media --</option>
                    <?php foreach ($mediaItems as $media): ?>
                        <option value="<?= htmlspecialchars($media->id) ?>"><?= htmlspecialchars(basename($media->file_path)) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="page_id">Select Page</label>
                <select name="page_id" id="page_id" class="form-control" required>
                    <option value="">-- Select Page --</option>
                    <?php foreach ($pages as $page): ?>
                        <option value="<?= htmlspecialchars($page->id) ?>"><?= htmlspecialchars($page->name) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary"><i class="fas fa-link"></i> Link Media</button>
        </form>
    </div>
    <!-- /.content-wrapper -->
</div>
<!-- ./wrapper -->
<!-- AdminLTE & Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3/dist/js/adminlte.min.js"></script>
</body>
</html>
