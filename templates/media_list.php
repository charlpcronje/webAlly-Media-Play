<!-- File: templates/media_list.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Media</title>
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
        <h1>Media</h1>

        <a href="/admin/media/upload" class="btn btn-primary mb-3"><i class="fas fa-upload"></i> Upload Media</a>
        <a href="/admin/media/link" class="btn btn-secondary mb-3"><i class="fas fa-link"></i> Link Media to Page</a>

        <?php if (empty($mediaItems)): ?>
            <p>No media found.</p>
        <?php else: ?>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>File</th>
                        <th>Type</th>
                        <th>Page</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($mediaItems as $media): ?>
                    <tr>
                        <td><?= htmlspecialchars($media->id) ?></td>
                        <td>
                            <a href="<?= htmlspecialchars($media->file_path) ?>" target="_blank">
                                <?= htmlspecialchars(basename($media->file_path)) ?>
                            </a>
                        </td>
                        <td><?= htmlspecialchars($media->media_type) ?></td>
                        <td><?= $media->page_id ? htmlspecialchars(Page::find($media->page_id)->name) : 'Unlinked' ?></td>
                        <td>
                            <a href="<?= htmlspecialchars($media->file_path) ?>" target="_blank" class="btn btn-sm btn-info"><i class="fas fa-eye"></i> View</a>
                            <!-- Add more actions if needed -->
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
    <!-- /.content-wrapper -->
</div>
<!-- ./wrapper -->
<!-- AdminLTE & Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3/dist/js/adminlte.min.js"></script>
</body>
</html>
