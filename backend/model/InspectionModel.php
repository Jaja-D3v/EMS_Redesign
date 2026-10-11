<?php
require_once __DIR__ . '/../config/db.php';

// funciton for add inspection list
function addInspectionChecklist(
    $extinguisher_code,
    $location,
    $capacity,
    $type,
    $class,
    $date_inspected,
    $inspected_by,
    $verified_and_approved_by,
    $action_taken,
    $target_date_of_implementation,
    $is_seal_ok,
    $is_pin_ok,
    $is_pressure_ok,
    $is_hose_ok,
    $is_nozzle_ok,
    $is_belt_ok,
    $is_cylinder_body_ok,
    $is_demarcation_line_ok,
    $is_signage_ok,
    $is_cleaning_of_unit_ok,
    $status,
    $branch,
    $remarks
) {

    global $conn;

    // 1. Insert inspection checklist
    $sql = "INSERT INTO inspection_checklist_tbl
            (
                extinguisher_code,
                location,
                capacity,
                type,
                class,
                date_inspected,
                inspected_by,
                verified_and_approved_by,
                action_taken,
                target_date_of_implementation,
                is_seal_ok,
                is_pin_ok,
                is_pressure_ok,
                is_hose_ok,
                is_nozzle_ok,
                is_belt_ok,
                is_cylinder_body_ok,
                is_demarcation_line_ok,
                is_signage_ok,
                is_cleaning_of_unit_ok,
                status,
                branch,
                remarks
            )
            VALUES (?, ?, ?, ?, ?, NOW(), ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = mysqli_prepare($conn, $sql);

    mysqli_stmt_bind_param(
        $stmt,
        "sssssssssiiiiiiiiiiiss",
        $extinguisher_code,
        $location,
        $capacity,
        $type,
        $class,
        $inspected_by,
        $verified_and_approved_by,
        $action_taken,
        $target_date_of_implementation,
        $is_seal_ok,
        $is_pin_ok,
        $is_pressure_ok,
        $is_hose_ok,
        $is_nozzle_ok,
        $is_belt_ok,
        $is_cylinder_body_ok,
        $is_demarcation_line_ok,
        $is_signage_ok,
        $is_cleaning_of_unit_ok,
        $status,
        $branch,
        $remarks
    );

    if (!mysqli_stmt_execute($stmt)) {
        return false;
    }

    // 2. Update fire_extinguishers_tbl
    $updateSql = "UPDATE fire_extinguishers_tbl
              SET last_date_inspected = NOW(),
                  inspected_by = ?
              WHERE extinguisher_code = ?";

    $updateStmt = mysqli_prepare($conn, $updateSql);

    mysqli_stmt_bind_param(
        $updateStmt,
        "ss",
        $inspected_by,
        $extinguisher_code
    );

    return mysqli_stmt_execute($updateStmt);
}


// Get current logged-in branch
function getBranch()
{
    return $_SESSION['Branch'] ?? null;
}


// ============================================================
// GET ALL LIST
// ============================================================

function getAllInspectionCheckList()
{
    global $conn;

    $branch = getBranch();

    if (empty($branch)) {
        return [];
    }

    $sql = "SELECT *
            FROM inspection_checklist_tbl
            WHERE LOWER(TRIM(branch)) = LOWER(TRIM(?))
            ORDER BY inspect_id DESC";

    $stmt = mysqli_prepare($conn, $sql);

    if (!$stmt) {
        return [];
    }

    mysqli_stmt_bind_param($stmt, "s", $branch);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);

    $data = mysqli_fetch_all($result, MYSQLI_ASSOC);

    mysqli_stmt_close($stmt);

    return $data;
}


// ============================================================
// GET ALL LIST BY ID
// ============================================================

function getInspectionCheckListById($id)
{
    global $conn;

    $sql = "SELECT *
            FROM inspection_checklist_tbl
            WHERE inspect_id = ?
            LIMIT 1";

    $stmt = mysqli_prepare($conn, $sql);

    if (!$stmt) {
        return null;
    }

    mysqli_stmt_bind_param(
        $stmt,
        "i",
        $id
    );

    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);

    $inspection = mysqli_fetch_assoc($result);

    mysqli_stmt_close($stmt);

    return $inspection ?: null;
}



