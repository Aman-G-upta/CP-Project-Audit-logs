<?php
include("../config/db.php");

$search    = $_GET['search'] ?? '';
$action    = $_GET['operation'] ?? '';
$startDate = $_GET['start_date'] ?? '';
$endDate   = $_GET['end_date'] ?? '';

$limit = 20; 
$page  = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $limit;

$whereSQL = " WHERE 1=1";
$types = "";
$params = [];

if (!empty($search)) {
    $whereSQL .= " AND actor LIKE ?";
    $types .= "s";
    $params[] = "%" . $search . "%";
}
if (!empty($action)) {
    $whereSQL .= " AND action = ?";
    $types .= "s";
    $params[] = $action;
}
if (!empty($startDate) && !empty($endDate)) {
    $whereSQL .= " AND created_at BETWEEN ? AND ?";
    $types .= "ss";
    $params[] = $startDate . " 00:00:00";
    $params[] = $endDate . " 23:59:59";
}

if (isset($_GET['export'])) {
    $sql = "SELECT * FROM audit_log $whereSQL ORDER BY created_at DESC";
    $stmt = $conn->prepare($sql);
    if (!empty($params)) { $stmt->bind_param($types, ...$params); }
    $stmt->execute();
    $result = $stmt->get_result();

    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="audit_logs_' . date('Y-m-d') . '.csv"');
    $output = fopen('php://output', 'w');
    fputcsv($output, ['Record ID', 'Time', 'User', 'IP Address', 'Module', 'Action', 'Old Data', 'New Data']);
    
    while ($row = $result->fetch_assoc()) {
        fputcsv($output, [
            $row['record_id'], $row['created_at'], $row['actor'], 
            $row['ip_address'], $row['module'], $row['action'], 
            $row['old_data'], $row['new_data']
        ]);
    }
    fclose($output);
    exit();
}

$countSql = "SELECT COUNT(*) as total FROM audit_log $whereSQL";
$stmt = $conn->prepare($countSql);
if (!empty($params)) { $stmt->bind_param($types, ...$params); }
$stmt->execute();
$countResult = $stmt->get_result();
$totalRecords = $countResult->fetch_assoc()['total'];
$totalPages = ceil($totalRecords / $limit);

$sql = "SELECT * FROM audit_log $whereSQL ORDER BY created_at DESC LIMIT ? OFFSET ?";
$types .= "ii";
$params[] = $limit;
$params[] = $offset;

$stmt = $conn->prepare($sql);
if (!empty($params)) { $stmt->bind_param($types, ...$params); }
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Enterprise Activity Log Viewer</title>
    <link rel="stylesheet" href="../css/logs.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>

    <main class="main-content">
        <div class="header-title">Audit Logs</div>

        <form method='GET' class="filter-bar">
            <input type="hidden" name="page" value="1"> 

            <div class="filter-group">
                <label>Search User</label>
                <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="e.g. John Doe" class="filter-input">
            </div>

            <div class="filter-group">
                <label>Action</label>
                <select name="operation" class="filter-input">
                    <option value="">All Actions</option>
                    <option value="insert" <?= $action == 'insert' ? 'selected' : '' ?>>Insert</option>
                    <option value="update" <?= $action == 'update' ? 'selected' : '' ?>>Update</option>
                    <option value="delete" <?= $action == 'delete' ? 'selected' : '' ?>>Delete</option>
                </select>
            </div>

            <div class="filter-group">
                <label>From</label>
                <input type="date" name="start_date" value="<?= $startDate ?>" class="filter-input">
            </div>

            <div class="filter-group">
                <label>To</label>
                <input type="date" name="end_date" value="<?= $endDate ?>" class="filter-input">
            </div>

            <button class="btn-apply" type="submit">Filter</button>
            <button class="btn-export" type="submit" name="export" value="true">
                <span>&#11015;</span> Export CSV
            </button>
        </form>

        <?php if ($totalRecords > 0): ?>
            <div class="pagination-container">
                <div class="page-count">
                    <?php
                        $startItem = $offset + 1;
                        $endItem = min($offset + $limit, $totalRecords);
                    ?>
                    Showing <b><?= $startItem ?>-<?= $endItem ?></b> of <b><?= $totalRecords ?></b>
                </div>

                <?php if ($totalPages > 1): ?>
                <div class="pagination-buttons">
                    <?php if ($page > 1): ?>
                        <a href="?page=<?= $page - 1 ?>&search=<?= $search ?>&operation=<?= $action ?>&start_date=<?= $startDate ?>&end_date=<?= $endDate ?>" class="page-link">Previous</a>
                    <?php else: ?>
                        <span class="page-link disabled">Previous</span>
                    <?php endif; ?>

                    <?php if ($page < $totalPages): ?>
                        <a href="?page=<?= $page + 1 ?>&search=<?= $search ?>&operation=<?= $action ?>&start_date=<?= $startDate ?>&end_date=<?= $endDate ?>" class="page-link">Next</a>
                    <?php else: ?>
                        <span class="page-link disabled">Next</span>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th width="8%">Record ID</th>
                        <th width="12%">Time</th>
                        <th width="12%">User</th>
                        <th width="10%">IP Address</th>
                        <th width="10%">Module</th>
                        <th width="8%">Action</th>
                        <th>Changes (Old &rarr; New)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result->num_rows > 0): ?>
                        <?php while($row = $result->fetch_assoc()) { ?>
                        <tr>
                            <td style="font-weight: 600; color: #111827;"><?= $row['record_id'] ?></td>
                            
                            <td style="color: #6b7280; font-size:13px;"><?= date('M d, Y H:i', strtotime($row['created_at'])) ?></td>
                            <td style="font-weight: 500; color: #111827;"><?= $row['actor'] ?></td>
                            <td style="font-family: monospace; color: #6b7280;"><?= $row['ip_address'] ?></td>
                            <td><?= $row['module'] ?></td>
                            
                            <td>
                                <?php 
                                    $badgeColor = '#374151'; 
                                    $bgBadge = '#f3f4f6';
                                    if($row['action'] == 'insert') { $badgeColor = '#059669'; $bgBadge = '#d1fae5'; } 
                                    if($row['action'] == 'update') { $badgeColor = '#2563eb'; $bgBadge = '#dbeafe'; } 
                                    if($row['action'] == 'delete') { $badgeColor = '#dc2626'; $bgBadge = '#fee2e2'; } 
                                ?>
                                <span style="background:<?=$bgBadge?>; color:<?=$badgeColor?>; padding: 3px 8px; border-radius: 99px; font-size: 11px; font-weight: 700; text-transform: uppercase;">
                                    <?= $row['action'] ?>
                                </span>
                            </td>

                            <td><div class="code-diff"><?= $row['old_data'] ?> &rarr; <?= $row['new_data'] ?></div></td>
                        </tr>
                        <?php } ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" style="text-align:center; padding: 40px; color: #6b7280;">
                                No activity logs found.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

    </main>
</body>
</html>
