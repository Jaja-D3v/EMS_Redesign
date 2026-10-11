<?php

require_once 'backend/authentication/SessionChecker.php';
require_once 'backend/controller/RejectedInspectionController.php';

$perPage = 6;

$currentPage = isset($_GET['page'])
    ? max(1, (int) $_GET['page'])
    : 1;

$totalRejected = count($rejectedInspections);

$totalPages = max(
    1,
    (int) ceil($totalRejected / $perPage)
);

if ($currentPage > $totalPages) {
    $currentPage = $totalPages;
}

$offset = ($currentPage - 1) * $perPage;

$paginatedInspections = array_slice(
    $rejectedInspections,
    $offset,
    $perPage
);

?>

<!DOCTYPE html>
<html lang="en">

<?php include 'partials/header.php'; ?>

<body>

    <?php include 'partials/side-nav.php'; ?>

    <div class="wrapper d-flex flex-column min-vh-100">

        <?php include 'partials/header-nav.php'; ?>

        <main class="body flex-grow-1 px-3 py-4">

            <div class="container-fluid">



                <!-- MAIN CARD -->
                <div class="card border-0 shadow-sm">

                    <!-- HEADER -->
                    <div class="card-header bg-body border-bottom py-3">

                        <div
                            class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-2">

                            <div>

                                <h6
                                    class="fw-semibold mb-1"
                                    style="font-size: 13px;">
                                    Rejected Inspections
                                </h6>

                                <small
                                    class="text-body-secondary"
                                    style="font-size: 11px;">
                                    Inspections that require correction or re-inspection.
                                </small>

                            </div>

                            <span
                                class="badge bg-danger-subtle text-danger"
                                style="font-size: 11px;">
                                <?= $totalRejected ?> Rejected
                            </span>

                        </div>

                    </div>

                    <!-- FILTER -->
                    <form method="GET" class="card border-0 shadow-sm mb-2">

                        <div class="card-body p-3">

                            <div class="row g-2">

                                <div class="col-12 col-lg">

                                    <input
                                        type="text"
                                        name="search"
                                        class="form-control"
                                        placeholder="Search code, location, or reason..."
                                        value="<?= htmlspecialchars(
                                                    $search,
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                ) ?>"
                                        style="font-size: 12px;">

                                </div>

                                <div class="col-12 col-sm-6 col-lg-auto">

                                    <select
                                        name="date"
                                        class="form-select"
                                        style="font-size: 12px;">

                                        <?php foreach (
                                            [
                                                'all' => 'All Inspection Dates',
                                                'today' => 'Today',
                                                'yesterday' => 'Yesterday',
                                                '7' => 'Last 7 Days',
                                                '30' => 'Last 30 Days'
                                            ] as $value => $label
                                        ): ?>

                                            <option
                                                value="<?= $value ?>"
                                                <?= $dateFilter === $value
                                                    ? 'selected'
                                                    : '' ?>>
                                                <?= $label ?>
                                            </option>

                                        <?php endforeach; ?>

                                    </select>

                                </div>

                                <div class="col-6 col-sm-auto">

                                    <button
                                        type="submit"
                                        class="btn btn-primary w-100"
                                        style="font-size: 12px;">
                                        Filter
                                    </button>

                                </div>

                                <div class="col-6 col-sm-auto">

                                    <a
                                        href="rejected-inspection.php"
                                        class="btn btn-light border w-100"
                                        style="font-size: 12px;">
                                        Clear
                                    </a>

                                </div>

                            </div>

                        </div>

                    </form>

                    <!-- DESKTOP TABLE -->
                    <div
                        class="table-responsive d-none d-md-block"
                        style="min-height: 390px;">

                        <table
                            class="table table-hover align-middle mb-0">

                            <thead class="table-light">

                                <tr>

                                    <th
                                        class="px-3"
                                        style="font-size: 12px;">
                                        Date Inspected
                                    </th>

                                    <th style="font-size: 12px;">
                                        Extinguisher Code
                                    </th>

                                    <th style="font-size: 12px;">
                                        Location
                                    </th>

                                    <th style="font-size: 12px;">
                                        Rejection Reason
                                    </th>

                                    <th style="font-size: 12px;">
                                        Rejection Date
                                    </th>

                                    <th
                                        class="text-end px-3"
                                        style="font-size: 12px;">
                                        Action
                                    </th>

                                </tr>

                            </thead>

                            <tbody>

                                <?php foreach (
                                    $paginatedInspections
                                    as $inspection
                                ): ?>

                                    <?php

                                    $inspectionDate = strtotime(
                                        $inspection['date_inspected']
                                    );

                                    $reason = !empty($inspection['reject_reason'])
                                        ? $inspection['reject_reason']
                                        : 'No reason provided.';

                                    $rejectionDate = !empty($inspection['rejection_date'])
                                        ? strtotime(
                                            $inspection['rejection_date']
                                        )
                                        : false;

                                    ?>

                                    <tr>

                                        <td class="px-3">

                                            <div
                                                class="fw-semibold"
                                                style="font-size: 12px;">

                                                <?= date(
                                                    'M d, Y',
                                                    $inspectionDate
                                                ) ?>

                                            </div>

                                            <small class="text-body-secondary">

                                                <?= date(
                                                    'h:i A',
                                                    $inspectionDate
                                                ) ?>

                                            </small>

                                        </td>

                                        <td
                                            class="fw-semibold"
                                            style="font-size: 12px;">

                                            <?= htmlspecialchars(
                                                $inspection['extinguisher_code'],
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>

                                        </td>

                                        <td style="font-size: 12px;">

                                            <?= htmlspecialchars(
                                                $inspection['location'],
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>

                                        </td>

                                        <td style="font-size: 12px;">

                                            <span
                                                class="badge bg-danger-subtle text-danger fw-normal text-wrap text-start">

                                                <?= htmlspecialchars(
                                                    $reason,
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                ) ?>

                                            </span>

                                        </td>

                                        <td style="font-size: 12px;">

                                            <?php if ($rejectionDate): ?>

                                                <?= date(
                                                    'M d, Y',
                                                    $rejectionDate
                                                ) ?>

                                            <?php else: ?>

                                                <span
                                                    class="text-body-secondary">
                                                    Not recorded
                                                </span>

                                            <?php endif; ?>

                                        </td>

                                        <td class="text-end px-3">

                                            <button
                                                type="button"
                                                class="btn btn-sm btn-outline-primary"
                                                style="font-size: 12px;"
                                                onclick="document.getElementById('rejectionDetails<?= (int) $inspection['inspect_id'] ?>').showModal();">

                                                View

                                            </button>

                                        </td>

                                    </tr>

                                <?php endforeach; ?>

                            </tbody>

                        </table>

                    </div>

                    <!-- MOBILE LIST -->
                    <div
                        class="d-md-none"
                        id="mobileRejectedInspectionList">

                        <?php foreach (
                            $paginatedInspections
                            as $inspection
                        ): ?>

                            <?php

                            $inspectionDate = strtotime(
                                $inspection['date_inspected']
                            );

                            $reason = !empty($inspection['reject_reason'])
                                ? $inspection['reject_reason']
                                : 'No reason provided.';

                            ?>

                            <div class="border-bottom p-3">

                                <div
                                    class="d-flex justify-content-between align-items-start gap-2 mb-3">

                                    <div>

                                        <div
                                            class="fw-semibold"
                                            style="font-size: 13px;">

                                            <?= htmlspecialchars(
                                                $inspection['extinguisher_code'],
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>

                                        </div>

                                        <small class="text-body-secondary">

                                            <?= date(
                                                'M d, Y h:i A',
                                                $inspectionDate
                                            ) ?>

                                        </small>

                                    </div>

                                    <span class="badge bg-danger">
                                        Rejected
                                    </span>

                                </div>

                                <div class="mb-3">

                                    <small
                                        class="text-body-secondary d-block mb-1">
                                        LOCATION
                                    </small>

                                    <div style="font-size: 12px;">

                                        <?= htmlspecialchars(
                                            $inspection['location'],
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>

                                    </div>

                                </div>

                                <div
                                    class="d-flex align-items-center justify-content-between gap-2">

                                    <span
                                        class="badge bg-danger-subtle text-danger fw-normal text-wrap text-start">

                                        <?= htmlspecialchars(
                                            $reason,
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>

                                    </span>

                                    <button
                                        type="button"
                                        class="btn btn-sm btn-outline-primary"
                                        style="font-size: 12px;"
                                        onclick="document.getElementById('rejectionDetails<?= (int) $inspection['inspect_id'] ?>').showModal();">

                                        View

                                    </button>

                                </div>

                            </div>

                        <?php endforeach; ?>

                    </div>

                    <!-- EMPTY STATE -->
                    <?php if (empty($rejectedInspections)): ?>

                        <div class="text-center p-5">

                            <div
                                class="text-body-secondary mb-2"
                                style="font-size: 30px;">
                                ✓
                            </div>

                            <div
                                class="fw-semibold mb-1"
                                style="font-size: 13px;">
                                No rejected inspections found
                            </div>

                            <div
                                class="text-body-secondary"
                                style="font-size: 12px;">
                                Try changing your search or filter.
                            </div>

                        </div>

                    <?php endif; ?>

                    <!-- PAGINATION -->
                    <div class="card-footer bg-body border-top">

                        <div
                            class="d-flex flex-column flex-sm-row align-items-center justify-content-between gap-3">

                            <small
                                class="text-body-secondary"
                                style="font-size: 12px;">

                                <?php if ($totalRejected > 0): ?>

                                    Showing
                                    <?= $offset + 1 ?>
                                    to
                                    <?= min(
                                        $offset + $perPage,
                                        $totalRejected
                                    ) ?>
                                    of
                                    <?= $totalRejected ?>
                                    entries

                                <?php else: ?>

                                    Showing 0 of 0 entries

                                <?php endif; ?>

                            </small>

                            <?php if ($totalPages > 1): ?>

                                <?php

                                $currentPageUrl = basename(
                                    $_SERVER['PHP_SELF']
                                );

                                ?>

                                <nav
                                    aria-label="Rejected inspection pagination">

                                    <ul class="pagination pagination-sm mb-0">

                                        <!-- PREVIOUS -->
                                        <li
                                            class="page-item <?= $currentPage <= 1
                                                                    ? 'disabled'
                                                                    : '' ?>">

                                            <?php if ($currentPage > 1): ?>

                                                <a
                                                    class="page-link"
                                                    href="<?= htmlspecialchars(
                                                                $currentPageUrl,
                                                                ENT_QUOTES,
                                                                'UTF-8'
                                                            ) ?>?<?= http_build_query([
                                                                        'page' => $currentPage - 1,
                                                                        'search' => $search,
                                                                        'date' => $dateFilter
                                                                    ]) ?>">
                                                    Previous
                                                </a>

                                            <?php else: ?>

                                                <span class="page-link">
                                                    Previous
                                                </span>

                                            <?php endif; ?>

                                        </li>

                                        <!-- PAGE NUMBERS -->
                                        <?php for (
                                            $page = 1;
                                            $page <= $totalPages;
                                            $page++
                                        ): ?>

                                            <li
                                                class="page-item <?= $page === $currentPage
                                                                        ? 'active'
                                                                        : '' ?>">

                                                <a
                                                    class="page-link"
                                                    href="<?= htmlspecialchars(
                                                                $currentPageUrl,
                                                                ENT_QUOTES,
                                                                'UTF-8'
                                                            ) ?>?<?= http_build_query([
                                                                        'page' => $page,
                                                                        'search' => $search,
                                                                        'date' => $dateFilter
                                                                    ]) ?>">

                                                    <?= $page ?>

                                                </a>

                                            </li>

                                        <?php endfor; ?>

                                        <!-- NEXT -->
                                        <li
                                            class="page-item <?= $currentPage >= $totalPages
                                                                    ? 'disabled'
                                                                    : '' ?>">

                                            <?php if ($currentPage < $totalPages): ?>

                                                <a
                                                    class="page-link"
                                                    href="<?= htmlspecialchars(
                                                                $currentPageUrl,
                                                                ENT_QUOTES,
                                                                'UTF-8'
                                                            ) ?>?<?= http_build_query([
                                                                        'page' => $currentPage + 1,
                                                                        'search' => $search,
                                                                        'date' => $dateFilter
                                                                    ]) ?>">
                                                    Next
                                                </a>

                                            <?php else: ?>

                                                <span class="page-link">
                                                    Next
                                                </span>

                                            <?php endif; ?>

                                        </li>

                                    </ul>

                                </nav>

                            <?php endif; ?>

                        </div>

                    </div>

                </div>

            </div>

        </main>

        <!-- =========================================================
             REJECTION DETAILS
             ========================================================= -->

        <?php foreach (
            $paginatedInspections
            as $inspection
        ): ?>

            <?php

            $inspectionDate = strtotime(
                $inspection['date_inspected']
            );

            $reason = !empty($inspection['reject_reason'])
                ? $inspection['reject_reason']
                : 'No reason provided.';

            $rejectedBy = !empty($inspection['rejected_by'])
                ? $inspection['rejected_by']
                : 'Not recorded';

            $rejectionDate = !empty($inspection['rejection_date'])
                ? strtotime(
                    $inspection['rejection_date']
                )
                : false;

            $targetDate = !empty($inspection['target_date_of_implementation'])
                ? strtotime(
                    $inspection['target_date_of_implementation']
                )
                : false;

            ?>

            <dialog
                id="rejectionDetails<?= (int) $inspection['inspect_id'] ?>"
                style="
                    width: min(900px, calc(100% - 24px));
                    max-width: 900px;
                    max-height: 90vh;
                    border: 0;
                    border-radius: 14px;
                    padding: 0;
                    overflow: hidden;
                    box-shadow: 0 20px 60px rgba(0, 0, 0, .25);
                ">

                <!-- MODAL HEADER -->
                <div class="bg-white border-bottom">

                    <div
                        class="d-flex align-items-center justify-content-between p-4">

                        <div
                            class="d-flex align-items-center gap-3">

                            <div
                                class="rounded-circle bg-danger d-flex align-items-center justify-content-center text-white"
                                style="
                                    width: 48px;
                                    height: 48px;
                                    font-size: 22px;
                                    flex-shrink: 0;
                                ">
                                ×
                            </div>

                            <div>

                                <h5
                                    class="fw-semibold mb-1"
                                    style="font-size: 18px;">
                                    Rejection Details
                                </h5>

                                <div
                                    class="text-body-secondary"
                                    style="font-size: 11px;">
                                    Inspection rejection information and corrective actions.
                                </div>

                            </div>

                        </div>

                        <button
                            type="button"
                            class="btn btn-light border-0"
                            style="
                                font-size: 22px;
                                line-height: 1;
                            "
                            onclick="this.closest('dialog').close();">

                            ×

                        </button>

                    </div>

                </div>

                <!-- MODAL BODY -->
                <div
                    class="p-4 bg-body"
                    style="max-height: calc(90vh - 150px); overflow-y: auto;">

                    <!-- STATUS SUMMARY -->
                    <div
                        class="rounded-3 border border-danger-subtle bg-danger-subtle p-3 mb-3">

                        <div class="row g-3 align-items-center">

                            <!-- STATUS -->
                            <div class="col-12 col-md-4">

                                <div
                                    class="d-flex align-items-center gap-3">

                                    <div
                                        class="rounded-circle bg-danger text-white d-flex align-items-center justify-content-center"
                                        style="
                                            width: 38px;
                                            height: 38px;
                                            font-size: 18px;
                                            flex-shrink: 0;
                                        ">
                                        ✓
                                    </div>

                                    <div>

                                        <div
                                            class="text-body-secondary"
                                            style="font-size: 10px;">
                                            STATUS
                                        </div>

                                        <span
                                            class="badge bg-danger mt-1"
                                            style="font-size: 10px;">
                                            Rejected
                                        </span>

                                    </div>

                                </div>

                            </div>

                            <!-- REJECTED BY -->
                            <div class="col-12 col-md-4">

                                <div>

                                    <div
                                        class="text-body-secondary"
                                        style="font-size: 10px;">
                                        REJECTED BY
                                    </div>

                                    <div
                                        class="fw-semibold mt-1"
                                        style="font-size: 12px;">

                                        <?= htmlspecialchars(
                                            $rejectedBy,
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>

                                    </div>

                                </div>

                            </div>

                            <!-- REJECTION DATE -->
                            <div class="col-12 col-md-4">

                                <div>

                                    <div
                                        class="text-body-secondary"
                                        style="font-size: 10px;">
                                        REJECTION DATE
                                    </div>

                                    <div
                                        class="fw-semibold mt-1"
                                        style="font-size: 12px;">

                                        <?= $rejectionDate
                                            ? date(
                                                'M d, Y',
                                                $rejectionDate
                                            )
                                            : 'Not recorded'
                                        ?>

                                    </div>

                                    <?php if ($rejectionDate): ?>

                                        <div
                                            class="text-body-secondary"
                                            style="font-size: 10px;">

                                            <?= date(
                                                'h:i A',
                                                $rejectionDate
                                            ) ?>

                                        </div>

                                    <?php endif; ?>

                                </div>

                            </div>

                        </div>

                    </div>

                    <!-- REJECTION REASON -->
                    <div class="card border mb-3">

                        <div class="card-body p-3">

                            <div
                                class="d-flex align-items-center gap-2 mb-3">

                                <div
                                    class="rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center"
                                    style="
                                        width: 32px;
                                        height: 32px;
                                        font-size: 16px;
                                    ">
                                    💬
                                </div>

                                <div
                                    class="fw-semibold"
                                    style="font-size: 13px;">
                                    Rejection Reason
                                </div>

                            </div>

                            <div
                                class="rounded-3 bg-danger-subtle p-3">

                                <div
                                    style="
                                        font-size: 12px;
                                        line-height: 1.6;
                                    ">

                                    <?= nl2br(
                                        htmlspecialchars(
                                            $reason,
                                            ENT_QUOTES,
                                            'UTF-8'
                                        )
                                    ) ?>

                                </div>

                            </div>

                        </div>

                    </div>

                    <!-- INSPECTION INFORMATION -->
                    <div class="card border mb-3">

                        <div class="card-body p-3">

                            <!-- SECTION HEADER -->
                            <div class="d-flex align-items-center gap-2 mb-3">

                                <div class="rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center" style=" width: 32px; height: 32px; font-size: 16px; ">
                                    ℹ
                                </div>

                                <div
                                    class="fw-semibold"
                                    style="font-size: 13px;">
                                    Inspection Information
                                </div>

                            </div>

                            <!-- INFORMATION -->
                            <div class="row g-3">

                                <!-- ROW 1 -->

                                <div class="col-12 col-md-6">

                                    <div
                                        class="text-body-secondary"
                                        style="font-size: 10px;">
                                        EXTINGUISHER CODE
                                    </div>

                                    <div
                                        class="fw-semibold mt-1"
                                        style="font-size: 12px;">

                                        <?= htmlspecialchars(
                                            $inspection['extinguisher_code'],
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>

                                    </div>

                                </div>

                                <div class="col-12 col-md-6">

                                    <div
                                        class="text-body-secondary"
                                        style="font-size: 10px;">
                                        LOCATION
                                    </div>

                                    <div
                                        class="fw-semibold mt-1"
                                        style="font-size: 12px;">

                                        <?= htmlspecialchars(
                                            $inspection['location'],
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>

                                    </div>

                                </div>

                                <!-- ROW 2 -->

                                <div class="col-12 col-md-6">

                                    <div
                                        class="text-body-secondary"
                                        style="font-size: 10px;">
                                        TYPE
                                    </div>

                                    <div
                                        class="mt-1"
                                        style="font-size: 12px;">

                                        <?= htmlspecialchars(
                                            $inspection['type'],
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>

                                    </div>

                                </div>

                                <div class="col-12 col-md-6">

                                    <div
                                        class="text-body-secondary"
                                        style="font-size: 10px;">
                                        CAPACITY
                                    </div>

                                    <div
                                        class="mt-1"
                                        style="font-size: 12px;">

                                        <?= htmlspecialchars(
                                            $inspection['capacity'],
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>

                                    </div>

                                </div>

                                <!-- ROW 3 -->

                                <div class="col-12 col-md-6">

                                    <div
                                        class="text-body-secondary"
                                        style="font-size: 10px;">
                                        DATE INSPECTED
                                    </div>

                                    <div
                                        class="mt-1"
                                        style="font-size: 12px;">

                                        <?= date(
                                            'M d, Y h:i A',
                                            $inspectionDate
                                        ) ?>

                                    </div>

                                </div>

                                <div class="col-12 col-md-6">

                                    <div
                                        class="text-body-secondary"
                                        style="font-size: 10px;">
                                        INSPECTED BY
                                    </div>

                                    <div
                                        class="mt-1"
                                        style="font-size: 12px;">

                                        <?= !empty($inspection['inspected_by'])
                                            ? htmlspecialchars(
                                                $inspection['inspected_by'],
                                                ENT_QUOTES,
                                                'UTF-8'
                                            )
                                            : 'Not recorded'
                                        ?>

                                    </div>

                                </div>

                                <!-- ROW 4 -->

                                <div class="col-12 col-md-6">

                                    <div
                                        class="text-body-secondary"
                                        style="font-size: 10px;">
                                        VERIFIED AND APPROVED BY
                                    </div>

                                    <div
                                        class="mt-1"
                                        style="font-size: 12px;">

                                        <?= !empty($inspection['verified_and_approved_by'])
                                            ? htmlspecialchars(
                                                $inspection['verified_and_approved_by'],
                                                ENT_QUOTES,
                                                'UTF-8'
                                            )
                                            : 'Not recorded'
                                        ?>

                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>

                    <!-- CORRECTIVE ACTION -->
                    <div class="card border">

                        <div class="card-body p-3">

                            <div
                                class="d-flex align-items-center gap-2 mb-3">

                                <div
                                    class="rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center"
                                    style="
                                        width: 32px;
                                        height: 32px;
                                        font-size: 16px;
                                    ">
                                    🔧
                                </div>

                                <div
                                    class="fw-semibold"
                                    style="font-size: 13px;">
                                    Corrective Action
                                </div>

                            </div>

                            <div class="row g-3">

                                <!-- ACTION TAKEN -->
                                <div class="col-12 col-md-7">

                                    <div
                                        class="rounded-3 bg-primary-subtle p-3 h-100">

                                        <div
                                            class="text-primary mb-2"
                                            style="font-size: 10px;">
                                            ACTION TAKEN
                                        </div>

                                        <div
                                            style="
                                                font-size: 12px;
                                                line-height: 1.6;
                                            ">

                                            <?= !empty($inspection['action_taken'])
                                                ? nl2br(
                                                    htmlspecialchars(
                                                        $inspection['action_taken'],
                                                        ENT_QUOTES,
                                                        'UTF-8'
                                                    )
                                                )
                                                : 'Not recorded'
                                            ?>

                                        </div>

                                    </div>

                                </div>

                                <!-- TARGET DATE -->
                                <div class="col-12 col-md-5">

                                    <div
                                        class="rounded-3 bg-success-subtle p-3 h-100">

                                        <div
                                            class="text-success mb-2"
                                            style="font-size: 10px;">
                                            TARGET DATE OF IMPLEMENTATION
                                        </div>

                                        <div
                                            class="fw-semibold"
                                            style="font-size: 12px;">

                                            <?= $targetDate
                                                ? date(
                                                    'M d, Y',
                                                    $targetDate
                                                )
                                                : 'Not recorded'
                                            ?>

                                        </div>

                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>

                <!-- MODAL FOOTER -->
                <div
                    class="border-top bg-white p-3 text-end">

                    <button
                        type="button"
                        class="btn btn-secondary"
                        style="font-size: 12px;"
                        onclick="this.closest('dialog').close();">

                        Close

                    </button>

                </div>

            </dialog>

        <?php endforeach; ?>

        <?php include 'partials/footer.php'; ?>

    </div>

    <!-- COREUI -->
    <script src="vendors/@coreui/coreui/js/coreui.bundle.min.js"></script>
    <script src="vendors/simplebar/js/simplebar.min.js"></script>

    <?php include_once 'notification/session_timeout.php'; ?>

</body>

</html>