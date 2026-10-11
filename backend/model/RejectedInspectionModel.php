<?php 
require_once __DIR__ . '/../config/db.php';



function getRejectedInspections(
    $inspectedBy,
    $branch,
    $search = '',
    $dateFilter = 'all'
) {
    global $conn;

    $sql = "
        SELECT
            inspect_id,
            extinguisher_code,
            branch,
            location,
            capacity,
            type,
            date_inspected,
            inspected_by,
            evaluation_status,
            rejected_by,
            reject_reason,
            action_taken,
            target_date_of_implementation,
            rejection_date
        FROM inspection_checklist_tbl AS i
        WHERE evaluation_status = 'Rejected'
        AND LOWER(TRIM(inspected_by)) = LOWER(TRIM(?))
        AND LOWER(TRIM(branch)) = LOWER(TRIM(?))
        AND inspect_id = (
            SELECT i2.inspect_id
            FROM inspection_checklist_tbl AS i2
            WHERE LOWER(TRIM(i2.extinguisher_code)) =
                  LOWER(TRIM(i.extinguisher_code))
            ORDER BY i2.date_inspected DESC, i2.inspect_id DESC
            LIMIT 1
        )
    ";

    $types = 'ss';
    $params = [$inspectedBy, $branch];

    if ($search !== '') {
        $sql .= "
            AND (
                extinguisher_code LIKE ?
                OR location LIKE ?
                OR reject_reason LIKE ?
            )
        ";

        $keyword = '%' . $search . '%';

        $types .= 'sss';

        array_push(
            $params,
            $keyword,
            $keyword,
            $keyword
        );
    }

    switch ($dateFilter) {
        case 'today':
            $sql .= " AND DATE(date_inspected) = CURDATE()";
            break;

        case 'yesterday':
            $sql .= " AND DATE(date_inspected) = DATE_SUB(CURDATE(), INTERVAL 1 DAY)";
            break;

        case '7':
            $sql .= " AND date_inspected >= DATE_SUB(NOW(), INTERVAL 7 DAY)";
            break;

        case '30':
            $sql .= " AND date_inspected >= DATE_SUB(NOW(), INTERVAL 30 DAY)";
            break;
    }

    $sql .= " ORDER BY date_inspected DESC, inspect_id DESC";

    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        return false;
    }

    $stmt->bind_param($types, ...$params);

    if (!$stmt->execute()) {
        $stmt->close();
        return false;
    }

    $result = $stmt->get_result();
    $data = $result->fetch_all(MYSQLI_ASSOC);

    $stmt->close();

    return $data;
}