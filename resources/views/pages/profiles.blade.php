@extends('layouts.frontend')
@section('title', 'Discover Trusted Professionals | Migratepay')

@section('content')
<style>
    :root {
        --mp-bg: #F3F6FC;
        --mp-white: #FFFFFF;
        --mp-text-primary: #17233D;
        --mp-text-secondary: #657186;
        --mp-border: #E2E8F2;
        --mp-blue: #3b82f6;
        --mp-purple: #8b5cf6;
        --mp-green: #10B981;
        --mp-orange: #F59E0B;
        --mp-cyan: #06b6d4;
        
        --mp-shadow-soft: 0 10px 25px -5px rgba(23, 35, 61, 0.05), 0 8px 10px -6px rgba(23, 35, 61, 0.01);
        --mp-shadow-hover: 0 20px 40px -10px rgba(59, 130, 246, 0.15), 0 10px 20px -15px rgba(139, 92, 246, 0.1);
        
        --mp-radius-lg: 24px;
        --mp-radius-md: 16px;
        --mp-transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
    }

    body {
        background-color: var(--mp-bg);
    }

    /* Animated Background */
    .mp-page-wrapper {
        position: relative;
        overflow: hidden;
        min-height: 100vh;
        padding-top: 120px;
        padding-bottom: 100px;
        z-index: 1;
    }
    
    .mp-bg-canvas {
        position: fixed;
        top: 0; left: 0; right: 0; bottom: 0;
        z-index: -1;
        pointer-events: none;
        overflow: hidden;
        background-color: var(--mp-bg);
    }
    
    .mp-bg-orb {
        position: absolute;
        border-radius: 50%;
        filter: blur(120px);
        opacity: 0.5;
        animation: floatOrb 25s ease-in-out infinite alternate;
    }
    .orb-1 {
        top: -10%; left: -10%;
        width: 60vw; height: 60vw;
        background: #EAF2FF; /* Soft blue glow */
        animation-delay: 0s;
    }
    .orb-2 {
        top: 20%; right: -20%;
        width: 50vw; height: 50vw;
        background: #F2EDFF; /* Soft lavender glow */
        animation-delay: -5s;
        animation-duration: 30s;
    }
    .orb-3 {
        bottom: -20%; left: 20%;
        width: 70vw; height: 70vw;
        background: #ECF9FB; /* Light cyan glow */
        animation-delay: -10s;
        animation-duration: 35s;
    }

    @keyframes floatOrb {
        0% { transform: translate(0, 0) scale(1); }
        33% { transform: translate(30px, -50px) scale(1.05); }
        66% { transform: translate(-20px, 20px) scale(0.95); }
        100% { transform: translate(40px, 40px) scale(1.1); }
    }

    /* Header Integration */
    /* Ensure the main nav sits nicely above the colored background */
    header.main-header {
        background: rgba(255, 255, 255, 0.95) !important;
        backdrop-filter: blur(20px) !important;
        border-bottom: 1px solid rgba(226, 232, 242, 0.8) !important;
        box-shadow: 0 4px 20px rgba(23, 35, 61, 0.03) !important;
    }

    /* Page Introduction */
    .mp-intro {
        text-align: center;
        max-width: 650px;
        margin: 0 auto 40px;
        padding: 0 20px;
        position: relative;
        z-index: 10;
    }
    .mp-eyebrow {
        display: inline-block;
        font-size: 11px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 2px;
        color: var(--mp-blue);
        background: rgba(59, 130, 246, 0.1);
        padding: 6px 12px;
        border-radius: 100px;
        margin-bottom: 16px;
    }
    .mp-title {
        font-size: 40px;
        font-weight: 900;
        color: var(--mp-text-primary);
        margin-bottom: 16px;
        line-height: 1.15;
        letter-spacing: -0.02em;
    }
    .mp-subtitle {
        font-size: 17px;
        color: var(--mp-text-secondary);
        line-height: 1.6;
        font-weight: 400;
    }

    /* Search Bar */
    .mp-search-wrap {
        max-width: 720px;
        margin: 0 auto 40px;
        padding: 0 20px;
        position: relative;
        z-index: 10;
    }
    .mp-search-box {
        display: flex;
        background: var(--mp-white);
        border-radius: 16px;
        padding: 8px 8px 8px 24px;
        box-shadow: var(--mp-shadow-soft);
        border: 2px solid transparent;
        transition: var(--mp-transition);
        align-items: center;
    }
    .mp-search-box:focus-within {
        border-color: rgba(139, 92, 246, 0.4); /* Purple/Blue accent */
        box-shadow: 0 0 0 6px rgba(139, 92, 246, 0.1), var(--mp-shadow-soft);
    }
    .mp-search-icon {
        color: #A0AABF;
        margin-right: 12px;
        transition: var(--mp-transition);
    }
    .mp-search-box:focus-within .mp-search-icon {
        color: var(--mp-purple);
    }
    .mp-search-input {
        flex: 1;
        border: none;
        outline: none;
        font-size: 16px;
        font-family: inherit;
        color: var(--mp-text-primary);
        background: transparent;
        font-weight: 500;
    }
    .mp-search-input::placeholder {
        color: #A0AABF;
        font-weight: 400;
    }
    .mp-search-btn {
        background: linear-gradient(135deg, var(--mp-blue), var(--mp-purple));
        color: white;
        border: none;
        padding: 14px 28px;
        border-radius: 12px;
        font-weight: 700;
        font-size: 15px;
        cursor: pointer;
        transition: var(--mp-transition);
    }
    .mp-search-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(139, 92, 246, 0.3);
    }

    /* Filters Bar */
    .mp-filters-bar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 40px;
        flex-wrap: wrap;
        gap: 16px;
        position: relative;
        z-index: 10;
    }
    .mp-results-count {
        font-weight: 800;
        color: var(--mp-text-primary);
        font-size: 16px;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .mp-results-count span {
        color: var(--mp-text-secondary);
        font-weight: 500;
    }
    
    .mp-filter-controls {
        display: flex;
        gap: 12px;
        align-items: center;
    }
    .mp-select {
        appearance: none;
        background: var(--mp-white) url('data:image/svg+xml;utf8,<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="%23657186" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9l6 6 6-6"/></svg>') no-repeat right 16px center;
        border: 1px solid var(--mp-border);
        padding: 10px 40px 10px 16px;
        border-radius: 12px;
        font-size: 14px;
        font-weight: 600;
        color: var(--mp-text-primary);
        cursor: pointer;
        outline: none;
        transition: var(--mp-transition);
        box-shadow: 0 2px 8px rgba(23,35,61,0.02);
    }
    .mp-select:hover, .mp-select:focus {
        border-color: var(--mp-blue);
        box-shadow: 0 4px 12px rgba(59,130,246,0.08);
        transform: translateY(-1px);
    }

    /* Profile Grid */
    .mp-grid {
        display: grid;
        grid-template-columns: repeat(1, 1fr);
        gap: 28px;
        position: relative;
        z-index: 10;
    }
    @media (min-width: 640px) { .mp-grid { grid-template-columns: repeat(2, 1fr); } }
    @media (min-width: 992px) { .mp-grid { grid-template-columns: repeat(3, 1fr); } }
    @media (min-width: 1280px){ .mp-grid { grid-template-columns: repeat(4, 1fr); } }

    /* Redesigned Profile Card */
    .mp-card {
        background: var(--mp-white);
        border-radius: var(--mp-radius-lg);
        box-shadow: var(--mp-shadow-soft);
        border: 1px solid var(--mp-border);
        transition: var(--mp-transition);
        position: relative;
        display: flex;
        flex-direction: column;
        animation: cardEnter 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        opacity: 0;
        transform: translateY(30px);
        overflow: hidden;
    }
    
    .mp-card:hover {
        transform: translateY(-8px);
        box-shadow: var(--mp-shadow-hover);
        border-color: rgba(139, 92, 246, 0.2);
    }
    
    /* Subtle glow around card on hover */
    .mp-card::after {
        content: '';
        position: absolute;
        inset: -1px;
        border-radius: var(--mp-radius-lg);
        background: linear-gradient(135deg, rgba(59,130,246,0.5), rgba(139,92,246,0.5));
        z-index: -1;
        opacity: 0;
        transition: var(--mp-transition);
    }
    .mp-card:hover::after {
        opacity: 0.15;
    }

    /* A. Colorful Header */
    .mp-card-header {
        height: 85px;
        position: relative;
        transition: var(--mp-transition);
    }
    /* Dynamic Header Gradients via inline style or classes */
    .gradient-1 { background: linear-gradient(135deg, #E0E7FF 0%, #C7D2FE 100%); }
    .gradient-2 { background: linear-gradient(135deg, #EDE9FE 0%, #DDD6FE 100%); }
    .gradient-3 { background: linear-gradient(135deg, #CCFBF1 0%, #99F6E4 100%); }
    .gradient-4 { background: linear-gradient(135deg, #DBEAFE 0%, #BFDBFE 100%); }
    .gradient-5 { background: linear-gradient(135deg, #FCE7F3 0%, #FBCFE8 100%); }

    .mp-card:hover .mp-card-header {
        filter: brightness(1.05) saturate(1.1);
    }

    /* Decorative header shapes */
    .mp-header-shape {
        position: absolute;
        background: rgba(255,255,255,0.2);
        border-radius: 50%;
        transition: var(--mp-transition);
    }
    .shape-1 { width: 60px; height: 60px; top: -20px; right: 20px; }
    .shape-2 { width: 40px; height: 40px; bottom: -10px; left: 40px; }
    .mp-card:hover .shape-1 { transform: scale(1.2) translate(-5px, 5px); }
    .mp-card:hover .shape-2 { transform: scale(1.5) translate(10px, -5px); }

    /* B. Profile Information */
    .mp-card-body {
        padding: 0 24px 20px;
        display: flex;
        flex-direction: column;
        flex-grow: 1;
        position: relative;
    }

    /* Avatar Overlapping Header */
    .mp-avatar-container {
        margin-top: -36px;
        margin-bottom: 12px;
        display: flex;
        justify-content: space-between;
        align-items: flex-end;
    }
    .mp-avatar-wrapper {
        position: relative;
        z-index: 2;
    }
    .mp-avatar {
        width: 72px;
        height: 72px;
        border-radius: 22px;
        background: linear-gradient(135deg, var(--mp-blue), var(--mp-purple));
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        font-weight: 800;
        color: var(--mp-white);
        border: 4px solid var(--mp-white);
        box-shadow: 0 6px 16px rgba(0,0,0,0.08);
        transition: var(--mp-transition);
        transform-origin: bottom left;
    }
    .mp-card:hover .mp-avatar {
        transform: scale(1.05);
        box-shadow: 0 8px 20px rgba(59,130,246,0.25);
    }
    .mp-verified-indicator {
        position: absolute;
        bottom: -2px;
        right: -2px;
        background: var(--mp-white);
        border-radius: 50%;
        padding: 2px;
        display: flex;
    }
    .mp-verified-indicator svg {
        background: var(--mp-blue);
        color: white;
        border-radius: 50%;
        padding: 3px;
        width: 22px;
        height: 22px;
    }

    /* Rating Pill */
    .mp-rating-pill {
        background: var(--mp-white);
        border: 1px solid var(--mp-border);
        color: var(--mp-text-primary);
        font-size: 13px;
        font-weight: 700;
        padding: 6px 10px;
        border-radius: 100px;
        display: flex;
        align-items: center;
        gap: 4px;
        box-shadow: 0 4px 10px rgba(0,0,0,0.03);
        margin-bottom: 8px; /* Alignment tweak */
    }
    .mp-rating-pill svg { color: var(--mp-orange); fill: var(--mp-orange); }
    .mp-rating-count { color: var(--mp-text-secondary); font-weight: 500; font-size: 12px; }

    /* Identity */
    .mp-name {
        font-size: 19px;
        font-weight: 800;
        color: var(--mp-text-primary);
        line-height: 1.2;
        margin-bottom: 4px;
        transition: var(--mp-transition);
    }
    .mp-card:hover .mp-name {
        color: var(--mp-blue);
    }
    
    .mp-title-text {
        font-size: 14px;
        font-weight: 600;
        color: var(--mp-text-secondary);
        margin-bottom: 12px;
        line-height: 1.4;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    /* Meta Info */
    .mp-meta-row {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        margin-bottom: 16px;
    }
    .mp-meta-item {
        display: flex;
        align-items: center;
        gap: 4px;
        font-size: 12px;
        color: var(--mp-text-secondary);
        font-weight: 500;
    }
    .mp-meta-item svg { color: #A0AABF; }

    /* C. Trust & Action Area */
    .mp-card-footer {
        padding: 16px 24px 24px;
        margin-top: auto;
    }
    
    .mp-action-btn {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        width: 100%;
        padding: 12px;
        background: linear-gradient(135deg, rgba(59,130,246,0.05), rgba(139,92,246,0.05));
        color: var(--mp-blue);
        border-radius: 12px;
        font-weight: 700;
        font-size: 14px;
        transition: var(--mp-transition);
        text-decoration: none;
    }
    .mp-action-btn svg { transition: var(--mp-transition); }
    
    .mp-card:hover .mp-action-btn {
        background: linear-gradient(135deg, var(--mp-blue), var(--mp-purple));
        color: var(--mp-white);
        box-shadow: 0 8px 16px rgba(139,92,246,0.25);
    }
    .mp-card:hover .mp-action-btn svg {
        transform: translateX(4px);
    }

    /* Animations */
    @keyframes cardEnter {
        0% { opacity: 0; transform: translateY(30px) scale(0.98); }
        100% { opacity: 1; transform: translateY(0) scale(1); }
    }
    
    /* Empty State */
    .mp-empty-state {
        grid-column: 1 / -1;
        text-align: center;
        padding: 80px 20px;
        background: var(--mp-white);
        border-radius: var(--mp-radius-lg);
        box-shadow: var(--mp-shadow-soft);
        border: 1px solid var(--mp-border);
    }
    .mp-empty-icon {
        width: 100px;
        height: 100px;
        background: linear-gradient(135deg, #EAF2FF, #F2EDFF);
        border-radius: 30px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 24px;
        color: var(--mp-purple);
        transform: rotate(-5deg);
    }
    .mp-empty-state h3 {
        font-size: 28px;
        font-weight: 900;
        color: var(--mp-text-primary);
        margin-bottom: 12px;
    }
    .mp-empty-state p {
        color: var(--mp-text-secondary);
        font-size: 16px;
        margin-bottom: 32px;
        max-width: 400px;
        margin-left: auto;
        margin-right: auto;
    }

</style>

<!-- Animated Background Layer -->
<div class="mp-bg-canvas">
    <div class="mp-bg-orb orb-1"></div>
    <div class="mp-bg-orb orb-2"></div>
    <div class="mp-bg-orb orb-3"></div>
</div>

<div class="mp-page-wrapper">
    <div class="container">
        <!-- Page Introduction -->
        <div class="mp-intro">
            <span class="mp-eyebrow">Migratepay Community</span>
            <h1 class="mp-title">Discover People You Can Trust</h1>
            <p class="mp-subtitle">Explore public profiles, discover skills and services, and find the right people for your next transaction.</p>
        </div>

        <!-- Search Bar -->
        <div class="mp-search-wrap">
            <form action="{{ route('profiles.index') }}" method="GET">
                <div class="mp-search-box">
                    <svg class="mp-search-icon" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                    <input type="text" name="search" class="mp-search-input" placeholder="Search users, services, skills or categories..." value="{{ request('search') }}">
                    <button type="submit" class="mp-search-btn">Search Community</button>
                </div>
                
                @if(request('location') || request('sort_by'))
                    <input type="hidden" name="location" value="{{ request('location') }}">
                    <input type="hidden" name="sort_by" value="{{ request('sort_by') }}">
                @endif
            </form>
        </div>

        <!-- Filters & Results Header -->
        <div class="mp-filters-bar">
            <div class="mp-results-count">
                {{ $profiles->total() }} <span>Public Profiles</span>
            </div>
            
            <form action="{{ route('profiles.index') }}" method="GET" class="mp-filter-controls" id="filter-form">
                @if(request('search'))
                    <input type="hidden" name="search" value="{{ request('search') }}">
                @endif
                
                <select name="sort_by" class="mp-select" onchange="document.getElementById('filter-form').submit()">
                    <option value="recommended" {{ request('sort_by') == 'recommended' ? 'selected' : '' }}>Sort By: Recommended</option>
                    <option value="newest" {{ request('sort_by') == 'newest' ? 'selected' : '' }}>Sort By: Newest</option>
                </select>
            </form>
        </div>

        <!-- Profile Grid -->
        <div class="mp-grid">
            @forelse($profiles as $index => $user)
                @php
                    // Dynamic styling & data calculation
                    $reviews = $user->reviewsReceived;
                    $avgRating = $reviews->count() > 0 ? round($reviews->avg('rating'), 1) : 0;
                    $initials = strtoupper(substr($user->name, 0, 2));
                    // Assign a random gradient class based on ID for visual variety
                    $gradIndex = ($user->id % 5) + 1;
                @endphp
                
                <div class="mp-card" style="animation-delay: {{ $index * 0.05 }}s;">
                    <!-- A. Colorful Header -->
                    <div class="mp-card-header gradient-{{ $gradIndex }}">
                        <div class="mp-header-shape shape-1"></div>
                        <div class="mp-header-shape shape-2"></div>
                    </div>
                    
                    <!-- B. Profile Information -->
                    <div class="mp-card-body">
                        <div class="mp-avatar-container">
                            <div class="mp-avatar-wrapper">
                                @if($user->profile_photo)
                                    <img src="{{ asset($user->profile_photo) }}" class="mp-avatar" style="object-fit: cover; object-position: {{ $user->profile_position_x ?? 50 }}% {{ $user->profile_position_y ?? 50 }}%;" alt="{{ $user->name }}">
                                @else
                                    <div class="mp-avatar">{{ $initials }}</div>
                                @endif
                                @if(($user->verification_status ?? 'NOT_VERIFIED') === 'VERIFIED')
                                    <div class="mp-verified-indicator" title="Verified Member">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                    </div>
                                @endif
                            </div>
                            
                            @if($avgRating > 0)
                                <div class="mp-rating-pill">
                                    <svg width="14" height="14" viewBox="0 0 24 24"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
                                    {{ number_format($avgRating, 1) }}
                                    <span class="mp-rating-count">({{ $reviews->count() }})</span>
                                </div>
                            @endif
                        </div>
                        
                        <!-- Identity -->
                        <h3 class="mp-name">{{ $user->name }}</h3>
                        @if($user->username)
                            <div style="font-size: 13px; color: var(--mp-blue); font-weight: 600; margin-bottom: 8px;">@{{ $user->username }}</div>
                        @endif
                        
                        <!-- Professional Title -->
                        <div class="mp-title-text">
                            @if($user->headline)
                                {{ $user->headline }}
                            @else
                                @if($user->role === 'seller') Professional Provider @else Verified Community Member @endif
                            @endif
                        </div>
                        
                        <!-- Meta Info -->
                        <div class="mp-meta-row">
                            @if($user->division || $user->district)
                                <div class="mp-meta-item">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                                    {{ collect([$user->district, $user->division])->filter()->join(', ') }}
                                </div>
                            @endif
                            <div class="mp-meta-item">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                                Member since {{ $user->created_at->format('M Y') }}
                            </div>
                        </div>
                    </div>
                    
                    <div class="mp-card-footer">
                        <a href="{{ route('profiles.show', $user->username ?? $user->user_id_string) }}" class="mp-action-btn">
                            View Profile
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                        </a>
                    </div>
                </div>
            @empty
                <!-- Empty State -->
                <div class="mp-empty-state">
                    <div class="mp-empty-icon">
                        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                    </div>
                    <h3>No Profiles Found</h3>
                    <p>Try changing your search terms or adjusting your filters to discover more people.</p>
                    <a href="{{ route('profiles.index') }}" class="mp-search-btn" style="text-decoration: none; display: inline-block;">Clear Filters</a>
                </div>
            @endforelse
        </div>
        
        <!-- Pagination -->
        @if($profiles->hasPages())
            <div style="margin-top: 50px; display: flex; justify-content: center;">
                {{ $profiles->links() }}
            </div>
        @endif

    </div>
</div>
@endsection
