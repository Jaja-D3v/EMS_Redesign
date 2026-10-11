<?php

require_once 'backend/authentication/SessionChecker.php';
require_once 'backend/controller/InspectionController.php';

$limit = 10;

// Current page
$page = isset($_GET['page'])
  ? (int) $_GET['page']
  : 1;

if ($page < 1) {
  $page = 1;
}

// Date filter
$date = $_GET['date'] ?? null;

if ($date === '') {
  $date = null;
}

// Offset
$offset = ($page - 1) * $limit;

// ========================================
// GET TOTAL PENDING APPROVALS
// ========================================

$totalRecords = getPendingApprovalTotal($date);

// ========================================
// TOTAL PAGES
// ========================================

$totalPages = (int) ceil($totalRecords / $limit);

// ========================================
// GET CURRENT PAGE DATA
// ========================================

$result = getPendingApprovals(
  $limit,
  $offset,
  $date
);

// ========================================
// CONVERT TO ARRAY
// ========================================

$inspectionChecklists = [];

if ($result) {
  while ($row = mysqli_fetch_assoc($result)) {
    $inspectionChecklists[] = $row;
  }
}

// ========================================
// PREVENT INVALID PAGE
// ========================================

if ($totalPages > 0 && $page > $totalPages) {

  $page = $totalPages;

  $offset = ($page - 1) * $limit;

  $result = getPendingApprovals(
    $limit,
    $offset,
    $date
  );

  $inspectionChecklists = [];

  if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
      $inspectionChecklists[] = $row;
    }
  }
}

?>

<!DOCTYPE html>

<!--
* CoreUI - Free Bootstrap Admin Template
* @version v5.5.0
* @link https://coreui.io/product/free-bootstrap-admin-template/
* Copyright (c) 2026 creativeLabs Łukasz Holeczek
* Licensed under MIT
-->

<html lang="en">

<?php include 'partials/header.php'; ?>

