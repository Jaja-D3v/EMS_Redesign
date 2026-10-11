<?php

require_once __DIR__ . '/backend/config/db.php';
require_once __DIR__ . '/backend/controller/MaintenanceController.php';
require_once 'backend/authentication/SessionChecker.php';


$controller = new MaintenanceController($conn);

if (isset($_POST['action'])) {

    switch ($_POST['action']) {

        case 'add':
            $controller->add();
            break;

        case 'update':
            $controller->update();
            break;

        case 'delete':
            $controller->delete();
            break;
    }
}

$data = $controller->index();

$branchData = $data['branch'];
$placement = $data['placement'];
$typeData = $data['type'];
$fireClassData = $data['fire_class'];
$capacityData = $data['capacity'];
$locationData = $data['location'];
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

<body>
    <?php include 'partials/side-nav.php'; ?>
    <div class="wrapper d-flex flex-column min-vh-100">
        <?php include 'partials/header-nav.php'; ?>

        <div class="container-fluid py-0">
            <div
                class="card border-0 shadow-sm text-white mb-1 mt-0 overflow-hidden"
                style="background: linear-gradient(135deg, #1d0870, #2408f5);">

                <div class="card-body p-4">

                    <div class="row align-items-center g-3">

                        <!-- Icon -->
                        <div class="col-auto">
                            <div
                                class="bg-white bg-opacity-10 rounded-3 p-3 d-flex align-items-center justify-content-center"
                                style="width: 64px; height: 64px;">

                                <i class="bi bi-tools fs-2"></i>

                            </div>
                        </div>

                        <!-- Title & Description -->
                        <div class="col">

                            <h2 class="fw-bold mb-1">
                                Maintenance
                            </h2>

                            <p class="mb-0 text-white-50">
                                Manage and maintain system dropdown options and reference data.
                            </p>

                        </div>

                    </div>

                </div>

            </div>

            <div class="container-fluid px-0" id="maintenance-options">

                <div class="row g-4">


                    <!-- =====================================================
             BRANCH
        ====================================================== -->

                    <div class="col-xl-4 col-lg-6">

                        <div class="card h-100 border border-primary-subtle shadow rounded-3 mt-3">

                            <div class="card-body">

                                <div class="d-flex justify-content-between align-items-center mb-3">

                                    <div class="d-flex align-items-center gap-3">

                                        <div class="bg-primary bg-opacity-10 text-primary rounded p-2">
                                            <i class="bi bi-buildings fs-4"></i>
                                        </div>

                                        <div>
                                            <h5 class="fw-bold mb-0">
                                                Branch
                                            </h5>

                                            <small class="text-muted">
                                                Manage branch options.
                                            </small>
                                        </div>

                                    </div>

                                    <button type="button"
                                        class="btn btn-primary btn-sm"
                                        data-bs-toggle="modal"
                                        data-bs-target="#addModal"
                                        onclick="setCategory('branch')">

                                        <i class="bi bi-plus-lg"></i>
                                        Add

                                    </button>

                                </div>


                                <!-- SEARCH -->

                                <form method="GET" class="mb-3">

                                    <div class="input-group">

                                        <span class="input-group-text bg-white">
                                            <i class="bi bi-search"></i>
                                        </span>

                                        <input type="text"
                                            name="branch_search"
                                            value="<?= htmlspecialchars($branchData['search']) ?>"
                                            class="form-control"
                                            placeholder="Search branch...">

                                        <button class="btn btn-primary">
                                            Search
                                        </button>

                                    </div>

                                </form>


                                <!-- TABLE -->

                                <div class="table-responsive" style="height: 270px;">

                                    <table class="table table-hover align-middle mb-0">

                                        <thead class="table-light">

                                            <tr>
                                                <th>#</th>
                                                <th>Branch Name</th>
                                                <th>Status</th>
                                                <th class="text-end">Actions</th>
                                            </tr>

                                        </thead>

                                        <tbody>

                                            <?php if (empty($branchData['records'])): ?>

                                                <tr>
                                                    <td colspan="4"
                                                        class="text-center text-muted py-4">

                                                        No branch found.

                                                    </td>
                                                </tr>

                                            <?php else: ?>

                                                <?php foreach ($branchData['records'] as $index => $row): ?>

                                                    <tr>

                                                        <td>
                                                            <?= (($branchData['page'] - 1) * 5) + $index + 1 ?>
                                                        </td>

                                                        <td>
                                                            <?= htmlspecialchars($row['value']) ?>
                                                        </td>

                                                        <td>
                                                            <span class="badge text-bg-success">
                                                                Active
                                                            </span>
                                                        </td>

                                                        <td class="text-end">

                                                            <button type="button"
                                                                class="btn btn-primary btn-sm"
                                                                onclick="editItem(
                                                            <?= $row['id'] ?>,
                                                            '<?= htmlspecialchars($row['value'], ENT_QUOTES) ?>'
                                                        )">

                                                                <i class="bi bi-pencil"></i>

                                                            </button>

                                                            <button type="button"
                                                                class="btn btn-danger btn-sm"
                                                                onclick="deleteItem(
                                                            <?= $row['id'] ?>,
                                                            '<?= htmlspecialchars($row['value'], ENT_QUOTES) ?>'
                                                        )">

                                                                <i class="bi bi-trash"></i>

                                                            </button>

                                                        </td>

                                                    </tr>

                                                <?php endforeach; ?>

                                            <?php endif; ?>

                                        </tbody>

                                    </table>

                                </div>


                                <!-- PAGINATION -->

                                <?php if ($branchData['total_pages'] > 1): ?>

                                    <nav class="mt-3">

                                        <ul class="pagination pagination-sm justify-content-center mb-0">

                                            <?php for (
                                                $i = 1;
                                                $i <= $branchData['total_pages'];
                                                $i++
                                            ): ?>

                                                <?php
                                                $params = $_GET;
                                                $params['branch_page'] = $i;
                                                $params['branch_search'] = $branchData['search'];

                                                $url = $_SERVER['PHP_SELF'] . '?' . http_build_query($params);
                                                ?>

                                                <li class="page-item <?= $i == $branchData['page'] ? 'active' : '' ?>">

                                                    <a class="page-link px-2 py-1"
                                                        href="<?= htmlspecialchars($url) ?>#maintenance-options">

                                                        <?= $i ?>

                                                    </a>

                                                </li>

                                            <?php endfor; ?>

                                        </ul>

                                    </nav>

                                <?php endif; ?>

                            </div>

                        </div>

                    </div>



                    <!-- =====================================================
             PLACEMENT
        ====================================================== -->

                    <div class="col-xl-4 col-lg-6">

                        <div class="card h-100 border border-primary-subtle shadow rounded-3 mt-3">

                            <div class="card-body">

                                <div class="d-flex justify-content-between align-items-center mb-3">

                                    <div class="d-flex align-items-center gap-3">

                                        <div class="bg-primary bg-opacity-10 text-primary rounded p-2">
                                            <i class="bi bi-wrench-adjustable fs-4"></i>
                                        </div>

                                        <div>
                                            <h5 class="fw-bold mb-0">
                                                Placement
                                            </h5>

                                            <small class="text-muted">
                                                Manage placement options.
                                            </small>
                                        </div>

                                    </div>

                                    <button type="button"
                                        class="btn btn-primary btn-sm"
                                        data-bs-toggle="modal"
                                        data-bs-target="#addModal"
                                        onclick="setCategory('placement')">

                                        <i class="bi bi-plus-lg"></i>
                                        Add

                                    </button>

                                </div>


                                <form method="GET" class="mb-3">

                                    <div class="input-group">

                                        <span class="input-group-text bg-white">
                                            <i class="bi bi-search"></i>
                                        </span>

                                        <input type="text"
                                            name="placement_search"
                                            value="<?= htmlspecialchars($placement['search']) ?>"
                                            class="form-control"
                                            placeholder="Search placement...">

                                        <button class="btn btn-primary">
                                            Search
                                        </button>

                                    </div>

                                </form>


                                <div class="table-responsive" style="height: 270px;">

                                    <table class="table table-hover align-middle mb-0">

                                        <thead class="table-light">

                                            <tr>
                                                <th>#</th>
                                                <th>Placement</th>
                                                <th>Status</th>
                                                <th class="text-end">Actions</th>
                                            </tr>

                                        </thead>

                                        <tbody>

                                            <?php if (empty($placement['records'])): ?>

                                                <tr>
                                                    <td colspan="4"
                                                        class="text-center text-muted py-4">

                                                        No Placement found.

                                                    </td>
                                                </tr>

                                            <?php else: ?>

                                                <?php foreach ($placement['records'] as $index => $row): ?>

                                                    <tr>

                                                        <td>
                                                            <?= (($placement['page'] - 1) * 5) + $index + 1 ?>
                                                        </td>

                                                        <td>
                                                            <?= htmlspecialchars($row['value']) ?>
                                                        </td>

                                                        <td>
                                                            <span class="badge text-bg-success">
                                                                Active
                                                            </span>
                                                        </td>

                                                        <td class="text-end">

                                                            <button type="button"
                                                                class="btn btn-primary btn-sm"
                                                                onclick="editItem(
                                                            <?= $row['id'] ?>,
                                                            '<?= htmlspecialchars($row['value'], ENT_QUOTES) ?>'
                                                        )">

                                                                <i class="bi bi-pencil"></i>

                                                            </button>

                                                            <button type="button"
                                                                class="btn btn-danger btn-sm"
                                                                onclick="deleteItem(
                                                            <?= $row['id'] ?>,
                                                            '<?= htmlspecialchars($row['value'], ENT_QUOTES) ?>'
                                                        )">

                                                                <i class="bi bi-trash"></i>

                                                            </button>

                                                        </td>

                                                    </tr>

                                                <?php endforeach; ?>

                                            <?php endif; ?>

                                        </tbody>

                                    </table>

                                </div>


                                <?php if ($placement['total_pages'] > 1): ?>

                                    <nav class="mt-3">

                                        <ul class="pagination pagination-sm justify-content-center mb-0">

                                            <?php for (
                                                $i = 1;
                                                $i <= $placement['total_pages'];
                                                $i++
                                            ): ?>

                                                <li class="page-item
                                        <?= $i == $placement['page'] ? 'active' : '' ?>">

                                                    <a class="page-link px-2 py-1"
                                                        href="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>?placement_page=<?= $i ?>&placement_search=<?= urlencode($placement['search']) ?>">

                                                        <?= $i ?>

                                                    </a>

                                                </li>

                                            <?php endfor; ?>

                                        </ul>

                                    </nav>

                                <?php endif; ?>

                            </div>

                        </div>

                    </div>



                    <!-- =====================================================
             TYPE
        ====================================================== -->

                    <div class="col-xl-4 col-lg-6">

                        <div class="card h-100 border border-primary-subtle shadow rounded-3 mt-3">

                            <div class="card-body">

                                <div class="d-flex justify-content-between align-items-center mb-3">

                                    <div class="d-flex align-items-center gap-3">

                                        <div class="bg-primary bg-opacity-10 text-primary rounded p-2">
                                            <i class="bi bi-fire fs-4"></i>
                                        </div>

                                        <div>
                                            <h5 class="fw-bold mb-0">
                                                Type
                                            </h5>

                                            <small class="text-muted">
                                                Manage extinguisher types.
                                            </small>
                                        </div>

                                    </div>

                                    <button type="button"
                                        class="btn btn-primary btn-sm"
                                        data-bs-toggle="modal"
                                        data-bs-target="#addModal"
                                        onclick="setCategory('type')">

                                        <i class="bi bi-plus-lg"></i>
                                        Add

                                    </button>

                                </div>


                                <form method="GET" class="mb-3">

                                    <div class="input-group">

                                        <span class="input-group-text bg-white">
                                            <i class="bi bi-search"></i>
                                        </span>

                                        <input type="text"
                                            name="type_search"
                                            value="<?= htmlspecialchars($typeData['search']) ?>"
                                            class="form-control"
                                            placeholder="Search type...">

                                        <button class="btn btn-primary">
                                            Search
                                        </button>

                                    </div>

                                </form>


                                <div class="table-responsive" style="height: 270px;">

                                    <table class="table table-hover align-middle mb-0">

                                        <thead class="table-light">

                                            <tr>
                                                <th>#</th>
                                                <th>Type</th>
                                                <th>Status</th>
                                                <th class="text-end">Actions</th>
                                            </tr>

                                        </thead>

                                        <tbody>

                                            <?php if (empty($typeData['records'])): ?>

                                                <tr>
                                                    <td colspan="4"
                                                        class="text-center text-muted py-4">

                                                        No type found.

                                                    </td>
                                                </tr>

                                            <?php else: ?>

                                                <?php foreach ($typeData['records'] as $index => $row): ?>

                                                    <tr>

                                                        <td>
                                                            <?= (($typeData['page'] - 1) * 5) + $index + 1 ?>
                                                        </td>

                                                        <td>
                                                            <?= htmlspecialchars($row['value']) ?>
                                                        </td>

                                                        <td>
                                                            <span class="badge text-bg-success">
                                                                Active
                                                            </span>
                                                        </td>

                                                        <td class="text-end">

                                                            <button type="button"
                                                                class="btn btn-primary btn-sm"
                                                                onclick="editItem(
                                                            <?= $row['id'] ?>,
                                                            '<?= htmlspecialchars($row['value'], ENT_QUOTES) ?>'
                                                        )">

                                                                <i class="bi bi-pencil"></i>

                                                            </button>

                                                            <button type="button"
                                                                class="btn btn-danger btn-sm"
                                                                onclick="deleteItem(
                                                            <?= $row['id'] ?>,
                                                            '<?= htmlspecialchars($row['value'], ENT_QUOTES) ?>'
                                                        )">

                                                                <i class="bi bi-trash"></i>

                                                            </button>

                                                        </td>

                                                    </tr>

                                                <?php endforeach; ?>

                                            <?php endif; ?>

                                        </tbody>

                                    </table>

                                </div>


                                <?php if ($typeData['total_pages'] > 1): ?>

                                    <nav class="mt-3">

                                        <ul class="pagination pagination-sm justify-content-center mb-0">

                                            <?php for (
                                                $i = 1;
                                                $i <= $typeData['total_pages'];
                                                $i++
                                            ): ?>

                                                <li class="page-item
                                        <?= $i == $typeData['page'] ? 'active' : '' ?>">

                                                    <a class="page-link px-2 py-1"
                                                        href="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>?type_page=<?= $i ?>&type_search=<?= urlencode($typeData['search']) ?>">

                                                        <?= $i ?>

                                                    </a>

                                                </li>

                                            <?php endfor; ?>

                                        </ul>

                                    </nav>

                                <?php endif; ?>

                            </div>

                        </div>

                    </div>



                    <!-- =====================================================
             FIRE CLASS
        ====================================================== -->

                    <div class="col-xl-4 col-lg-6">

                        <div class="card h-100 border border-primary-subtle shadow rounded-3 mt-3">

                            <div class="card-body">

                                <div class="d-flex justify-content-between align-items-center mb-3">

                                    <div class="d-flex align-items-center gap-3">

                                        <div class="bg-primary bg-opacity-10 text-primary rounded p-2">
                                            <i class="bi bi-fire fs-4"></i>
                                        </div>

                                        <div>
                                            <h5 class="fw-bold mb-0">
                                                Fire Class
                                            </h5>

                                            <small class="text-muted">
                                                Manage fire class options.
                                            </small>
                                        </div>

                                    </div>

                                    <button type="button"
                                        class="btn btn-primary btn-sm"
                                        data-bs-toggle="modal"
                                        data-bs-target="#addModal"
                                        onclick="setCategory('fire_class')">

                                        <i class="bi bi-plus-lg"></i>
                                        Add

                                    </button>

                                </div>


                                <form method="GET" class="mb-3">

                                    <div class="input-group">

                                        <span class="input-group-text bg-white">
                                            <i class="bi bi-search"></i>
                                        </span>

                                        <input type="text"
                                            name="fire_class_search"
                                            value="<?= htmlspecialchars($fireClassData['search']) ?>"
                                            class="form-control"
                                            placeholder="Search fire class...">

                                        <button class="btn btn-primary">
                                            Search
                                        </button>

                                    </div>

                                </form>


                                <div class="table-responsive" style="height: 270px;">

                                    <table class="table table-hover align-middle mb-0">

                                        <thead class="table-light">

                                            <tr>
                                                <th>#</th>
                                                <th>Fire Class</th>
                                                <th>Status</th>
                                                <th class="text-end">Actions</th>
                                            </tr>

                                        </thead>

                                        <tbody>

                                            <?php if (empty($fireClassData['records'])): ?>

                                                <tr>
                                                    <td colspan="4"
                                                        class="text-center text-muted py-4">

                                                        No fire class found.

                                                    </td>
                                                </tr>

                                            <?php else: ?>

                                                <?php foreach ($fireClassData['records'] as $index => $row): ?>

                                                    <tr>

                                                        <td>
                                                            <?= (($fireClassData['page'] - 1) * 5) + $index + 1 ?>
                                                        </td>

                                                        <td>
                                                            <?= htmlspecialchars($row['value']) ?>
                                                        </td>

                                                        <td>
                                                            <span class="badge text-bg-success">
                                                                Active
                                                            </span>
                                                        </td>

                                                        <td class="text-end">

                                                            <button type="button"
                                                                class="btn btn-primary btn-sm"
                                                                onclick="editItem(
                                                            <?= $row['id'] ?>,
                                                            '<?= htmlspecialchars($row['value'], ENT_QUOTES) ?>'
                                                        )">

                                                                <i class="bi bi-pencil"></i>

                                                            </button>

                                                            <button type="button"
                                                                class="btn btn-danger btn-sm"
                                                                onclick="deleteItem(
                                                            <?= $row['id'] ?>,
                                                            '<?= htmlspecialchars($row['value'], ENT_QUOTES) ?>'
                                                        )">

                                                                <i class="bi bi-trash"></i>

                                                            </button>

                                                        </td>

                                                    </tr>

                                                <?php endforeach; ?>

                                            <?php endif; ?>

                                        </tbody>

                                    </table>

                                </div>


                                <?php if ($fireClassData['total_pages'] > 1): ?>

                                    <nav class="mt-3">

                                        <ul class="pagination pagination-sm justify-content-center mb-0">

                                            <?php for (
                                                $i = 1;
                                                $i <= $fireClassData['total_pages'];
                                                $i++
                                            ): ?>

                                                <li class="page-item
                                        <?= $i == $fireClassData['page'] ? 'active' : '' ?>">

                                                    <a class="page-link px-2 py-1"
                                                        href="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>?fire_class_page=<?= $i ?>&fire_class_search=<?= urlencode($fireClassData['search']) ?>">

                                                        <?= $i ?>

                                                    </a>

                                                </li>

                                            <?php endfor; ?>

                                        </ul>

                                    </nav>

                                <?php endif; ?>

                            </div>

                        </div>

                    </div>



                    <!-- =====================================================
             CAPACITY
        ====================================================== -->

                    <div class="col-xl-4 col-lg-6">

                        <div class="card h-100 border border-primary-subtle shadow rounded-3 mt-3">

                            <div class="card-body">

                                <div class="d-flex justify-content-between align-items-center mb-3">

                                    <div class="d-flex align-items-center gap-3">

                                        <div class="bg-primary bg-opacity-10 text-primary rounded p-2">
                                            <i class="bi bi-box-seam fs-4"></i>
                                        </div>

                                        <div>
                                            <h5 class="fw-bold mb-0">
                                                Capacity
                                            </h5>

                                            <small class="text-muted">
                                                Manage capacity options.
                                            </small>
                                        </div>

                                    </div>

                                    <button type="button"
                                        class="btn btn-primary btn-sm"
                                        data-bs-toggle="modal"
                                        data-bs-target="#addModal"
                                        onclick="setCategory('capacity')">

                                        <i class="bi bi-plus-lg"></i>
                                        Add

                                    </button>

                                </div>


                                <form method="GET" class="mb-3">

                                    <div class="input-group">

                                        <span class="input-group-text bg-white">
                                            <i class="bi bi-search"></i>
                                        </span>

                                        <input type="text"
                                            name="capacity_search"
                                            value="<?= htmlspecialchars($capacityData['search']) ?>"
                                            class="form-control"
                                            placeholder="Search capacity...">

                                        <button class="btn btn-primary">
                                            Search
                                        </button>

                                    </div>

                                </form>


                                <div class="table-responsive" style="height: 270px;">

                                    <table class="table table-hover align-middle mb-0">

                                        <thead class="table-light">

                                            <tr>
                                                <th>#</th>
                                                <th>Capacity</th>
                                                <th>Status</th>
                                                <th class="text-end">Actions</th>
                                            </tr>

                                        </thead>

                                        <tbody>

                                            <?php if (empty($capacityData['records'])): ?>

                                                <tr>
                                                    <td colspan="4"
                                                        class="text-center text-muted py-4">

                                                        No capacity found.

                                                    </td>
                                                </tr>

                                            <?php else: ?>

                                                <?php foreach ($capacityData['records'] as $index => $row): ?>

                                                    <tr>

                                                        <td>
                                                            <?= (($capacityData['page'] - 1) * 5) + $index + 1 ?>
                                                        </td>

                                                        <td>
                                                            <?= htmlspecialchars($row['value']) ?>
                                                        </td>

                                                        <td>
                                                            <span class="badge text-bg-success">
                                                                Active
                                                            </span>
                                                        </td>

                                                        <td class="text-end">

                                                            <button type="button"
                                                                class="btn btn-primary btn-sm"
                                                                onclick="editItem(
                                                            <?= $row['id'] ?>,
                                                            '<?= htmlspecialchars($row['value'], ENT_QUOTES) ?>'
                                                        )">

                                                                <i class="bi bi-pencil"></i>

                                                            </button>

                                                            <button type="button"
                                                                class="btn btn-danger btn-sm"
                                                                onclick="deleteItem(
                                                            <?= $row['id'] ?>,
                                                            '<?= htmlspecialchars($row['value'], ENT_QUOTES) ?>'
                                                        )">

                                                                <i class="bi bi-trash"></i>

                                                            </button>

                                                        </td>

                                                    </tr>

                                                <?php endforeach; ?>

                                            <?php endif; ?>

                                        </tbody>

                                    </table>

                                </div>


                                <?php if ($capacityData['total_pages'] > 1): ?>

                                    <nav class="mt-3">

                                        <ul class="pagination pagination-sm justify-content-center mb-0">

                                            <?php for (
                                                $i = 1;
                                                $i <= $capacityData['total_pages'];
                                                $i++
                                            ): ?>

                                                <li class="page-item
                                        <?= $i == $capacityData['page'] ? 'active' : '' ?>">

                                                    <a class="page-link px-2 py-1"
                                                        href="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>?capacity_page=<?= $i ?>&capacity_search=<?= urlencode($capacityData['search']) ?>">

                                                        <?= $i ?>

                                                    </a>

                                                </li>

                                            <?php endfor; ?>

                                        </ul>

                                    </nav>

                                <?php endif; ?>

                            </div>

                        </div>

                    </div>



                    <!-- =====================================================
             LOCATION / AREA
        ====================================================== -->
