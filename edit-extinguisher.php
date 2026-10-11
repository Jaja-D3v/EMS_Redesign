<?php
include 'backend/controller/FireExtinguisherController.php';
// require_once 'backend/authentication/SessionChecker.php'; 

$id = $_GET['id'];
$data = getFireExtinguisherById($id);
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

        <div class="row justify-content-center">
            <div class="col-12 col-lg-9 col-xl-8">
                <!-- here -->
                <div class="card shadow-sm">

                    <!-- Header -->
                    <div class="card-header">
                        <h5 class="mb-0">Edit Fire Extinguisher</h5>
                        <small class="text-body-secondary">
                            Edit the fire extinguisher information below.
                        </small>
                    </div>

                    <div class="card-body">
                        <form class="row g-3" action="backend/controller/FireExtinguisherController.php" method="post">

                            <!-- tell the controller that this is update -->
                            <input type="hidden" name="action" value="update">

                            <input
                                type="hidden"
                                name="extinguisher_id"
                                value="<?= htmlspecialchars($data['extinguisher_id']) ?>">

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
                                    placeholder="e.g. E01-106"
                                    value="<?= htmlspecialchars($data['extinguisher_code']) ?>"
                                    readonly
                                    required>
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

                                    <option value="<?= htmlspecialchars($data['type']) ?>" selected>
                                        <?= htmlspecialchars($data['type']) ?>
                                    </option>

                                    <option value="Dry Chemical">Dry Chemical</option>
                                    <option value="CO2">CO2</option>
                                    <option value="Water">Water</option>
                                    <option value="Foam">Foam</option>
                                    <option value="Wet Chemical">Wet Chemical</option>
                                    <option value="HCFC-123">HCFC</option>

                                </select>
                            </div>


                            <!-- Capacity -->
                            <div class="col-md-6">
                                <label for="capacity" class="form-label">
                                    Capacity
                                </label>

                                <input
                                    type="text"
                                    class="form-control"
                                    id="capacity"
                                    name="capacity"
                                    placeholder="e.g. 10 lbs"
                                    list="capacityOptions"
                                    value="<?= htmlspecialchars($data['capacity']) ?>"
                                    required>

                                <datalist id="capacityOptions">
                                    <option value="10 lbs">
                                    <option value="20 lbs">
                                    <option value="50 lbs">
                                </datalist>
                            </div>


                            <!-- Location -->
                            <div class="col-md-6">
                                <label for="location" class="form-label">
                                    Location
                                </label>

                                <input
                                    type="text"
                                    class="form-control"
                                    id="location"
                                    name="location"
                                    value="<?= htmlspecialchars($data['location']) ?>"
                                    placeholder="e.g. Building 1 - 2nd Floor"
                                    required>
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
                                    name="manufactured_date"
                                    value="<?= htmlspecialchars($data['manufactured_date']) ?>">
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
                                    value="<?= htmlspecialchars($data['expiration_date']) ?>"
                                    readonly
                                    required>

                                <small class="text-muted">
                                    Automatically calculated as 3 years from manufactured date.
                                </small>
                            </div>


                            <!-- Class -->
                            <div class="col-md-6">
                                <label for="class" class="form-label">
                                    Fire Class
                                </label>

                                <select
                                    id="class"
                                    name="class"
                                    class="form-select"
                                    required>

                                    <option value="<?= htmlspecialchars($data['class']) ?>" selected>
                                        <?= htmlspecialchars($data['class']) ?>
                                    </option>

                                    <option value="A">A</option>
                                    <option value="B">B</option>
                                    <option value="C">C</option>
                                    <option value="D">D</option>
                                    <option value="AB">AB</option>
                                    <option value="ABC">ABC</option>
                                    <option value="BC">BC</option>

                                </select>
                            </div>


                            <!-- Placement -->
                            <div class="col-md-6">
                                <label for="placement" class="form-label">
                                    Placement
                                </label>

                                <input
                                    type="text"
                                    class="form-control"
                                    id="placement"
                                    name="placement"
                                    list="placementOptions"
                                    value="<?= htmlspecialchars($data['placement']) ?>"
                                    placeholder="e.g. Wall Mounted"
                                    required>

                                <datalist id="placementOptions">
                                    <option value="Wall Mounted">
                                    <option value="Floor Standing">
                                    <option value="Cabinet">
                                    <option value="Vehicle">
                                </datalist>
                            </div>


                            <!-- Condition Status -->
                            <div class="col-md-6">
                                <label for="conditionStatus" class="form-label">
                                    Condition Status
                                </label>

                                <select
                                    id="conditionStatus"
                                    name="condition_status"
                                    class="form-select"
                                    required>

                                    <option
                                        value="<?= htmlspecialchars($data['condition_status']) ?>"
                                        selected>
                                        <?= htmlspecialchars($data['condition_status']) ?>
                                    </option>

                                    <option value="Good">
                                        Good
                                    </option>

                                    <option value="Not Good">
                                        Not Good
                                    </option>

                                </select>
                            </div>


                            <!-- Remarks -->
                            <div class="col-md-6">
                                <label for="remarks" class="form-label">
                                    Remarks
                                </label>

                                <input
                                    type="text"
                                    class="form-control"
                                    id="remarks"
                                    name="remarks"
                                    value="<?= htmlspecialchars($data['remarks'] ?? '') ?>"
                                    placeholder="e.g. Needs inspection">
                            </div>


                            <!-- Buttons -->
                            <div class="col-12">
                                <hr class="my-2">

                                <div class="d-flex justify-content-end gap-2">

                                    <button
                                        type="submit"
                                        class="btn btn-primary">
                                        Update Info
                                    </button>

                                </div>
                            </div>

                        </form>
                    </div>

                </div>
                <!-- END -->
            </div>
        </div>






        <!-- CONTENT HERE -->



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