<body>

  <?php include 'partials/side-nav.php'; ?>

  <div class="wrapper d-flex flex-column min-vh-100">

    <?php include 'partials/header-nav.php'; ?>

    <!-- CONTENT -->
    <div class="container-fluid py-0">

      <!-- Page Header -->
      <div
        class="card border-0 shadow-sm text-white mb-1 mt-0 overflow-hidden"
        style="background: linear-gradient(135deg, #c81e3a, #ff7657);">

        <div class="card-body p-4">

          <div class="row align-items-center g-3">

            <div class="col-auto">

              <div class="bg-white bg-opacity-25 rounded-3 p-3 fs-2">

                <i class="bi bi-clipboard2-check"></i>

              </div>

            </div>

            <div class="col">

              <h2 class="fw-bold mb-1">
                Inspection List
              </h2>

              <p class="mb-0 text-white-50">
                Review and approve inspection reports.
              </p>

            </div>

          </div>

        </div>

      </div>


      <!-- Filters -->

      <div class="card border-0 shadow-sm mb-3">

        <div class="card-body p-3">

          <div class="row g-2">

            <!-- Search -->

            <div class="col-12 col-md-6 col-lg-6">

              <div class="input-group">

                <span class="input-group-text bg-white">

                  <i class="bi bi-search"></i>

                </span>

                <input
                  type="text"
                  class="form-control"
                  id="inspectionSearch"
                  placeholder="Search by FE code or location...">

              </div>

            </div>


            <!-- Status -->


            <!-- Date -->

            <div class="col-12 col-md-3 col-lg-3">

              <div class="input-group">

                <span class="input-group-text bg-white">

                  <i class="bi bi-calendar3"></i>

                </span>

                <input
                  type="date"
                  class="form-control"
                  id="inspectionDateFilter">

              </div>

            </div>

          </div>

        </div>

      </div>


      <!-- Inspection Table -->

      <div class="card border-0 shadow-sm">

        <!-- Table -->

        <div class="card-body p-0">

          <div class="table-responsive">

            <table class="table table-hover align-middle mb-0">

              <thead class="table-light">

                <tr>

                  <th class="ps-4 py-3 text-nowrap">
                    FE Code
                  </th>

                  <th class="py-3 text-nowrap">
                    Location
                  </th>

                  <th class="py-3 text-nowrap">
                    Date Inspected
                  </th>

                  <th class="py-3 text-nowrap">
                    Status
                  </th>

                  <th class="py-3 text-nowrap text-end pe-3">
                    Actions
                  </th>

                </tr>

              </thead>


              <tbody id="inspectionList">

                <?php if (!empty($inspectionChecklists)): ?>

                  <?php foreach ($inspectionChecklists as $inspection): ?>

                    <?php

                    $status = $inspection['evaluation_status'] ?? 'Pending';

                    switch ($status) {

                      case 'Approved':

                        $statusClass = 'bg-success-subtle text-success';
                        $statusIcon = 'bi-check-circle-fill';
                        $statusFilterValue = 'approved';
                        $statusLabel = 'Approved';

                        break;


                      case 'Rejected':

                        $statusClass = 'bg-danger-subtle text-danger';
                        $statusIcon = 'bi-x-circle-fill';
                        $statusFilterValue = 'rejected';
                        $statusLabel = 'Rejected';

                        break;


                      case 'Pending':
                      default:

                        $statusClass = 'bg-warning-subtle text-warning-emphasis';
                        $statusIcon = 'bi-clock';
                        $statusFilterValue = 'pending';
                        $statusLabel = 'Pending Approval';

                        break;
                    }


                    /*
                |--------------------------------------------------------------------------
                | Inspection Date
                |--------------------------------------------------------------------------
                */

                    $inspectionDate = '';

                    if (!empty($inspection['date_inspected'])) {

                      $timestamp = strtotime(
                        $inspection['date_inspected']
                      );

                      if ($timestamp !== false) {

                        $inspectionDate = date(
                          'Y-m-d',
                          $timestamp
                        );
                      }
                    }

                    ?>

                    <tr
                      class="inspection-row"

                      data-fe-code="<?= htmlspecialchars(
                                      strtolower(
                                        $inspection['extinguisher_code'] ?? ''
                                      )
                                    ) ?>"

                      data-location="<?= htmlspecialchars(
                                        strtolower(
                                          $inspection['location'] ?? ''
                                        )
                                      ) ?>"

                      data-status="<?= htmlspecialchars(
                                      $statusFilterValue
                                    ) ?>"

                      data-date="<?= htmlspecialchars(
                                    $inspectionDate
                                  ) ?>">

                      <!-- FE Code -->

                      <td class="ps-4">

                        <span class="text-primary fw-semibold">

                          <?= htmlspecialchars(
                            $inspection['extinguisher_code'] ?? '—'
                          ) ?>

                        </span>

                      </td>


                      <!-- Location -->

                      <td>

                        <?= htmlspecialchars(
                          $inspection['location'] ?? '—'
                        ) ?>

                      </td>


                      <!-- Date Inspected -->

                      <td class="text-nowrap">

                        <?php if (!empty($inspection['date_inspected'])): ?>

                          <?php

                          $timestamp = strtotime(
                            $inspection['date_inspected']
                          );

                          ?>

                          <?php if ($timestamp !== false): ?>

                            <?= date(
                              'M d, Y',
                              $timestamp
                            ) ?>

                            <small class="text-body-secondary d-block">

                              <?= date(
                                'h:i A',
                                $timestamp
                              ) ?>

                            </small>

                          <?php else: ?>

                            —

                          <?php endif; ?>

                        <?php else: ?>

                          —

                        <?php endif; ?>

                      </td>


                      <!-- Status -->

                      <td>

                        <span
                          class="badge rounded-pill <?= $statusClass ?> px-3 py-2">

                          <i
                            class="bi <?= $statusIcon ?> me-1">
                          </i>

                          <?= htmlspecialchars(
                            $statusLabel
                          ) ?>

                        </span>

                      </td>


                      <!-- Actions -->

                      <td class="text-end pe-3">

                        <div class="d-flex justify-content-end gap-1">

                          <button
                            type="button"
                            class="btn btn-sm btn-primary"
                            onclick="viewInspection(<?= (int) $inspection['inspect_id'] ?>)">

                            <i class="bi bi-eye me-1"></i>

                            View

                          </button>


                          <?php if ($status === 'Pending'): ?>

                            <!-- Approve -->

                            <button
                              type="button"
                              class="btn btn-sm btn-success"
                              onclick="approveInspection(<?= (int) $inspection['inspect_id'] ?>)">

                              <i class="bi bi-check-lg me-1"></i>

                              Approve

                            </button>


                            <!-- Reject -->

                            <button
                              type="button"
                              class="btn btn-sm btn-danger"
                              onclick="rejectInspection(<?= (int) $inspection['inspect_id'] ?>)">

                              <i class="bi bi-x-lg me-1"></i>

                              Reject

                            </button>


                          <?php else: ?>

                            <!-- Details -->

                            <a
                              href="view-inspection.php?id=<?= urlencode($inspection['inspect_id']) ?>"
                              class="btn btn-sm btn-light border">

                              <i class="bi bi-file-text me-1"></i>

                              Details

                            </a>

                          <?php endif; ?>

                        </div>

                      </td>

                    </tr>

                  <?php endforeach; ?>


                  <!-- No Search Result -->

                  <tr
                    id="noSearchResult"
                    style="display: none;">

                    <td
                      colspan="5"
                      class="text-center py-5">

                      <div class="text-body-secondary">

                        <i
                          class="bi bi-search fs-1 d-block mb-2">
                        </i>

                        No inspection records found.

                      </div>

                    </td>

                  </tr>


                <?php else: ?>

                  <!-- No Database Records -->

                  <tr>

                    <td
                      colspan="5"
                      class="text-center py-5">

                      <div class="text-body-secondary">

                        <i
                          class="bi bi-clipboard-x fs-1 d-block mb-2">
                        </i>

                        <div class="fw-semibold">
                          No inspection records found.
                        </div>

                        <small>
                          There are currently no inspections for approval.
                        </small>

                      </div>

                    </td>

                  </tr>

                <?php endif; ?>

              </tbody>

            </table>

          </div>

        </div>


        <!-- ========================================================== -->
        <!-- PAGINATION FOOTER -->
        <!-- ========================================================== -->

        <div
          class="position-fixed bottom-0 end-0 bg-body border-top shadow py-2"
          style="width: calc(100% - 260px); z-index: 1020;">

          <div class="container-fluid px-3 px-md-4">

            <div class="d-flex flex-column flex-sm-row justify-content-between align-items-center gap-2">

              <!-- Showing -->
              <div class="text-body-secondary small text-center text-sm-start">
                Showing
                <strong>
                  <?= $totalRecords > 0 ? min($offset + 1, $totalRecords) : 0 ?>
                </strong>
                -
                <strong>
                  <?= min($offset + $limit, $totalRecords) ?>
                </strong>
                of
                <strong>
                  <?= $totalRecords ?>
                </strong>
                inspections
              </div>

              <!-- Pagination -->
              <?php if ($totalPages > 1): ?>

                <nav aria-label="Inspection pagination">

                  <ul class="pagination pagination-sm mb-0">

                    <!-- Previous -->
                    <li
                      class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">

                      <a
                        class="page-link px-3"
                        href="inspection-approvals.php?page=<?= max(1, $page - 1) ?><?= !empty($date) ? '&date=' . urlencode($date) : '' ?>"
                        aria-label="Previous">

                        <i class="bi bi-chevron-left"></i>

                        <span class="d-none d-sm-inline ms-1">
                          Previous
                        </span>

                      </a>

                    </li>

                    <!-- Page Numbers -->
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>

                      <li
                        class="page-item <?= ($i == $page) ? 'active' : '' ?>">

                        <a
                          class="page-link px-3"
                          href="inspection-approvals.php?page=<?= $i ?><?= !empty($date) ? '&date=' . urlencode($date) : '' ?>">

                          <?= $i ?>

                        </a>

                      </li>

                    <?php endfor; ?>

                    <!-- Next -->
                    <li
                      class="page-item <?= ($page >= $totalPages) ? 'disabled' : '' ?>">

                      <a
                        class="page-link px-3"
                        href="inspection-approvals.php?page=<?= min($totalPages, $page + 1) ?><?= !empty($date) ? '&date=' . urlencode($date) : '' ?>"
                        aria-label="Next">

                        <span class="d-none d-sm-inline me-1">
                          Next
                        </span>

                        <i class="bi bi-chevron-right"></i>

                      </a>

                    </li>

                  </ul>

                </nav>

              <?php endif; ?>

            </div>

          </div>

        </div>

      </div>

    </div>


    <?php include './partials/view-inspection-modal.php'; ?>




    <!-- CoreUI and necessary plugins -->

    <script src="vendors/@coreui/coreui/js/coreui.bundle.min.js"></script>

    <script src="./js/inspection-approvals.js"></script>

    <script src="vendors/simplebar/js/simplebar.min.js"></script>


    <?php include_once 'notification/session_timeout.php'; ?>

</body>

</html>