@extends('layouts.crm.crm')
@section('content')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-theme@1.0.0-beta.10/dist/select2-bootstrap.min.css"
  rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<style>
  .select2-container .select2-selection--single {
    height: calc(2.25rem + 2px);
    border: 1px solid #ced4da;
    border-radius: 0.25rem;
    padding: 0.375rem 0.75rem;
  }
</style>

<div class="pc-container">
  <div class="pc-content">
    <div class="page-header mb-0 pb-0">
      <div class="page-block">
        <div class="row align-items-center">
          <div class="col-md-12">
            <div class="page-header-title h2">
              <h4 class="mb-0">PAMM</h4>
            </div>
          </div>
        </div>
      </div>
    </div>
    @include('pamm.pamm_header')
    <div class="row">
      <div class="col-sm-11">
        <div class="card">
        <div class="card-header"><h4>Add Investment</h4></div>
          <div class="card-body">
            <form method="post" id="createInvestorForm" class="ajaxForm">
              <div class="modal-body">
                <div class="row gy-4">
                  <div class="row align-items-top mt-4">
                    <input type="hidden" name="action" value="create_investments" />
                    <label for="manager_id" class="form-label col-4">Money Manager: </label>
                    <div class="col-8">
                      <select id="manager_id" name="manager_id" class="form-control money-manager-select" required>
                        <option value="" disabled selected>Select Money Manager</option>
                      </select>
                      <div id="investment_manager_details" class="mt-2">
                      </div>
                    </div>
                  </div>
                  <div class="row align-items-top mt-4">
                    <label for="offer_id" class="form-label col-4">Offer: </label>
                    <div class="col-8">
                      <select id="offer_id" name="offer_id" class="form-control offer-select" required>
                        <option value="" disabled selected>Select Offer</option>
                      </select>
                      <div class="container mt-4">
                        <div class="table-responsive">
                          <table class="table table-bordered table-striped" id="performanceFeesTable">
                            <tbody>
                            </tbody>
                          </table>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="row align-items-top mt-4">
                    <label for="owner_id" class="form-label col-4">Owner Account: </label>
                    <div class="col-8">
                      <select class="form-control" name="owner_id" required>
                        <option value="">Select Account</option>
                        <?php foreach ($liveaccounts as $liveaccount) { ?>
                          <option value="<?= $liveaccount->trade_id ?>"><?= $liveaccount->trade_id ?></option>
                        <?php } ?>
                      </select>
                    </div>
                  </div>
                  <div class="row align-items-top mt-4">
                    <label for="amount" class="form-label col-4">Initial Investment: </label>
                    <div class="col-8">
                      <input type="number" min="1" class="form-control" name="amount" autocomplete="off" id="amount"
                        required>
                    </div>
                  </div>
                </div>
              </div>
              <div class="modal-footer mt-3">
                <input type="submit" class="btn btn-primary" name="create_investment" value="Create">
              </div>
            </form>

          </div>
        </div>
      </div>

    </div>
  </div>
