<div class="sidebar sidebar-dark sidebar-fixed border-end" id="sidebar">
  <div class="sidebar-header border-bottom gap-2">

    <div class="sidebar-brand d-flex align-items-center justify-content-center gap-2 w-auto overflow-hidden">
      <img src="assets/img/KPFEMS.png"
        alt="Equipment Management System"
        class="img-fluid"
        style="
         max-height: 70px;
         width: auto;
         filter: drop-shadow(0 0 3px rgba(255,255,255,0.9))
                 drop-shadow(0 0 7px rgba(255,255,255,0.5));
          ">

      <div class="ims-sidebar-wordmark text-wrap"><strong>IMS Safety</strong><span>Equipment management</span></div>
<!-- <div class="d-flex flex-column">
        <span class="fw-bold"
          style="font-size: 0.95rem; letter-spacing: 1px; color: #ffffff;">
          EQUIPMENT
        </span>
        <span class="fw-semibold"
          style="font-size: 0.65rem; letter-spacing: 1px; color: #ffffff;">
          MANAGEMENT SYSTEM
        </span>
      </div> -->
    </div>

    <button class="btn-close d-lg-none flex-shrink-0 opacity-100" type="button" aria-label="Close navigation" onclick="coreui.Sidebar.getInstance(document.querySelector('#sidebar')).toggle()"></button>
  </div>
  <ul class="sidebar-nav" data-coreui="navigation" data-simplebar>
    <li class="nav-title">Navigation</li>
    <li class="nav-item">

      <!-- side-bar navigation -->
      <a class="nav-link" href="dashboard.php">
        <i class="fa-solid fa-gauge"></i>
        Dashboard
      </a>

      <a class="nav-link" href="QR-code.php">
        <i class="fa-solid fa-qrcode"></i>
        Scan QR CODE
      </a>
      <?php if (strtolower(trim($_SESSION['Role'] ?? '')) === 'admin'): ?>
        <a class="nav-link" href="QR-generator.php">
          <i class="fa-solid fa-qrcode"></i>
          Print QR Code
        </a>
      <?php endif; ?>

      <!-- Fire Extinguisher List -->
    <li class="nav-item">

      <!-- Parent -->
      <a
        class="nav-link d-flex align-items-center"
        data-bs-toggle="collapse"
        href="#fireExtinguisherMenu"
        role="button"
        aria-expanded="false"
        aria-controls="fireExtinguisherMenu">

        <i class="fa-solid fa-fire-extinguisher me-2"></i>

        <span>Fire Extinguisher</span>

        <i class="bi bi-chevron-down ms-auto submenu-chevron"></i>
      </a>


      <!-- Submenu -->
      <div
        class="collapse"
        id="fireExtinguisherMenu">

        <ul class="nav flex-column ms-3">

          <!-- Active Fire Extinguishers -->
          <li class="nav-item">

            <a class="nav-link" href="list-extinguisher.php">

              <i class="bi bi-fire me-2"></i>

              Active Fire Extinguishers

            </a>

          </li>


          <!-- Expiring Soon -->
          <li class="nav-item">

            <a class="nav-link" href="expiring-extinguisher.php">
              <i class="bi bi-hourglass-split me-2"></i>
              Expiring Soon
            </a>
          </li>


          <!-- Archived -->
          <li class="nav-item">

            <a class="nav-link" href="archived-extinguisher.php">
              <i class="bi bi-trash3 me-2"></i>
              Deleted
            </a>
          </li>
        </ul>
      </div>
    </li>
    <?php
    $currentStatus = $_GET['status'] ?? '';
    ?>

    <?php if (strtolower(trim($_SESSION['Role'] ?? '')) === 'admin'): ?>
      <!-- Inspection Approvals -->
      <li class="nav-item">

        <a
          class="nav-link d-flex align-items-center"
          data-bs-toggle="collapse"
          href="#inspectionApprovalMenu"
          role="button"
          aria-expanded="false"
          aria-controls="inspectionApprovalMenu">

          <i class="bi bi-clipboard-check me-2"></i>

          <span>Inspection Approvals</span>

          <i class="bi bi-chevron-down ms-auto submenu-chevron"></i>
        </a>

        <div class="collapse" id="inspectionApprovalMenu">
          <ul class="nav flex-column ms-3">
            <li class="nav-item">
              <a class="nav-link" href="inspection-pending.php">
                <i class="bi bi-hourglass-split me-2"></i>
                Pending
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="inspection-approved.php">
                <i class="bi bi-check-circle me-2"></i>
                Approved
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="inspection-rejected.php">
                <i class="bi bi-x-circle me-2"></i>
                Rejected
              </a>
            </li>
          </ul>
        </div>
      </li>
    <?php endif; ?>

    <!-- rejected inspection -->
    <?php if (strtolower(trim($_SESSION['Role'] ?? '')) === 'inspector'): ?>
      <li class="nav-item">
        <a class="nav-link" href="rejected-inspection.php">
         <i class="fa-solid fa-file-circle-xmark"></i>
          Rejected Inspection
        </a>
      </li>
    <?php endif; ?>

    <!-- Activity Log -->
    <li class="nav-item">

      <a
        class="nav-link"
        href="activity-log.php">

        <i class="fa-solid fa-user-pen me-2"></i>
        Activity Log

      </a>

    </li>
    <?php if (strtolower(trim($_SESSION['Role'] ?? '')) === 'admin'): ?>

      <!-- Maintenance -->
      <li class="nav-item">

        <a class="nav-link" href="maintenance.php">
          <i class="bi bi-tools me-2"></i>
          Maintenance

        </a>

      </li>
    <?php endif; ?>

    <!-- end navigation -->
    </li>
    <li class="nav-divider"></li>
    <li class="nav-title">Extras</li>
  </ul>
  <div class="sidebar-footer border-top d-none d-md-flex justify-content-center py-2">
    <small class="text-white">
      IMS · Equipment Management System
    </small>
  </div>
</div>