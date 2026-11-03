
@extends('layouts.frontend')
@section('title', $user->name . ' - Public Profile | Migratepay')

@section('content')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap');

    :root {
        --mp-navy: #0A1930;
        --mp-dark-text: #172B4D;
        --mp-blue: #0052FF;
        --mp-blue-hover: #0040CC;
        --mp-bg: #F4F7FE;
        --mp-white: #FFFFFF;
        --mp-text-primary: #334155;
        --mp-text-secondary: #64748B;
        --mp-border: #E2E8F0;
        --mp-green: #10B981;
        --mp-orange: #F59E0B;
        --mp-purple: #8B5CF6;
        --mp-shadow: 0 10px 30px -10px rgba(0,0,0,0.06);
        --mp-radius: 24px;
        --font-family: 'Plus Jakarta Sans', sans-serif;
    }
    
    body {
        background-color: var(--mp-bg);
        font-family: var(--font-family);
    }
    
    .profile-page-wrapper {
        padding: 200px 20px 80px;
        max-width: 1200px;
        margin: 0 auto;
    }
    
    /* Cover Banner */
    .hero-banner {
        width: 100%;
        height: 280px;
        border-radius: var(--mp-radius);
        position: relative;
        overflow: hidden;
        margin-bottom: 30px;
        box-shadow: var(--mp-shadow);
        background: linear-gradient(135deg, #1e3a8a, #0f172a);
    }
    
    .hero-bg-img {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
        z-index: 1;
    }
    
    .hero-overlay {
        position: absolute;
        inset: 0;
        background: linear-gradient(to right, rgba(10, 25, 48, 0.95) 0%, rgba(10, 25, 48, 0.7) 40%, rgba(10, 25, 48, 0.1) 100%);
        z-index: 2;
    }
    
    .hero-content {
        position: absolute;
        inset: 0;
        z-index: 3;
        display: flex;
        align-items: center;
        padding: 40px;
        gap: 30px;
    }
    
    .hero-avatar-box {
        width: 150px;
        height: 150px;
        border-radius: 28px;
        border: 5px solid var(--mp-white);
        background: var(--mp-white);
        overflow: hidden;
        flex-shrink: 0;
        position: relative;
    }
    .hero-avatar-box img { width: 100%; height: 100%; object-fit: cover; }
    .hero-avatar-default {
        width: 100%; height: 100%;
        background: linear-gradient(135deg, var(--mp-blue), var(--mp-purple));
        display: flex; align-items: center; justify-content: center;
        color: white; font-size: 50px; font-weight: 900;
    }
    
    .hero-info {
        flex: 1;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }
    
    .hero-name-row {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 12px;
    }
    .hero-name {
        font-size: 32px;
        font-weight: 800;
        color: var(--mp-white);
        margin: 0;
        letter-spacing: -0.5px;
    }
    .hero-blue-check {
        width: 24px; height: 24px;
        background: #0052FF;
        color: white;
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
    }
    
    .hero-badge-row {
        margin-bottom: 16px;
    }
    .hero-badge {
        display: inline-flex;
        background: #0052FF;
        color: white;
        font-size: 13px;
        font-weight: 600;
        padding: 6px 16px;
        border-radius: 100px;
        align-items: center;
    }
    .hero-badge.pending { background: rgba(245, 158, 11, 0.9); }
    .hero-badge.unverified { background: rgba(100, 116, 139, 0.9); }
    .hero-badge.rejected { background: rgba(239, 68, 68, 0.9); }
    
    .hero-meta-row {
        display: flex;
        align-items: center;
        gap: 24px;
        color: rgba(255, 255, 255, 0.9);
        font-size: 14px;
        font-weight: 500;
        margin-bottom: 12px;
    }
    .hero-meta-item {
        display: flex;
        align-items: center;
        gap: 8px;
    }
    
    .hero-bio {
        color: rgba(255, 255, 255, 0.8);
        font-size: 15px;
        margin: 0;
        max-width: 600px;
        line-height: 1.5;
    }
    
    .hero-actions {
        position: absolute;
        top: 30px;
        right: 30px;
        z-index: 4;
    }
    
    /* Stats Bar (4 independent cards) */
    .stats-bar {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 24px;
        margin-bottom: 30px;
    }
    
    .stat-card {
        background: var(--mp-white);
        border-radius: 20px;
        padding: 24px;
        display: flex;
        align-items: center;
        gap: 16px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.03);
        border: 1px solid var(--mp-border);
    }
    
    .stat-icon {
        width: 56px;
        height: 56px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    
    .stat-content {
        display: flex;
        flex-direction: column;
    }
    .stat-label {
        font-size: 12px;
        font-weight: 700;
        color: var(--mp-text-secondary);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 4px;
    }
    .stat-value {
        font-size: 26px;
        font-weight: 800;
        color: var(--mp-dark-text);
        line-height: 1;
        margin-bottom: 4px;
    }
    .stat-support {
        font-size: 12px;
        font-weight: 500;
        color: #94A3B8;
    }
    
    /* Main Content Grid */
    .main-content {
        display: grid;
        grid-template-columns: 320px 1fr;
        gap: 24px;
    }
    
    /* Sidebar / About Card */
    .about-card {
        background: var(--mp-white);
        border-radius: var(--mp-radius);
        padding: 30px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.03);
        border: 1px solid var(--mp-border);
    }
    
    .about-header {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 16px;
    }
    .about-header-icon {
        width: 32px;
        height: 32px;
        background: #F0F4FF;
        color: #0052FF;
        border-radius: 8px;
        display: flex; align-items: center; justify-content: center;
    }
    .about-header h3 {
        font-size: 18px;
        font-weight: 800;
        color: var(--mp-dark-text);
        margin: 0;
    }
    
    .about-bio-text {
        font-size: 14px;
        color: var(--mp-text-secondary);
        line-height: 1.6;
        margin-bottom: 30px;
    }
    
    .about-section {
        margin-bottom: 24px;
    }
    .about-section:last-child { margin-bottom: 0; }
    
    .about-s-title {
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 13px;
        font-weight: 700;
        color: var(--mp-dark-text);
        margin-bottom: 12px;
    }
    .about-s-icon {
        width: 24px; height: 24px;
        background: #F1F5F9;
        color: #64748B;
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
    }
    .about-s-content {
        padding-left: 34px;
        font-size: 14px;
        color: var(--mp-text-secondary);
    }
    
    /* Reviews Card */
    .reviews-card {
        background: var(--mp-white);
        border-radius: var(--mp-radius);
        padding: 30px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.03);
        border: 1px solid var(--mp-border);
        display: flex;
        flex-direction: column;
    }
    
    .reviews-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
        padding-bottom: 20px;
        border-bottom: 1px solid var(--mp-border);
        flex-wrap: wrap;
        gap: 16px;
    }
    
    .rh-left {
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .rh-icon {
        width: 32px; height: 32px;
        background: #E0F2FE;
        color: #0284C7;
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
    }
    .rh-left h3 {
        font-size: 20px;
        font-weight: 800;
        color: var(--mp-dark-text);
        margin: 0;
    }
    
    .rh-tabs {
        display: flex;
        align-items: center;
        gap: 4px;
    }
    .rtab {
        background: transparent;
        border: none;
        padding: 8px 20px;
        border-radius: 100px;
        font-size: 14px;
        font-weight: 600;
        color: var(--mp-text-secondary);
        cursor: pointer;
        transition: 0.2s;
    }
    .rtab:hover { color: var(--mp-dark-text); }
    .rtab.active {
        background: #0052FF;
        color: white;
        box-shadow: 0 4px 10px rgba(0, 82, 255, 0.2);
    }
    
    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 60px 20px;
    }
    .empty-icon {
        width: 80px; height: 80px;
        background: #F8FAFC;
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        margin: 0 auto 20px;
        color: #0052FF;
    }
    .empty-title {
        font-size: 20px; font-weight: 800; color: var(--mp-dark-text); margin-bottom: 8px;
    }
    .empty-desc {
        font-size: 15px; color: var(--mp-text-secondary); margin: 0;
    }
    
    /* Review Item */
    .review-item {
        padding: 24px 0;
        border-bottom: 1px solid var(--mp-border);
    }
    .review-item:last-child { border-bottom: none; padding-bottom: 0; }
    
    .ri-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 12px;
    }
    
    .ri-user {
        display: flex;
        align-items: center;
        gap: 16px;
    }
    
    .ri-avatar {
        width: 48px; height: 48px;
        border-radius: 50%;
        background: #F1F5F9;
        display: flex; align-items: center; justify-content: center;
        font-size: 16px; font-weight: 800; color: #475569;
        overflow: hidden;
    }
    
    .ri-name {
        font-size: 15px; font-weight: 800; color: var(--mp-dark-text); margin-bottom: 4px;
        display: flex; align-items: center; gap: 10px;
    }
    .ri-stars {
        display: flex; gap: 2px;
    }
    
    .ri-meta {
        text-align: right;
    }
    .ri-date {
        font-size: 13px; color: var(--mp-text-secondary); font-weight: 500; margin-bottom: 6px;
    }
    .ri-badge {
        display: inline-block;
        background: #F1F5F9; color: #475569;
        font-size: 12px; font-weight: 700;
        padding: 4px 12px; border-radius: 100px;
    }
    .ri-badge.buyer { background: #E0E7FF; color: #4338CA; }
    .ri-badge.seller { background: #FAE8FF; color: #A21CAF; }
    
    .ri-comment {
        font-size: 15px; color: var(--mp-text-secondary); line-height: 1.6; margin: 0;
    }

    @media (max-width: 1024px) {
        .main-content { grid-template-columns: 1fr; }
    }
    @media (max-width: 900px) {
        .stats-bar { grid-template-columns: repeat(2, 1fr); }
    }
    @media (max-width: 768px) {
        .hero-banner { height: auto; min-height: 340px; }
        .hero-content { flex-direction: column; text-align: center; padding: 30px; }
        .hero-avatar-box { width: 120px; height: 120px; margin: 0 auto; }
        .hero-name-row { justify-content: center; }
        .hero-meta-row { flex-direction: column; gap: 8px; align-items: center; }
        .stats-bar { grid-template-columns: 1fr; }
    }
</style>

<div class="profile-page-wrapper">
    <!-- Hero Banner Section -->
    <div class="hero-banner">
        @if($user->cover_photo)
            <img src="{{ asset($user->cover_photo) }}" alt="Cover" class="hero-bg-img" style="object-position: {{ $user->cover_position_x ?? 50 }}% {{ $user->cover_position_y ?? 50 }}%;">
        @else
            <!-- Default Scenic Background matching demo -->
            <img src="https://images.unsplash.com/photo-1464822759023-fed622ff2c3b?ixlib=rb-4.0.3&auto=format&fit=crop&w=1600&q=80" alt="Scenic Cover" class="hero-bg-img">
        @endif
        
        <div class="hero-overlay"></div>
        
        <div class="hero-content">
            <div class="hero-avatar-box">
                @if($user->profile_photo)
                    <img src="{{ asset($user->profile_photo) }}" alt="{{ $user->name }}" style="object-position: {{ $user->profile_position_x ?? 50 }}% {{ $user->profile_position_y ?? 50 }}%;">
                @else
                    <div class="hero-avatar-default">
                        {{ strtoupper(substr($user->name, 0, 2)) }}
                    </div>
                @endif
            </div>
            
            <div class="hero-info">
                <div class="hero-name-row">
                    <h1 class="hero-name">{{ $user->name }}</h1>
                    @php $vStatus = $user->verification_status ?? 'NOT_VERIFIED'; @endphp
                    @if($vStatus === 'VERIFIED')
                        <div class="hero-blue-check">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"></polyline></svg>
                        </div>
                    @endif
                </div>
                
                <div class="hero-badge-row">
                    @if($vStatus === 'VERIFIED')
                        <span class="hero-badge">Verified Member</span>
                    @elseif($vStatus === 'PENDING')
                        <span class="hero-badge pending">Verification Pending</span>
                    @elseif($vStatus === 'REJECTED')
                        <span class="hero-badge rejected">Verification Rejected</span>
                    @else
                        <span class="hero-badge unverified">Unverified Member</span>
                    @endif
                </div>
                
                <div class="hero-meta-row">
                    @if($user->location || $user->division || $user->district)
                        <div class="hero-meta-item">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                            {{ $user->location ?: collect([$user->district, $user->division])->filter()->join(', ') }}
                        </div>
                    @endif
                    <div class="hero-meta-item">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                        Joined {{ $user->created_at->format('M Y') }}
                    </div>
                </div>
                
                <p class="hero-bio">
                    {{ $user->bio ?: 'Verified member of the Migratepay community.' }}
                </p>
            </div>
        </div>

    </div>
    
    @php
        $reviews = $user->reviewsReceived;
        $avgRating = 0;
        if ($reviews->count() > 0) {
            $avgRating = round($reviews->avg(fn($r) => ($r->communication + $r->behavior + $r->delivery + $r->reliability) / 4), 1);
        }
    @endphp

    <!-- 4 Independent Statistic Cards -->
    <div class="stats-bar">
        <div class="stat-card">
            <div class="stat-icon" style="background: #E2FBF1; color: #10B981;">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
            </div>
            <div class="stat-content">
                <span class="stat-label">Deals Completed</span>
                <span class="stat-value">{{ $completedDealsCount }}</span>
                <span class="stat-support">{{ $completedDealsCount == 0 ? 'No completed deals yet' : 'Total successful escrows' }}</span>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon" style="background: #FEF3C7; color: #F59E0B;">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
            </div>
            <div class="stat-content">
                <span class="stat-label">Overall Rating</span>
                <span class="stat-value">{{ number_format($avgRating, 1) }}</span>
                <span class="stat-support">Based on {{ $reviews->count() }} reviews</span>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon" style="background: #E0F2FE; color: #0052FF;">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
            </div>
            <div class="stat-content">
                <span class="stat-label">Total Reviews</span>
                <span class="stat-value">{{ $reviews->count() }}</span>
                <span class="stat-support">{{ $reviews->count() == 0 ? 'No reviews yet' : 'Verified feedback' }}</span>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon" style="background: #F3E8FF; color: #8B5CF6;">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline></svg>
            </div>
            <div class="stat-content">
                <span class="stat-label">Response Rate</span>
                <span class="stat-value">N/A</span>
                <span class="stat-support">Not available</span>
            </div>
        </div>
    </div>

    <!-- Main Content Layout -->
    <div class="main-content">
        <!-- Sidebar / About Unified Card -->
        <div>
            <div class="about-card">
                <div class="about-header">
                    <div class="about-header-icon">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                    </div>
                    <h3>About / Professional Bio</h3>
                </div>
                <p class="about-bio-text">
                    {{ $user->bio ?: 'Verified member of the Migratepay community.' }}
                </p>
                
                <div class="about-section">
                    <div class="about-s-title">
                        <div class="about-s-icon"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"></path></svg></div>
                        Marketplace Skills
                    </div>
                    <div class="about-s-content">
                        @if($user->skills && count($user->skills) > 0)
                            {{ implode(', ', array_filter($user->skills)) }}
                        @else
                            Not specified
                        @endif
                    </div>
                </div>
                
                <div class="about-section">
                    <div class="about-s-title">
                        <div class="about-s-icon"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"></circle><line x1="2" y1="12" x2="22" y2="12"></line><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"></path></svg></div>
                        Languages
                    </div>
                    <div class="about-s-content">
                        @if($user->languages && count($user->languages) > 0 && array_filter($user->languages))
                            {{ implode(', ', array_filter($user->languages)) }}
                        @else
                            Not specified
                        @endif
                    </div>
                </div>
                
                <div class="about-section">
                    <div class="about-s-title">
                        <div class="about-s-icon"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M22 10v6M2 10l10-5 10 5-10 5z"></path><path d="M6 12v5c3 3 9 3 12 0v-5"></path></svg></div>
                        Education & Certifications
                    </div>
                    <div class="about-s-content">
                        @if($user->education && count($user->education) > 0 && array_filter($user->education))
                            <div style="margin-bottom:4px;">{{ implode(', ', array_filter($user->education)) }}</div>
                        @endif
                        @if($user->certifications && count((array)$user->certifications) > 0 && array_filter((array)$user->certifications))
                            <div>{{ implode(', ', array_filter((array)$user->certifications)) }}</div>
                        @endif
                        @if((!$user->education || count(array_filter($user->education)) == 0) && (!$user->certifications || count(array_filter((array)$user->certifications)) == 0))
                            Not specified
                        @endif
                    </div>
                </div>

                <div class="about-section">
                    <div class="about-s-title">
                        <div class="about-s-icon"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"></path><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"></path></svg></div>
                        Professional Links
                    </div>
                    <div class="about-s-content">
                        @if($user->professional_links && count($user->professional_links) > 0 && array_filter($user->professional_links))
                            @foreach(array_filter($user->professional_links) as $link)
                                <a href="{{ strpos(trim($link), 'http') === 0 ? trim($link) : 'https://'.trim($link) }}" target="_blank" style="color: #0052FF; text-decoration: none; display: block; margin-bottom: 4px;">{{ trim($link) }}</a>
                            @endforeach
                        @else
                            Not specified
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Reviews Card -->
        <div class="reviews-card">
            <div class="reviews-header">
                <div class="rh-left">
                    <div class="rh-icon">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
                    </div>
                    <h3>Reviews ({{ $reviews->count() }})</h3>
                </div>
                
                <div class="rh-tabs">
                    <button class="rtab active" data-target="all">All Reviews</button>
                    <button class="rtab" data-target="buyer">As Buyer</button>
                    <button class="rtab" data-target="seller">As Seller</button>
                </div>
            </div>

            <div id="reviewsList">
                @if($reviews->count() > 0)
                    @foreach($reviews as $review)
                        @php
                            $profileOwnerRole = $review->role === 'buyer' ? 'seller' : 'buyer';
                            $reviewerName = $review->reviewer->name ?? 'Unknown User';
                            $reviewerInitial = strtoupper(substr($reviewerName, 0, 1));
                            $overallStars = round(((float)($review->communication ?? 0) + (float)($review->behavior ?? 0) + (float)($review->delivery ?? 0) + (float)($review->reliability ?? 0)) / 4);
                        @endphp
                        <div class="review-item" data-role="{{ $profileOwnerRole }}">
                            <div class="ri-header">
                                <div class="ri-user">
                                    <div class="ri-avatar">
                                        @if($review->reviewer && $review->reviewer->profile_photo)
                                            <img src="{{ asset($review->reviewer->profile_photo) }}" alt="{{ $reviewerName }}" style="width:100%; height:100%; object-fit:cover;">
                                        @else
                                            {{ $reviewerInitial }}
                                        @endif
                                    </div>
                                    <div>
                                        <div class="ri-name">
                                            {{ $reviewerName }}
                                        </div>
                                        <div class="ri-stars">
                                            @for($i = 1; $i <= 5; $i++)
                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="{{ $i <= $overallStars ? '#F59E0B' : '#E2E8F0' }}" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
                                            @endfor
                                        </div>
                                    </div>
                                </div>
                                <div class="ri-meta">
                                    <div class="ri-date">{{ $review->created_at->format('M d, Y') }}</div>
                                    <div class="ri-badge {{ $profileOwnerRole }}">As {{ ucfirst($profileOwnerRole) }}</div>
                                </div>
                            </div>
                            <p class="ri-comment" style="padding-left: 64px;">
                                {{ $review->comment ?: 'No comment provided.' }}
                            </p>
                        </div>
                    @endforeach
                @endif
            </div>

            <!-- Empty States -->
            <div id="emptyStateAll" class="empty-state" style="display: {{ $reviews->count() == 0 ? 'block' : 'none' }};">
                <div class="empty-icon">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="currentColor"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
                </div>
                <h4 class="empty-title">No Reviews Yet</h4>
                <p class="empty-desc">Be the first to leave a review for this member.</p>
            </div>

            <div id="emptyStateBuyer" class="empty-state" style="display: none;">
                <div class="empty-icon">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="currentColor"><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"></path></svg>
                </div>
                <h4 class="empty-title">No Buyer Reviews</h4>
                <p class="empty-desc">No reviews received as a buyer yet.</p>
            </div>

            <div id="emptyStateSeller" class="empty-state" style="display: none;">
                <div class="empty-icon">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="currentColor"><path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                </div>
                <h4 class="empty-title">No Seller Reviews</h4>
                <p class="empty-desc">No reviews received as a seller yet.</p>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const tabs = document.querySelectorAll('.rtab');
    const reviews = document.querySelectorAll('.review-item');
    const emptyAll = document.getElementById('emptyStateAll');
    const emptyBuyer = document.getElementById('emptyStateBuyer');
    const emptySeller = document.getElementById('emptyStateSeller');

    tabs.forEach(tab => {
        tab.addEventListener('click', () => {
            tabs.forEach(t => t.classList.remove('active'));
            tab.classList.add('active');

            const target = tab.getAttribute('data-target');
            let visibleCount = 0;

            reviews.forEach(review => {
                if (target === 'all' || review.getAttribute('data-role') === target) {
                    review.style.display = 'block';
                    visibleCount++;
                } else {
                    review.style.display = 'none';
                }
            });

            emptyAll.style.display = 'none';
            emptyBuyer.style.display = 'none';
            emptySeller.style.display = 'none';

            if (visibleCount === 0) {
                if (target === 'all') emptyAll.style.display = 'block';
                if (target === 'buyer') emptyBuyer.style.display = 'block';
                if (target === 'seller') emptySeller.style.display = 'block';
            }
        });
    });
});
</script>
@endsection
