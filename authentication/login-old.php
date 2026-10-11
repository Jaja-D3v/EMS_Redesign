<?php
require_once __DIR__ . '../../backend/controller/BranchController.php';


$branches = getAllBranchesController();
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

<head>
  <base href="./../">
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
  <meta name="description" content="CoreUI - Open Source Bootstrap Admin Template">
  <meta name="author" content="Łukasz Holeczek">
  <meta name="keyword" content="Bootstrap,Admin,Template,Open,Source,jQuery,CSS,HTML,RWD,Dashboard">
  <title>Log In | EMS Equipment Management System</title>
  <link rel="icon" type="image/png" sizes="192x192" href="assets/favicon/android-icon-192x192.png">
  <link rel="manifest" href="assets/favicon/manifest.json">
  <meta name="msapplication-TileColor" content="#ffffff">
  <meta name="msapplication-TileImage" content="assets/favicon/ms-icon-144x144.png">
  <meta name="theme-color" content="#ffffff">
  <!-- Vendors styles-->
  <link rel="stylesheet" href="vendors/simplebar/css/simplebar.css">
  <link rel="stylesheet" href="css/vendors/simplebar.css">
  <!-- Main styles for this application-->
  <link href="css/style.css" rel="stylesheet">
  <!-- We use those styles to show code examples, you should remove them in your application.-->
  <script src="js/config.js"></script>
  <script src="js/color-modes.js"></script>
  <!-- added by jajadev -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>