// ============================================================
// GET ALL PENDING APPROVAL
// ============================================================
function getAllPendingApproval(
    $limit = 10,
    $offset = 0,
    $date = null,
    $branch = 'all'
) {
    global $conn;

    $sql = "SELECT *
            FROM inspection_checklist_tbl
            WHERE 1 = 1";

    $types = "";
    $params = [];

    // ========================================================
    // DATE FILTER - AS OF SELECTED DATE
    // ========================================================
    //
    // Example:
    // Filter = 2026-10-06
    //
    // Includes:
    // 2026-10-06 08:00:00
    // 2026-10-06 14:30:00
    // 2026-10-06 23:59:59
    //
    // Excludes:
    // 2026-10-07 00:00:00
    //
    // ========================================================

    if (!empty($date)) {

        $sql .= "
            AND date_inspected < DATE_ADD(?, INTERVAL 1 DAY)
        ";

        $types .= "s";
        $params[] = $date;
    }

    // ========================================================
    // BRANCH FILTER
    // ========================================================

    if ($branch !== 'all' && !empty($branch)) {

        $sql .= "
            AND LOWER(TRIM(branch)) = LOWER(TRIM(?))
        ";

        $types .= "s";
        $params[] = $branch;
    }

    // ========================================================
    // LATEST RECORD PER EXTINGUISHER CODE
    // AS OF SELECTED DATE
    // ========================================================
    //
    // date_inspected = primary basis
    // time = included automatically because DATETIME is compared
    // inspect_id = tie-breaker if exact same datetime
    //
    // ========================================================

    $sql .= "
        AND NOT EXISTS (
            SELECT 1
            FROM inspection_checklist_tbl AS newer

            WHERE newer.extinguisher_code =
                  inspection_checklist_tbl.extinguisher_code

            AND (
                newer.date_inspected >
                inspection_checklist_tbl.date_inspected

                OR (
                    newer.date_inspected =
                    inspection_checklist_tbl.date_inspected

                    AND newer.inspect_id >
                    inspection_checklist_tbl.inspect_id
                )
            )
    ";

    // ========================================================
    // NEWER RECORD MUST ALSO BE WITHIN SELECTED DATE
    // ========================================================

    if (!empty($date)) {

        $sql .= "
            AND newer.date_inspected < DATE_ADD(?, INTERVAL 1 DAY)
        ";

        $types .= "s";
        $params[] = $date;
    }

    $sql .= "
        )
    ";

    // ========================================================
    // STATUS
    // ========================================================

    $sql .= "
        AND evaluation_status = 'Pending'
    ";

    // ========================================================
    // PAGINATION
    // ========================================================

    $sql .= "
        ORDER BY date_inspected DESC, inspect_id DESC
        LIMIT ? OFFSET ?
    ";

    $types .= "ii";

    $params[] = (int) $limit;
    $params[] = (int) $offset;

    // ========================================================
    // PREPARE
    // ========================================================

    $stmt = mysqli_prepare($conn, $sql);

    if (!$stmt) {
        return false;
    }

    // ========================================================
    // BIND PARAMETERS
    // ========================================================

    if (!empty($types)) {

        mysqli_stmt_bind_param(
            $stmt,
            $types,
            ...$params
        );
    }

    // ========================================================
    // EXECUTE
    // ========================================================

    if (!mysqli_stmt_execute($stmt)) {
        mysqli_stmt_close($stmt);
        return false;
    }

    // ========================================================
    // RESULT
    // ========================================================

    $result = mysqli_stmt_get_result($stmt);

    mysqli_stmt_close($stmt);

    return $result;
}


// ============================================================
// FOR COUNT ALL PENDING APPROVAL
// ============================================================

