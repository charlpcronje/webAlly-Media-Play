<!-- File: templates/pages_list.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Pages</title>
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
        <h1>Pages</h1>

        <a href="/admin/pages/create" class="btn btn-primary mb-3"><i class="fas fa-plus"></i> Create New Page</a>

        <?php if (empty($pages)): ?>
            <p>No pages found.</p>
        <?php else: ?>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Slug</th>
                        <th>Archived</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pages as $page): ?>
                    <tr>
                        <td><?= htmlspecialchars($page->id) ?></td>
                        <td><?= htmlspecialchars($page->name) ?></td>
                        <td><?= htmlspecialchars($page->slug) ?></td>
                        <td><?= $page->is_archived ? 'Yes' : 'No' ?></td>
                        <td>
                            <a href="/admin/pages/edit/<?= htmlspecialchars($page->id) ?>" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i> Edit</a>
                            <?php if (!$page->is_archived): ?>
                                <form action="/admin/pages/archive/<?= htmlspecialchars($page->id) ?>" method="post" style="display:inline;" onsubmit="return confirm('Are you sure you want to archive this page?');">
                                    <button type="submit" class="btn btn-sm btn-danger"><i class="fas fa-archive"></i> Archive</button>
                                </form>
                            <?php endif; ?>
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
