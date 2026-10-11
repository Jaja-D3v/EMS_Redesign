<?php
require_once 'backend/authentication/SessionChecker.php';
require_once 'backend/controller/FireExtinguisherController.php';
include './backend/controller/QRCodeGeneratorController.php';
include './backend/controller/DropdownController.php';


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

<body class="ims-inventory-page ims-archived-page">
    <?php include 'partials/side-nav.php'; ?>
    <div class="wrapper d-flex flex-column min-vh-100">
        <?php include 'partials/header-nav.php'; ?>

        <!-- CONTENT HERE -->
        <?php

        $limit = 10;

        $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;

        if ($page < 1) {
            $page = 1;
        }

        $offset = ($page - 1) * $limit;

        $info = getAllDeletedFireExtinguishers($limit, $offset);

        $totalRecords = getTotalDeletedFireExtinguishers();

        $totalPages = (int) ceil($totalRecords / $limit);
        ?>

        <div class="container-fluid py-0">

            <!-- ============================= -->
            <!-- PAGE HEADER -->
            <!-- ============================= -->

            <div
                class="card ims-inventory-hero border mb-1 mt-0 overflow-hidden"
                style="background: #fff;">

                <div class="card-body p-4">

                    <div class="row align-items-center g-3">

                        <!-- Icon -->
                        <div class="col-auto">

                            <div class="bg-white bg-opacity-10 rounded-3 p-3 fs-3">

                                <i class="bi bi-trash3"></i>

                            </div>

                        </div>

                        <!-- Title & Description -->
                        <div class="col">
                            <h2 class="fw-bold mb-1">
                                Deleted Fire Extinguishers
                            </h2>

                            <p class="mb-0 text-white-50">
                                View deleted fire extinguishers.
                            </p>

                        </div>

                    </div>

                </div>

            </div>

        </div>


        <div class="body flex-grow-1">

            <div class="container-fluid py-0">

                <div class="container py-4">

                    <!-- ============================= -->
                    <!-- SEARCH -->
                    <!-- ============================= -->

                    <div class="ims-inventory-controls d-flex flex-column flex-md-row gap-2 mb-2">

                        <div class="col-12 col-md-6 col-lg-4">

                        <div class="input-group input-group-sm">

                            <span class="input-group-text bg-body border-end-0">

                                <i class="bi bi-search text-primary"></i>

                            </span>

                            <input
                                type="text"
                                id="searchDeletedExtinguisher"
                                class="form-control form-control-sm border-start-0 ps-1"
                                placeholder="Search FE code or location...">

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
                                    class="form-select form-select-sm"
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

                    </div>


                    <!-- ============================= -->
                    <!-- DELETED FIRE EXTINGUISHERS -->
                    <!-- ============================= -->

                    <div id="deletedExtinguisherContainer">
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
          ?>
          <tr class="extinguisher-card border border-secondary-subtle rounded-2" data-placement="<?= htmlspecialchars($data['placement'] ?? '') ?>">
            <td data-label="Equipment / Code"><div class="ims-inventory-primary"><i class="bi bi-archive" aria-hidden="true"></i>Fire Extinguisher</div><div class="ims-inventory-secondary extinguisher-code"><i class="bi bi-qr-code me-1" aria-hidden="true"></i><?= htmlspecialchars($data['extinguisher_code']) ?></div></td>
            <td data-label="Branch / Location"><div class="ims-inventory-primary"><?= htmlspecialchars($data['branch']) ?></div><div class="ims-inventory-secondary extinguisher-location"><i class="bi bi-geo-alt me-1" aria-hidden="true"></i><?= htmlspecialchars($data['location']) ?></div></td>
            <td data-label="Type / Capacity"><div class="ims-inventory-primary"><?= htmlspecialchars($data['type']) ?> <span class="ims-capacity-value"><?= htmlspecialchars($data['capacity']) ?></span></div><div class="ims-inventory-secondary"><?php if (!empty($data['class'])): ?>Class <?= htmlspecialchars($data['class']) ?><?php endif; ?><?php if (!empty($data['class']) && !empty($data['placement'])): ?><span class="mx-1">·</span><?php endif; ?><?php if (!empty($data['placement'])): ?><?= htmlspecialchars(ucfirst($data['placement'])) ?><?php endif; ?></div></td>
            <td data-label="Expiration Date"><?php if (!empty($data['expiration_date'])): ?><div class="ims-inventory-primary"><?= htmlspecialchars(date('M d, Y', strtotime($data['expiration_date']))) ?></div><?php else: ?><span class="ims-inventory-secondary">Not recorded</span><?php endif; ?><div class="ims-inventory-secondary"><span class="ims-condition-badge ims-archived-badge"><i class="bi bi-archive" aria-hidden="true"></i>Archived</span></div></td>
            <td data-label="Condition"><span class="ims-condition-badge <?= htmlspecialchars($badgeClass) ?>"><i class="bi <?= htmlspecialchars($statusIcon) ?>" aria-hidden="true"></i><?= htmlspecialchars($condition) ?></span></td>
            <td data-label="Actions" class="text-end ims-inventory-actions"><button type="button" class="btn btn-sm ims-view-button view-extinguisher-btn" data-id="<?= htmlspecialchars($data['extinguisher_id']) ?>" data-bs-toggle="modal" data-bs-target="#viewDeletedFireExtinguisherModal" aria-label="View archived extinguisher <?= htmlspecialchars($data['extinguisher_code']) ?>"><i class="bi bi-eye me-1" aria-hidden="true"></i>View</button></td>
          </tr>
        <?php endforeach; ?>
        <?php if (empty($info)): ?>
          <tr class="ims-inventory-empty"><td colspan="6" role="status">No archived fire extinguishers.</td></tr>
        <?php endif; ?>
        <tr id="deletedSearchEmptyRow" class="ims-inventory-empty" hidden><td colspan="6" role="status">No archived records match your search on this page.</td></tr>
      </tbody>
    </table>
  </div>
