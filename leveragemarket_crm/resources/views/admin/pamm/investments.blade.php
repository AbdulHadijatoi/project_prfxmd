@extends('layouts.admin.admin')
@section('content')
    <div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title" id="addUserModalLabel1">Add Investment</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="post" id="createInvestorForm">
                    <div class="modal-body">
                        <div class="row gy-4">
                            <div class="col-12">
                                <label for="input-label" class="form-label">Money Manager</label>
                                <select id="manager_id" name="manager_id" class="form-control money-manager-select"
                                    required>
                                    <option value="">Select Account</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label for="input-label" class="form-label">Offer</label>
                                <select id="offer_id" name="offer_id" class="form-control offer-select" required>
                                    <option value="">Select Offer</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <div class="col-12">
                                    <label for="input-label" class="form-label">Userame</label>
                                    <select class="form-control live-acc-select" name="owner_id" required>
                                        <option value="">Select Account</option>
                                    </select>
                                    <!-- <input type="text" class="form-control" name="owner_id" required> -->
                                </div>
                                <!-- <div class="col-12">
                      <label for="input-label" class="form-label">Password</label>
                      <input type="password" class="form-control" name="trade_password" required>
                    </div> -->
                                <div class="col-12">
                                    <label for="input-label" class="form-label">Initial Investment</label>
                                    <input type="number" min="0" class="form-control" name="amount"
                                        autocomplete="off" id="amount" required>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <input type="submit" class="btn btn-primary" name="add_user" value="Create">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="page-header">
                <h1 class="page-title">Investments</h1>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">PAMM</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Investments</li>
                </ol>
            </div>
            <div class="d-flex justify-content-end mb-3">
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">
                    Add Investment
                </button>
            </div>
            <div class="row">
                <div class="col-xl-12">
                    <div class="card custom-card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="tableInvestments" class="ajaxDataTable table table-bordered text-nowrap w-100">
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        $(document).ready(function() {
            const table = $('.ajaxDataTable').DataTable({
                order: [
                    [0, "desc"]
                ],
                ajax: {
                    url: "/admin/pamm/get_investments",
                    type: "GET",
                    data: {
                        action: 'get_investments'
                    },
                },
                columns: [{
                        data: 'id',
                        title: '#'
                    },
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
                            return moment(row.tradingIntervalEnd).format('YYYY') !== '0001' ?
                                `${start} - ${end}` : "";
                        }
                    },
                    {
                        data: 'offerName',
                        title: 'Offer'
                    },
                    {
                        data: 'createdDt',
                        title: 'Created',
                        render: (data) => moment(data).format("YYYY-MM-DD HH:mm:ss")
                    }
                ],
                initComplete: function() {
                    let dropdown = $('.investment-select');
                    let data = this.api().ajax.json().data;

                    if (data && data.length) {
                        let options = '<option></option>';
                        $.each(data, function(index, item) {
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

        $(document).ready(function() {
            select2init();
            $.ajax({
                url: '/admin/pamm/get_money_managers',
                type: "GET",
                data: {},
                success: function(resp) {
                    // const resp = JSON.parse(response);
                    const data = resp.data;
                    const dropdown = $('.money-manager-select');
                    dropdown.empty();
                    dropdown.append('<option></option>');
                    $.each(data, function(index, item) {
                        if (item.isPublic == true && item.configurationName != null) {
                            //[$${item.funds} - ${item.investmentCountActive} Investments]
                            dropdown.append(
                                `<option value="${btoa(item.id)}">
                              ${item.name} - ${item.accountId}
                          </option>`
                            );
                        }
                    });
                    dropdown.select2({
                        dropdownParent: $('#addUserModal'),
                        placeholder: 'Select a Money Manager',
                        allowClear: true
                    });
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching money manager data:', error);
                }
            });
        });

        function select2init() {
            $('.live-acc-select').select2({
                dropdownParent: $('#addUserModal'),
                ajax: {
                    url: '/admin/ajax',
                    type: "GET",
                    data: function(params) {
                        var searchValue = params.term;
                        return {
                            term: searchValue,
                            action: 'getLiveAccounts'
                        };
                    },
                    processResults: function(data) {
                        data = JSON.parse(data);
                        return {
                            results: $.map(data, function(item) {
                                console.log(item.trade_id + " [" + item.name + " - " + item.email +
                                "]");
                                return {
                                    text: item.trade_id + " [" + item.name + " - " + item.email + "]",
                                    id: item.trade_id
                                }
                            })
                        };
                    }
                }
            });
        }

        $(document).on('change', '#manager_id', function() {
            let manager_id = $('#manager_id').val();
            $.ajax({
                url: '/admin/pamm/get_manager_offer',
                type: "POST",
                data: {
                    'manager_id': manager_id
                },
                success: function(response) {
                    const resp = JSON.parse(response);
                    console.log(resp);
                    $('#offer_id').empty();
                    $('#offer_id').append('<option value="">Select an Offer</option>');
                    resp.data.forEach(function(item) {
                        if (item.isActive == true) {
                            $('#offer_id').append(
                                $('<option>', {
                                    value: item.id,
                                    text: item.name,
                                    'data-mindeposit': item.settings
                                        .minInitialInvestment
                                })
                            );
                        }
                    });
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching manager data:', error);
                }
            });
        });
        $(document).on('change', '#offer_id', function() {
            var selectedOption = $(this).find(':selected');
            var minimumValue = selectedOption.data('mindeposit');
            $('#amount').attr('min', minimumValue);
            $('#amount').val('');
        });

        $('#createInvestorForm').on('submit', function(e) {
            e.preventDefault();
            let formData = $(this).serialize();
            $.ajax({
                url: '/admin/pamm/create_investments',
                method: 'POST',
                data: formData,
                success: function(response) {
                    Swal.fire({
                        title: response.message,
                        icon: response.status,
                    }).then(() => {
                        location.reload();
                    });
                },
                error: function(xhr, status, error) {
                    Swal.fire({
                        title: 'Error: Could not create Investment. Please try again.',
                        icon: 'danger',
                    }).then(() => {
                        // location.reload();
                    });
                }
            });
        });
    </script>
@endsection