function getPendingApprovalCount(
    $date = null,
    $branch = 'all'
) {
    global $conn;

    $sql = "SELECT COUNT(*) AS total
            FROM inspection_checklist_tbl
            WHERE 1 = 1";

    $types = "";
    $params = [];

    // ========================================================
    // DATE FILTER - AS OF SELECTED DATE
    // ========================================================

    if (!empty($date)) {

        $sql .= "
            AND date_inspected < DATE_ADD(?, INTERVAL 1 DAY)
        ";

        $types .= "s";
        $params[] = $date;
    }

    // ========================================================
    // BRANCH FILTER
    // ========================================================

    if ($branch !== 'all' && !empty($branch)) {

        $sql .= "
            AND LOWER(TRIM(branch)) = LOWER(TRIM(?))
        ";

        $types .= "s";
        $params[] = $branch;
    }

    // ========================================================
    // LATEST RECORD PER EXTINGUISHER CODE
    // AS OF SELECTED DATE
    // ========================================================

    $sql .= "
        AND NOT EXISTS (
            SELECT 1
            FROM inspection_checklist_tbl AS newer

            WHERE newer.extinguisher_code =
                  inspection_checklist_tbl.extinguisher_code

            AND (
                newer.date_inspected >
                inspection_checklist_tbl.date_inspected

                OR (
                    newer.date_inspected =
                    inspection_checklist_tbl.date_inspected

                    AND newer.inspect_id >
                    inspection_checklist_tbl.inspect_id
                )
            )
    ";

    // ========================================================
    // NEWER RECORD MUST ALSO BE WITHIN SELECTED DATE
    // ========================================================

    if (!empty($date)) {

        $sql .= "
            AND newer.date_inspected < DATE_ADD(?, INTERVAL 1 DAY)
        ";

        $types .= "s";
        $params[] = $date;
    }

    $sql .= "
        )
    ";

    // ========================================================
    // STATUS
    // ========================================================

    $sql .= "
        AND evaluation_status = 'Pending'
    ";

    // ========================================================
    // PREPARE
    // ========================================================

    $stmt = mysqli_prepare($conn, $sql);

    if (!$stmt) {
        return 0;
    }

    // ========================================================
    // BIND PARAMETERS
    // ========================================================

    if (!empty($types)) {

        mysqli_stmt_bind_param(
            $stmt,
            $types,
            ...$params
        );
    }

    // ========================================================
    // EXECUTE
    // ========================================================

    if (!mysqli_stmt_execute($stmt)) {
        mysqli_stmt_close($stmt);
        return 0;
    }

    // ========================================================
    // RESULT
    // ========================================================

    $result = mysqli_stmt_get_result($stmt);

    $row = mysqli_fetch_assoc($result);

    mysqli_stmt_close($stmt);

    return (int) ($row['total'] ?? 0);
}


// For generating report
function getAllApprovedApprovalIds($date = null, $branch = null)
{
    global $conn;

    // Default branch kapag walang ipinasa
    if ($branch === null || $branch === '') {
        $branch = isset($_SESSION['Branch'])
            ? $_SESSION['Branch']
            : 'all';
    }

    $sql = "
        SELECT inspection_checklist_tbl.inspect_id
        FROM inspection_checklist_tbl

        WHERE 1 = 1
    ";

    $types = "";
    $params = array();

    // ========================================================
    // DATE FILTER - SELECTED DATE ONLY
    // ========================================================

    if (!empty($date)) {

        $sql .= "
            AND inspection_checklist_tbl.date_inspected >= ?
            AND inspection_checklist_tbl.date_inspected
                < DATE_ADD(?, INTERVAL 1 DAY)
        ";

        $types .= "ss";
        $params[] = $date;
        $params[] = $date;
    }

    // ========================================================
    // BRANCH FILTER
    // ========================================================

    if ($branch !== 'all' && !empty($branch)) {

        $sql .= "
            AND LOWER(TRIM(inspection_checklist_tbl.branch))
                = LOWER(TRIM(?))
        ";

        $types .= "s";
        $params[] = $branch;
    }

    // ========================================================
    // GET ONLY THE LATEST INSPECTION
    // PER EXTINGUISHER CODE
    // ========================================================

    $sql .= "
        AND NOT EXISTS (
            SELECT 1
            FROM inspection_checklist_tbl AS newer

            WHERE newer.extinguisher_code =
                  inspection_checklist_tbl.extinguisher_code

            AND (
                newer.date_inspected >
                inspection_checklist_tbl.date_inspected

                OR (

                    newer.date_inspected =
                    inspection_checklist_tbl.date_inspected

                    AND newer.inspect_id >
                    inspection_checklist_tbl.inspect_id

                )
            )
    ";

    // ========================================================
    // WHEN DATE IS SELECTED:
    // NEWER RECORD MUST ALSO BE WITHIN SELECTED DATE
    // ========================================================

    if (!empty($date)) {

        $sql .= "
            AND newer.date_inspected >= ?
            AND newer.date_inspected
                < DATE_ADD(?, INTERVAL 1 DAY)
        ";

        $types .= "ss";
        $params[] = $date;
        $params[] = $date;
    }

    // CLOSE NOT EXISTS
    $sql .= "
        )
    ";

    // ========================================================
    // APPROVED ONLY
    // ========================================================

    $sql .= "
        AND inspection_checklist_tbl.evaluation_status = 'Approved'
    ";

    // ========================================================
    // ORDER
    // ========================================================

    $sql .= "
        ORDER BY
            inspection_checklist_tbl.date_inspected DESC,
            inspection_checklist_tbl.inspect_id DESC
    ";

    // ========================================================
    // PREPARE
    // ========================================================

    $stmt = mysqli_prepare($conn, $sql);

    if (!$stmt) {
        return array();
    }

    // ========================================================
    // BIND PARAMETERS
    // ========================================================

    if (!empty($types)) {

        mysqli_stmt_bind_param(
            $stmt,
            $types,
            ...$params
        );
    }

    // ========================================================
    // EXECUTE
    // ========================================================

    if (!mysqli_stmt_execute($stmt)) {

        mysqli_stmt_close($stmt);

        return array();
    }

    // ========================================================
    // RESULT
    // ========================================================

    $result = mysqli_stmt_get_result($stmt);

    $ids = array();

    while ($row = mysqli_fetch_assoc($result)) {

        $ids[] = (int) $row['inspect_id'];
    }

    mysqli_stmt_close($stmt);

    return $ids;
}

