<!-- File: templates/media_upload.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Upload Media</title>
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
        <h1>Upload Media</h1>

        <form action="/admin/media/upload" method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label for="media_file">Select File</label>
                <input type="file" name="media_file" id="media_file" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="media_type">Media Type</label>
                <select name="media_type" id="media_type" class="form-control" required>
                    <option value="image">Image</option>
                    <option value="video">Video</option>
                    <option value="audio">Audio</option>
                    <option value="other">Other</option>
                </select>
            </div>
            <div class="form-group">
                <label for="page_id">Link to Page (optional)</label>
                <select name="page_id" id="page_id" class="form-control">
                    <option value="">-- Unlinked --</option>
                    <?php foreach ($pages as $page): ?>
                        <option value="<?= htmlspecialchars($page->id) ?>"><?= htmlspecialchars($page->name) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="btn btn-success"><i class="fas fa-upload"></i> Upload Media</button>
        </form>
    </div>
    <!-- /.content-wrapper -->
</div>
<!-- ./wrapper -->
<!-- AdminLTE & Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3/dist/js/adminlte.min.js"></script>
</body>
</html>