<body>

  <div class="bg-body-tertiary min-vh-100 d-flex align-items-center py-3 py-lg-4">

    <div class="container">

      <div class="row justify-content-center">

        <div class="col-12 col-xl-11 col-xxl-10">

          <!-- Main Login Container -->
          <div class="card border-0 shadow-lg overflow-hidden rounded-4">

            <div class="row g-0 min-vh-lg-75">


              <!-- =================================================
                   LEFT SIDE - LOGIN
                   ================================================= -->
              <div class="col-12 col-lg-5 bg-white">

                <div class="p-4 p-md-5 h-100 d-flex flex-column justify-content-center">

                  <!-- Logo -->
                  <div class="d-flex align-items-center gap-3 mb-4">

                    <div class="d-flex align-items-center justify-content-center
                             bg-danger text-white rounded-3 shadow-sm flex-shrink-0"
                      style="width: 54px; height: 54px;">

                      <i class="bi bi-fire fs-2"></i>

                    </div>

                    <div>

                      <div class="fw-bold fs-3 lh-1">
                        Firefighter
                      </div>

                      <div class="text-body-secondary small">
                        Equipment Management System
                      </div>

                    </div>

                  </div>


                  <!-- Login Heading -->
                  <div class="d-flex gap-3 mb-4">

                    <div class="bg-danger rounded-pill flex-shrink-0" style="width: 4px;">
                    </div>

                    <div>

                      <h1 class="h3 fw-bold mb-1">
                        Welcome Back!
                      </h1>

                      <p class="text-body-secondary small mb-0">
                        Sign in to your account to continue to the
                        Fire Equipment Management System.
                      </p>

                    </div>

                  </div>

                  <form action="./backend/controller/AuthController.php" method="post" autocomplete="off" >


                    <!-- Username -->
                    <div class="mb-3">

                      <label class="form-label small fw-semibold" for="email">

                        Username or Employee Code

                      </label>

                      <div class="input-group">

                        <span class="input-group-text bg-white">

                          <i class="bi bi-person text-body-secondary"></i>

                        </span>

                        <input class="form-control" name="username" id="email" type="text"
                          placeholder="Username or Employee Code" autocomplete="off" required>

                      </div>

                    </div>


                    <!-- Password -->
                    <div class="mb-4">

                      <label class="form-label small fw-semibold" for="password">

                        Password

                      </label>

                      <div class="input-group">

                        <span class="input-group-text bg-white">

                          <i class="bi bi-lock text-body-secondary"></i>

                        </span>

                        <input class="form-control" id="password" name="password" type="password"
                          placeholder="Your password" autocomplete="off" required>

                        <button class="btn btn-outline-secondary" type="button" id="togglePassword"
                          aria-label="Show password">

                          <i class="bi bi-eye" id="passwordIcon">
                          </i>

                        </button>

                      </div>

                    </div>

                    <!-- Branch -->
                    <div class="mb-4">

                      <label class="form-label small fw-semibold" for="branch">
                        Branch
                      </label>

                      <div class="input-group">

                        <span class="input-group-text bg-white">
                          <i class="bi bi-building text-body-secondary"></i>
                        </span>

                        <select class="form-select" name="branch" id="branch" required>
                          <option value="" selected disabled>
                            Select your branch
                          </option>

                          <?php foreach ($branches as $branch): ?>

                          <option value="<?= htmlspecialchars($branch['branch_name']) ?>">
                            <?= htmlspecialchars($branch['branch_name']) ?>
                          </option>

                          <?php endforeach; ?>

                        </select>

                      </div>

                    </div>


                    <!-- Sign In -->
                    <div class="d-grid">

                      <button class="btn btn-danger py-2 fw-semibold" type="submit">

                        <i class="bi bi-box-arrow-in-right me-2"></i>

                        Log In

                      </button>

                    </div>

                  </form>


                  <!-- Security Notice -->
                  <div class="text-body-secondary small mt-4">

                    <i class="bi bi-shield-check text-danger me-1"></i>

                    Authorized personnel only. Keep your account secure.

                  </div>

                </div>

              </div>


              <!-- =================================================
                   RIGHT SIDE - FIRE EQUIPMENT VISUAL
                   ================================================= -->
              <div class="col-12 col-lg-7 d-none d-lg-flex
                       bg-danger-subtle position-relative
                       align-items-center justify-content-center overflow-hidden">


                <!-- Decorative Fire Icon -->
                <div class="position-absolute top-0 end-0
                         translate-middle-y me-n5 mt-5
                         text-danger opacity-25">

                  <i class="bi bi-fire" style="font-size: 15rem;">
                  </i>

                </div>


                <!-- Main Visual -->
                <div class="position-relative text-center p-4 p-xl-5">

                  <!-- Firefighter Icon -->
                  <div class="mb-3">

                    <div class="d-inline-flex align-items-center justify-content-center
                             bg-white text-danger rounded-circle shadow-sm" style="width: 100px; height: 100px;">

                      <i class="bi bi-person-badge" style="font-size: 4rem;">
                      </i>

                    </div>

                  </div>


                  <!-- Main Title -->
                  <h2 class="fw-bold text-dark mb-2">

                    Fire Equipment

                    <span class="text-danger">
                      Management
                    </span>

                  </h2>

                  <p class="text-body-secondary mx-auto mb-4" style="max-width: 520px;">

                    Manage fire extinguishers, inspections,
                    equipment records, and safety information
                    in one centralized system.

                  </p>


                  <!-- Equipment Cards -->
                  <div class="row g-3 justify-content-center">


                    <!-- Fire Extinguisher -->
                    <div class="col-12 col-sm-4">

                      <div class="card border-0 shadow-sm h-100">

                        <div class="card-body py-3">

                          <div class="d-inline-flex align-items-center
                                   justify-content-center
                                   bg-danger-subtle text-danger
                                   rounded-3 mb-2" style="width: 48px; height: 48px;">

                            <i class="bi bi-fire fs-4"></i>

                          </div>

                          <div class="fw-semibold small">
                            Fire Extinguishers
                          </div>

                          <small class="text-body-secondary">
                            Equipment records
                          </small>

                        </div>

                      </div>

                    </div>


                    <!-- Inspection -->
                    <div class="col-12 col-sm-4">

                      <div class="card border-0 shadow-sm h-100">

                        <div class="card-body py-3">

                          <div class="d-inline-flex align-items-center
                                   justify-content-center
                                   bg-success-subtle text-success
                                   rounded-3 mb-2" style="width: 48px; height: 48px;">

                            <i class="bi bi-clipboard-check fs-4"></i>

                          </div>

                          <div class="fw-semibold small">
                            Inspections
                          </div>

                          <small class="text-body-secondary">
                            Safety monitoring
                          </small>

                        </div>

                      </div>

                    </div>


                    <!-- QR -->
                    <div class="col-12 col-sm-4">

                      <div class="card border-0 shadow-sm h-100">

                        <div class="card-body py-3">

                          <div class="d-inline-flex align-items-center
                                   justify-content-center
                                   bg-primary-subtle text-primary
                                   rounded-3 mb-2" style="width: 48px; height: 48px;">

                            <i class="bi bi-qr-code-scan fs-4"></i>

                          </div>

                          <div class="fw-semibold small">
                            QR Scanning
                          </div>

                          <small class="text-body-secondary">
                            Quick access
                          </small>

                        </div>

                      </div>

                    </div>

                  </div>


                  <!-- Safety Message -->
                  <div class="alert alert-danger border-0 mt-4 mb-0 text-start">

                    <div class="d-flex gap-3">

                      <i class="bi bi-shield-fill-check fs-4"></i>

                      <div>

                        <div class="fw-semibold">
                          Safety Starts With Preparedness
                        </div>

                        <small>
                          Keep your fire safety equipment
                          inspected and ready when needed.
                        </small>

                      </div>

                    </div>

                  </div>

                </div>

              </div>


            </div>

          </div>

        </div>

      </div>

    </div>

  </div>


  <!-- =================================================
       PASSWORD TOGGLE
       ================================================= -->
  <script>

    const togglePassword =
      document.getElementById('togglePassword');

    const password =
      document.getElementById('password');

    const passwordIcon =
      document.getElementById('passwordIcon');


    if (togglePassword && password && passwordIcon) {

      togglePassword.addEventListener('click', function () {

        const showPassword =
          password.type === 'password';

        password.type =
          showPassword ? 'text' : 'password';

        passwordIcon.classList.toggle(
          'bi-eye',
          !showPassword
        );

        passwordIcon.classList.toggle(
          'bi-eye-slash',
          showPassword
        );

        togglePassword.setAttribute(
          'aria-label',
          showPassword
            ? 'Hide password'
            : 'Show password'
        );

      });

    }

  </script>

</body>

</html>