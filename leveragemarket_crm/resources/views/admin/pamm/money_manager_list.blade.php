@extends('layouts.admin.admin')
@section('content')
    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                  <div class="modal-content">
                    <div class="modal-header">
                      <h6 class="modal-title" id="addUserModalLabel1">Create Money Manager</h6>
                      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form method="post" id="createMoneyManagerForm">
                      <div class="modal-body">
                        <div class="row gy-4">
                          <div class="col-12">
                            <label for="input-label" class="form-label">Owner Account</label>
                            <select class="form-select live-acc-select" name="owner_id" required>
                              <option value="">Select Account</option>
                            </select>
                          </div>
                          <div class="col-12">
                            <div class="row">
                              <div class="col-lg-6">
                                <div class="form-check">
                                  <input class="form-check-input account_type" value="new" type="radio" name="account_type"
                                    id="new_trade_account">
                                  <label class="form-check-label" for="new_trade_account">
                                    New Trading Account
                                  </label>
                                </div>
                              </div>
                              <div class="col-lg-6">
                                <div class="form-check">
                                  <input class="form-check-input account_type" value="existing" type="radio" name="account_type"
                                    id="existing_trade_account" checked>
                                  <label class="form-check-label" for="existing_trade_account">
                                    Existing
                                  </label>
                                </div>
                              </div>
                            </div>
                          </div>
                          <div class="col-12 d-none existing-account">
                            <label for="input-label" class="form-label">Trading Account</label>
                            <select name="trade_id" class="form-control live-acc-select">
                              <option value="">Select Account</option>
                            </select>
                          </div>
                          <div class="col-12 d-none new-account">
                            <div class="col-12">
                              <label for="input-label" class="form-label">Name</label>
                              <input type="text" class="form-control" name="name" autocomplete="off">
                            </div>
                            <div class="col-12">
                              <label for="input-label" class="form-label">Password</label>
                              <input type="password" class="form-control" name="password" autocomplete="off">
                              <small class="form-text text-muted">
                                Password must be at least 8 characters long, include at least one uppercase letter, one lowercase
                                letter, one digit, and one special character.
                              </small>
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
            <div class="page-header">
                <h1 class="page-title">Money Managers</h1>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">PAMM</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Money Managerss</li>
                </ol>
            </div>
            <div class="d-flex justify-content-end mb-3">
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">
                  Create Money Manager
                </button>
              </div>
            <div class="row">
                <div class="col-xl-12">
                    <div class="card custom-card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="tableMoneyManagers" class="ajaxDataTable table table-bordered text-nowrap w-100">
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        $(document).ready(function () {
          $('input[name="account_type"][value="new"]').prop('checked', true).trigger('change');
          select2init();
        });
        $(document).on("change", ".account_type", function () {
          var account_type = $(this).val();
          if (account_type == "new") {
            $('.new-account').removeClass('d-none');
            $('.existing-account').addClass('d-none');
          } else {
            $('.new-account').addClass('d-none');
            $('.existing-account').removeClass('d-none');
          }
        });
        $('#createMoneyManagerForm').on('submit', function (e) {
          e.preventDefault();
          let formData = $(this).serialize();
          $.ajax({
            url: '/admin/pamm/create_money_manager',
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
                title: 'Error: Could not create Money Manager. Please try again.',
                icon: 'danger',
              }).then(() => {
                // location.reload();
              });
            }
          });
        });


        function select2init() {
          $('.live-acc-select').select2({
            dropdownParent: $('#addUserModal'),
            ajax: {
              url: '/admin/ajax',
              type: "GET",
              data: function (params) {
                var searchValue = params.term;
                return {
                  term: searchValue,
                  action: 'getLiveAccounts'
                };
              },
              processResults: function (data) {
                data = JSON.parse(data);
                return {
                  results: $.map(data, function (item) {
                    console.log(item.trade_id + " [" + item.name + " - " + item.email + "]");
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

        $('#tableMoneyManagers').on("draw.dt", dTSelection).DataTable({
          order: [
            [0, "desc"]
          ],
          "ajax": {
            "url": "/admin/pamm/get_money_managers",
            "type": "GET"
          },
          columns: [
            {
              data: 'id',
              title: '#'
            },
            {
              data: '',
              title: 'Action',
              render: function(data, row, row_data) {
                var return_data = "<a href='/admin/pamm/money_manager_details?id=" + btoa(row_data.id)+ "'><span class='btn btn-primary text-white'>View</span></a>";
                return return_data;
              }
            },
            {
              data: 'name',
              title: '',
              render: function(data, row, row_data) {
                var return_data = "<a href='/admin/pamm/money_manager_details?id=" +btoa(row_data.id)+ "'>"+data+"</a>";
                return return_data;
              }
            },
            {
              data: 'accountId',
              title: 'Trading Account'
            },
            {
              data: 'funds',
              title: 'Total Funds',
              render: function (data) {
                return '$' + data;
              }
            },
            {
              data: 'profitTotal',
              title: 'Investors',
              render: function (data) {
                return data > 0 ? '<span class="text-success">$' + data + '</span>' : (data < 0 ? '<span class="text-danger">$' + data + '</span>' : '<span class="text-muted">$' + data + '</span>');
              }
            },
            {
              data: 'isPublic',
              title: 'Visibility',
              render: function (data) {
                return data == true ? '<span class="text-success"><i class="fa fa-users p-1" aria-hidden="true"></i>Public</span>' : '<span class="text-muted"><i class="fa fa-lock p-1" aria-hidden="true"></i>Private</span>';
              }
            },
            {
              data: 'ownerId',
              title: 'Owner',
            },
            {
              data: 'configurationName',
              title: 'Configuration',
            },
            {
              data: 'currency',
              title: 'Currency',
            },
            {
              data: 'createdDt',
              title: 'created',
              render: function (data) {
                return moment(data).format("YYYY-MM-DD HH:mm:ss");
              }
            }
          ]
        });

        function dTSelection() {

        }


      </script>
@endsection