<!-- 
                    <div class="col-xl-4 col-lg-6">

                        <div class="card h-100 border border-primary-subtle shadow rounded-3 mt-3">

                            <div class="card-body">

                                <div class="d-flex justify-content-between align-items-center mb-3">

                                    <div class="d-flex align-items-center gap-3">

                                        <div class="bg-primary bg-opacity-10 text-primary rounded p-2">
                                            <i class="bi bi-geo-alt fs-4"></i>
                                        </div>

                                        <div>
                                            <h5 class="fw-bold mb-0">
                                                Location / Area
                                            </h5>

                                            <small class="text-muted">
                                                Manage location options.
                                            </small>
                                        </div>

                                    </div>

                                    <button type="button"
                                        class="btn btn-primary btn-sm"
                                        data-bs-toggle="modal"
                                        data-bs-target="#addModal"
                                        onclick="setCategory('location')">

                                        <i class="bi bi-plus-lg"></i>
                                        Add

                                    </button>

                                </div>


                                <form method="GET" class="mb-3">

                                    <div class="input-group">

                                        <span class="input-group-text bg-white">
                                            <i class="bi bi-search"></i>
                                        </span>

                                        <input type="text"
                                            name="location_search"
                                            value="<?= htmlspecialchars($locationData['search']) ?>"
                                            class="form-control"
                                            placeholder="Search location...">

                                        <button class="btn btn-primary">
                                            Search
                                        </button>

                                    </div>

                                </form>


                                <div class="table-responsive" style="height: 270px;">

                                    <table class="table table-hover align-middle mb-0">

                                        <thead class="table-light">

                                            <tr>
                                                <th>#</th>
                                                <th>Location / Area</th>
                                                <th>Status</th>
                                                <th class="text-end">Actions</th>
                                            </tr>

                                        </thead>

                                        <tbody>

                                            <?php if (empty($locationData['records'])): ?>

                                                <tr>
                                                    <td colspan="4"
                                                        class="text-center text-muted py-4">

                                                        No location found.

                                                    </td>
                                                </tr>

                                            <?php else: ?>

                                                <?php foreach ($locationData['records'] as $index => $row): ?>

                                                    <tr>

                                                        <td>
                                                            <?= (($locationData['page'] - 1) * 5) + $index + 1 ?>
                                                        </td>

                                                        <td>
                                                            <?= htmlspecialchars($row['value']) ?>
                                                        </td>

                                                        <td>
                                                            <span class="badge text-bg-success">
                                                                Active
                                                            </span>
                                                        </td>

                                                        <td class="text-end">

                                                            <button type="button"
                                                                class="btn btn-primary btn-sm"
                                                                onclick="editItem(
                                                            <?= $row['id'] ?>,
                                                            '<?= htmlspecialchars($row['value'], ENT_QUOTES) ?>'
                                                        )">

                                                                <i class="bi bi-pencil"></i>

                                                            </button>

                                                            <button type="button"
                                                                class="btn btn-danger btn-sm"
                                                                onclick="deleteItem(
                                                            <?= $row['id'] ?>,
                                                            '<?= htmlspecialchars($row['value'], ENT_QUOTES) ?>'
                                                        )">

                                                                <i class="bi bi-trash"></i>

                                                            </button>

                                                        </td>

                                                    </tr>

                                                <?php endforeach; ?>

                                            <?php endif; ?>

                                        </tbody>

                                    </table>

                                </div>


                                <?php if ($locationData['total_pages'] > 1): ?>

                                    <nav class="mt-3">

                                        <ul class="pagination pagination-sm justify-content-center mb-0">

                                            <?php for (
                                                $i = 1;
                                                $i <= $locationData['total_pages'];
                                                $i++
                                            ): ?>

                                                <li class="page-item
                                        <?= $i == $locationData['page'] ? 'active' : '' ?>">

                                                    <a class="page-link px-2 py-1"
                                                        href="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>?location_page=<?= $i ?>&location_search=<?= urlencode($locationData['search']) ?>">

                                                        <?= $i ?>

                                                    </a>

                                                </li>

                                            <?php endfor; ?>

                                        </ul>

                                    </nav>

                                <?php endif; ?>

                            </div>

                        </div>

                    </div> -->

                </div>

            </div>
        </div>

        <!-- CONTENT HERE -->





    </div>
    </div>

    <!-- Add Modal -->
    <div class="modal fade" id="addModal" tabindex="-1">

        <div class="modal-dialog modal-dialog-centered">

            <div class="modal-content">

                <form method="POST">

                    <div class="modal-header">

                        <h5 class="modal-title">
                            Add Dropdown Option
                        </h5>

                        <button type="button"
                            class="btn-close"
                            data-bs-dismiss="modal">
                        </button>

                    </div>


                    <div class="modal-body">

                        <input type="hidden"
                            name="action"
                            value="add">

                        <div class="mb-3">

                            <label class="form-label">
                                Category
                            </label>

                            <select name="category"
                                id="addCategory"
                                class="form-select"
                                required>

                                <option value="">
                                    Select category
                                </option>

                                <option value="branch">
                                    Branch
                                </option>

                                <option value="placement">
                                    Placement
                                </option>

                                <option value="type">
                                    Type
                                </option>

                                <option value="fire_class">
                                    Fire Class
                                </option>

                                <option value="capacity">
                                    Capacity
                                </option>

                                <option value="location">
                                    Location / Area
                                </option>

                            </select>

                        </div>


                        <div class="mb-3">

                            <label class="form-label">
                                Value
                            </label>

                            <input type="text"
                                name="value"
                                class="form-control"
                                placeholder="Enter value"
                                required>

                        </div>

                    </div>


                    <div class="modal-footer">

                        <button type="button"
                            class="btn btn-secondary"
                            data-bs-dismiss="modal">

                            Cancel

                        </button>

                        <button type="submit"
                            class="btn btn-primary">

                            <i class="bi bi-save"></i>

                            Save

                        </button>

                    </div>

                </form>

            </div>

        </div>

    </div>
    <!-- Edit Modal -->
    <div class="modal fade" id="editModal" tabindex="-1">

        <div class="modal-dialog modal-dialog-centered">

            <div class="modal-content">

                <form method="POST">

                    <div class="modal-header">

                        <h5 class="modal-title">
                            Edit Dropdown Option
                        </h5>

                        <button type="button"
                            class="btn-close"
                            data-bs-dismiss="modal">
                        </button>

                    </div>


                    <div class="modal-body">

                        <input type="hidden"
                            name="action"
                            value="update">

                        <input type="hidden"
                            name="id"
                            id="editId">


                        <div class="mb-3">

                            <label class="form-label">
                                Value
                            </label>

                            <input type="text"
                                name="value"
                                id="editValue"
                                class="form-control"
                                required>

                        </div>

                    </div>


                    <div class="modal-footer">

                        <button type="button"
                            class="btn btn-secondary"
                            data-bs-dismiss="modal">

                            Cancel

                        </button>

                        <button type="submit"
                            class="btn btn-primary">

                            <i class="bi bi-save"></i>

                            Update

                        </button>

                    </div>

                </form>

            </div>

        </div>

    </div>
    <!-- delete modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1">

        <div class="modal-dialog modal-dialog-centered">

            <div class="modal-content">

                <form method="POST">

                    <div class="modal-header">

                        <h5 class="modal-title">
                            Deactivate Option
                        </h5>

                        <button type="button"
                            class="btn-close"
                            data-bs-dismiss="modal">
                        </button>

                    </div>


                    <div class="modal-body">

                        <input type="hidden"
                            name="action"
                            value="delete">

                        <input type="hidden"
                            name="id"
                            id="deleteId">

                        <div class="alert alert-warning mb-0">

                            <i class="bi bi-exclamation-triangle"></i>

                            Are you sure you want to deactivate
                            <strong id="deleteValue"></strong>?

                        </div>

                    </div>


                    <div class="modal-footer">

                        <button type="button"
                            class="btn btn-secondary"
                            data-bs-dismiss="modal">

                            Cancel

                        </button>

                        <button type="submit"
                            class="btn btn-danger">

                            <i class="bi bi-trash"></i>

                            Deactivate

                        </button>

                    </div>

                </form>

            </div>

        </div>

    </div>
    <script>
        function setCategory(category) {
            document.getElementById('addCategory').value = category;
        }


        function editItem(id, value) {
            document.getElementById('editId').value = id;

            document.getElementById('editValue').value = value;

            const modal =
                new bootstrap.Modal(
                    document.getElementById('editModal')
                );

            modal.show();
        }


        function deleteItem(id, value) {
            document.getElementById('deleteId').value = id;

            document.getElementById('deleteValue').textContent = value;

            const modal =
                new bootstrap.Modal(
                    document.getElementById('deleteModal')
                );

            modal.show();
        }
    </script>
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