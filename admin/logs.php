<?php
include("../config/db.php");

// where=[];
$query = "SELECT * FROM audit_log";

if(!empty($_GET['operation'])){
    $operation=$_GET['operation'];
    $query .=" WHERE action = '$operation'";
    // $where[]="operation= '$operation'";

}
$query .= " ORDER BY created_at DESC";

// $query = "SELECT * FROM audit_log ORDER BY created_at DESC";
$result = $conn->query($query);

// echo $query;

?>

<!DOCTYPE html>
<html>
<head>
    <title>Activity Log Viewer</title>
    <link rel="stylesheet" href="../css/logs.css">
</head>
<body>

<h2>Activity Log Viewer</h2>


<form method='GET' class="form">

    Action :
    <select name="operation" id="" >
        <option value="">All</option>
        <option value="insert">Insert</option>
        <option value="update">Update</option>
        <option value="delete">Delete</option>
    </select>

    <button class="button" type="submit" >Apply</button>


</form>


<div style="max-width:100%; overflow-x:auto;">

    <table>
        <tr>
            <th>ID</th>
            <th>User</th>
            <th>Module</th>
            <th>Action</th>
            <th>Record ID</th>
            <th>IP Address</th>
            <th>Time</th>
            <th>Old Data</th>
            <th>New Data</th>
        </tr>
        
        <?php while($row = $result->fetch_assoc()) { ?>
        <tr>
            <td><?= $row['id'] ?></td>
            <td><?= $row['actor'] ?></td>
            <td><?= $row['module'] ?></td>
            <td><?= $row['action'] ?></td>
            <td><?= $row['record_id'] ?></td>
            <td><?= $row['ip_address'] ?></td>
            <td><?= $row['created_at'] ?></td>
            <td><?= $row['old_data']?></td>
            <td><?= $row['new_data'] ?></td>
        </tr>
        <?php } ?>
        
    </table>
</div>

</body>
</html>