// ============================================================
// GET ALL APPROVED APPROVAL - REVISING LOGIC
// ============================================================

function getAllApprovedApproval(
    $limit = 10,
    $offset = 0,
    $date = null,
    $branch = 'all'
) {
    global $conn;

    $sql = "SELECT *
            FROM inspection_checklist_tbl
            WHERE 1 = 1";

    $types = "";
    $params = array();

    // ========================================================
    // DATE FILTER - SELECTED DATE ONLY
    // ========================================================

    if (!empty($date)) {

        $sql .= "
            AND date_inspected >= ?
            AND date_inspected < DATE_ADD(?, INTERVAL 1 DAY)
        ";

        $types .= "ss";
        $params[] = $date;
        $params[] = $date;
    }
    // ========================================================
    // BRANCH FILTER - FROM URL
    // ========================================================

    $branch = trim(isset($_GET['branch']) ? $_GET['branch'] : $_SESSION['Branch']);

    if ($branch !== 'all' && $branch !== '') {

        $sql .= "
        AND LOWER(TRIM(branch)) = LOWER(TRIM(?))
    ";

        $types .= "s";
        $params[] = $branch;
    }
    // ========================================================
    // LATEST RECORD PER EXTINGUISHER CODE
    // ========================================================

    $sql .= "
        AND NOT EXISTS (
            SELECT 1
            FROM inspection_checklist_tbl AS newer

            WHERE newer.extinguisher_code =
                  inspection_checklist_tbl.extinguisher_code

            AND (
                newer.date_inspected >
                inspection_checklist_tbl.date_inspected

                OR (
                    newer.date_inspected =
                    inspection_checklist_tbl.date_inspected

                    AND newer.inspect_id >
                    inspection_checklist_tbl.inspect_id
                )
            )
    ";

    // ========================================================
    // WHEN DATE IS SELECTED:
    // NEWER RECORD MUST ALSO BE WITHIN SELECTED DATE
    // ========================================================

    if (!empty($date)) {

        $sql .= "
            AND newer.date_inspected >= ?
            AND newer.date_inspected < DATE_ADD(?, INTERVAL 1 DAY)
        ";

        $types .= "ss";
        $params[] = $date;
        $params[] = $date;
    }

    // CLOSE NOT EXISTS
    $sql .= "
        )
    ";

    // ========================================================
    // STATUS
    // ========================================================

    $sql .= "
        AND evaluation_status = 'Approved'
    ";

    // ========================================================
    // PAGINATION
    // ========================================================

    $sql .= "
        ORDER BY date_inspected DESC, inspect_id DESC
        LIMIT ? OFFSET ?
    ";

    $types .= "ii";

    $params[] = (int) $limit;
    $params[] = (int) $offset;

    // ========================================================
    // PREPARE
    // ========================================================

    $stmt = mysqli_prepare($conn, $sql);

    if (!$stmt) {
        return false;
    }

    // ========================================================
    // BIND PARAMETERS
    // ========================================================

    if (!empty($types)) {

        mysqli_stmt_bind_param(
            $stmt,
            $types,
            ...$params
        );
    }

    // ========================================================
    // EXECUTE
    // ========================================================

    if (!mysqli_stmt_execute($stmt)) {
        mysqli_stmt_close($stmt);
        return false;
    }

    // ========================================================
    // RESULT
    // ========================================================

    $result = mysqli_stmt_get_result($stmt);

    mysqli_stmt_close($stmt);

    return $result;
}


