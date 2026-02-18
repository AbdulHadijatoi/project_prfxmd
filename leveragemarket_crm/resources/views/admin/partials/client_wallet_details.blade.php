<div id="wallet-details-ajax-container">
    <div class="mb-3" id="wallet-search-form">
        @if(request('id'))
            <input type="hidden" name="id" value="{{ request('id') }}" id="wallet-search-id">
        @endif
        <div class="input-group">
            <input type="text" name="wallet_search" class="form-control wallet-search-input" placeholder="Search by name, currency, network or address..." value="{{ request('wallet_search') }}" autocomplete="off">
            <button type="button" class="btn btn-primary wallet-search-btn"><i class="ti ti-search"></i> Search</button>
            @if(request('wallet_search'))
                <a href="{{ request()->url() }}?id={{ request('id') }}" class="btn btn-outline-secondary wallet-details-ajax-link">Clear</a>
            @endif
        </div>
    </div>
    @if (isset($wallet_accounts) && $wallet_accounts->count() > 0)
        <table class="table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Wallet Name</th>
                    <th>Currency</th>
                    <th>Network</th>
                    <th>Address</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($wallet_accounts as $acc)
                    <tr>
                        <td>CWA{{ sprintf('%04u', $acc->client_wallet_id) }}</td>
                        <td>{{ $acc->wallet_name }}</td>
                        <td>{{ $acc->wallet_currency }}</td>
                        <td>{{ $acc->wallet_network }}</td>
                        <td>{{ $acc->wallet_address }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mt-2">
            <div class="text-muted small">
                Showing {{ $wallet_accounts->firstItem() ?? 0 }} to {{ $wallet_accounts->lastItem() ?? 0 }} of {{ $wallet_accounts->total() }} entries
            </div>
            <div class="wallet-details-pagination">{{ $wallet_accounts->links() }}</div>
        </div>
    @else
        <p class="text-muted mb-0">No wallet details.@if(request('wallet_search')) Try a different search.@endif</p>
    @endif
</div>
