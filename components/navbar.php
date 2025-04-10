<nav class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme" id="layout-navbar">
  <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none">
    <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)">
      <i class="bx bx-menu bx-sm"></i>
    </a>
  </div>

  <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">

    <ul class="navbar-nav flex-row align-items-center ms-auto">
      <!-- Place this tag where you want the button to render. -->

      <!-- User -->
      <li class="nav-item navbar-dropdown dropdown-user dropdown">
        <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
          <div class="avatar"> <!-- avatar-online -->
            <img src="<?= $helpers->get_avatar_link($LOGIN_USER->id) ?>" class="avatar avatar-md rounded-circle" style="object-fit: cover" />
          </div>
        </a>
        <ul class="dropdown-menu dropdown-menu-end">
          <li>
            <a class="dropdown-item" href="#">
              <div class="d-flex">
                <div class="flex-shrink-0 me-3">
                  <div class="avatar"><!-- avatar-online -->
                    <img src="<?= $helpers->get_avatar_link($LOGIN_USER->id) ?>" class="avatar avatar-md rounded-circle" style="object-fit: cover" />
                  </div>
                </div>
                <div class="flex-grow-1">
                  <span class="fw-semibold d-block"><?= $helpers->get_full_name($LOGIN_USER->id) ?></span>
                  <small class="text-muted text-capitalize"><?= $LOGIN_USER->role ?></small>
                </div>
              </div>
            </a>
          </li>
          <li>
            <div class="dropdown-divider"></div>
          </li>
          <li>
            <a class="dropdown-item" href="<?= SERVER_NAME . "/views/profile" ?>">
              <i class="bx bx-user me-2"></i>
              <span class="align-middle">My Profile</span>
            </a>
          </li>
          <?php
          if ($LOGIN_USER->company_id) :
            $companyData = $helpers->select_all_individual("company", "id='$LOGIN_USER->company_id'");
            $verification_id = empty($companyData->verification_id) ? null : $companyData->verification_id;
          ?>
            <li>
              <a class="dropdown-item" href="javascript:void(0)" onclick="handleCheckStatus(`<?= $verification_id ?>`)">
                <i class="bx bx-check-circle me-2"></i>
                <span class="align-middle">Check Status</span>
              </a>
            </li>
          <?php endif; ?>
          <li>
            <a class="dropdown-item" href="<?= SERVER_NAME . "/backend/nodes?action=logout" ?>">
              <i class="bx bx-power-off me-2"></i>
              <span class="align-middle">Log Out</span>
            </a>
          </li>
        </ul>
      </li>
      <!--/ User -->
    </ul>
  </div>
</nav>