{{-- resources/views/activeview.blade.php --}}
@extends('layouts.app') {{-- Assuming you have a master layout --}}

@section('title', 'User Activity Details')



@section('content')
<div class="avl-detail-container">
    <!-- Header with Back Button -->
    <div class="avl-detail-header">
        <a href="{{ route('activity.logs') }}" class="avl-back-btn">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M19 12H5M12 19l-7-7 7-7"/>
            </svg>
            Back to Activity Logs
        </a>
    </div>

    <!-- User Profile Section -->
    <div class="avl-user-profile-card">
        <div class="avl-user-header">
            <div class="avl-user-avatar-large">
                {{ strtoupper(substr($user->name ?? $user->username, 0, 2)) }}
            </div>
            <div class="avl-user-info">
                <h1 class="avl-user-name">{{ $user->name ?? $user->username }}</h1>
                <p class="avl-user-email">{{ $user->email }}</p>
                <div class="avl-user-meta">
                    <span class="avl-meta-item">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                            <circle cx="12" cy="10" r="3"></circle>
                        </svg>
                        {{ $user->last_ip ?? 'N/A' }}
                    </span>
                    <span class="avl-meta-item">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"></circle>
                            <polyline points="12 6 12 12 16 14"></polyline>
                        </svg>
                        Last seen: {{ $user->last_seen ? $user->last_seen->diffForHumans() : 'N/A' }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter & Export Section -->
    <div class="avl-detail-filters">
        <div class="avl-filters-left">
            <form action="{{ route('activity.filter', $user->id) }}" method="GET" class="d-flex gap-3 flex-wrap">
                <div class="avl-filter-group">
                    <label class="avl-label">Action Type</label>
                    <select name="action_type" class="avl-select">
                        <option value="">All Actions</option>
                        @foreach(['Login','Logout','Create','Update','Delete','View'] as $action)
                            <option value="{{ $action }}" {{ request('action_type') == $action ? 'selected' : '' }}>
                                {{ $action }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="avl-filter-group">
                    <label class="avl-label">Date Range</label>
                    <div class="avl-date-range">
                        <input type="date" name="from" class="avl-input avl-input-sm" value="{{ request('from', date('Y-m-d')) }}">
                        <span class="avl-separator">to</span>
                        <input type="date" name="to" class="avl-input avl-input-sm" value="{{ request('to', date('Y-m-d')) }}">
                    </div>
                </div>

                <button type="submit" class="avl-btn avl-btn-secondary">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="1 4 1 10 7 10"></polyline>
                        <polyline points="23 20 23 14 17 14"></polyline>
                        <path d="M20.49 9A9 9 0 0 0 5.64 5.64L1 10m22 4l-4.64 4.36A9 9 0 0 1 3.51 15"></path>
                    </svg>
                    Reset
                </button>
            </form>
        </div>

        <a href="{{ route('activity.export', $user->id) }}" class="avl-btn avl-btn-success">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                <polyline points="7 10 12 15 17 10"></polyline>
                <line x1="12" y1="15" x2="12" y2="3"></line>
            </svg>
            Export CSV
        </a>
    </div>

    <!-- Activity Timeline -->
    <div class="avl-timeline-container">
        <div class="avl-timeline">
            @foreach($activities as $activity)
            <div class="avl-timeline-item">
                <div class="avl-timeline-marker avl-marker-{{ strtolower($activity->action) }}"></div>
                <div class="avl-timeline-content">
                    <div class="avl-activity-card">
                        <div class="avl-activity-header">
                            <span class="avl-badge avl-badge-{{ $activity->badge_color ?? 'blue' }}">
                                {{ $activity->action }}
                            </span>
                            <span class="avl-timestamp">{{ $activity->created_at->format('Y-m-d H:i:s') }} UTC</span>
                        </div>
                        <p class="avl-activity-description">{{ $activity->description }}</p>

                        @if(!empty($activity->details))
                        <div class="avl-activity-details">
                            <p class="avl-details-title">Details:</p>
                            @foreach($activity->details as $detail)
                                <div class="avl-detail-item {{ $detail['changed'] ?? '' }}">
                                    <span class="avl-detail-label">{{ $detail['label'] }}:</span>
                                    <span class="avl-detail-old">{{ $detail['old'] ?? '' }}</span>
                                    @if(isset($detail['new']))
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <polyline points="9 18 15 12 9 6"></polyline>
                                        </svg>
                                        <span class="avl-detail-new">{{ $detail['new'] }}</span>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                        @endif

                        <div class="avl-activity-meta">
                            <div class="avl-meta-row">
                                <span class="avl-meta-label">IP Address:</span>
                                <span class="avl-meta-value">{{ $activity->ip }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Load More -->
    <div class="avl-load-more">
        <button class="avl-btn avl-btn-load" id="loadMoreActivities">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="6 9 12 15 18 9"></polyline>
            </svg>
            Load More Activities
        </button>
    </div>
</div>
@endsection
