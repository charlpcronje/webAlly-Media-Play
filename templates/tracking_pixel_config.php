<!-- File: templates/tracking_pixel_config.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Configure Tracking Pixels</title>
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
        <h1>Configure Tracking Pixels for Page ID: <?= htmlspecialchars($pageId) ?></h1>

        <?php if ($success): ?>
            <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <!-- Form to Add New Tracking Pixel -->
        <div class="card card-primary mb-4">
            <div class="card-header">
                <h3 class="card-title">Add New Tracking Pixel</h3>
            </div>
            <form action="/admin/tracking-pixel-config?page_id=<?= htmlspecialchars($pageId) ?>" method="post" enctype="multipart/form-data">
                <div class="card-body">
                    <div class="form-group">
                        <label for="pixel_name">Tracking Pixel Name</label>
                        <input type="text" name="pixel_name" id="pixel_name" class="form-control" placeholder="Enter unique name" required>
                    </div>
                    <div class="form-group">
                        <label for="pixel_image">Tracking Pixel Image (GIF or PNG)</label>
                        <input type="file" name="pixel_image" id="pixel_image" class="form-control-file" accept=".gif,.png" required>
                    </div>
                    <div class="form-group">
                        <label for="download_speed">Download Speed (Bytes per Second)</label>
                        <input type="number" name="download_speed" id="download_speed" class="form-control" min="0" value="1" required>
                        <small class="form-text text-muted">
                            Set to <strong>0</strong> for no limit, <strong>1</strong> for 1 byte per second, etc.
                        </small>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" name="add_pixel" class="btn btn-success"><i class="fas fa-plus"></i> Add Tracking Pixel</button>
                </div>
            </form>
        </div>

        <!-- Existing Tracking Pixels List -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Existing Tracking Pixels</h3>
            </div>
            <div class="card-body">
                <?php if (empty($trackingPixels)): ?>
                    <p>No tracking pixels configured for this page.</p>
                <?php else: ?>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Image</th>
                                <th>Download Speed (Bytes/sec)</th>
                                <th>Embed URL</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($trackingPixels as $tp): ?>
                            <tr>
                                <td><?= htmlspecialchars($tp->name) ?></td>
                                <td>
                                    <img src="<?= htmlspecialchars($tp->image_path) ?>" alt="<?= htmlspecialchars($tp->name) ?>" style="max-width: 100px;">
                                </td>
                                <td><?= htmlspecialchars($tp->download_speed) ?></td>
                                <td>
                                    <?php
                                        // Generate a unique tracking_id
                                        $trackingId = bin2hex(random_bytes(16));

                                        // Create a new tracking pixel session
                                        TrackingPixelSession::create($pageId, $trackingId, $tp->id);

                                        // Construct the embed URL
                                        $embedUrl = "https://" . $_SERVER['HTTP_HOST'] . "/tracking_pixel." . htmlspecialchars(pathinfo($tp->image_path, PATHINFO_EXTENSION)) . "?ext=" . htmlspecialchars(pathinfo($tp->image_path, PATHINFO_EXTENSION)) . "&page_id=" . urlencode($pageId) . "&tracking_id=" . urlencode($trackingId);
                                    ?>
                                    <input type="text" value="<?= htmlspecialchars($embedUrl) ?>" readonly class="form-control" onclick="this.select();" />
                                    <button onclick="copyToClipboard(this.previousElementSibling)" class="btn btn-sm btn-secondary mt-1"><i class="fas fa-copy"></i> Copy</button>
                                </td>
                                <td>
                                    <form action="/admin/tracking-pixel-config?page_id=<?= htmlspecialchars($pageId) ?>" method="post" onsubmit="return confirm('Are you sure you want to delete this tracking pixel?');">
                                        <input type="hidden" name="pixel_id" value="<?= htmlspecialchars($tp->id) ?>">
                                        <button type="submit" name="delete_pixel" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i> Delete</button>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>

    </div>
    <!-- /.content-wrapper -->
</div>
<!-- ./wrapper -->
<!-- AdminLTE & Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3/dist/js/adminlte.min.js"></script>
<script>
    function copyToClipboard(element) {
        element.select();
        element.setSelectionRange(0, 99999); // For mobile devices
        document.execCommand("copy");
        alert("Copied to clipboard: " + element.value);
    }
</script>
</body>
</html>
