<?php
include './backend/controller/FireExtinguisherController.php';
include './backend/controller/DropdownController.php';
include 'backend/controller/QRCodeGeneratorController.php';
require_once 'backend/authentication/SessionChecker.php';
include_once 'notification/loading-for-generating-report.php';


$branchDropdown = getAllDropdownBranches();

?>

<!DOCTYPE html>
<!--
* CoreUI - Free Bootstrap Admin Template
* @version v5.5.0
* @link https://coreui.io/product/free-bootstrap-admin-template/
* Copyright (c) 2026 creativeLabs Łukasz Holeczek
* Licensed under MIT (https://github.com/coreui/coreui-free-bootstrap-admin-template/blob/main/LICENSE)
-->

<html lang="en">
<?php include 'partials/header.php'; ?>

<body class="ims-inventory-page">
  <?php include 'partials/side-nav.php'; ?>
  <div class="wrapper d-flex flex-column min-vh-100">
    <?php
    include 'partials/header-nav.php';
    include_once 'notification/updated_fe_success.php';
    include_once 'notification/delete_fe_success.php';
    include_once 'notification/added_fe_success.php';
    ?>
    <div class="container-fluid py-0">

      <div
        class="card ims-inventory-hero border mb-1 mt-0 overflow-hidden"
        style="background: #fff;">

        <div class="card-body p-3">

          <div class="row align-items-center g-3">

            <!-- Icon -->
            <div class="col-auto">

              <div class="bg-white bg-opacity-10 rounded-3 p-3 fs-3">
                <i class="bi bi-fire"></i>
              </div>

            </div>

            <!-- Title & Description -->
            <div class="col">

              <h2 class="fw-bold mb-1">
                Active Fire Extinguishers
              </h2>

              <p class="mb-0 text-white-50">
                Manage and monitor all active and registered fire extinguishers.
              </p>

            </div>

            <!-- Generate Report -->
            <div class="col-12 col-md-auto">

              <button
                type="button"
                class="btn btn-light px-3 py-2 w-100"
                id="generateInventoryReportBtn">
                <i class="bi bi-file-earmark-pdf me-1"></i>
                Generate Inventory Report
              </button>

            </div>

          </div>

        </div>

      </div>

    </div>

    <div class="body flex-grow-1">
      <div class="container-fluid py-0">
        <div class="container py-2">

          <div class="row justify-content-center">

            <!-- Content -->



            <div class="ims-inventory-controls d-flex flex-column flex-lg-row align-items-stretch align-items-lg-center gap-2 mb-2">

              <!-- Sort - Left -->
              <div>
                <div class="input-group input-group-sm">
                  <span class="input-group-text bg-body border-end-0">
                    <i class="bi bi-funnel text-primary"></i>
                  </span>

                  <select
                    id="conditionFilter"
                    class="form-select form-select-sm border-start-0 ps-1"
                    aria-label="Filter by condition">

                    <option value="all"
                      <?= ($_GET['condition'] ?? 'all') === 'all'
                        ? 'selected'
                        : '' ?>>
                      All Conditions
                    </option>

                    <option value="good"
                      <?= ($_GET['condition'] ?? '') === 'good'
                        ? 'selected'
                        : '' ?>>
                      Good Condition
                    </option>

                    <option value="not-good"
                      <?= ($_GET['condition'] ?? '') === 'not-good'
                        ? 'selected'
                        : '' ?>>
                      Not Good
                    </option>

                  </select>
                </div>
              </div>

              <!-- Placement Type Filter -->
              <div>
                <div class="input-group input-group-sm">
                  <span class="input-group-text bg-body border-end-0">
                    <i class="bi bi-pin-map text-primary"></i>
                  </span>

                  <select
                    id="placementTypeFilter"
                    class="form-select form-select-sm border-start-0 ps-1">

                    <option value="all"
                      <?= ($_GET['placement'] ?? 'all') === 'all'
                        ? 'selected'
                        : '' ?>>
                      All Placement Types
                    </option>

                    <option value="installed"
                      <?= ($_GET['placement'] ?? '') === 'installed'
                        ? 'selected'
                        : '' ?>>
                      Installed
                    </option>

                    <option value="spare"
                      <?= ($_GET['placement'] ?? '') === 'spare'
                        ? 'selected'
                        : '' ?>>
                      Spare
                    </option>

                  </select>
                </div>
              </div>


              <!-- Branch Filter - ADMIN ONLY -->
              <?php if (strtolower(trim($_SESSION['Role'] ?? '')) === 'admin'): ?>

                <?php
                // Branch ng naka-login na Admin
                $adminBranch = trim($_SESSION['Branch'] ?? '');

                // Kung may branch sa URL, iyon ang gagamitin.
                // Kung wala, gamitin ang branch ng Admin.
                $selectedBranch = $_GET['branch'] ?? $adminBranch;

                // Kung walang branch sa session at wala rin sa URL,
                // default sa All Branches
                if (empty($selectedBranch)) {
                  $selectedBranch = 'all';
                }

                // Get all branches
                $branches = getAllDropdownBranches();
                ?>

                <div class="col-12 col-md-auto">

                  <select
                    class="form-select form-select-sm w-100"
                    id="branchFilter"
                    >

                    <!-- All Branches -->
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

                </div>

              <?php endif; ?>


              <script>
                document.addEventListener('DOMContentLoaded', function() {

                  const branchFilter = document.getElementById('branchFilter');

                  if (!branchFilter) {
                    return;
                  }

                  branchFilter.addEventListener('change', function() {

                    const selectedBranch = this.value;

                    const url = new URL(window.location.href);

                    // Always set the branch parameter.
                    // This allows "all" to remain selected after reload.
                    url.searchParams.set('branch', selectedBranch);
                    url.searchParams.set('page', '1');

                    // Reload page with selected branch
                    window.location.href = url.toString();

                  });

                });
              </script>


              <!-- Add + Search - Right -->
              <div class="d-grid d-sm-flex flex-fill flex-wrap flex-sm-nowrap gap-2 ms-lg-auto">

                <button
                  type="button"
                  class="btn btn-primary btn-sm text-nowrap"
                  data-bs-toggle="modal"
                  data-bs-target="#addFireExtinguisherModal">
                  <i class="bi bi-plus-lg me-1"></i>
                  Add Fire Extinguisher
                </button>

                <div class="input-group input-group-sm flex-fill">
                  <input
                    type="text"
                    id="fireExtinguisherSearchInput"
                    class="form-control"
                    placeholder="Search..."
                    value="<?= htmlspecialchars(
                              $_GET['search'] ?? '',
                              ENT_QUOTES,
                              'UTF-8'
                            ) ?>">

                  <button
                    id="searchFireExtinguisherBtn"
                    class="btn btn-primary"
                    type="button">
                    Search
                  </button>
                </div>



              </div>

            </div>
            <?php

            $limit = 10;

            $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;

            if ($page < 1) {
              $page = 1;
            }

            $offset = ($page - 1) * $limit;


            // ============================================================
            // PLACEMENT TYPE FILTER
            // ============================================================

            $placementType = strtolower(
              trim($_GET['placement'] ?? 'all')
            );

            $conditionType = strtolower(
              trim($_GET['condition'] ?? 'all')
            );

            $search = trim($_GET['search'] ?? '');


            // ============================================================
            // GET FIRE EXTINGUISHERS
            // ============================================================

            $info = getAllFireExtinguishers(
              $limit,
              $offset,
              $placementType,
              $conditionType,
              $search
            );


            // ============================================================
            // GET FILTERED TOTAL
            // ============================================================

            $totalRecords = getTotalFireExtinguishers(
              $placementType,
              $conditionType,
              $search
            );

            $totalPages = (int) ceil(
              $totalRecords / $limit
            );


            // ============================================================
            // PREVENT INVALID PAGE
            // ============================================================

            if ($totalPages > 0 && $page > $totalPages) {

              $page = $totalPages;

              $offset = ($page - 1) * $limit;

              $info = getAllFireExtinguishers(
                $limit,
                $offset,
                $placementType,
                $conditionType,
                $search
              );
            }
            // The Controller/Model may return false when records cannot be loaded.
            $inventoryLoadFailed = !is_iterable($info);
            if ($inventoryLoadFailed) {
              $info = [];
            }
            $inventoryIsEmpty = !$inventoryLoadFailed && (
              is_array($info)
                ? empty($info)
                : (isset($info->num_rows) && (int) $info->num_rows === 0)
            );
            ?>
            
            <div id="inventoryResults" class="ims-inventory-results">
              <div class="ims-inventory-table-wrap">
                <table class="table ims-inventory-table align-middle mb-0">
                  <thead><tr><th scope="col">Equipment / Code</th><th scope="col">Branch / Location</th><th scope="col">Type / Capacity</th><th scope="col">Expiration Date</th><th scope="col">Condition</th><th scope="col" class="text-end">Actions</th></tr></thead>
                  <tbody>
                    <?php foreach ($info as $data): ?>
                      <?php
                      $condition = $data['condition_status'];
                      $conditionIsGood = ($condition === 'Good');
                      $badgeClass = $conditionIsGood ? 'ims-condition-good' : 'ims-condition-service';
                      $statusIcon = $conditionIsGood ? 'bi-check-circle-fill' : 'bi-exclamation-circle-fill';
                      $expirationBadge = null;
                      $expirationBadgeClass = '';
                      $expirationIcon = '';
                      if (!empty($data['expiration_date'])) {
                        $today = new DateTime('today');
                        $expirationDate = new DateTime($data['expiration_date']);
                        $twoMonthsFromNow = (clone $today)->modify('+2 months');
                        if ($expirationDate < $today) {
                          $expiredDays = $today->diff($expirationDate)->days;
                          $expirationBadge = "Expired {$expiredDays} days ago";
                          $expirationBadgeClass = 'ims-expiration-expired';
                          $expirationIcon = 'bi-exclamation-triangle-fill';
                        } elseif ($expirationDate <= $twoMonthsFromNow) {
                          $remainingDays = $today->diff($expirationDate)->days;
                          $expirationBadge = "Expires in {$remainingDays} days";
                          $expirationBadgeClass = 'ims-expiration-soon';
                          $expirationIcon = 'bi-hourglass-split';
                        }
                      }
                      ?>
                      <tr class="extinguisher-card border border-secondary-subtle rounded-2" data-placement="<?= htmlspecialchars($data['placement'] ?? '') ?>">
                        <td data-label="Equipment / Code"><div class="ims-inventory-primary text-break"><i class="bi bi-fire" aria-hidden="true"></i><span>Fire Extinguisher</span></div><div class="ims-inventory-secondary text-break"><i class="bi bi-qr-code me-1" aria-hidden="true"></i><?= htmlspecialchars($data['extinguisher_code']) ?></div></td>
                        <td data-label="Branch / Location"><div class="ims-inventory-primary text-break"><?= htmlspecialchars($data['branch']) ?></div><div class="ims-inventory-secondary text-break"><i class="bi bi-geo-alt me-1" aria-hidden="true"></i><?= htmlspecialchars($data['location']) ?></div></td>
                        <td data-label="Type / Capacity"><div class="ims-inventory-primary text-break"><?= htmlspecialchars($data['type']) ?> <span class="ims-capacity-value"><?= htmlspecialchars($data['capacity']) ?></span></div><div class="ims-inventory-secondary text-break"><?php if (!empty($data['class'])): ?>Class <?= htmlspecialchars($data['class']) ?><?php endif; ?><?php if (!empty($data['class']) && !empty($data['placement'])): ?><span class="mx-1">·</span><?php endif; ?><?php if (!empty($data['placement'])): ?><?= htmlspecialchars(ucfirst($data['placement'])) ?><?php endif; ?></div></td>
                        <td data-label="Expiration Date"><?php if (!empty($data['expiration_date'])): ?><div class="ims-inventory-primary text-break"><?= htmlspecialchars(date('M d, Y', strtotime($data['expiration_date']))) ?></div><?php if ($expirationBadge !== null): ?><div class="ims-inventory-secondary text-break <?= htmlspecialchars($expirationBadgeClass) ?>"><i class="bi <?= htmlspecialchars($expirationIcon) ?> me-1" aria-hidden="true"></i><?= htmlspecialchars($expirationBadge) ?></div><?php endif; ?><?php else: ?><span class="ims-inventory-secondary text-break">Not recorded</span><?php endif; ?></td>
                        <td data-label="Condition"><span class="ims-condition-badge <?= htmlspecialchars($badgeClass) ?>"><i class="bi <?= htmlspecialchars($statusIcon) ?>" aria-hidden="true"></i><?= htmlspecialchars($condition) ?></span></td>
                        <td data-label="Actions" class="text-end ims-inventory-actions"><button type="button" class="btn btn-sm ims-view-button view-extinguisher-btn" data-id="<?= htmlspecialchars($data['extinguisher_id']) ?>" data-bs-toggle="modal" data-bs-target="#viewFireExtinguisherModal" aria-label="View extinguisher <?= htmlspecialchars($data['extinguisher_code']) ?>"><i class="bi bi-eye me-1" aria-hidden="true"></i>View</button></td>
                      </tr>
                    <?php endforeach; ?>
                    <?php if ($inventoryLoadFailed): ?>
                      <tr class="ims-inventory-empty ims-inventory-error"><td colspan="6" role="status">Inventory records could not be loaded. Please refresh the page or contact an administrator.</td></tr>
                    <?php elseif ($inventoryIsEmpty): ?>
                      <tr class="ims-inventory-empty"><td colspan="6" role="status">No fire extinguishers match the selected filters.</td></tr>
                    <?php endif; ?>
                  </tbody>
                </table>
              </div>
            </div>
            <!-- PAGINATION -->
            <div id="inventoryPagination" class="card border border-primary-subtle shadow-sm rounded-3 mt-3">

              <div class="card-footer bg-body border-0 px-3 py-3">

                <div class="d-flex flex-column flex-sm-row align-items-center justify-content-between gap-3">

                  <small class="text-body-secondary" style="font-size: 12px;">
                    <?php if ($totalRecords > 0): ?>
                      Showing
                      <strong class="text-body"><?= $offset + 1 ?></strong>
                      to
                      <strong class="text-body">
                        <?= min($offset + $limit, $totalRecords) ?>
                      </strong>
                      of
                      <strong class="text-body"><?= $totalRecords ?></strong>
                      entries
                    <?php else: ?>
                      Showing 0 of 0 entries
                    <?php endif; ?>
                  </small>

                  <?php if ($totalPages > 1): ?>

                    <?php
                    $currentPageUrl = basename($_SERVER['PHP_SELF']);

                    $paginationParams = $_GET;
                    unset($paginationParams['page']);

                    $previousParams = $paginationParams;
                    $previousParams['page'] = max(1, $page - 1);

                    $nextParams = $paginationParams;
                    $nextParams['page'] = min($totalPages, $page + 1);
                    ?>

                    <nav aria-label="Fire extinguisher pagination">
                      <ul class="pagination pagination-sm mb-0">

                        <!-- Previous -->
                        <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                          <?php if ($page > 1): ?>

                            <a
                              class="page-link"
                              href="<?= htmlspecialchars($currentPageUrl, ENT_QUOTES, 'UTF-8') ?>?<?= http_build_query($previousParams) ?>"
                              aria-label="Previous">
                              <i class="bi bi-chevron-left"></i>
                              <span class="d-none d-sm-inline ms-1">Previous</span>
                            </a>

                          <?php else: ?>

                            <span class="page-link">
                              <i class="bi bi-chevron-left"></i>
                              <span class="d-none d-sm-inline ms-1">Previous</span>
                            </span>

                          <?php endif; ?>
                        </li>

                        <!-- Page Numbers -->
                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>

                          <?php
                          $pageParams = $paginationParams;
                          $pageParams['page'] = $i;
                          ?>

                          <li class="page-item <?= $i === $page ? 'active' : '' ?>">
                            <a
                              class="page-link"
                              href="<?= htmlspecialchars($currentPageUrl, ENT_QUOTES, 'UTF-8') ?>?<?= http_build_query($pageParams) ?>">
                              <?= $i ?>
                            </a>
                          </li>

                        <?php endfor; ?>

                        <!-- Next -->
                        <li class="page-item <?= $page >= $totalPages ? 'disabled' : '' ?>">
                          <?php if ($page < $totalPages): ?>

                            <a
                              class="page-link"
                              href="<?= htmlspecialchars($currentPageUrl, ENT_QUOTES, 'UTF-8') ?>?<?= http_build_query($nextParams) ?>"
                              aria-label="Next">
                              <span class="d-none d-sm-inline me-1">Next</span>
                              <i class="bi bi-chevron-right"></i>
                            </a>

                          <?php else: ?>

                            <span class="page-link">
                              <span class="d-none d-sm-inline me-1">Next</span>
                              <i class="bi bi-chevron-right"></i>
                            </span>

                          <?php endif; ?>
                        </li>

                      </ul>
                    </nav>

                  <?php endif; ?>

                </div>

              </div>
            </div>

            <?php include 'partials/edit-fire-extinguisher-modal.php' ?>
            <!-- this form is for add new extinguisher -->
            <div
              class="modal fade"
              id="addFireExtinguisherModal"
              tabindex="-1"
              aria-labelledby="addFireExtinguisherModalLabel"
              aria-hidden="true">
              <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">

                <div class="modal-content rounded-4 border-0 shadow">

                  <!-- Header -->
                  <div class="modal-header px-4 py-3">
                    <div>
                      <h5 class="modal-title fw-semibold mb-1" id="addFireExtinguisherModalLabel">
                        Add Fire Extinguisher
                      </h5>

                      <small class="text-body-secondary">
                        Register a new fire extinguisher
                      </small>
                    </div>

                    <button
                      type="button"
                      class="btn-close"
                      data-bs-dismiss="modal"
                      aria-label="Close"></button>
                  </div>


                  <!-- Body -->
                  <div class="modal-body px-4 py-4">

                    <form
                      class="row g-3"
                      action="backend/controller/FireExtinguisherController.php"
                      method="post">

                      <input
                        type="hidden"
                        name="action"
                        value="add-new-extinguisher">


                      <!-- Fire Extinguisher Code -->
                      <div class="col-md-6">
                        <label for="extinguisherCode" class="form-label">
                          Fire Extinguisher Code
                        </label>

                        <input
                          type="text"
                          class="form-control"
                          id="extinguisherCode"
                          name="extinguisher_code"
                          placeholder="e.g. FE-001"
                          required>

                        <div id="extinguisherCodeFeedback" class="small mt-1"></div>
                      </div>


                      <!-- Type -->
                      <div class="col-md-6">
                        <label for="type" class="form-label">
                          Type
                        </label>

                        <select
                          id="type"
                          name="type"
                          class="form-select"
                          required>
                          <option value="" selected disabled>
                            Select type
                          </option>

                          <?php
                          $types = getAllTypeDropdownController();

                          if (!empty($types)):
                            foreach ($types as $type):
                          ?>
                              <option value="<?= htmlspecialchars($type['value']) ?>">
                                <?= htmlspecialchars($type['value']) ?>
                              </option>
                          <?php
                            endforeach;
                          endif;
                          ?>
                        </select>
                      </div>


                      <!-- Capacity -->
                      <div class="col-md-4">
                        <label for="capacity" class="form-label">
                          Capacity
                        </label>

                        <select
                          class="form-select"
                          id="capacity"
                          name="capacity"
                          required>
                          <option value="" selected disabled>
                            Select capacity
                          </option>

                          <?php
                          $capacities = getAllCapacityDropdownController();

                          if (!empty($capacities)):
                            foreach ($capacities as $capacity):
                          ?>
                              <option value="<?= htmlspecialchars($capacity['value']) ?>">
                                <?= htmlspecialchars($capacity['value']) ?>
                              </option>
                          <?php
                            endforeach;
                          endif;
                          ?>
                        </select>
                      </div>


                      <!-- Class -->
                      <div class="col-md-4">
                        <label for="fireClass" class="form-label">
                          Fire Class
                        </label>

                        <select
                          id="fireClass"
                          name="class"
                          class="form-select"
                          required>
                          <option value="" selected disabled>
                            Select class
                          </option>

                          <?php
                          $classes = getAllClassDropdownController();

                          if (!empty($classes)):
                            foreach ($classes as $class):
                          ?>
                              <option value="<?= htmlspecialchars($class['value']) ?>">
                                <?= htmlspecialchars($class['value']) ?>
                              </option>
                          <?php
                            endforeach;
                          endif;
                          ?>

                        </select>
                      </div>


                      <!-- Placement -->
                      <div class="col-md-4">
                        <label for="placement" class="form-label">
                          Placement
                        </label>

                        <select
                          id="placement"
                          name="placement"
                          class="form-select"
                          required>
                          <option value="" selected disabled>
                            Select placement
                          </option>

                          <?php
                          $placements = getAllPlacementDropdownController();

                          if (!empty($placements)):
                            foreach ($placements as $placement):
                          ?>
                              <option value="<?= htmlspecialchars($placement['value']) ?>">
                                <?= htmlspecialchars($placement['value']) ?>
                              </option>
                          <?php
                            endforeach;
                          endif;
                          ?>
                        </select>
                      </div>

                      <!-- Location -->
                      <div class="col-12 col-md-8">
                        <label for="location" class="form-label">
                          Location
                        </label>

                        <input
                          type="text"
                          class="form-control"
                          id="location"
                          name="location"
                          placeholder="e.g. Building 1 - 2nd Floor"
                          required>
                      </div>

                      <!-- Condition -->
                      <div class="col-12 col-md-4">
                        <label for="conditionStatus" class="form-label">
                          Condition
                        </label>

                        <select
                          id="conditionStatus"
                          name="condition_status"
                          class="form-select"
                          required>

                          <option value="" selected disabled>
                            Select condition
                          </option>

                          <option value="Good">Good</option>
                          <option value="Not Good">Not Good</option>

                        </select>
                      </div>


                      <!-- Manufactured Date -->
                      <div class="col-md-6">
                        <label for="manufacturedDate" class="form-label">
                          Manufactured Date
                        </label>

                        <input
                          type="date"
                          class="form-control"
                          id="manufacturedDate"
                          name="manufactured_date">
                      </div>


                      <!-- Expiration Date -->
                      <div class="col-md-6">
                        <label for="expirationDate" class="form-label">
                          Expiration Date
                        </label>

                        <input
                          type="date"
                          class="form-control"
                          id="expirationDate"
                          name="expiration_date"
                          required
                          readonly>
                      </div>

                      <!-- Branch -->
                      <div class="col-12 col-md-4">
                        <label for="branch" class="form-label">
                          Branch
                        </label>

                        <select
                          id="branch"
                          name="branch"
                          class="form-select"
                          required
                          onchange="generateFireExtinguisherCode()">
                          <option value="" selected disabled>
                            Select branch
                          </option>

                          <?php foreach ($branchDropdown as $branch): ?>
                            <option value="<?= htmlspecialchars($branch['value']) ?>">
                              <?= htmlspecialchars($branch['value']) ?>
                            </option>
                          <?php endforeach; ?>

                        </select>
                      </div>

                      <div class="col-12 text-end">
                        <small class="text-body-secondary">
                          Automatically computed as 3 years from the manufactured date.
                        </small>
                      </div>


                      <!-- Remarks -->
                      <div class="col-12">
                        <label for="remarks" class="form-label">
                          Remarks
                        </label>

                        <textarea
                          class="form-control"
                          id="remarks"
                          name="remarks"
                          rows="2"
                          placeholder="Additional remarks (optional)"></textarea>
                      </div>

                      <!-- Footer -->
                      <div class="modal-footer px-4 py-3">

                        <button
                          type="button"
                          class="btn btn-light border"
                          data-bs-dismiss="modal">
                          Cancel
                        </button>

                        <button
                          type="submit"
                          id="addFireExtinguisherBtn"
                          class="btn btn-primary px-4">
                          <i class="bi bi-plus-lg me-1"></i>
                          Add Fire Extinguisher
                        </button>

                      </div>

                    </form>

                  </div>

                </div>
              </div>
            </div>

            <!-- this is for view modal -->

            <!-- View Fire Extinguisher Modal -->
            <div
              class="modal fade"
              id="viewFireExtinguisherModal"
              tabindex="-1"
              aria-labelledby="viewFireExtinguisherModalLabel"
              aria-hidden="true">

              <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">

                <div class="modal-content rounded-4 border-0 shadow-lg overflow-hidden">

                  <!-- Header -->
                  <div class="modal-header border-0 bg-danger-subtle px-4 py-3">

                    <div class="d-flex align-items-center gap-3">

                      <div
                        class="d-flex align-items-center justify-content-center
                   bg-danger text-white rounded-3 flex-shrink-0"
                        style="width: 46px; height: 46px;">
                        <i class="bi bi-fire fs-4"></i>
                      </div>

                      <div>
                        <h5
                          class="modal-title fw-bold mb-1"
                          id="viewFireExtinguisherModalLabel">
                          Fire Extinguisher Details
                        </h5>

                        <small class="text-body-secondary">
                          View complete fire extinguisher information.
                        </small>
                      </div>


                    </div>

                    <button
                      type="button"
                      class="btn-close"
                      data-bs-dismiss="modal"
                      aria-label="Close">
                    </button>

                  </div>


                  <!-- Body -->
                  <div class="modal-body p-3 p-md-4">

                    <!-- Equipment Summary -->
                    <div class="card border-0 bg-light-subtle rounded-4 mb-3">

                      <div class="card-body p-3">

                        <div class="row align-items-center g-3">

                          <!-- Code -->
                          <div class="col-12 col-md-7">

                            <div class="text-body-secondary small mb-1">
                              Fire Extinguisher Code
                            </div>

                            <div class="d-flex align-items-center gap-2">

                              <div
                                class="d-flex align-items-center justify-content-center
                           bg-danger bg-opacity-10 text-danger rounded-3"
                                style="width: 36px; height: 36px;">
                                <i class="bi bi-qr-code"></i>
                              </div>

                              <input
                                type="text"
                                class="form-control-plaintext fw-bold fs-4 p-0"
                                id="viewExtinguisherCode"
                                readonly>

                            </div>

                          </div>


                          <!-- Condition -->
                          <div class="col-12 col-md-5">

                            <div class="text-body-secondary small mb-1">
                              Current Condition
                            </div>

                            <div class="d-flex align-items-center">

                              <input
                                type="text"
                                class="form-control-plaintext fw-bold p-0"
                                id="viewConditionStatus"
                                readonly>

                            </div>

                          </div>

                        </div>

                      </div>

                    </div>


                    <!-- Basic Information -->
                    <div class="mb-3">

                      <div class="d-flex align-items-center gap-2 mb-2">

                        <div
                          class="d-flex align-items-center justify-content-center
                     bg-primary bg-opacity-10 text-primary rounded-3"
                          style="width: 32px; height: 32px;">
                          <i class="bi bi-info-circle"></i>
                        </div>

                        <div>
                          <div class="fw-bold">
                            Basic Information
                          </div>

                          <small class="text-body-secondary">
                            Equipment specifications and placement
                          </small>
                        </div>

                      </div>


                      <div class="row g-2">

                        <!-- Type -->
                        <div class="col-12 col-md-6">

                          <div class="border rounded-3 p-3 h-100">

                            <label class="form-label small text-body-secondary mb-1">
                              <i class="bi bi-fire me-1 text-warning"></i>
                              Type
                            </label>

                            <input
                              type="text"
                              class="form-control-plaintext fw-semibold p-0"
                              id="viewType"
                              readonly>

                          </div>

                        </div>


                        <!-- Capacity -->
                        <div class="col-6 col-md-3">

                          <div class="border rounded-3 p-3 h-100">

                            <label class="form-label small text-body-secondary mb-1">
                              <i class="bi bi-box-seam me-1 text-primary"></i>
                              Capacity
                            </label>

                            <input
                              type="text"
                              class="form-control-plaintext fw-semibold p-0"
                              id="viewCapacity"
                              readonly>

                          </div>

                        </div>


                        <!-- Fire Class -->
                        <div class="col-6 col-md-3">

                          <div class="border rounded-3 p-3 h-100">

                            <label class="form-label small text-body-secondary mb-1">
                              <i class="bi bi-shield-check me-1 text-success"></i>
                              Fire Class
                            </label>

                            <input
                              type="text"
                              class="form-control-plaintext fw-semibold p-0"
                              id="viewClass"
                              readonly>

                          </div>

                        </div>


                        <!-- Placement -->
                        <div class="col-12 col-md-6">

                          <div class="border rounded-3 p-3 h-100">

                            <label class="form-label small text-body-secondary mb-1">
                              <i class="bi bi-pin-map me-1 text-danger"></i>
                              Placement
                            </label>

                            <input
                              type="text"
                              class="form-control-plaintext fw-semibold p-0"
                              id="viewPlacement"
                              readonly>

                          </div>

                        </div>


                        <!-- Location -->
                        <div class="col-12 col-md-6">
                          <div class="border rounded-3 p-3 h-100">
                            <label class="form-label small text-body-secondary mb-1">
                              <i class="bi bi-geo-alt me-1 text-primary"></i>
                              Location
                            </label>

                            <input
                              type="text"
                              class="form-control-plaintext fw-semibold p-0"
                              id="viewLocation"
                              readonly>

                          </div>
                        </div>
                      </div>
                    </div>


                    <!-- Important Dates -->
                    <div class="mb-3">

                      <div class="d-flex align-items-center gap-2 mb-2">

                        <div
                          class="d-flex align-items-center justify-content-center
                     bg-warning bg-opacity-10 text-warning rounded-3"
                          style="width: 32px; height: 32px;">
                          <i class="bi bi-calendar3"></i>
                        </div>

                        <div>
                          <div class="fw-bold">
                            Important Dates
                          </div>

                          <small class="text-body-secondary">
                            Manufacturing and expiration information
                          </small>
                        </div>

                      </div>


                      <div class="row g-2">

                        <!-- Manufactured Date -->
                        <div class="col-12 col-md-6">
                          <div class="border rounded-3 p-3">
                            <label class="form-label small text-body-secondary mb-1">
                              <i class="bi bi-calendar-event me-1 text-secondary"></i>
                              Manufactured Date
                            </label>
                            <input
                              type="date"
                              class="form-control-plaintext fw-semibold p-0"
                              id="viewManufacturedDate"
                              readonly>
                          </div>
                        </div>

                        <!-- Refilled Date -->
                        <div class="col-12 col-md-6">
                          <div class="border rounded-3 p-3">
                            <label class="form-label small text-body-secondary mb-1">
                              <i class="bi bi-arrow-repeat me-1 text-secondary"></i>
                              Last Refilled Date
                            </label>
                            <input
                              type="date"
                              class="form-control-plaintext fw-semibold p-0"
                              id="viewLastRefilledDate"
                              readonly>
                          </div>
                        </div>


                        <!-- Expiration Date -->
                        <div class="col-12 col-md-6">

                          <div class="border rounded-3 p-3">

                            <label class="form-label small text-body-secondary mb-1">
                              <i class="bi bi-calendar-x me-1 text-danger"></i>
                              Expiration Date
                            </label>

                            <input
                              type="date"
                              class="form-control-plaintext fw-semibold p-0"
                              id="viewExpirationDate"
                              readonly>

                            <small class="text-body-secondary d-block mt-1">
                              Automatically calculated as 3 years from the manufacturing or refill date.
                            </small>

                          </div>

                        </div>

                      </div>

                    </div>

                    <!-- Remarks -->
                    <div>

                      <div class="d-flex align-items-center gap-2 mb-2">

                        <div
                          class="d-flex align-items-center justify-content-center bg-secondary bg-opacity-10 text-secondary rounded-3"
                          style="width: 32px; height: 32px;">
                          <i class="bi bi-chat-left-text"></i>
                        </div>

                        <div>
                          <div class="fw-bold">
                            Remarks
                          </div>

                          <small class="text-body-secondary">
                            Additional information
                          </small>
                        </div>

                      </div>


                      <div class="border rounded-3 p-3 bg-light-subtle">

                        <textarea
                          class="form-control-plaintext text-body-secondary p-0"
                          id="viewRemarks"
                          rows="2"
                          readonly></textarea>

                      </div>

                    </div>

                  </div>


                  <!-- Footer -->
                  <div class="modal-footer border-0 bg-light-subtle px-3 px-md-4 py-3">

                    <button
                      type="button"
                      class="btn btn-light border px-3"
                      data-bs-dismiss="modal">
                      Close
                    </button>

                    <?php if (strtolower(trim($_SESSION['Role'] ?? '')) === 'admin'): ?>


                      <button
                        type="button"
                        id="viewEditFireExtinguisherBtn"
                        class="btn btn-warning px-4 edit-extinguisher-btn"
                        data-id=""
                        data-bs-toggle="modal"
                        data-bs-target="#editFireExtinguisherModal">

                        <i class="bi bi-pencil me-1"></i>
                        Edit

                      </button>

                      <a
                        href="#"
                        id="viewDeleteFireExtinguisherBtn"
                        class="btn btn-danger px-3">

                        <i class="bi bi-trash me-1"></i>
                        Archive

                      </a>
                    <?php endif; ?>

                  </div>

                </div>

              </div>

            </div>
            <script>
              // LOGIC FOR EXPIRATION DATE AUTO FILL BASED ON MANUFACTURE DATE
              const manufacturedDate = document.getElementById('manufacturedDate');
              const expirationDate = document.getElementById('expirationDate');

              manufacturedDate.addEventListener('change', function() {

                if (!this.value) {
                  expirationDate.value = '';
                  return;
                }

                const date = new Date(this.value + 'T00:00:00');

                // Add 3 years
                date.setFullYear(date.getFullYear() + 3);

                // Format to YYYY-MM-DD
                const year = date.getFullYear();
                const month = String(date.getMonth() + 1).padStart(2, '0');
                const day = String(date.getDate()).padStart(2, '0');

                expirationDate.value = `${year}-${month}-${day}`;
              });
            </script>
            <!-- end form is for add new extinguisher -->


            <!-- End content -->

          </div>
        </div>
      </div>
    </div>
    <?php include 'partials/footer.php'; ?>
  </div>



  <!-- CoreUI and necessary plugins-->
  <script src="vendors/@coreui/coreui/js/coreui.bundle.min.js"></script>
  <script src="vendors/simplebar/js/simplebar.min.js"></script>
  <script src="js/list-extinguisher.js"></script>

  <script>
    (function() {
      const searchInput = document.getElementById(
        'fireExtinguisherSearchInput'
      );

      const searchButton = document.getElementById(
        'searchFireExtinguisherBtn'
      );

      if (!searchInput || !searchButton) {
        return;
      }

      // =====================================================
      // UPDATE URL WHILE TYPING
      // Hindi pa ito magre-request ng search results.
      // =====================================================

      function updateSearchUrl() {
        const url = new URL(window.location.href);
        const searchValue = searchInput.value.trim();

        if (searchValue !== '') {
          url.searchParams.set('search', searchValue);
        } else {
          url.searchParams.delete('search');
        }

        // Bawat bagong search ay magsisimula sa page 1.
        url.searchParams.delete('page');

        window.history.replaceState({},
          '',
          url.toString()
        );
      }

      searchInput.addEventListener('input', function() {
        updateSearchUrl();

    
        if (searchInput.value.trim() === '') {
          loadInventoryResults(window.location.href);
        }
      });

      // =====================================================
      // FETCH SERVER-SIDE RESULTS WITHOUT PAGE RELOAD
      // =====================================================

      async function loadInventoryResults(requestUrl) {
        const originalButtonText = searchButton.innerHTML;

        searchButton.disabled = true;
        searchButton.innerHTML =
          '<span class="spinner-border spinner-border-sm me-1" ' +
          'aria-hidden="true"></span> Searching...';

        try {
          const response = await fetch(requestUrl, {
            method: 'GET',
            headers: {
              'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin'
          });

          if (!response.ok) {
            throw new Error(
              'Request failed. HTTP status: ' + response.status
            );
          }

          const html = await response.text();

          const parser = new DOMParser();

          const parsedDocument = parser.parseFromString(
            html,
            'text/html'
          );

          const newResults = parsedDocument.querySelector(
            '#inventoryResults'
          );

          const newPagination = parsedDocument.querySelector(
            '#inventoryPagination'
          );

          const currentResults = document.querySelector(
            '#inventoryResults'
          );

          const currentPagination = document.querySelector(
            '#inventoryPagination'
          );

          if (
            !newResults ||
            !newPagination ||
            !currentResults ||
            !currentPagination
          ) {
            throw new Error(
              'Hindi makita ang inventory results o pagination wrapper.'
            );
          }

          // Palitan lamang ang cards.
          currentResults.innerHTML = newResults.innerHTML;
          const reportButton = document.getElementById('generateInventoryReportBtn');
          if (reportButton) reportButton.disabled = !currentResults.querySelector('.extinguisher-card');

          // Keep the server-rendered empty table row and responsive wrapper.

          // Palitan ang entry count at pagination links.
          currentPagination.replaceWith(
            newPagination.cloneNode(true)
          );

        } catch (error) {
          console.error(
            'Inventory search error:',
            error
          );

          alert(
            'Hindi ma-load ang search results. ' +
            'Pakisubukan ulit.'
          );

        } finally {
          searchButton.disabled = false;
          searchButton.innerHTML = originalButtonText;
        }
      }

      // =====================================================
      // SEARCH BUTTON
      // =====================================================

      searchButton.addEventListener('click', function() {
        updateSearchUrl();

        loadInventoryResults(
          window.location.href
        );
      });

      // =====================================================
      // ENTER KEY
      // =====================================================

      searchInput.addEventListener('keydown', function(event) {
        if (event.key === 'Enter') {
          event.preventDefault();

          updateSearchUrl();

          loadInventoryResults(
            window.location.href
          );
        }
      });

      // =====================================================
      // AJAX PAGINATION
      // =====================================================

      document.addEventListener('click', function(event) {
        const pageLink = event.target.closest(
          '#inventoryPagination a.page-link[href]'
        );

        if (!pageLink) {
          return;
        }

        event.preventDefault();

        const targetUrl = new URL(
          pageLink.href,
          window.location.origin
        );

        window.history.pushState({},
          '',
          targetUrl.toString()
        );

        loadInventoryResults(
          targetUrl.toString()
        );
      });

      // =====================================================
      // BROWSER BACK / FORWARD
      // =====================================================

      window.addEventListener('popstate', function() {
        loadInventoryResults(
          window.location.href
        );
      });

    })();
  </script>


  <?php include_once 'notification/session_timeout.php'; ?>

</body>

</html>
