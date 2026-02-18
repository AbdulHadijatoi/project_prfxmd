@extends('layouts.crm.crm')

<style>
    .podium-container {
        display: flex;
        justify-content: center;
        align-items: flex-end;
    }
    .rank-card{
        width: 30%;
    }
    .ranks {
        display: flex;
        justify-content: center;
        top: 10%;
        margin-top: -68px;
    }
    .ranks img {
        width: 100px;
        height: 100px;
    }
    .rank-card:nth-child(1) {
        height: 200px;
    }
    .rank-card:nth-child(2) {
        height: 250px;
    }
    .rank-card:nth-child(3) {
        height: 150px;
    }
    .rank-list-name {
        text-align: center;
    }
    .rank-list-name h3 {
        color: #333333;
        font-weight: 700;
    }
    .ranks-first {
        display: flex;
        justify-content: center;
        top: 10%;
        margin-top: -90px;
        position: relative;
    }
    .first-prize {
        position: absolute;
        bottom: 85px;
        width:85px;
    }
</style>
@section('content')
    <div class="pc-container">
        <div class="pc-content">
            <div class="page-header">
                <div class="page-block">
                    <div class="row align-items-center">
                        <div class="col-md-12">
                            <div class="page-header-title h5">
                                <h2 class="mb-0">Leader Board</h2>
                            </div>
                        </div>
                    </div>
                </div>
            </div>



            <div class="podium-container" style="margin-top: 10em !important;">
                <div class="card rank-card mx-2">
                    <div class="ranks">
                        <img src="/assets/images/user.png" class="" alt="...">
                    </div>
                    <div class="card-body rank-list-name text-center d-flex flex-column align-items-center">
                        <h5 class="card-title">2nd Rank</h5>
                        <h3>{{ $leaderboard[1]->profit ?? '---' }}</h3>
                        <h5 class="card-text">{{ $leaderboard[1]->user['fullname'] ?? '---' }}</h5>
                    </div>
                </div>
                <div class="card rank-card mx-2">
                    <div class="ranks ranks-first">
                        <img src="./assets/images/crown.svg" class="first-prize" style="width: 60px;margin-bottom: -14px;">
                        <img src="/assets/images/user.png" class="" alt="...">
                    </div>
                    <div class="card-body rank-list-name-2 text-center d-flex flex-column align-items-center">
                        <h4 class="card-title">1st Rank</h4>
                        <h2>{{ $leaderboard[0]->profit ?? '---' }}</h2>
                        <h4 class="card-text">{{ $leaderboard[0]->user['fullname'] ?? '---' }}</h4>
                    </div>
                </div>
                <div class="card rank-card mx-2">
                    <div class="ranks">
                        <img src="/assets/images/user.png" class="" alt="...">
                    </div>
                    <div class="card-body rank-list-name-3 text-center d-flex flex-column align-items-center">
                        <h5 class="card-title">3rd Rank</h5>
                        <h3>{{ $leaderboard[2]->profit ?? '---' }}</h3>
                        <h5 class="card-text">{{ $leaderboard[2]->user['fullname'] ?? '---' }}</h5>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-12">
                            <div class="px-5 table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th scope="col">Rank</th>
                                            <th scope="col">Name</th>
                                            <th scope="col">Profit</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($leaderboard as $index => $scoreboard)
                                            <tr>
                                                <td>
                                                    {{ $index + 1 }}
                                                </td>
                                                <td>
                                                    <a href="#">
                                                        <div class='d-flex align-items-center'>
                                                            <div class='me-2'><svg xmlns='http://www.w3.org/2000/svg'
                                                                    width='28' height='28' viewBox='0 0 24 24'
                                                                    fill='none' stroke='#000000' stroke-width='1.5'
                                                                    stroke-linecap='round' stroke-linejoin='round'
                                                                    size='28' color='#000000'
                                                                    class='tabler-icon tabler-icon-user-square-rounded'>
                                                                    <path d='M12 13a3 3 0 1 0 0 -6a3 3 0 0 0 0 6z'></path>
                                                                    <path
                                                                        d='M12 3c7.2 0 9 1.8 9 9s-1.8 9 -9 9s-9 -1.8 -9 -9s1.8 -9 9 -9z'>
                                                                    </path>
                                                                    <path
                                                                        d='M6 20.05v-.05a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v.05'>
                                                                    </path>
                                                                </svg></div>
                                                            <div>
                                                                <div class='lh-1'>
                                                                    <span>{{ $scoreboard->user['fullname'] }}
                                                                    </span>
                                                                </div>
                                                                <div class='lh-1'><span
                                                                        class='fs-11 text-muted'>{{ $scoreboard->email }}</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </a>
                                                </td>
                                                <td>
                                                    {{ $scoreboard->profit }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
    </div>
@endsection
