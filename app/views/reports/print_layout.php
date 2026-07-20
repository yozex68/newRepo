<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?> | SmartHUB Report Export</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        body {
            background-color: #ffffff;
            color: #000000;
            font-family: Arial, sans-serif;
            padding: 30px;
        }
        .report-header {
            border-bottom: 2px solid #000000;
            padding-bottom: 15px;
            margin-bottom: 30px;
        }
        .table th {
            background-color: #f8fafc !important;
            color: #000000 !important;
            border-bottom: 2px solid #000000 !important;
        }
        @media print {
            .no-print {
                display: none;
            }
            body {
                padding: 0;
            }
        }
    </style>
</head>
<body onload="window.print()">

    <div class="container-fluid">
        <!-- Report Header -->
        <div class="report-header d-flex justify-content-between align-items-end">
            <div>
                <h1 class="h3 fw-bold mb-1">SmartHUB Digital Library</h1>
                <h2 class="h5 text-secondary mb-0"><?= $title ?></h2>
            </div>
            <div class="text-end text-muted small">
                <div>Export Date: <?= date('Y-m-d H:i:s') ?></div>
                <div class="no-print mt-2">
                    <button onclick="window.print()" class="btn btn-sm btn-dark"><i class="fas fa-print"></i> Print</button>
                    <button onclick="window.close()" class="btn btn-sm btn-outline-secondary">Close</button>
                </div>
            </div>
        </div>

        <!-- Data Grid Table -->
        <div class="table-responsive">
            <table class="table table-bordered table-striped align-middle" style="font-size: 0.85rem;">
                <thead>
                    <tr>
                        <?php foreach ($headers as $h): ?>
                            <th><?= $h ?></th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($data)): ?>
                        <?php foreach ($data as $row): ?>
                            <tr>
                                <?php foreach ($row as $val): ?>
                                    <td><?= htmlspecialchars($val, ENT_QUOTES, 'UTF-8') ?></td>
                                <?php endforeach; ?>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="<?= count($headers) ?>" class="text-center py-4">No records found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Footer -->
        <div class="mt-5 border-top pt-3 text-center text-muted" style="font-size: 0.75rem;">
            &copy; <?= date('Y') ?> SmartHUB Digital Library - BIT 2 Internet and Web Development Report.
        </div>
    </div>

</body>
</html>