// ============================================================
// FOR COUNT ALL APPROVED APPROVAL
// ============================================================

function getApprovedApprovalCount(
    $date = null,
    $branch = 'all'
) {
    global $conn;

    $sql = "SELECT COUNT(*) AS total
            FROM inspection_checklist_tbl
            WHERE 1 = 1";

    $types = "";
    $params = array();

    // ========================================================
    // DATE FILTER - SELECTED DATE ONLY
    // ========================================================

    if (!empty($date)) {

        $sql .= "
            AND date_inspected >= ?
            AND date_inspected < DATE_ADD(?, INTERVAL 1 DAY)
        ";

        $types .= "ss";
        $params[] = $date;
        $params[] = $date;
    }

    // ========================================================
    // BRANCH FILTER
    // ========================================================

    if ($branch !== 'all' && !empty($branch)) {

        $sql .= "
            AND LOWER(TRIM(branch)) = LOWER(TRIM(?))
        ";

        $types .= "s";
        $params[] = $branch;
    }

    // ========================================================
    // LATEST RECORD PER EXTINGUISHER CODE
    // ========================================================

    $sql .= "
        AND NOT EXISTS (
            SELECT 1
            FROM inspection_checklist_tbl AS newer

            WHERE newer.extinguisher_code =
                  inspection_checklist_tbl.extinguisher_code

            AND (
                newer.date_inspected >
                inspection_checklist_tbl.date_inspected

                OR (
                    newer.date_inspected =
                    inspection_checklist_tbl.date_inspected

                    AND newer.inspect_id >
                    inspection_checklist_tbl.inspect_id
                )
            )
    ";

    // ========================================================
    // WHEN DATE IS SELECTED:
    // NEWER RECORD MUST ALSO BE WITHIN SELECTED DATE
    // ========================================================

    if (!empty($date)) {

        $sql .= "
            AND newer.date_inspected >= ?
            AND newer.date_inspected < DATE_ADD(?, INTERVAL 1 DAY)
        ";

        $types .= "ss";
        $params[] = $date;
        $params[] = $date;
    }

    // CLOSE NOT EXISTS
    $sql .= "
        )
    ";

    // ========================================================
    // STATUS
    // ========================================================

    $sql .= "
        AND evaluation_status = 'Approved'
    ";

    // ========================================================
    // PREPARE
    // ========================================================

    $stmt = mysqli_prepare($conn, $sql);

    if (!$stmt) {
        return 0;
    }

    // ========================================================
    // BIND PARAMETERS
    // ========================================================

    if (!empty($types)) {

        mysqli_stmt_bind_param(
            $stmt,
            $types,
            ...$params
        );
    }

    // ========================================================
    // EXECUTE
    // ========================================================

    if (!mysqli_stmt_execute($stmt)) {
        mysqli_stmt_close($stmt);
        return 0;
    }

    // ========================================================
    // RESULT
    // ========================================================

    $result = mysqli_stmt_get_result($stmt);

    $row = mysqli_fetch_assoc($result);

    mysqli_stmt_close($stmt);

    return (int) ($row['total'] ?? 0);
}

// ============================================================
// GET ALL REJECTED APPROVAL
// ============================================================

