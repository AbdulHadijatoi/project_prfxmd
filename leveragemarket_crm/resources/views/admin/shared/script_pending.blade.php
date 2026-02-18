<script>
    $(document).ready(function() {
        $('#tableAllTransactions').DataTable({
            order: [[4, "desc"]],
            "ajax": {
                "url": "/admin/ajax",
                "type": "GET",
                data: function(d) {
                    d.action = 'getPendingAllTransactions';
                    d.startdate = $('.atStartDate').val();
                    d.enddate = $('.atEndDate').val();
                    // d.option = $('.atOption').val();
                }
            },
            columns: [{
                    data: 'email',
                    name: 'email',
                    title:'Email',
                    render: function(data, row, row_data) {
                        var return_data = "<a href='/admin/client_details?id=" + row_data
                            .enc_id +
                            "'><div class='d-flex align-items-center'><div class='me-2'><svg xmlns='http://www.w3.org/2000/svg' width='28' height='28' viewBox='0 0 24 24' fill='none' stroke='#000000' stroke-width='1.5' stroke-linecap='round' stroke-linejoin='round' size='28' color='#000000' class='tabler-icon tabler-icon-user-square-rounded'><path d='M12 13a3 3 0 1 0 0 -6a3 3 0 0 0 0 6z'></path><path d='M12 3c7.2 0 9 1.8 9 9s-1.8 9 -9 9s-9 -1.8 -9 -9s1.8 -9 9 -9z'></path><path d='M6 20.05v-.05a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v.05'></path></svg></div><div><div class='lh-1'><span>" +
                            row_data.fullname +
                            "</span></div><div class='lh-1'><span class='fs-11 text-muted'>" +
                            row_data.email + "</span></div></div></div></a>";
                        return return_data;
                    }
                },
                {
                    data: 'amount',
                    name: 'amount',
                    title:'Amount'
                },
                {
                    data: 'transaction_type',
                    name: 'transaction_type',
                    title:'Transaction Type'
                },
                {
                    data: 'payment_mode',
                    name: 'payment_mode',
                    title:'Payment Mode'
                },
                {
                    data: 'deposit_date',
                    name: 'deposit_date',
                    title:'Date',
                    render: function(data, type, row) {
                        var dateTime = row.deposit_date.split(' ');
                        var date = dateTime[0];
                        var time = dateTime[1];
                        var return_data = "<div class='d-grid'><div class='date'>" + date +
                            "</div><div class='time text-muted'>" + time + "</div></div>";
                        return return_data;
                    }
                },
                {
                    data: 'status',
                    name: 'status',
                    title:'Status'
                },
                {
                    data: 'action',
                    name: 'action',
                    title:'Action',
                    orderable: false,
                    searchable: false
                },
            ]
        });
        $('#tableWalletDeposit').DataTable({
            order: [[3, "desc"]],
            "ajax": {
                "url": "/admin/ajax",
                "type": "GET",
                data: function(d) {
                    d.action = 'getPendingWalletDeposit';
                    d.startdate = $('.wdStartDate').val();
                    d.enddate = $('.wdEndDate').val();
                }
            },
            columns: [{
                    data: 'email',
                    name: 'email',
                    title:'Email',
                    render: function(data, row, row_data) {
                        var return_data = "<a href='/admin/client_details?id=" + row_data
                            .enc_id +
                            "'><div class='d-flex align-items-center'><div class='me-2'><svg xmlns='http://www.w3.org/2000/svg' width='28' height='28' viewBox='0 0 24 24' fill='none' stroke='#000000' stroke-width='1.5' stroke-linecap='round' stroke-linejoin='round' size='28' color='#000000' class='tabler-icon tabler-icon-user-square-rounded'><path d='M12 13a3 3 0 1 0 0 -6a3 3 0 0 0 0 6z'></path><path d='M12 3c7.2 0 9 1.8 9 9s-1.8 9 -9 9s-9 -1.8 -9 -9s1.8 -9 9 -9z'></path><path d='M6 20.05v-.05a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v.05'></path></svg></div><div><div class='lh-1'><span>" +
                            row_data.fullname +
                            "</span></div><div class='lh-1'><span class='fs-11 text-muted'>" +
                            row_data.email + "</span></div></div></div></a>";
                        return return_data;
                    }
                },
                {
                    data: 'amount',
                    name: 'amount',
                    title:'Amount'
                },
                {
                    data: 'payment_mode',
                    name: 'payment_mode',
                    title:'Payment Mode'
                },
                {
                    data: 'deposit_date',
                    name: 'deposit_date',
                    title:'Deposit Date',
                    render: function(data, type, row) {
                        var dateTime = row.deposit_date.split(' ');
                        var date = dateTime[0];
                        var time = dateTime[1];
                        var return_data = "<div class='d-grid'><div class='date'>" + date +
                            "</div><div class='time text-muted'>" + time + "</div></div>";
                        return return_data;
                    }
                },
                {
                    data: 'status',
                    name: 'status',
                    title:'Status'
                },
                {
                    data: 'action',
                    name: 'action',
                    title:'Action',
                    orderable: false,
                    searchable: false
                },
            ]
        });
        $('#tableWalletWithdrawal').DataTable({
            order: [[3, "desc"]],
            "ajax": {
                "url": "/admin/ajax",
                "type": "GET",
                data: function(d) {
                    d.action = 'getPendingWalletWithdrawal';
                    d.startdate = $('.wwStartDate').val();
                    d.enddate = $('.wwEndDate').val();
                }
            },
            columns: [{
                    data: 'email',
                    name: 'email',
                    title:'Email',
                    render: function(data, row, row_data) {
                        var return_data = "<a href='/admin/client_details?id=" + row_data
                            .enc_id +
                            "'><div class='d-flex align-items-center'><div class='me-2'><svg xmlns='http://www.w3.org/2000/svg' width='28' height='28' viewBox='0 0 24 24' fill='none' stroke='#000000' stroke-width='1.5' stroke-linecap='round' stroke-linejoin='round' size='28' color='#000000' class='tabler-icon tabler-icon-user-square-rounded'><path d='M12 13a3 3 0 1 0 0 -6a3 3 0 0 0 0 6z'></path><path d='M12 3c7.2 0 9 1.8 9 9s-1.8 9 -9 9s-9 -1.8 -9 -9s1.8 -9 9 -9z'></path><path d='M6 20.05v-.05a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v.05'></path></svg></div><div><div class='lh-1'><span>" +
                            row_data.fullname +
                            "</span></div><div class='lh-1'><span class='fs-11 text-muted'>" +
                            row_data.email + "</span></div></div></div></a>";
                        return return_data;
                    }
                },
                {
                    data: 'amount',
                    name: 'amount',
                    title:'Amount'
                },
                {
                    data: 'payment_mode',
                    name: 'payment_mode',
                    title:'Payment Mode'
                },
                {
                    data: 'withdraw_date',
                    name: 'withdraw_date',
                    title:'Withdraw Date',
                    render: function(data, type, row) {
                        var dateTime = row.withdraw_date.split(' ');
                        var date = dateTime[0];
                        var time = dateTime[1];
                        var return_data = "<div class='d-grid'><div class='date'>" + date +
                            "</div><div class='time text-muted'>" + time + "</div></div>";
                        return return_data;
                    }
                },
                {
                    data: 'status',
                    name: 'status',
                    title:'Status'
                },
                {
                    data: 'action',
                    name: 'action',
                    title:'Action',
                    orderable: false,
                    searchable: false
                },
            ]
        });
        $('#tableTradingDeposit').DataTable({
            order: [[5, "desc"]],
            "ajax": {
                "url": "/admin/ajax",
                "type": "GET",
                data: function(d) {
                    d.action = 'getPendingTradingDeposit';
                    d.startdate = $('.tdStartDate').val();
                    d.enddate = $('.tdEndDate').val();
                }
            },
            order: [
                ['0', 'desc']
            ],
            columns: [{
                    data: 'id',
                    name: '#',
                    title:'ID',
                    render: function(data) {
                        var dd = data;
                        return "TWID" + dd.toString().padStart(4, 0);
                    }
                },
                {
                    data: 'account_no',
                    name: 'account_no',
                    title:'Trade ID',
                },
                {
                    data: 'amount',
                    name: 'amount',
                    title:'Amount'
                },
                {
                    data: 'adj_amount',
                    name: 'adj_amount',
                    title: 'Adj. Amount',
                    render: function(data, type, row) {
                        var return_data=(data==null)?'':'$'+data;
                        return return_data;
                    }
                },
                {
                    data: 'deposit_type',
                    name: 'deposit_type',
                    title:'Deposit Type',
                },
                {
                    data: 'deposit_from',
                    name: 'deposit_from',
                    title:'Deposit From',
                },
                {
                    data: 'deposit_date',
                    name: 'deposit_date',
                    title:'Deposit Date',
                    render: function(data, type, row) {
                        var dateTime = row.deposit_date.split(' ');
                        var date = dateTime[0];
                        var time = dateTime[1];
                        var return_data = "<div class='d-grid'><div class='date'>" + date +
                            "</div><div class='time text-muted'>" + time + "</div></div>";
                        return return_data;
                    }
                },
                {
                    data: 'status',
                    name: 'status',
                    title:'Status'
                },
                {
                    data: 'action',
                    name: 'action',
                    title:'Action',
                    orderable: false,
                    searchable: false
                },
            ]
        });
        $('#tableTradingWithdrawal').DataTable({
            order: [[5, "desc"]],
            "ajax": {
                "url": "/admin/ajax",
                "type": "GET",
                data: function(d) {
                    d.action = 'getPendingTradingWithdrawal';
                    d.startdate = $('.twStartDate').val();
                    d.enddate = $('.twEndDate').val();
                }
            },
            columns: [{
                    data: 'account_no',
                    name: 'account_no',
                    title:'Trade ID',
                },
                {
                    data: 'email',
                    name: 'email',
                    title: 'Email',
                    render: function(data, row, row_data) {
                        var return_data = "<a href='/admin/client_details?id=" + row_data
                            .enc_id +
                            "'><div class='d-flex align-items-center'><div class='me-2'><svg xmlns='http://www.w3.org/2000/svg' width='28' height='28' viewBox='0 0 24 24' fill='none' stroke='#000000' stroke-width='1.5' stroke-linecap='round' stroke-linejoin='round' size='28' color='#000000' class='tabler-icon tabler-icon-user-square-rounded'><path d='M12 13a3 3 0 1 0 0 -6a3 3 0 0 0 0 6z'></path><path d='M12 3c7.2 0 9 1.8 9 9s-1.8 9 -9 9s-9 -1.8 -9 -9s1.8 -9 9 -9z'></path><path d='M6 20.05v-.05a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v.05'></path></svg></div><div><div class='lh-1'><span>" +
                            row_data.fullname +
                            "</span></div><div class='lh-1'><span class='fs-11 text-muted'>" +
                            row_data.email + "</span></div></div></div></a>";
                        return return_data;
                    }
                },
                {
                    data: 'amount',
                    name: 'amount',
                    title:'Amount'
                },
                {
                    data: 'withdraw_type',
                    name: 'withdraw_type',
                    title:'Withdraw Type'
                },
                {
                    data: 'withdraw_to',
                    name: 'withdraw_from',
                    title:'Withdraw To'
                },
                {
                    data: 'withdraw_date',
                    name: 'withdraw_date',
                    title:'Withdraw Date',
                    render: function(data, type, row) {
                        var dateTime = row.withdraw_date.split(' ');
                        var date = dateTime[0];
                        var time = dateTime[1];
                        var return_data = "<div class='d-grid'><div class='date'>" + date +
                            "</div><div class='time text-muted'>" + time + "</div></div>";
                        return return_data;
                    }
                },
                {
                    data: 'status',
                    name: 'status',
                    title:'Status'
                },
                {
                    data: 'action',
                    name: 'action',
                    title:'Action',
                    orderable: false,
                    searchable: false
                },
            ]
        });

    });
    $(document).on("click", ".dtDateFilter", function() {
        $(this).parents('.tab-pane').find('.ajaxDataTable').DataTable().ajax.reload();
    });
</script>
