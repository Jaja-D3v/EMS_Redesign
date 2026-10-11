<?php
require_once __DIR__ . '/../config/db.php';


function getExtinguisher($branch = 'all')
{
    global $conn;

    $sql = "
        SELECT
            extinguisher_id,
            extinguisher_code,
            type,
            location,
            branch
        FROM fire_extinguishers_tbl
        WHERE archived = 0
    ";

    // Kapag hindi all, branch-specific
    if ($branch !== 'all') {
        $sql .= "
            AND LOWER(TRIM(branch)) = LOWER(TRIM(?))
        ";
    }

    $sql .= " ORDER BY extinguisher_code ASC";

    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        return false;
    }

    if ($branch !== 'all') {
        $stmt->bind_param("s", $branch);
    }

    $stmt->execute();

    return $stmt->get_result();
}

function getBranches()
{
    global $conn;

    $sql = "
        SELECT DISTINCT branch
        FROM fire_extinguishers_tbl
        WHERE archived = 0
          AND branch IS NOT NULL
          AND TRIM(branch) != ''
        ORDER BY branch ASC
    ";

    return $conn->query($sql);
}