function getAllRejectedApproval(
    $limit = 10,
    $offset = 0,
    $date = null,
    $branch = 'all'
) {
    global $conn;

    $sql = "SELECT *
            FROM inspection_checklist_tbl
            WHERE 1 = 1";

    $types = "";
    $params = [];

    // ========================================================
    // DATE FILTER - AS OF SELECTED DATE
    // ========================================================

    if (!empty($date)) {

        $sql .= "
            AND date_inspected < DATE_ADD(?, INTERVAL 1 DAY)
        ";

        $types .= "s";
        $params[] = $date;
    }

    // ========================================================
    // BRANCH FILTER
    // ========================================================

    if ($branch !== 'all' && !empty($branch)) {

        $sql .= "
            AND LOWER(TRIM(branch)) = LOWER(TRIM(?))
        ";

        $types .= "s";
        $params[] = $branch;
    }

    // ========================================================
    // LATEST RECORD PER EXTINGUISHER CODE
    // AS OF SELECTED DATE
    // ========================================================

    $sql .= "
        AND NOT EXISTS (
            SELECT 1
            FROM inspection_checklist_tbl AS newer

            WHERE newer.extinguisher_code =
                  inspection_checklist_tbl.extinguisher_code

            AND (
                newer.date_inspected >
                inspection_checklist_tbl.date_inspected

                OR (
                    newer.date_inspected =
                    inspection_checklist_tbl.date_inspected

                    AND newer.inspect_id >
                    inspection_checklist_tbl.inspect_id
                )
            )
    ";

    // ========================================================
    // NEWER RECORD MUST ALSO BE WITHIN SELECTED DATE
    // ========================================================

    if (!empty($date)) {

        $sql .= "
            AND newer.date_inspected < DATE_ADD(?, INTERVAL 1 DAY)
        ";

        $types .= "s";
        $params[] = $date;
    }

    $sql .= "
        )
    ";

    // ========================================================
    // STATUS
    // ========================================================

    $sql .= "
        AND evaluation_status = 'Rejected'
    ";

    // ========================================================
    // PAGINATION
    // ========================================================

    $sql .= "
        ORDER BY date_inspected DESC, inspect_id DESC
        LIMIT ? OFFSET ?
    ";

    $types .= "ii";

    $params[] = (int) $limit;
    $params[] = (int) $offset;

    // ========================================================
    // PREPARE
    // ========================================================

    $stmt = mysqli_prepare($conn, $sql);

    if (!$stmt) {
        return false;
    }

    // ========================================================
    // BIND PARAMETERS
    // ========================================================

    if (!empty($types)) {

        mysqli_stmt_bind_param(
            $stmt,
            $types,
            ...$params
        );
    }

    // ========================================================
    // EXECUTE
    // ========================================================

    if (!mysqli_stmt_execute($stmt)) {
        mysqli_stmt_close($stmt);
        return false;
    }

    // ========================================================
    // RESULT
    // ========================================================

    $result = mysqli_stmt_get_result($stmt);

    mysqli_stmt_close($stmt);

    return $result;
}


// ============================================================
// GET REJECTED APPROVAL COUNT
// ============================================================

function getRejectedApprovalCount(
    $date = null,
    $branch = 'all'
) {
    global $conn;

    $sql = "SELECT COUNT(*) AS total
            FROM inspection_checklist_tbl
            WHERE 1 = 1";

    $types = "";
    $params = [];

    // ========================================================
    // DATE FILTER - AS OF SELECTED DATE
    // ========================================================

    if (!empty($date)) {

        $sql .= "
            AND date_inspected < DATE_ADD(?, INTERVAL 1 DAY)
        ";

        $types .= "s";
        $params[] = $date;
    }

    // ========================================================
    // BRANCH FILTER
    // ========================================================

    if ($branch !== 'all' && !empty($branch)) {

        $sql .= "
            AND LOWER(TRIM(branch)) = LOWER(TRIM(?))
        ";

        $types .= "s";
        $params[] = $branch;
    }

    // ========================================================
    // LATEST RECORD PER EXTINGUISHER CODE
    // AS OF SELECTED DATE
    // ========================================================

    $sql .= "
        AND NOT EXISTS (
            SELECT 1
            FROM inspection_checklist_tbl AS newer

            WHERE newer.extinguisher_code =
                  inspection_checklist_tbl.extinguisher_code

            AND (
                newer.date_inspected >
                inspection_checklist_tbl.date_inspected

                OR (
                    newer.date_inspected =
                    inspection_checklist_tbl.date_inspected

                    AND newer.inspect_id >
                    inspection_checklist_tbl.inspect_id
                )
            )
    ";

    // ========================================================
    // NEWER RECORD MUST ALSO BE WITHIN SELECTED DATE
    // ========================================================

    if (!empty($date)) {

        $sql .= "
            AND newer.date_inspected < DATE_ADD(?, INTERVAL 1 DAY)
        ";

        $types .= "s";
        $params[] = $date;
    }

    $sql .= "
        )
    ";

    // ========================================================
    // STATUS
    // ========================================================

    $sql .= "
        AND evaluation_status = 'Rejected'
    ";

    // ========================================================
    // PREPARE
    // ========================================================

    $stmt = mysqli_prepare($conn, $sql);

    if (!$stmt) {
        return 0;
    }

    // ========================================================
    // BIND PARAMETERS
    // ========================================================

    if (!empty($types)) {

        mysqli_stmt_bind_param(
            $stmt,
            $types,
            ...$params
        );
    }

    // ========================================================
    // EXECUTE
    // ========================================================

    if (!mysqli_stmt_execute($stmt)) {
        mysqli_stmt_close($stmt);
        return 0;
    }

    // ========================================================
    // RESULT
    // ========================================================

    $result = mysqli_stmt_get_result($stmt);

    $row = mysqli_fetch_assoc($result);

    mysqli_stmt_close($stmt);

    return (int) ($row['total'] ?? 0);
}

