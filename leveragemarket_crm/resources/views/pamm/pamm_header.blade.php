<?php $current_page = basename($_SERVER['REQUEST_URI'], ".php"); ?>
<div class="row">
  <div class="col-sm-12">
    <div class="card">
      <div class="card-body p-0">
        <ul class="nav nav-tabs checkout-tabs mb-0" id="pammTab" role="tablist">
          <li class="nav-item" role="presentation"><a
              class="nav-link <?php echo ($current_page == 'investment') ? 'active' : ''; ?>"
              href="/pamm/investments">
              <div class="media align-items-center">
                <div class="avtar avtar-s"><i class="feather icon-credit-card"></i></div>
                <div class="media-body ms-2">
                  <h6 class="mb-0">Create New</h6>
                </div>
              </div>
            </a></li>
          <!-- <li class="nav-item" role="presentation"><a
              class="nav-link <?php echo ($current_page == 'deposits') ? 'active' : ''; ?>" href="/pamm/deposits.php">
              <div class="media align-items-center">
                <div class="avtar avtar-s"><i data-feather="share"></i></div>
                <div class="media-body ms-2">
                  <h6 class="mb-0">Deposits</h6>
                </div>
              </div>
            </a></li> -->
          <li class="nav-item" role="presentation"><a
              class="nav-link <?php echo ($current_page == 'investment_list') ? 'active' : ''; ?>"
              href="/pamm/investment_list">
              <div class="media align-items-center">
                <div class="avtar avtar-s"><i class="feather icon-dollar-sign"></i></div>
                <div class="media-body ms-2">
                  <h6 class="mb-0">Investments</h6>
                </div>
              </div>
            </a></li>
        </ul>
      </div>
    </div>
  </div>
</div>
