<?php
require_once __DIR__ . '/../config/db.php';


function getAllBranches()
{
    global $conn;

    $sql = "
        SELECT branch_id, branch_name
        FROM branches_tbl
        WHERE status = 'active'
        ORDER BY branch_name ASC
    ";

    $result = $conn->query($sql);

    $branches = [];

    while ($row = $result->fetch_assoc()) {
        $branches[] = $row;
    }

    return $branches;
}