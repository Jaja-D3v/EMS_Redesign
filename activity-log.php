<?php

require_once 'backend/authentication/SessionChecker.php';
require_once 'backend/controller/ActivityLogController.php';

$limit = 10;

$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;

if ($page < 1) {
  $page = 1;
}

$date = isset($_GET['date']) ? $_GET['date'] : '';

$offset = ($page - 1) * $limit;

$activityLogs = getActivityLogsPaginated(
  $limit,
  $offset,
  '',
  $date
);

$totalRecords = getActivityLogsTotal(
  '',
  $date
);

$totalPages = (int) ceil($totalRecords / $limit);

?>

<!DOCTYPE html>
<html lang="en">

<?php include 'partials/header.php'; ?>

<body>

  <?php include 'partials/side-nav.php'; ?>

  <div class="wrapper d-flex flex-column min-vh-100">

    <?php include 'partials/header-nav.php'; ?>
    <div class="body flex-grow-1 bg-body-tertiary">
      <div class="container-lg px-4 py-4">

        <div class="card border-0 shadow-sm rounded-4 mb-3">
          <div class="card-body p-3 p-md-4">

            <div class="d-flex flex-column flex-lg-row align-items-start justify-content-between gap-4">

              <!-- TITLE -->
              <div class="d-flex align-items-center gap-3">
                <div class="d-flex align-items-center justify-content-center bg-success-subtle text-success rounded-4 flex-shrink-0 p-3">
                  <i class="bi bi-clock-history fs-4"></i>
                </div>

                <div class="min-width-0">
                  <h2 class="fw-bold mb-1 fs-4">
                    Activity Logs
                  </h2>

                  <p class="text-body-secondary mb-0 small">
                    Track system activities, user actions, and recent fire extinguisher updates.
                  </p>
                </div>
              </div>

              <!-- RIGHT SIDE -->
              <div class="d-flex flex-column align-items-lg-end gap-2 w-100 w-lg-auto">

                <!-- DATE + TOTAL -->
                <div class="d-flex flex-column flex-sm-row align-items-stretch align-items-sm-center justify-content-end gap-2">

                  <!-- DATE -->
                  <form method="GET" class="d-flex">
                    <div class="input-group">
                      <span class="input-group-text bg-body border-end-0">
                        <i class="bi bi-calendar3 text-primary"></i>
                      </span>

                      <input
                        type="date"
                        name="date"
                        value="<?= htmlspecialchars($date) ?>"
                        class="form-control border-start-0 fw-semibold"
                        onchange="this.form.submit()">
                    </div>
                  </form>

                  <!-- TOTAL -->
                  <div class="d-flex align-items-center justify-content-center gap-2 bg-body-tertiary border rounded-3 px-3 py-2 text-body-secondary small text-nowrap">

                    <i class="bi bi-list-ul text-success"></i>

                    <span>
                      <?php if (!empty($date)): ?>
                        <?= $totalRecords ?> activities
                      <?php else: ?>
                        <?= $totalRecords ?> total activities
                      <?php endif; ?>
                    </span>

                  </div>

                </div>

                <!-- SELECTED DATE -->
                <?php if (!empty($date)): ?>
                  <div class="small text-body-secondary text-lg-end">
                    Showing activities for
                    <strong>
                      <?= date('F d, Y', strtotime($date)) ?>
                    </strong>
                  </div>
                <?php endif; ?>

              </div>

            </div>

          </div>
        </div>

        <?php if ($activityLogs && mysqli_num_rows($activityLogs) > 0): ?>
          <?php while ($log = mysqli_fetch_assoc($activityLogs)): ?>
            <?php
            $action = strtolower($log['action']);

            if ($action === 'add fire extinguisher') {
              $icon = 'bi-plus-lg';
              $color = 'success';
              $title = 'Fire Extinguisher Added';
            } elseif ($action === 'update fire extinguisher') {
              $icon = 'bi-pencil';
              $color = 'warning';
              $title = 'Fire Extinguisher Updated';
            } elseif ($action === 'delete fire extinguisher') {
              $icon = 'bi-trash';
              $color = 'danger';
              $title = 'Fire Extinguisher Deleted';
            } elseif ($action === 'inspect fire extinguisher') {
              $icon = 'bi-clipboard-check';
              $color = 'info';
              $title = 'Inspection Completed';
            } else {
              $icon = 'bi-activity';
              $color = 'secondary';
              $title = $log['action'];
            }
            ?>

            <div class="card border shadow-sm rounded-4 mb-2">
              <div class="card-body p-3 p-md-4">
                <div class="row g-3 align-items-start">

                  <div class="col-auto">
                    <div class="d-flex align-items-center justify-content-center bg-<?= $color ?>-subtle text-<?= $color ?> rounded-circle p-3">
                      <i class="bi <?= $icon ?> fs-5"></i>
                    </div>
                  </div>

                  <div class="col min-width-0">
                    <div class="d-flex flex-wrap align-items-center gap-2 mb-1">
                      <span class="fw-semibold"><?= htmlspecialchars($title) ?></span>
                      <span class="badge rounded-pill bg-<?= $color ?>-subtle text-<?= $color ?>">
                        <?= htmlspecialchars($log['action']) ?>
                      </span>
                    </div>

                    <div class="small text-body-secondary text-break mb-2">
                      <?= htmlspecialchars($log['description']) ?>
                    </div>

                    <div class="small text-body-secondary">
                      <i class="bi bi-person me-1"></i>
                      <?= htmlspecialchars($log['user_name']) ?>
                    </div>
                  </div>

                  <div class="col-12 col-md-auto">
                    <div class="small text-body-secondary d-flex align-items-center gap-2 justify-content-md-end border-top pt-2 pt-md-0 mt-1 mt-md-0">
                      <i class="bi bi-clock"></i>
                      <span class="text-break">
                        <?= date('M d, Y • h:i A', strtotime($log['created_at'])) ?>
                      </span>
                    </div>
                  </div>

                </div>
              </div>
            </div>

          <?php endwhile; ?>
        <?php else: ?>
          <div class="card border shadow-sm rounded-4">
            <div class="card-body text-center text-body-secondary py-5">
              <i class="bi bi-activity fs-2 d-block mb-2"></i>
              No activity found.
            </div>
          </div>
        <?php endif; ?>

        <?php if ($totalPages > 1): ?>
          <div class="card border shadow-sm rounded-4 mt-4 mb-3">
            <div class="card-body p-3">
              <div class="d-flex flex-column flex-md-row align-items-center justify-content-between gap-3">

                <div class="small text-body-secondary text-center">
                  Showing
                  <strong><?= min($offset + 1, $totalRecords) ?></strong>
                  -
                  <strong><?= min($offset + $limit, $totalRecords) ?></strong>
                  of
                  <strong><?= $totalRecords ?></strong>
                  activities
                </div>

                <nav aria-label="Activity log pagination">
                  <ul class="pagination pagination-sm mb-0 flex-wrap justify-content-center gap-1">

                    <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
                      <a class="page-link rounded-2" href="activity-log.php?date=<?= urlencode($date) ?>&page=<?= max(1, $page - 1) ?>" aria-label="Previous">
                        <i class="bi bi-chevron-left"></i>
                      </a>
                    </li>

                    <?php
                    $startPage = max(1, $page - 2);
                    $endPage = min($totalPages, $page + 2);
                    ?>

                    <?php if ($startPage > 1): ?>
                      <li class="page-item">
                        <a class="page-link rounded-2" href="activity-log.php?date=<?= urlencode($date) ?>&page=1">1</a>
                      </li>

                      <?php if ($startPage > 2): ?>
                        <li class="page-item disabled">
                          <span class="page-link rounded-2">...</span>
                        </li>
                      <?php endif; ?>
                    <?php endif; ?>

                    <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
                      <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                        <a class="page-link rounded-2" href="activity-log.php?date=<?= urlencode($date) ?>&page=<?= $i ?>">
                          <?= $i ?>
                        </a>
                      </li>
                    <?php endfor; ?>

                    <?php if ($endPage < $totalPages): ?>
                      <?php if ($endPage < $totalPages - 1): ?>
                        <li class="page-item disabled">
                          <span class="page-link rounded-2">...</span>
                        </li>
                      <?php endif; ?>

                      <li class="page-item">
                        <a class="page-link rounded-2" href="activity-log.php?date=<?= urlencode($date) ?>&page=<?= $totalPages ?>">
                          <?= $totalPages ?>
                        </a>
                      </li>
                    <?php endif; ?>

                    <li class="page-item <?= ($page >= $totalPages) ? 'disabled' : '' ?>">
                      <a class="page-link rounded-2" href="activity-log.php?date=<?= urlencode($date) ?>&page=<?= min($totalPages, $page + 1) ?>" aria-label="Next">
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

    <?php include 'partials/footer.php'; ?>

  </div>

  <!-- CoreUI and necessary plugins -->
  <script src="vendors/@coreui/coreui/js/coreui.bundle.min.js"></script>
  <script src="vendors/simplebar/js/simplebar.min.js"></script>

  <script>
    const header = document.querySelector("header.header");

    document.addEventListener("scroll", () => {
      if (header) {
        header.classList.toggle(
          "shadow-sm",
          document.documentElement.scrollTop > 0
        );
      }
    });
  </script>

  <?php include_once 'notification/session_timeout.php'; ?>

</body>

</html>