<!-- File: templates/page_views_list.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Page Views</title>
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
        <h1>Page Views</h1>

        <?php if (empty($pageViews)): ?>
            <p>No page views recorded.</p>
        <?php else: ?>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Page Name</th>
                        <th>Session ID</th>
                        <th>Start Time</th>
                        <th>End Time</th>
                        <th>Duration (seconds)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pageViews as $pv): ?>
                    <tr>
                        <td><?= htmlspecialchars($pv['id']) ?></td>
                        <td><?= htmlspecialchars($pv['page_name']) ?></td>
                        <td><?= htmlspecialchars($pv['session_id']) ?></td>
                        <td><?= htmlspecialchars($pv['start_time']) ?></td>
                        <td><?= htmlspecialchars($pv['end_time']) ?></td>
                        <td><?= htmlspecialchars($pv['duration_seconds']) ?></td>
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