</div>

                    <!-- ============================= -->
                    <!-- SEARCH SCRIPT -->
                    <!-- ============================= -->

                    <!-- ============================= -->

                    <script>
                        const deletedSearchInput =
                            document.getElementById('searchDeletedExtinguisher');

                        if (deletedSearchInput) {

                            deletedSearchInput.addEventListener('input', function() {

                                const searchValue =
                                    this.value.trim().toLowerCase();

                                const cards =
                                    document.querySelectorAll(
                                        '#deletedExtinguisherContainer .extinguisher-card'
                                    );

                                let visibleCount = 0;

                                cards.forEach(card => {

                                    const codeElement =
                                        card.querySelector('.extinguisher-code');

                                    const locationElement =
                                        card.querySelector('.extinguisher-location');

                                    const code = codeElement ?
                                        codeElement.textContent.trim().toLowerCase() :
                                        '';

                                    const location = locationElement ?
                                        locationElement.textContent.trim().toLowerCase() :
                                        '';

                                    const match =
                                        searchValue === '' ||
                                        code.includes(searchValue) ||
                                        location.includes(searchValue);

                                    if (match) {

                                        card.style.display = '';

                                        visibleCount++;

                                    } else {

                                        card.style.display = 'none';

                                    }

                                });

                                const emptyRow = document.getElementById('deletedSearchEmptyRow');
                                if (emptyRow) emptyRow.hidden = !(searchValue !== '' && cards.length > 0 && visibleCount === 0);

                            });

                        }
                    </script>


                    <!-- ============================= -->
                    <!-- PAGINATION -->
                    <?php
                    $archivedPaginationParams = $_GET;
                    unset($archivedPaginationParams['page']);
                    $archivedPageUrl = basename($_SERVER['PHP_SELF']);
                    $archivedPreviousParams = $archivedPaginationParams;
                    $archivedPreviousParams['page'] = max(1, $page - 1);
                    $archivedNextParams = $archivedPaginationParams;
                    $archivedNextParams['page'] = min($totalPages, $page + 1);
                    ?>
                    <!-- ============================= -->

                    <?php if ($totalPages > 1): ?>

                        <div
                            class="ims-archived-pagination bg-body border-top py-3 mt-3">

                            <div class="container-fluid px-2 px-sm-3 px-md-4">

                                <div
                                    class="d-flex flex-column flex-sm-row justify-content-between align-items-center gap-2">

                                    <!-- Showing -->

                                    <div
                                        class="text-body-secondary small text-center text-sm-start">

                                        Showing

                                        <strong>
                                            <?= min($offset + 1, $totalRecords) ?>
                                        </strong>

                                        -

                                        <strong>
                                            <?= min($offset + $limit, $totalRecords) ?>
                                        </strong>

                                        of

                                        <strong>
                                            <?= $totalRecords ?>
                                        </strong>

                                        deleted fire extinguishers

                                    </div>


                                    <!-- Pagination -->

                                    <nav aria-label="Deleted fire extinguisher pagination">

                                        <ul class="pagination pagination-sm mb-0">

                                            <!-- Previous -->

                                            <li
                                                class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">

                                                <a
                                                    class="page-link px-2 px-sm-3"
                                                    href="<?= htmlspecialchars($archivedPageUrl, ENT_QUOTES, 'UTF-8') ?>?<?= http_build_query($archivedPreviousParams) ?>"
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
                                                        class="page-link px-2 px-sm-3"
                                                        href="<?= htmlspecialchars($archivedPageUrl, ENT_QUOTES, 'UTF-8') ?>?<?= http_build_query(array_merge($archivedPaginationParams, ['page' => $i])) ?>">

                                                        <?= $i ?>

                                                    </a>

                                                </li>

                                            <?php endfor; ?>


                                            <!-- Next -->

                                            <li
                                                class="page-item <?= ($page >= $totalPages) ? 'disabled' : '' ?>">

                                                <a
                                                    class="page-link px-2 px-sm-3"
                                                    href="<?= htmlspecialchars($archivedPageUrl, ENT_QUOTES, 'UTF-8') ?>?<?= http_build_query($archivedNextParams) ?>"
                                                    aria-label="Next">

                                                    <span class="d-none d-sm-inline me-1">
                                                        Next
                                                    </span>

                                                    <i class="bi bi-chevron-right"></i>

                                                </a>

                                            </li>

                                        </ul>

                                    </nav>

                                </div>

                            </div>

                        </div>

                    <?php endif; ?>

                </div>

            </div>

        </div>

        <!-- modal for view  -->

        <!-- ======================================== -->
        <!-- VIEW DELETED FIRE EXTINGUISHER MODAL -->
        <!-- ======================================== -->

        <div
            class="modal fade"
            id="viewDeletedFireExtinguisherModal"
            tabindex="-1"
            aria-labelledby="viewDeletedFireExtinguisherModalLabel"
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
                                    id="viewDeletedFireExtinguisherModalLabel">

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
                        <div class="card border-0 bg-body-tertiary rounded-4 mb-4">

                            <div class="card-body p-3 p-md-4">

                                <div class="d-flex align-items-center gap-2 mb-3">

                                    <div
                                        class="d-flex align-items-center justify-content-center
                                bg-danger-subtle text-danger rounded-2"
                                        style="width: 34px; height: 34px;">

                                        <i class="bi bi-info-circle"></i>

                                    </div>

                                    <div>
                                        <h6 class="fw-bold mb-0">
                                            Equipment Summary
                                        </h6>

                                        <small class="text-body-secondary">
                                            Basic equipment information
                                        </small>
                                    </div>

                                </div>


                                <div class="row g-3">

                                    <!-- Code -->
                                    <div class="col-md-6">

                                        <label class="form-label small fw-semibold text-body-secondary mb-1">
                                            Fire Extinguisher Code
                                        </label>

                                        <div class="input-group">

                                            <span class="input-group-text bg-white border-end-0">
                                                <i class="bi bi-upc-scan text-danger"></i>
                                            </span>

                                            <input
                                                type="text"
                                                id="deletedViewExtinguisherCode"
                                                class="form-control border-start-0 ps-0"
                                                readonly>

                                        </div>

                                    </div>


                                    <!-- Branch -->
                                    <div class="col-md-6">

                                        <label class="form-label small fw-semibold text-body-secondary mb-1">
                                            Branch
                                        </label>

                                        <div class="input-group">

                                            <span class="input-group-text bg-white border-end-0">
                                                <i class="bi bi-building text-danger"></i>
                                            </span>

                                            <input
                                                type="text"
                                                id="deletedViewBranch"
                                                class="form-control border-start-0 ps-0"
                                                readonly>

                                        </div>

                                    </div>

                                </div>

                            </div>

                        </div>


                        <!-- Equipment Details -->
                        <div class="mb-3">

                            <div class="d-flex align-items-center gap-2 mb-3">

                                <div
                                    class="d-flex align-items-center justify-content-center
                            bg-danger-subtle text-danger rounded-2"
                                    style="width: 34px; height: 34px;">

                                    <i class="bi bi-fire"></i>

                                </div>

                                <div>
                                    <h6 class="fw-bold mb-0">
                                        Equipment Details
                                    </h6>

                                    <small class="text-body-secondary">
                                        Fire extinguisher specifications
                                    </small>
                                </div>

                            </div>


                            <div class="row g-3">

                                <!-- Type -->
                                <div class="col-md-6">

                                    <label class="form-label small fw-semibold text-body-secondary">
                                        Type
                                    </label>

                                    <input
                                        type="text"
                                        id="deletedViewType"
                                        class="form-control"
                                        readonly>

                                </div>


                                <!-- Capacity -->
                                <div class="col-md-6">

                                    <label class="form-label small fw-semibold text-body-secondary">
                                        Capacity
                                    </label>

                                    <input
                                        type="text"
                                        id="deletedViewCapacity"
                                        class="form-control"
                                        readonly>

                                </div>


                                <!-- Fire Class -->
                                <div class="col-md-6">

                                    <label class="form-label small fw-semibold text-body-secondary">
                                        Fire Class
                                    </label>

                                    <input
                                        type="text"
                                        id="deletedViewClass"
                                        class="form-control"
                                        readonly>

                                </div>


                                <!-- Placement -->
                                <div class="col-md-6">

                                    <label class="form-label small fw-semibold text-body-secondary">
                                        Placement
                                    </label>

                                    <input
                                        type="text"
                                        id="deletedViewPlacement"
                                        class="form-control"
                                        readonly>

                                </div>


                                <!-- Location -->
                                <div class="col-md-6">

                                    <label class="form-label small fw-semibold text-body-secondary">
                                        Location
                                    </label>

                                    <input
                                        type="text"
                                        id="deletedViewLocation"
                                        class="form-control"
                                        readonly>

                                </div>


                                <!-- Condition -->
                                <div class="col-md-6">

                                    <label class="form-label small fw-semibold text-body-secondary">
                                        Condition
                                    </label>

                                    <input
                                        type="text"
                                        id="deletedViewConditionStatus"
                                        class="form-control"
                                        readonly>

                                </div>

                            </div>

                        </div>


                        <!-- Dates & Maintenance -->
                        <div class="border-top pt-4 mt-4">

                            <div class="d-flex align-items-center gap-2 mb-3">

                                <div
                                    class="d-flex align-items-center justify-content-center
                            bg-danger-subtle text-danger rounded-2"
                                    style="width: 34px; height: 34px;">

                                    <i class="bi bi-calendar3"></i>

                                </div>

                                <div>
                                    <h6 class="fw-bold mb-0">
                                        Dates & Maintenance
                                    </h6>

                                    <small class="text-body-secondary">
                                        Equipment dates and maintenance information
                                    </small>
                                </div>

                            </div>


                            <div class="row g-3">

                                <!-- Manufactured Date -->
                                <div class="col-md-4">

                                    <label class="form-label small fw-semibold text-body-secondary">
                                        Manufactured Date
                                    </label>

                                    <div class="input-group">

                                        <span class="input-group-text bg-body-tertiary border-end-0">
                                            <i class="bi bi-calendar-event"></i>
                                        </span>

                                        <input
                                            type="text"
                                            id="deletedViewManufacturedDate"
                                            class="form-control border-start-0 ps-0"
                                            readonly>

                                    </div>

                                </div>


                                <!-- Last Refilled -->
                                <div class="col-md-4">

                                    <label class="form-label small fw-semibold text-body-secondary">
                                        Last Refilled Date
                                    </label>

                                    <div class="input-group">

                                        <span class="input-group-text bg-body-tertiary border-end-0">
                                            <i class="bi bi-arrow-repeat"></i>
                                        </span>

                                        <input
                                            type="text"
                                            id="deletedViewLastRefilledDate"
                                            class="form-control border-start-0 ps-0"
                                            readonly>

                                    </div>

                                </div>


                                <!-- Expiration -->
                                <div class="col-md-4">

                                    <label class="form-label small fw-semibold text-body-secondary">
                                        Expiration Date
                                    </label>

                                    <div class="input-group">

                                        <span class="input-group-text bg-body-tertiary border-end-0">
                                            <i class="bi bi-calendar-x"></i>
                                        </span>

                                        <input
                                            type="text"
                                            id="deletedViewExpirationDate"
                                            class="form-control border-start-0 ps-0"
                                            readonly>

                                    </div>

                                </div>

                            </div>

                        </div>


                        <!-- Remarks -->
                        <div class="border-top pt-4 mt-4">

                            <div class="d-flex align-items-center gap-2 mb-3">

                                <div
                                    class="d-flex align-items-center justify-content-center
                            bg-danger-subtle text-danger rounded-2"
                                    style="width: 34px; height: 34px;">

                                    <i class="bi bi-chat-left-text"></i>

                                </div>

                                <div>
                                    <h6 class="fw-bold mb-0">
                                        Remarks
                                    </h6>

                                    <small class="text-body-secondary">
                                        Additional information
                                    </small>
                                </div>

                            </div>


                            <textarea
                                id="deletedViewRemarks"
                                class="form-control"
                                rows="3"
                                readonly></textarea>

                        </div>

                    </div>


                    <!-- Footer -->
                    <div class="modal-footer border-0 bg-body-tertiary px-3 px-md-4 py-3">

                        <button
                            type="button"
                            class="btn btn-light border px-4"
                            data-bs-dismiss="modal">

                            <i class="bi bi-x-lg me-1"></i>
                            Close

                        </button>
                        <?php if (strtolower(trim($_SESSION['Role'] ?? '')) === 'admin'): ?>
                            <button
                                type="button"
                                id="restoreDeletedFireExtinguisherBtn"
                                class="btn btn-success px-4"
                                data-id="">

                                <i class="bi bi-arrow-counterclockwise me-1"></i>
                                Restore

                            </button>

                        <?php endif; ?>


                    </div>

                </div>
            </div>

        </div>
        <script>
            document.querySelectorAll('.view-extinguisher-btn').forEach(button => {

                button.addEventListener('click', function() {

                    const id = this.dataset.id;

                    const card = this.closest('.extinguisher-card');

                    const codeElement = card ?
                        card.querySelector('.extinguisher-code') :
                        null;

                    const extinguisherCode = codeElement ?
                        codeElement.textContent.trim() :
                        '';

                    const url =
                        `backend/controller/FireExtinguisherController.php` +
                        `?action=getDeleted` +
                        `&id=${encodeURIComponent(id)}` +
                        `&code=${encodeURIComponent(extinguisherCode)}`;

                    // Ilagay ang ID at extinguisher code sa browser URL
                    const browserUrl = new URL(window.location.href);

                    // browserUrl.searchParams.set('id', id);
                    browserUrl.searchParams.set('code', extinguisherCode);

                    window.history.replaceState({},
                        '',
                        browserUrl.toString()
                    );

                    fetch(url)

                        .then(response => {

                            if (!response.ok) {
                                throw new Error(
                                    'Failed to fetch fire extinguisher.'
                                );
                            }

                            return response.json();

                        })

                        .then(result => {

                            if (!result.success) {

                                alert(
                                    result.message ||
                                    'Failed to load fire extinguisher.'
                                );

                                return;
                            }

                            const data = result.data;


                            document.getElementById(
                                'deletedViewExtinguisherCode'
                            ).value = data.extinguisher_code ?? '';


                            document.getElementById(
                                'deletedViewBranch'
                            ).value = data.branch ?? '';


                            document.getElementById(
                                'deletedViewType'
                            ).value = data.type ?? '';


                            document.getElementById(
                                'deletedViewCapacity'
                            ).value = data.capacity ?? '';


                            document.getElementById(
                                'deletedViewClass'
                            ).value = data.class ?? '';


                            document.getElementById(
                                'deletedViewPlacement'
                            ).value = data.placement ?? '';


                            document.getElementById(
                                'deletedViewLocation'
                            ).value = data.location ?? '';


                            document.getElementById(
                                'deletedViewConditionStatus'
                            ).value = data.condition_status ?? '';


                            document.getElementById(
                                'deletedViewManufacturedDate'
                            ).value = data.manufactured_date ?? '';


                            document.getElementById(
                                'deletedViewLastRefilledDate'
                            ).value = data.refilled_date ?? '';


                            document.getElementById(
                                'deletedViewExpirationDate'
                            ).value = data.expiration_date ?? '';


                            document.getElementById(
                                'deletedViewRemarks'
                            ).value = data.remarks ?? '';

                            // Set ID for Restore button
                            const restoreButton = document.getElementById(
                                'restoreDeletedFireExtinguisherBtn'
                            );

                            if (restoreButton) {
                                restoreButton.dataset.id = data.extinguisher_id;
                                restoreButton.dataset.code = data.extinguisher_code;
                            }
                        })

                        .catch(error => {

                            console.error(error);

                        });

                });

            });



            const restoreDeletedButton = document.getElementById('restoreDeletedFireExtinguisherBtn');
            if (restoreDeletedButton) {
                restoreDeletedButton.addEventListener('click', function() {

                    const id = this.dataset.id;

                    if (!id) {
                        alert('Invalid fire extinguisher ID.');
                        return;
                    }

                    const confirmed = confirm(
                        'Are you sure you want to restore this fire extinguisher?\n\n' +
                        'The fire extinguisher will be returned to the active list.'
                    );

                    if (!confirmed) {
                        return;
                    }

                    const code = this.dataset.code || '';

                    window.location.href =
                        `backend/controller/FireExtinguisherController.php` +
                        `?action=restore` +
                        `&id=${encodeURIComponent(id)}` +
                        `&code=${encodeURIComponent(code)}`;

                });
            }
        </script>

    </div>
    </div>
    <?php include 'partials/footer.php'; ?>
    </div>
    <!-- CoreUI and necessary plugins-->
    <script src="vendors/@coreui/coreui/js/coreui.bundle.min.js"></script>
    <script src="vendors/simplebar/js/simplebar.min.js"></script>
    <script>
        const header = document.querySelector("header.header");

        document.addEventListener("scroll", () => {
            if (header) {
                header.classList.toggle("shadow-sm", document.documentElement.scrollTop > 0);
            }
        });
    </script>
    <?php include_once 'notification/session_timeout.php'; ?>

</body>

</html>