// ============================================================
// FOR UPDATE EVALUATION STATUS
// ============================================================

function updateEvaluationStatus($id, $evaluation_status)
{
    global $conn;

    $branch = getBranch();

    if (empty($branch)) {
        return false;
    }

    $sql = "UPDATE inspection_checklist_tbl
            SET evaluation_status = ?,
                rejection_date = CASE
                    WHEN LOWER(TRIM(?)) = 'rejected'
                    THEN NOW()
                    ELSE NULL
                END
            WHERE inspect_id = ?
              AND LOWER(TRIM(branch)) = LOWER(TRIM(?))";

    $stmt = mysqli_prepare($conn, $sql);

    if (!$stmt) {
        return false;
    }

    mysqli_stmt_bind_param(
        $stmt,
        "ssis",
        $evaluation_status,
        $evaluation_status,
        $id,
        $branch
    );

    $result = mysqli_stmt_execute($stmt);

    mysqli_stmt_close($stmt);

    return $result;
}

function Approved_by($name, $id)
{
    global $conn;

    $sql = "
        UPDATE inspection_checklist_tbl
        SET verified_and_approved_by = ?
        WHERE inspect_id = ?
    ";

    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        return false;
    }

    $stmt->bind_param("si", $name, $id);

    return $stmt->execute();
}


function Rejected_by($name, $id)
{
    global $conn;

    $sql = "
        UPDATE inspection_checklist_tbl
        SET rejected_by = ?
        WHERE inspect_id = ?
    ";

    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        return false;
    }

    $stmt->bind_param("si", $name, $id);

    return $stmt->execute();
}





function updateCorrectiveAction(
    $inspect_id,
    $action_taken,
    $target_date,
    $rejection_reason
) {
    global $conn;

    $branch = getCurrentBranch();

    if (empty($branch)) {
        return false;
    }

    $sql = "UPDATE inspection_checklist_tbl
            SET
                action_taken = ?,
                target_date_of_implementation = ?,
                reject_reason = ?
            WHERE inspect_id = ?
              AND LOWER(TRIM(branch)) = LOWER(TRIM(?))";

    $stmt = mysqli_prepare($conn, $sql);

    if (!$stmt) {
        return false;
    }

    mysqli_stmt_bind_param(
        $stmt,
        "sssis",
        $action_taken,
        $target_date,
        $rejection_reason,
        $inspect_id,
        $branch
    );

    $success = mysqli_stmt_execute($stmt);

    mysqli_stmt_close($stmt);

    return $success;
}

function updateFireExtinguisherExpiryDate($exp_date, $code)
{
    global $conn;



    $sql = "UPDATE fire_extinguishers_tbl
            SET expiration_date = ?
            WHERE extinguisher_code = ?";

    $stmt = mysqli_prepare($conn, $sql);

    if (!$stmt) {
        return false;
    }

    mysqli_stmt_bind_param(
        $stmt,
        "ss",
        $exp_date,
        $code
    );

    $success = mysqli_stmt_execute($stmt);

    mysqli_stmt_close($stmt);

    return $success;
}



// ============================================================
// GET APPROVED INSPECTION REPORTS
// EXACT RECORDS FROM APPROVED LIST
// ============================================================

