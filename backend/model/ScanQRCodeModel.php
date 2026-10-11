<?php

require_once __DIR__ . '/../config/db.php';

function getFeInfoByCode($code)
{
    global $conn;

    $sql = "
        SELECT
            extinguisher_code,
            location,
            type,
            capacity,
            class,
            branch
        FROM fire_extinguishers_tbl
        WHERE extinguisher_code = ?
    ";

    $stmt = mysqli_prepare($conn, $sql);

    if (!$stmt) {
        return false;
    }

    mysqli_stmt_bind_param(
        $stmt,
        "s",
        $code
    );

    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);

    return mysqli_fetch_assoc($result);
}