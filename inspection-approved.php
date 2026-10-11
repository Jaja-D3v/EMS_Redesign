<?php

require_once 'backend/authentication/SessionChecker.php';
require_once 'backend/controller/InspectionController.php';
require_once 'backend/controller/DropdownController.php';

$limit = 10;

$page = isset($_GET['page'])
  ? (int) $_GET['page']
  : 1;

if ($page < 1) {
  $page = 1;
}

$date = $_GET['date'] ?? null;

if ($date === '') {
  $date = null;
}

$totalRecords = getApprovedApprovalTotal($date);
$totalPages = (int) ceil($totalRecords / $limit);

if ($totalPages > 0 && $page > $totalPages) {
  $page = $totalPages;
}

$offset = ($page - 1) * $limit;

$result = getApprovedApprovals(
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


// _________________
$date = $_GET['date'] ?? null;

if ($date === '') {
  $date = null;
}

$branch = $_GET['branch'] ?? $_SESSION['Branch'];
$approvedReportIds = getAllApprovedApprovalIdsController($date, $branch);
$approvedReportIdString = implode(',', $approvedReportIds);
?>

<!DOCTYPE html>

<!-- CoreUI - Free Bootstrap Admin Template
     @version v5.5.0
     @link https://coreui.io/product/free-bootstrap-admin-template/
     Copyright (c) 2026 creativeLabs Łukasz Holeczek
     Licensed under MIT
-->

<html lang="en">

<?php
include 'partials/header.php';
?>

<body>

  <?php include 'partials/side-nav.php'; ?>
  <div class="wrapper d-flex flex-column min-vh-100">
    <?php include 'partials/header-nav.php'; ?>
    <div class="container-fluid py-0 inspection-content">

      <!-- Page Header -->
      <div
        class="card border-0 shadow-sm text-white mb-3 mt-0 overflow-hidden inspection-header"
        style="background: linear-gradient(135deg, #1e3dc8, #57f9ff);">

        <div class="card-body p-4">
          <div class="row align-items-center g-3">

            <div class="col-auto">
              <div class="bg-white bg-opacity-25 rounded-3 p-3 fs-2 inspection-header-icon">
                <i class="bi bi-clipboard2-check"></i>
              </div>
            </div>

            <div class="col">
              <h2 class="fw-bold mb-1">
                Approved Inspection Record
              </h2>

              <p class="mb-0 text-white-50">
                Approved inspection reports.
              </p>
            </div>

          </div>
        </div>

      </div>

      <!-- Filters -->
      <div class="card border-0 shadow-sm mb-3 inspection-filter-card">
        <div class="card-body">
          <div class="row g-3 align-items-center">

            <!-- Search -->
            <div class="col-12 col-lg">
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

            <!-- Date Filter -->
            <div class="col-12 col-md-6 col-lg-3">
              <div class="input-group">
                <span class="input-group-text bg-white">
                  <i class="bi bi-calendar3"></i>
                </span>
                <input
                  type="date"
                  class="form-control"
                  id="inspectionDateFilter"
                  value="<?= htmlspecialchars($date ?? '') ?>"
                  onchange="
                          var selectedDate = this.value;
                          var branchFilter = document.getElementById('branchFilter');
                          var branch = branchFilter ? branchFilter.value : '';

                          var url = 'inspection-approved.php?page=1';

                          if (selectedDate !== '') {
                              url += '&date=' + encodeURIComponent(selectedDate);
                          }

                          if (branch !== '') {
                              url += '&branch=' + encodeURIComponent(branch);
                          }

                          window.location.href = url;
                      ">
              </div>
            </div>

            <!-- Branch Filter -->
            <?php if (strtolower(trim($_SESSION['Role'] ?? '')) === 'admin'): ?>

              <?php
              $adminBranch = trim($_SESSION['Branch'] ?? '');
              $selectedBranch = $_GET['branch'] ?? $adminBranch;

              if (empty($selectedBranch)) {
                $selectedBranch = 'all';
              }

              $branches = getAllDropdownBranches();
              ?>

              <div class="col-12 col-md-6 col-lg-3">
                <select class="form-select w-100" id="branchFilter">

                  <option
                    value="all"
                    <?= $selectedBranch === 'all' ? 'selected' : '' ?>>
                    All Branches
                  </option>

                  <?php if (!empty($branches)): ?>

                    <?php foreach ($branches as $branch): ?>

                      <option
                        value="<?= htmlspecialchars($branch['value']) ?>"
                        <?= $selectedBranch === $branch['value'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($branch['value']) ?>
                      </option>

                    <?php endforeach; ?>

                  <?php endif; ?>

                </select>

                <script>
                  document.getElementById('branchFilter')?.addEventListener('change', function() {
                    const branch = this.value;
                    const date = document.getElementById('inspectionDateFilter')?.value || '';

                    let url = 'inspection-approved.php?page=1';

                    if (date) {
                      url += '&date=' + encodeURIComponent(date);
                    }

                    if (branch) {
                      url += '&branch=' + encodeURIComponent(branch);
                    }

                    window.location.href = url;
                  });
                </script>
              </div>

            <?php endif; ?>

            <!-- Generate Report -->
            <div class="col-12 col-lg-auto">
              <?php if (!empty($approvedReportIds)): ?>

                <a
                  href="helpers/generate-report.php?ids=<?= urlencode($approvedReportIdString) ?>"
                  class="btn btn-primary w-100"
                  onclick="generateReport(event, this.href)">
                  <i class="bi bi-file-earmark-pdf me-1"></i>
                  Generate Report
                </a>

              <?php else: ?>

                <button
                  type="button"
                  class="btn btn-primary w-100"
                  disabled>
                  <i class="bi bi-file-earmark-pdf me-1"></i>
                  Generate Report
                </button>

              <?php endif; ?>
            </div>

          </div>
        </div>
      </div>

      <!-- Inspection Table -->
      <div class="card border-0 shadow-sm inspection-table-card">

        <div class="card-body p-0">

          <div
            class="bg-body border-top shadow-sm py-2 inspection-pagination"
            style="position: sticky; bottom: 0; z-index: 1;">

            <table class="table table-hover align-middle inspection-table">

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
                    Branch
                  </th>

                  <th class="py-3 text-nowrap">
                    Status
                  </th>

                  <th class="py-3 text-nowrap text-end pe-4">
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

                    $inspectionDate = '';

                    if (!empty($inspection['date_inspected'])) {
                      $timestamp = strtotime($inspection['date_inspected']);

                      if ($timestamp !== false) {
                        $inspectionDate = date('Y-m-d', $timestamp);
                      }
                    }
                    ?>

                    <tr
                      class="inspection-row"
                      data-fe-code="<?= htmlspecialchars(
                                      strtolower($inspection['extinguisher_code'] ?? '')
                                    ) ?>"
                      data-location="<?= htmlspecialchars(
                                        strtolower($inspection['location'] ?? '')
                                      ) ?>"
                      data-status="<?= htmlspecialchars($statusFilterValue) ?>"
                      data-date="<?= htmlspecialchars($inspectionDate) ?>">

                      <td class="ps-4">
                        <span class="text-primary fw-semibold">
                          <?= htmlspecialchars(
                            $inspection['extinguisher_code'] ?? '—'
                          ) ?>
                        </span>
                      </td>

                      <td>
                        <?= htmlspecialchars(
                          $inspection['location'] ?? '—'
                        ) ?>
                      </td>

                      <td class="text-nowrap">
                        <?php if (!empty($inspection['date_inspected'])): ?>

                          <?php
                          $timestamp = strtotime($inspection['date_inspected']);
                          ?>

                          <?php if ($timestamp !== false): ?>

                            <?= date('M d, Y', $timestamp) ?>

                            <small class="text-body-secondary d-block">
                              <?= date('h:i A', $timestamp) ?>
                            </small>

                          <?php else: ?>

                            —

                          <?php endif; ?>

                        <?php else: ?>

                          —

                        <?php endif; ?>
                      </td>

                      <td>
                        <?= htmlspecialchars(
                          $inspection['branch'] ?? '—'
                        ) ?>
                      </td>

                      <td>
                        <span
                          class="badge rounded-pill <?= $statusClass ?> px-3 py-2">

                          <i class="bi <?= $statusIcon ?> me-1"></i>

                          <?= htmlspecialchars($statusLabel) ?>

                        </span>
                      </td>

                      <td class="text-end pe-4">
                        <div class="inspection-actions">

                          <button
                            type="button"
                            class="btn btn-sm btn-primary"
                            onclick="viewInspection(<?= (int) $inspection['inspect_id'] ?>)">

                            <i class="bi bi-eye me-1"></i>
                            View

                          </button>

                          <?php if ($status === 'Pending'): ?>

                            <button
                              type="button"
                              class="btn btn-sm btn-success"
                              onclick="approveInspection(<?= (int) $inspection['inspect_id'] ?>)">

                              <i class="bi bi-check-lg me-1"></i>
                              Approve

                            </button>

                            <button
                              type="button"
                              class="btn btn-sm btn-danger"
                              onclick="rejectInspection(<?= (int) $inspection['inspect_id'] ?>)">

                              <i class="bi bi-x-lg me-1"></i>
                              Reject

                            </button>

                          <?php endif; ?>

                        </div>
                      </td>

                    </tr>

                  <?php endforeach; ?>

                  <!-- Search Result State -->
                  <tr
                    id="noSearchResult"
                    style="display: none;">

                    <td
                      colspan="6"
                      class="text-center py-5">

                      <div class="text-body-secondary">
                        <i class="bi bi-search fs-1 d-block mb-2"></i>
                        No inspection records found.
                      </div>

                    </td>

                  </tr>

                <?php else: ?>

                  <!-- Empty State -->
                  <tr>
                    <td
                      colspan="6"
                      class="text-center py-5">

                      <div class="text-body-secondary inspection-empty-state">
                        <div>
                          <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                          No inspection records found.
                        </div>
                      </div>

                    </td>
                  </tr>

                <?php endif; ?>

              </tbody>

            </table>

          </div>

        </div>

        <!-- Pagination -->
        <div
          class="position-fixed bottom-0 start-0 end-0 bg-body border-top shadow-sm py-2 inspection-pagination"
          style="z-index: 1;">

          <div class="container-fluid px-3 px-md-4">

            <div
              class="inspection-pagination-inner d-flex flex-column flex-md-row justify-content-between align-items-center gap-2">

              <div class="text-body-secondary small text-center text-md-start">
                Showing

                <strong>
                  <?= $totalRecords > 0 ? $offset + 1 : 0 ?>
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

              <?php if ($totalPages > 1): ?>

                <nav aria-label="Inspection pagination">

                  <ul class="pagination pagination-sm mb-0">

                    <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">

                      <a
                        class="page-link"
                        href="inspection-rejected.php?page=<?= max(1, $page - 1) ?><?= !empty($date) ? '&date=' . urlencode($date) : '' ?>"
                        aria-label="Previous">

                        <i class="bi bi-chevron-left"></i>

                        <span class="d-none d-lg-inline ms-1">
                          Previous
                        </span>

                      </a>

                    </li>

                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>

                      <li
                        class="page-item d-none d-md-block <?= ($i == $page) ? 'active' : '' ?>">

                        <a
                          class="page-link"
                          href="inspection-rejected.php?page=<?= $i ?><?= !empty($date) ? '&date=' . urlencode($date) : '' ?>">

                          <?= $i ?>

                        </a>

                      </li>

                    <?php endfor; ?>

                    <li class="page-item d-md-none active">

                      <span class="page-link">
                        <?= $page ?> / <?= $totalPages ?>
                      </span>

                    </li>

                    <li class="page-item <?= ($page >= $totalPages) ? 'disabled' : '' ?>">

                      <a
                        class="page-link"
                        href="inspection-rejected.php?page=<?= min($totalPages, $page + 1) ?><?= !empty($date) ? '&date=' . urlencode($date) : '' ?>"
                        aria-label="Next">

                        <span class="d-none d-lg-inline me-1">
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

    <script src="vendors/@coreui/coreui/js/coreui.bundle.min.js"></script>
    <script src="./js/inspection-approvals.js"></script>
    <script src="vendors/simplebar/js/simplebar.min.js"></script>

    <?php
    include_once 'notification/session_timeout.php';
    include_once 'notification/loading-for-generating-report.php';
    ?>



</body>

</html>