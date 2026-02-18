@extends('layouts.crm.crm')
@section('content')
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.19/css/dataTables.bootstrap4.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css">

<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<style>
  .paginate_button {
    padding: 0 5px !important;
    cursor: pointer !important;
  }
</style>

<div class="modal fade" id="addDepositModal" tabindex="-1" aria-labelledby="addDepositModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h6 class="modal-title" id="addDepositModalLabel1">Add Deposit</h6>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form method="post" id="createDepositForm">
        <div class="modal-body">
          <div class="row gy-4">
            <div class="col-12">
              <label for="input-label" class="form-label">Investment</label>
              <select id="investmentId" name="investmentId" class="form-control investment-select" required>
              </select>
            </div>
            <div class="col-12">
              <label for="input-label" class="form-label">Amount</label>
              <input type="number" min="0" class="form-control" name="amount" autocomplete="off" id="amount" required>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <input type="submit" class="btn btn-primary" name="add_deposit" value="Deposit">
          <button type="button" class="btn btn-secondary text-secondary" data-bs-dismiss="modal">Close</button>
        </div>
      </form>
    </div>
  </div>
</div>


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
    @include('pamm.pamm_header');
    <div class="row">
      <div class="card">
        <div class="d-flex justify-content-end mt-3">
          <button id="depositAddBtn" disabled data-bs-toggle="modal" data-bs-target="#addDepositModal"
            class="btn btn-primary bg-primary text-white">
            <span class="mb-0 text-white">Add Deposit</span><i class="ti ti-database-import f-18"></i>
          </button>
        </div>
        <div class="card-body table-responsive">
          <table class="ajaxDataTable table table-bordered text-nowrap w-100">
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
<script>
    $(document).ready(function () {
      const table = $('.ajaxDataTable').DataTable({
        order: [[0, "desc"]],
        ajax: {
          url: "/pamm/fetchclient_investments",
          type: "GET",
          data: {},
        },
        columns: [
          { data: 'id', title: '#' },
          {
            data: 'ownerId',
            title: 'Owner Account',
            render: (data, options, row) =>
              `<span class="text-muted">#${row.ownerId} (${row.ownerServerName})</span>`
          },
          {
            data: 'managerName',
            title: 'PAMM',
            render: (data, options, row) =>
              `${row.managerName} <span class="text-muted">#${row.managerId}</span>`
          },
          {
            data: 'fundsTotal',
            title: 'Funds',
            render: (data) => formatCurrency(data, data == 0)
          },
          {
            data: 'profitNet',
            title: 'Net Profit',
            render: formatProfit
          },
          {
            data: 'profitTotal',
            title: 'Trade Results',
            render: formatProfit
          },
          {
            data: 'profitIntervalTotal',
            title: 'Trade Results',
            render: formatProfit
          },
          {
            data: 'currency',
            title: 'Trading Interval',
            render: (data, options, row) => {
              const start = moment(row.tradingIntervalStart).format("MMM D, YYYY");
              const end = moment(row.tradingIntervalEnd).format("MMM D, YYYY");
              return moment(row.tradingIntervalEnd).format('YYYY') !== '0001' ? `${start} - ${end}` : "";
            }
          },
          { data: 'offerName', title: 'Offer' },
          {
            data: 'createdDt',
            title: 'Created',
            render: (data) => moment(data).format("YYYY-MM-DD HH:mm:ss")
          }
        ],
        initComplete: function () {
          let dropdown = $('.investment-select');
          let data = this.api().ajax.json().data;

          if (data && data.length) {
            let options = '<option></option>';
            $.each(data, function (index, item) {
              options += `<option data-owner="${item.isManagerOwned}" data-manager="${btoa(item.managerId)}" value="${btoa(item.id)}">
                          #${item.id} - ${item.ownerId} (${item.managerName})
                        </option>`;
            });
            dropdown.html(options);
          }

          dropdown.select2({
            dropdownParent: $('#addDepositModal'),
            placeholder: 'Select an investment',
            allowClear: true
          }).addClass('d-block');
          $('.select2-container').addClass('d-block');
          $('#depositAddBtn').prop('disabled', false);
        }
      });

      $('.ajaxDataTable').on("draw.dt", dTSelection);
    });
    function dTSelection() {}
    const formatCurrency = (data, isMuted = false) => {
      const formatted = `$${parseFloat(data).toFixed(2)}`;
      return isMuted ? `<span class="text-muted">${formatted}</span>` : formatted;
    };

    const formatProfit = (data) => {
      const formatted = `$${parseFloat(data).toFixed(2)}`;
      if (data == 0) return `<span class="text-muted">${formatted}</span>`;
      return `<span class="${data > 0 ? 'text-success' : 'text-danger'}">${formatted}</span>`;
    };
    $('#createDepositForm').on('submit', function (e) {
      e.preventDefault();
      let formData = $(this).serialize();
      formData += '&action=deposit_investments';

      let selectedOption = $('#investmentId option:selected');
      let managerId = selectedOption.data('manager');
      let isManagerOwned = selectedOption.data('owner');
      formData += '&managerId=' + managerId+'&isManagerOwned='+isManagerOwned;

      $.ajax({
        url: '/pamm/deposit_investments',
        method: 'POST',
        data: formData,
        success: function (response) {
          Swal.fire({
            title: response.message,
            icon: response.status,
          }).then(() => {
            location.reload();
          });
        },
        error: function (xhr, status, error) {
          Swal.fire({
            title: 'Error: Could not create Investment. Please try again.',
            icon: 'danger',
          }).then(() => {
            location.reload();
          });
        }
      });
    });

  </script>
@include('admin.pamm.pamm_scripts');
@endsection