</div>
<script>
  $(document).ready(function () {
    $.ajax({
      url: '/pamm/get_money_managers',
      type: "GET",
      data: {},
      success: function (resp) {
        const data = resp.data;
        const dropdown = $('.money-manager-select');
        dropdown.empty();
        dropdown.append('<option></option>');
        $.each(data, function (index, item) {
          if (item.isPublic == true && item.configurationName != null) {
            dropdown.append(
              `<option data-funds="${item.funds}" data-investors="${item.investmentCountActive}" value="${btoa(item.id)}">
                        ${item.name} - ${item.accountId}
                    </option>`
            );
          }
        });
        dropdown.select2({
          placeholder: 'Select a Money Manager',
          allowClear: true
        });
      },
      error: function (xhr, status, error) {
        console.error('Error fetching money manager data:', error);
      }
    });
  });
  $(document).on('change', '#manager_id', function () {
    let manager_id = $('#manager_id').val();

    const selectedOption = $(this).find(':selected');
    const funds = selectedOption.data('funds');
    const investors = selectedOption.data('investors');
    let html = `<div class="">
    <span class="text-muted">Funds: </span><span>$${funds}</span>
    <span class="ms-3 text-muted">Investors: </span><span>${investors}</span>
    </div>`;
    $('#investment_manager_details').html(html);

    $.ajax({
      url: '/pamm/get_manager_offer',
      type: "POST",
      data: {'manager_id': manager_id },
      success: function (response) {
        const resp = JSON.parse(response);
        $('#offer_id').empty();
        $('#offer_id').append('<option value="">Select an Offer</option>');
        resp.data.forEach(function (item) {
          if (item.isActive == true) {
            $('#offer_id').append(
              $('<option>', {
                value: item.id,
                text: item.name,
                'data-mindeposit': item.settings.minDeposit
              })
            );
          }
        });
      },
      error: function (xhr, status, error) {
        console.error('Error fetching manager data:', error);
      }
    });
  });
  $(document).on('change', '#offer_id', function () {
    var id = $(this).val();
    var selectedOption = $(this).find(':selected');
    var manager_id = $('#manager_id').val();
    var minimumValue = selectedOption.data('mindeposit');
    if (minimumValue > 0) {
      $('#amount').attr('min', minimumValue);
    }
    $('#amount').val('');
    $.ajax({
      url: '/pamm/offers_money_manager',
      method: 'POST',
      data: {
        id: manager_id
      },
      beforeSend: function () {
        Swal.fire({
          title: 'Fetching ....',
          text: 'Please wait while we process your request.',
          icon: 'info',
          allowOutsideClick: false,
          didOpen: () => {
            Swal.showLoading();
          }
        });
      },
      success: function (resp) {
        Swal.close();
        // var resp = JSON.parse(response);
        const filteredData = resp.data.filter(item => item.id == id);
        const perfomanceFees = filteredData[0].settings.performanceFees.levels;
        const $tableBody = $('#performanceFeesTable tbody');
        $tableBody.empty();
        if (perfomanceFees) {
          const $bodyRow1 = $('<tr><td>Equity</td></tr>');
          const $bodyRow2 = $('<tr><td>Fee(%)</td></tr>');
          $.each(perfomanceFees, function (index, fee) {
            const $levelCell = $('<td></td>').text('$' + parseFloat(fee.level).toFixed(2));
            const $valueCell = $('<td></td>').text(fee.value + '%');
            $bodyRow1.append($levelCell);
            $bodyRow2.append($valueCell);
          });
          $tableBody.append($bodyRow1, $bodyRow2);
        }
      },
      error: function (xhr, status, error) {
        Swal.close();
        Swal.fire({
          title: 'Error: Could Not Find Offer. Please try again.',
          icon: 'danger',
        }).then(() => {
        });
      }
    });

  });

</script>
<script>
    $('.ajaxForm').on('submit', function (e) {
        e.preventDefault();
        var $form = $(this);
        var formData = $form.serialize();
        var action = $form.find('[name="action"]').val();
        $.ajax({
            url: `/pamm/${action}`,
            type: 'POST',
            data: formData,
            beforeSend: function () {
                $form.find('button[type="submit"]').prop('disabled', true);
                Swal.fire({
                    title: 'Submitting...',
                    text: 'Please wait while we process your request.',
                    icon: 'info',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
            },
            success: function (response) {
                if (response) {
                    try {
                        response = JSON.parse(response);
                    } catch (e) {
                    }
                }
                if (response.status == 'success') {
                    $('.modal').modal('hide');
                    Swal.fire({
                        title: 'Success!',
                        text: 'Request submitted successfully!',
                        icon: 'success',
                        confirmButtonText: 'OK'
                    }).then((result) => {
                        Swal.fire({
                            title: 'Please hold on',
                            text: 'The page is refreshing. This may take a moment.',
                            icon: 'info',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        title: 'Error!',
                        text: response.message,
                        icon: 'error',
                        confirmButtonText: 'OK'
                    }).then((result) => {
                        // location.reload();
                    });
                }
            },
            error: function (xhr, status, error) {
                Swal.fire({
                    title: 'Error!',
                    text: 'There was an issue with the request.',
                    icon: 'error',
                    confirmButtonText: 'OK'
                }).then((result) => {
                    // location.reload();
                });
            },
            complete: function () {
                $form.find('button[type="submit"]').prop('disabled', false);
            }
        });
    });
</script>

@endsection