function getApprovedInspectionReports($ids)
{
    global $conn;

    if (empty($ids)) {
        return [];
    }

    // Make sure IDs are integers
    $ids = array_map('intval', $ids);

    // Remove duplicates
    $ids = array_values(array_unique($ids));

    if (empty($ids)) {
        return [];
    }

    // ========================================================
    // CREATE PLACEHOLDERS
    // ========================================================

    $placeholders = implode(
        ',',
        array_fill(0, count($ids), '?')
    );

    // ========================================================
    // QUERY
    // ========================================================

    $sql = "
        SELECT
            i.inspect_id,
            i.extinguisher_code,
            i.location,
            i.capacity,
            i.type,
            i.class,

            i.date_inspected,
            i.inspected_by,
            i.verified_and_approved_by,

            i.action_taken,
            i.target_date_of_implementation,

            f.remarks AS extinguisher_remarks,

            i.is_seal_ok,
            i.is_pin_ok,
            i.is_pressure_ok,
            i.is_hose_ok,
            i.is_nozzle_ok,
            i.is_belt_ok,
            i.is_cylinder_body_ok,
            i.is_demarcation_line_ok,
            i.is_signage_ok,
            i.is_cleaning_of_unit_ok,

            i.status,
            i.evaluation_status,
            i.branch

        FROM inspection_checklist_tbl AS i

        LEFT JOIN fire_extinguishers_tbl AS f
            ON TRIM(f.extinguisher_code) =
               TRIM(i.extinguisher_code)

        WHERE i.inspect_id IN ($placeholders)

        AND i.evaluation_status = 'Approved'

        ORDER BY
            i.date_inspected ASC,
            i.inspect_id ASC
    ";

    // ========================================================
    // PREPARE
    // ========================================================

    $stmt = mysqli_prepare($conn, $sql);

    if (!$stmt) {
        return [];
    }

    // ========================================================
    // BIND IDS
    // ========================================================

    $types = str_repeat('i', count($ids));

    mysqli_stmt_bind_param(
        $stmt,
        $types,
        ...$ids
    );

    // ========================================================
    // EXECUTE
    // ========================================================

    if (!mysqli_stmt_execute($stmt)) {
        mysqli_stmt_close($stmt);
        return [];
    }

    // ========================================================
    // GET RESULT
    // ========================================================

    $result = mysqli_stmt_get_result($stmt);

    $inspections = [];

    while ($row = mysqli_fetch_assoc($result)) {
        $inspections[] = $row;
    }

    mysqli_stmt_close($stmt);

    return $inspections;
}


function updateFireExtinguisherStatus($extinguisher_code, $status, $remarks)
{
    global $conn;

    $sql = "UPDATE fire_extinguishers_tbl
            SET condition_status = ?,
                remarks = ?
            WHERE extinguisher_code = ?";

    $stmt = mysqli_prepare($conn, $sql);

    if (!$stmt) {
        return false;
    }

    mysqli_stmt_bind_param(
        $stmt,
        "sss",
        $status,
        $remarks,
        $extinguisher_code
    );

    $success = mysqli_stmt_execute($stmt);

    mysqli_stmt_close($stmt);

    return $success;
}

// get fire extinguisher for inventory report
function getFireExtinguishersForInventoryReport($ids)
{
    global $conn;

    if (empty($ids)) {
        return [];
    }

    $placeholders = implode(
        ',',
        array_fill(0, count($ids), '?')
    );

    $sql = "
        SELECT
            extinguisher_code,
            location,
            capacity,
            type,
            class,
            manufactured_date,
            placement,
            condition_status,
            remarks,
            branch

        FROM fire_extinguishers_tbl

        WHERE extinguisher_id IN ($placeholders)
          AND archived = 0

        ORDER BY extinguisher_id ASC
    ";

    $stmt = mysqli_prepare($conn, $sql);

    if (!$stmt) {
        return [];
    }

    $types = str_repeat('i', count($ids));

    $ids = array_map('intval', $ids);

    mysqli_stmt_bind_param(
        $stmt,
        $types,
        ...$ids
    );

    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);

    $extinguishers = [];

    while ($row = mysqli_fetch_assoc($result)) {
        $extinguishers[] = $row;
    }

    mysqli_stmt_close($stmt);

    return $extinguishers;
}
