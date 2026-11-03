@extends('layouts.frontend')
@section('title', 'Blog | Migratepay — Escrow Insights & Fintech News')
@section('content')

<style>
    /* ========================
       BLOG PAGE — DESIGN SYSTEM
    ======================== */
    .blog-hero {
        padding: 160px 0 70px;
        background: linear-gradient(135deg, #002147 0%, #004f3a 60%, #006A4E 100%);
        position: relative;
        overflow: hidden;
    }
    .blog-hero::before {
        content: '';
        position: absolute;
        top: -50%; right: -10%;
        width: 600px; height: 600px;
        background: radial-gradient(circle, rgba(0,106,78,0.25) 0%, transparent 70%);
        border-radius: 50%;
    }
    .blog-hero::after {
        content: '';
        position: absolute;
        bottom: -30%; left: -5%;
        width: 450px; height: 450px;
        background: radial-gradient(circle, rgba(255,255,255,0.04) 0%, transparent 70%);
        border-radius: 50%;
    }
    .blog-hero-inner { position: relative; z-index: 2; }
    .blog-hero-eyebrow {
        display: inline-flex; align-items: center; gap: 8px;
        background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.15);
        padding: 6px 16px; border-radius: 50px; margin-bottom: 24px;
        font-size: 11px; font-weight: 800; letter-spacing: 3px;
        text-transform: uppercase; color: #6ee7b7;
    }
    .blog-hero h1 {
        font-family: 'Lexend', sans-serif;
        font-size: clamp(36px, 5vw, 58px);
        font-weight: 900; color: #fff;
        line-height: 1.08; letter-spacing: -2px;
        margin-bottom: 20px;
    }
    .blog-hero h1 span { color: #6ee7b7; }
    .blog-hero p {
        font-size: 17px; color: rgba(255,255,255,0.7);
        max-width: 560px; line-height: 1.75;
    }

    /* Active category filter bar */
    .category-filter-bar {
        background: #fff;
        border-bottom: 1px solid #f1f5f9;
        position: sticky; top: 73px; z-index: 50;
        padding: 0;
        box-shadow: 0 2px 12px rgba(0,0,0,0.04);
    }
    .category-filter-scroll {
        display: flex; align-items: center; gap: 4px;
        padding: 14px 0; overflow-x: auto;
        scrollbar-width: none;
    }
    .category-filter-scroll::-webkit-scrollbar { display: none; }
    .filter-chip {
        flex-shrink: 0; padding: 7px 18px; border-radius: 50px;
        font-size: 11px; font-weight: 800; letter-spacing: 1px; text-transform: uppercase;
        cursor: pointer; border: 1px solid #e2e8f0;
        background: #fff; color: #64748b;
        transition: all 0.2s ease;
    }
    .filter-chip:hover { border-color: #006A4E; color: #006A4E; background: #f0fdf4; }
    .filter-chip.active { background: #006A4E; color: #fff; border-color: #006A4E; }

    /* ========================
       3-COLUMN LAYOUT
    ======================== */
    .blog-layout {
        display: grid;
        grid-template-columns: 260px 1fr 240px;
        gap: 40px;
        padding: 56px 0 80px;
        align-items: start;
    }
    @media (max-width: 1100px) {
        .blog-layout { grid-template-columns: 1fr 220px; }
        .blog-left-col { display: none; }
    }
    @media (max-width: 768px) {
        .blog-layout { grid-template-columns: 1fr; }
        .blog-right-col { display: none; }
    }

    /* ========================
       LEFT SIDEBAR — RECENT / MOST READ
    ======================== */
    .sidebar-card {
        background: #fff;
        border: 1px solid #f1f5f9;
        border-radius: 20px;
        overflow: hidden;
        margin-bottom: 24px;
        box-shadow: 0 2px 12px rgba(0,0,0,0.03);
    }
    .sidebar-header {
        display: flex; align-items: center; gap: 10px;
        padding: 18px 20px 14px;
        border-bottom: 1px solid #f1f5f9;
    }
    .sidebar-header-dot {
        width: 10px; height: 10px; border-radius: 50%;
        background: #006A4E; flex-shrink: 0;
    }
    .sidebar-header h3 {
        font-family: 'Lexend', sans-serif;
        font-size: 11px; font-weight: 900;
        letter-spacing: 2px; text-transform: uppercase;
        color: #002147;
    }
    .recent-post-item {
        display: flex; gap: 12px; align-items: flex-start;
        padding: 14px 20px;
        border-bottom: 1px solid #f8fafc;
        cursor: pointer;
        transition: background 0.2s;
    }
    .recent-post-item:last-child { border-bottom: none; }
    .recent-post-item:hover { background: #f8fafc; }
    .recent-post-num {
        font-size: 22px; font-weight: 900; font-family: 'Lexend', sans-serif;
        color: #e2e8f0; line-height: 1; flex-shrink: 0;
        width: 28px; text-align: center; padding-top: 2px;
    }
    .recent-post-info h4 {
        font-size: 12px; font-weight: 700; color: #002147;
        line-height: 1.45; margin-bottom: 5px;
        transition: color 0.2s;
    }
    .recent-post-item:hover .recent-post-info h4 { color: #006A4E; }
    .recent-post-meta {
        font-size: 10px; color: #94a3b8; font-weight: 600;
        text-transform: uppercase; letter-spacing: 0.5px;
    }

    /* Tag Cloud */
    .tag-cloud { padding: 16px 20px; display: flex; flex-wrap: wrap; gap: 8px; }
    .tag-pill {
        font-size: 10px; font-weight: 800; padding: 5px 12px;
        border-radius: 50px; text-transform: uppercase; letter-spacing: 0.5px;
        cursor: pointer; transition: all 0.2s;
        border: 1px solid #e2e8f0; color: #64748b; background: #fff;
    }
    .tag-pill:hover { background: #002147; color: #fff; border-color: #002147; }

    /* Newsletter widget */
    .newsletter-widget { padding: 20px; }
    .newsletter-widget p { font-size: 12px; color: #64748b; margin-bottom: 14px; line-height: 1.6; }
    .newsletter-input-wrap { display: flex; flex-direction: column; gap: 8px; }
    .newsletter-input-wrap input {
        width: 100%; padding: 10px 14px; border-radius: 10px;
        border: 1px solid #e2e8f0; font-size: 12px; outline: none;
        transition: border 0.2s;
    }
    .newsletter-input-wrap input:focus { border-color: #006A4E; }
    .newsletter-input-wrap button {
        width: 100%; padding: 10px; border-radius: 10px;
        background: #006A4E; color: #fff; font-size: 11px;
        font-weight: 800; letter-spacing: 1px; text-transform: uppercase;
        border: none; cursor: pointer; transition: background 0.2s;
    }
    .newsletter-input-wrap button:hover { background: #004f3a; }

    /* ========================
       CENTER — BLOG GRID
    ======================== */
    .blog-section-label {
        display: flex; align-items: center; gap: 14px;
        margin-bottom: 28px;
    }
    .blog-section-label h2 {
        font-family: 'Lexend', sans-serif;
        font-size: 13px; font-weight: 900; letter-spacing: 3px;
        text-transform: uppercase; color: #002147;
        border-left: 3px solid #006A4E; padding-left: 12px;
    }
    .blog-section-label .post-count {
        font-size: 10px; font-weight: 800; color: #94a3b8;
        background: #f8fafc; border: 1px solid #f1f5f9;
        padding: 3px 10px; border-radius: 50px;
    }

    .blog-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 24px;
    }
    @media (max-width: 640px) { .blog-grid { grid-template-columns: 1fr; } }

    /* Post Card */
    .post-card {
        background: #fff;
        border: 1px solid #f1f5f9;
        border-radius: 20px;
        overflow: hidden;
        transition: all 0.3s cubic-bezier(0.165, 0.84, 0.44, 1);
        cursor: pointer;
        display: flex; flex-direction: column;
    }
    .post-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 24px 48px -12px rgba(0,33,71,0.1);
        border-color: #006A4E;
    }
    .post-thumbnail {
        height: 185px;
        position: relative;
        overflow: hidden;
        flex-shrink: 0;
    }
    .post-thumbnail-inner {
        width: 100%; height: 100%;
        display: flex; align-items: center; justify-content: center;
        font-family: 'Lexend', sans-serif;
        font-size: 56px; font-weight: 900;
        transition: transform 0.4s ease;
    }
    .post-card:hover .post-thumbnail-inner { transform: scale(1.06); }
    .post-cat-badge {
        position: absolute; bottom: 12px; left: 12px;
        font-size: 9px; font-weight: 900; letter-spacing: 1.5px;
        text-transform: uppercase; padding: 4px 12px; border-radius: 50px;
        background: rgba(255,255,255,0.95);
        backdrop-filter: blur(8px);
        color: #002147;
    }
    .post-card-body { padding: 20px; flex: 1; display: flex; flex-direction: column; }
    .post-card-body h3 {
        font-family: 'Lexend', sans-serif;
        font-size: 14px; font-weight: 800;
        color: #002147; line-height: 1.45;
        margin-bottom: 10px;
        transition: color 0.2s;
    }
    .post-card:hover .post-card-body h3 { color: #006A4E; }
    .post-card-body p {
        font-size: 12px; color: #64748b; line-height: 1.7;
        margin-bottom: 16px; flex: 1;
    }
    .post-card-footer {
        display: flex; align-items: center; justify-content: space-between;
        padding-top: 14px; border-top: 1px solid #f1f5f9;
    }
    .post-author-info { display: flex; align-items: center; gap: 8px; }
    .post-author-avatar {
        width: 26px; height: 26px; border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-size: 10px; font-weight: 900; color: #fff;
        flex-shrink: 0;
    }
    .post-author-name { font-size: 10px; font-weight: 700; color: #002147; }
    .post-date { font-size: 9px; color: #94a3b8; font-weight: 600; text-transform: uppercase; }
    .post-read-link {
        font-size: 10px; font-weight: 800; color: #006A4E;
        letter-spacing: 0.5px; text-transform: uppercase;
        display: flex; align-items: center; gap: 4px;
        transition: gap 0.2s;
    }
    .post-card:hover .post-read-link { gap: 8px; }

    /* ========================
       PAGINATION
    ======================== */
    .blog-pagination {
        display: flex; align-items: center; justify-content: center;
        gap: 6px; margin-top: 48px; padding-top: 32px;
        border-top: 1px solid #f1f5f9;
    }
    .page-btn {
        min-width: 38px; height: 38px; border-radius: 10px;
        border: 1px solid #e2e8f0; background: #fff;
        font-size: 12px; font-weight: 800; color: #64748b;
        cursor: pointer; display: flex; align-items: center; justify-content: center;
        transition: all 0.2s; padding: 0 12px;
    }
    .page-btn:hover { border-color: #006A4E; color: #006A4E; background: #f0fdf4; }
    .page-btn.active { background: #006A4E; color: #fff; border-color: #006A4E; }
    .page-btn:disabled { opacity: 0.3; cursor: not-allowed; }
    .page-btn.dots { cursor: default; border-color: transparent; background: transparent; }
    .page-btn.dots:hover { color: #64748b; background: transparent; border-color: transparent; }

    /* ========================
       RIGHT SIDEBAR — CATEGORIES
    ======================== */
    .category-list { padding: 8px 0; }
    .category-item {
        display: flex; align-items: center; justify-content: space-between;
        padding: 11px 20px; cursor: pointer;
        border-left: 3px solid transparent;
        transition: all 0.2s;
    }
    .category-item:hover { border-left-color: #006A4E; background: #f8fafc; }
    .category-item.active { border-left-color: #006A4E; background: #f0fdf4; }
    .category-item-name {
        display: flex; align-items: center; gap: 10px;
        font-size: 12px; font-weight: 700; color: #002147;
        transition: color 0.2s;
    }
    .category-item:hover .category-item-name,
    .category-item.active .category-item-name { color: #006A4E; }
    .category-icon {
        width: 28px; height: 28px; border-radius: 8px;
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0;
    }
    .category-icon svg { width: 14px; height: 14px; }
    .category-count {
        font-size: 10px; font-weight: 800; color: #94a3b8;
        background: #f8fafc; border: 1px solid #f1f5f9;
        padding: 2px 8px; border-radius: 50px;
        min-width: 28px; text-align: center;
    }
    .category-item.active .category-count { background: #dcfce7; color: #006A4E; border-color: #bbf7d0; }

    /* Social links */
    .social-links { padding: 16px 20px; display: flex; gap: 10px; flex-wrap: wrap; }
    .social-link {
        width: 36px; height: 36px; border-radius: 10px;
        background: #f8fafc; border: 1px solid #f1f5f9;
        display: flex; align-items: center; justify-content: center;
        cursor: pointer; transition: all 0.2s;
    }
    .social-link:hover { background: #002147; border-color: #002147; }
    .social-link:hover svg { stroke: #fff; }
    .social-link svg { width: 15px; height: 15px; stroke: #002147; }

    /* No posts message */
    .no-posts {
        grid-column: 1 / -1;
        text-align: center; padding: 60px 20px;
        color: #94a3b8;
    }
    .no-posts svg { width: 48px; height: 48px; margin: 0 auto 16px; opacity: 0.3; }
    .no-posts h3 { font-size: 18px; font-weight: 800; color: #002147; margin-bottom: 8px; }
    .no-posts p { font-size: 14px; }

    /* ========================
       SINGLE POST — NEWSPAPER STYLE
    ======================== */
    .newspaper-header {
        background: #fff;
        border-bottom: 3px solid #000;
        padding: 40px 0 0;
        margin-top: 80px;
    }
    .newspaper-top-bar {
        display: flex; justify-content: space-between; align-items: center;
        padding: 10px 0; border-bottom: 1px solid #f1f1f1;
        font-size: 12px; font-weight: 700; color: #444;
    }
    .newspaper-logo-area {
        display: flex; justify-content: space-between; align-items: center;
        padding: 30px 0;
    }
    .newspaper-logo {
        font-family: 'Lexend', sans-serif;
        font-size: 42px; font-weight: 900; color: #000;
        letter-spacing: -2px; line-height: 1;
        display: flex; align-items: center; gap: 5px;
    }
    .newspaper-logo span { color: #d0021b; }
    .newspaper-banner-ad {
        width: 728px; height: 90px;
        background: #f1f1f1; border: 1px solid #ddd;
        display: flex; align-items: center; justify-content: center;
        font-size: 12px; font-weight: 800; color: #999; text-transform: uppercase;
    }
    .newspaper-nav {
        border-top: 1px solid #000;
        display: flex; gap: 20px; padding: 12px 0;
        font-size: 13px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px;
    }
    .newspaper-nav a { color: #000; transition: color 0.2s; }
    .newspaper-nav a:hover { color: #d0021b; }

    .post-single-container { padding: 40px 0 80px; background: #fff; }
    .post-single-grid {
        display: grid; grid-template-columns: 1fr 340px; gap: 40px;
    }
    @media (max-width: 1024px) { .post-single-grid { grid-template-columns: 1fr; } }

    .post-main-content { overflow: hidden; }
    .post-breadcrumbs {
        font-size: 11px; font-weight: 700; color: #999;
        text-transform: uppercase; letter-spacing: 1px;
        margin-bottom: 25px;
        display: flex; align-items: center; gap: 8px;
    }
    .post-breadcrumbs a { color: #444; }
    .post-breadcrumbs .separator { font-size: 8px; }

    .post-header { margin-bottom: 30px; }
    .post-header-cat {
        display: inline-block; background: #d0021b; color: #fff;
        font-size: 10px; font-weight: 800; padding: 4px 10px;
        text-transform: uppercase; letter-spacing: 2px;
        margin-bottom: 15px;
    }
    .post-header h1 {
        font-size: 46px; font-weight: 800; color: #000;
        line-height: 1.1; letter-spacing: -1.5px;
        margin-bottom: 20px;
    }
    .post-meta-detailed {
        display: flex; align-items: center; gap: 15px;
        font-size: 12px; font-weight: 700; color: #777;
        padding-bottom: 20px; border-bottom: 1px solid #f1f1f1;
        margin-bottom: 20px;
    }
    .post-meta-detailed img { width: 24px; height: 24px; border-radius: 50%; object-fit: cover; }
    .post-meta-detailed .author-name { color: #000; }
    .social-share-bar {
        display: flex; gap: 8px; margin-bottom: 30px;
    }
    .share-btn {
        display: flex; align-items: center; gap: 8px;
        padding: 8px 16px; font-size: 11px; font-weight: 800;
        color: #fff; text-transform: uppercase; letter-spacing: 1px;
        cursor: pointer; border: none; transition: opacity 0.2s;
    }
    .share-btn.fb { background: #3b5998; }
    .share-btn.tw { background: #1da1f2; }
    .share-btn.pin { background: #bd081c; }
    .share-btn.wa { background: #25d366; }
    .share-btn:hover { opacity: 0.9; }

    .post-featured-image {
        width: 100%; border: 1px solid #f1f1f1;
        margin-bottom: 35px;
    }
    .post-featured-image img { width: 100%; display: block; }

    .post-body-text {
        font-size: 18px; line-height: 1.85; color: #222;
        letter-spacing: -0.01em;
    }
    .post-body-text p { margin-bottom: 25px; }
    .post-body-text h2 { font-size: 28px; font-weight: 800; margin: 40px 0 20px; color: #000; }
    .post-body-text blockquote {
        margin: 40px 0; padding: 30px 40px;
        border-left: 5px solid #d0021b;
        background: #f9f9f9;
        font-size: 24px; font-style: italic; font-weight: 600;
        color: #000; line-height: 1.4;
    }
    
    .post-body-text a {
        color: #006A4E;
        text-decoration: underline;
        font-weight: 600;
        transition: color 0.2s;
    }
    .post-body-text a:hover {
        color: #004f3a;
    }

    .post-footer-tags {
        margin-top: 50px; padding: 30px 0;
        border-top: 1px solid #f1f1f1; border-bottom: 1px solid #f1f1f1;
        display: flex; align-items: center; gap: 15px;
    }
    .post-footer-tags span { font-size: 12px; font-weight: 800; text-transform: uppercase; color: #000; }
    .post-tag {
        font-size: 11px; font-weight: 700; color: #666;
        padding: 5px 12px; border: 1px solid #eee;
    }

    .author-bio-card {
        margin-top: 40px; background: #f9f9f9;
        padding: 40px; display: flex; gap: 30px; align-items: flex-start;
    }
    .author-bio-avatar { width: 100px; height: 100px; border-radius: 50%; overflow: hidden; flex-shrink: 0; }
    .author-bio-avatar img { width: 100%; height: 100%; object-fit: cover; }
    .author-bio-info h3 { font-size: 18px; font-weight: 800; margin-bottom: 10px; color: #000; }
    .author-bio-info p { font-size: 14px; color: #555; line-height: 1.6; margin-bottom: 15px; }
    .author-socials { display: flex; gap: 12px; }
    .author-socials a { color: #999; transition: color 0.2s; }
    .author-socials a:hover { color: #000; }

    .related-articles { margin-top: 60px; }
    .related-articles-header {
        display: flex; align-items: center; justify-content: space-between;
        margin-bottom: 25px; border-bottom: 2px solid #000;
    }
    .related-articles-header h3 {
        background: #000; color: #fff;
        font-size: 13px; font-weight: 800; text-transform: uppercase;
        padding: 8px 15px; letter-spacing: 1px;
    }
    .related-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; }
    @media (max-width: 640px) { .related-grid { grid-template-columns: 1fr; } }
    .related-item h4 { font-size: 14px; font-weight: 800; margin-top: 15px; line-height: 1.45; }

    /* SINGLE POST SIDEBAR */
    .sidebar-widget { margin-bottom: 50px; }
    .widget-title {
        border-bottom: 2px solid #000; margin-bottom: 20px;
    }
    .widget-title h3 {
        display: inline-block; background: #000; color: #fff;
        font-size: 11px; font-weight: 800; text-transform: uppercase;
        padding: 6px 12px; letter-spacing: 1px;
    }
    .popular-post-item {
        display: flex; gap: 15px; margin-bottom: 20px; align-items: center;
    }
    .popular-post-thumb { width:100px; height:70px; background:#eee; flex-shrink:0; overflow:hidden; }
    .popular-post-thumb img { width:100%; height:100%; object-fit:cover; }
    .popular-post-info h4 { font-size: 13px; font-weight: 800; line-height: 1.4; color:#000; }
    .popular-post-date { font-size: 10px; color:#999; font-weight:700; margin-top:5px; }

    /* COMMENTS SECTION */
    .comments-section { margin-top: 60px; }
    .comments-header { border-bottom: 2px solid #000; margin-bottom: 30px; }
    .comments-header h3 { font-size: 13px; font-weight: 800; text-transform: uppercase; padding: 8px 0; }
    .comment-item { padding: 30px 0; border-bottom: 1px solid #f1f1f1; display: flex; gap: 20px; }
    .comment-avatar { width: 50px; height: 50px; border-radius: 50%; background: #eee; flex-shrink: 0; }
    .comment-info h4 { font-size: 14px; font-weight: 800; margin-bottom: 5px; }
    .comment-date { font-size: 11px; color: #999; margin-bottom: 10px; font-weight: 700; }
    .comment-text { font-size: 14px; color: #444; line-height: 1.6; }

    .comment-form-title { border-bottom: 2px solid #000; margin: 50px 0 30px; }
    .comment-form-title h3 { font-size: 13px; font-weight: 800; text-transform: uppercase; padding: 8px 0; }
    .comment-form textarea { width: 100%; padding: 20px; border: 1px solid #ddd; height: 150px; font-size: 14px; margin-bottom: 20px; outline: none; }
    .comment-submit { background: #000; color: #fff; padding: 12px 30px; font-size: 12px; font-weight: 800; text-transform: uppercase; border: none; cursor: pointer; }

    .hidden { display: none !important; }
</style>

{{-- ===== ARCHIVE VIEW ===== --}}
<div id="archiveView">
    {{-- ===== HERO ===== --}}
    <section class="blog-hero">
        <div class="container blog-hero-inner">
            <div class="blog-hero-eyebrow">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="M12 2L3 7v10l9 5 9-5V7l-9-5z"/></svg>
                The Escrow Archive
            </div>
            <h1>Insights on <span>Digital Trust</span><br>& Secure Commerce</h1>
            <p>Expert analysis on fintech trends, Bangladesh's digital marketplace, escrow security, and how to protect every deal you make online.</p>
        </div>
    </section>

    {{-- ===== CATEGORY FILTER BAR ===== --}}
    <div class="category-filter-bar">
        <div class="container">
            <div class="category-filter-scroll" id="filterChips">
                <button class="filter-chip active" data-cat="all">All Posts</button>
                <button class="filter-chip" data-cat="Security">Security</button>
                <button class="filter-chip" data-cat="Fintech">Fintech</button>
                <button class="filter-chip" data-cat="Usage">How-To</button>
                <button class="filter-chip" data-cat="Compliance">Compliance</button>
                <button class="filter-chip" data-cat="Updates">Updates</button>
                <button class="filter-chip" data-cat="Awareness">Awareness</button>
                <button class="filter-chip" data-cat="Guides">Guides</button>
                <button class="filter-chip" data-cat="Legal">Legal</button>
            </div>
        </div>
    </div>

    {{-- ===== MAIN CONTENT ===== --}}
    <div class="container">
        <div class="blog-layout">

            {{-- LEFT SIDEBAR --}}
            <aside class="blog-left-col">
                {{-- Most Read --}}
                <div class="sidebar-card">
                    <div class="sidebar-header">
                        <div class="sidebar-header-dot"></div>
                        <h3>Most Read</h3>
                    </div>
                    <div id="mostReadList"></div>
                </div>

                {{-- Recent Posts --}}
                <div class="sidebar-card">
                    <div class="sidebar-header">
                        <div class="sidebar-header-dot" style="background:#002147"></div>
                        <h3>Recent Posts</h3>
                    </div>
                    <div id="recentPostList"></div>
                </div>

                {{-- Tags --}}
                <div class="sidebar-card">
                    <div class="sidebar-header">
                        <div class="sidebar-header-dot" style="background:#f59e0b"></div>
                        <h3>Tags</h3>
                    </div>
                    <div class="tag-cloud">
                        <span class="tag-pill">Escrow</span>
                        <span class="tag-pill">Bangladesh</span>
                        <span class="tag-pill">Fintech</span>
                        <span class="tag-pill">bKash</span>
                        <span class="tag-pill">Nagad</span>
                        <span class="tag-pill">Fraud</span>
                        <span class="tag-pill">Digital</span>
                        <span class="tag-pill">Payment</span>
                        <span class="tag-pill">Security</span>
                        <span class="tag-pill">Freelance</span>
                        <span class="tag-pill">F-Commerce</span>
                        <span class="tag-pill">Privacy</span>
                        <span class="tag-pill">KYC</span>
                        <span class="tag-pill">Compliance</span>
                    </div>
                </div>
            </aside>

            {{-- CENTER — BLOG POSTS --}}
            <main class="blog-center-col">
                <div class="blog-section-label">
                    <h2 id="gridLabel">Latest Posts</h2>
                    <span class="post-count" id="postCountLabel">Loading...</span>
                </div>
                <div class="blog-grid" id="blogGrid">
                    {{-- Populated by JS --}}
                </div>
                <div class="blog-pagination" id="paginationWrap"></div>
            </main>

            {{-- RIGHT SIDEBAR --}}
            <aside class="blog-right-col">
                {{-- Categories --}}
                <div class="sidebar-card">
                    <div class="sidebar-header">
                        <div class="sidebar-header-dot"></div>
                        <h3>Categories</h3>
                    </div>
                    <div class="category-list" id="categoryList"></div>
                </div>

                {{-- Social / Stay Connected --}}
                <div class="sidebar-card">
                    <div class="sidebar-header">
                        <div class="sidebar-header-dot" style="background:#3b82f6"></div>
                        <h3>Stay Connected</h3>
                    </div>
                    <div class="social-links">
                        <div class="social-link" title="Facebook">
                            <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 2h-3a5 5 0 00-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 011-1h3z"/></svg>
                        </div>
                        <div class="social-link" title="Twitter">
                            <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M23 3a10.9 10.9 0 01-3.14 1.53 4.48 4.48 0 00-7.86 3v1A10.66 10.66 0 013 4s-4 9 5 13a11.64 11.64 0 01-7 2c9 5 20 0 20-11.5a4.5 4.5 0 00-.08-.83A7.72 7.72 0 0023 3z"/></svg>
                        </div>
                    </div>
                </div>
            </aside>

        </div>{{-- /blog-layout --}}
    </div>{{-- /container --}}
</div>{{-- /archiveView --}}

{{-- ===== SINGLE POST VIEW ===== --}}
<div id="singlePostView" class="hidden" style="margin-top: 100px;">
    <section class="post-single-container">
        <div class="container">
            <div class="post-single-grid" id="postSingleContent">
                {{-- Populated by JS --}}
            </div>
        </div>
    </section>
</div>

<script>
@verbatim
// ========================
// BLOG DATA
// ========================
// Base static posts
let ALL_POSTS = [
    {
        id: 1, cat: 'Security',
        title: '10 Ways to Avoid F-Commerce Scams in Bangladesh',
        excerpt: 'Online sellers are evolving their fraud tactics faster than ever. Here is the definitive guide to protecting yourself and your business in 2026.',
        author: 'Asif Rahman', authorInitials: 'AR', authorColor: '#006A4E',
        date: 'March 20, 2026', readTime: '6 min', reads: 4821,
        gradient: 'linear-gradient(135deg, #002147 0%, #006A4E 100%)', letter: '🛡',
    },
    {
        id: 2, cat: 'Fintech',
        title: 'The Future of Freelance Payments in Bangladesh 2026',
        excerpt: 'With international platforms restricting Bangladeshi accounts, local fintech escrow solutions are becoming the lifeline for remote workers.',
        author: 'Nusrat Jahan', authorInitials: 'NJ', authorColor: '#7c3aed',
        date: 'March 18, 2026', readTime: '5 min', reads: 3240,
        gradient: 'linear-gradient(135deg, #1e1b4b 0%, #7c3aed 100%)', letter: '💸',
    },
    {
        id: 3, cat: 'Usage',
        title: 'How to Use Migratepay for Large Vehicle Sales',
        excerpt: 'Buying and selling cars online in Bangladesh carries massive risk. Step-by-step: how to structure your escrow deal to stay fully protected.',
        author: 'Rafiqul Islam', authorInitials: 'RI', authorColor: '#d97706',
        date: 'March 15, 2026', readTime: '7 min', reads: 2910,
        gradient: 'linear-gradient(135deg, #78350f 0%, #d97706 100%)', letter: '🚗',
    },
    {
        id: 4, cat: 'Security',
        title: 'Why Bank-Grade Escrow is the Gold Standard for Digital Trade',
        excerpt: 'Not all escrow services are created equal. We break down what "bank-grade" actually means and why it matters for your transaction safety.',
        author: 'Asif Rahman', authorInitials: 'AR', authorColor: '#006A4E',
        date: 'March 12, 2026', readTime: '8 min', reads: 5102,
        gradient: 'linear-gradient(135deg, #0f172a 0%, #006A4E 100%)', letter: '🏦',
    },
    {
        id: 5, cat: 'Compliance',
        title: 'Regulatory Updates for BD Digital Merchants 2026',
        excerpt: 'The Bangladesh Bank has issued new guidelines for digital payment operators. Here is everything merchants need to know to stay compliant.',
        author: 'Sadia Akter', authorInitials: 'SA', authorColor: '#dc2626',
        date: 'March 10, 2026', readTime: '4 min', reads: 1875,
        gradient: 'linear-gradient(135deg, #450a0a 0%, #dc2626 100%)', letter: '📋',
    },
    {
        id: 6, cat: 'Updates',
        title: 'New Payment Gateways: Nagad & bKash Integration Update',
        excerpt: 'Migratepay now supports instant settlement via Nagad and bKash. Here is a full breakdown of the new supported payment rails.',
        author: 'Dev Team', authorInitials: 'DT', authorColor: '#0369a1',
        date: 'March 8, 2026', readTime: '3 min', reads: 6421,
        gradient: 'linear-gradient(135deg, #0c4a6e 0%, #0369a1 100%)', letter: '⚡',
    },
    {
        id: 7, cat: 'Awareness',
        title: 'Inside the Mind of a Digital Fraud Operator in Bangladesh',
        excerpt: 'An investigative report into how online scammers operate in Bangladeshi F-commerce markets, and the social engineering tactics they deploy.',
        author: 'Nusrat Jahan', authorInitials: 'NJ', authorColor: '#7c3aed',
        date: 'March 5, 2026', readTime: '10 min', reads: 7830,
        gradient: 'linear-gradient(135deg, #2d1b69 0%, #7c3aed 100%)', letter: '🎭',
    },
    {
        id: 8, cat: 'Guides',
        title: 'Complete Guide to KYC Verification on Migratepay',
        excerpt: 'Everything you need to know about the KYC process: what documents to submit, how long it takes, and how to resolve common verification errors.',
        author: 'Rafiqul Islam', authorInitials: 'RI', authorColor: '#d97706',
        date: 'March 3, 2026', readTime: '5 min', reads: 3100,
        gradient: 'linear-gradient(135deg, #451a03 0%, #d97706 100%)', letter: '✅',
    },
    {
        id: 9, cat: 'Legal',
        title: 'Understanding Your Rights as a Buyer in Escrow Disputes',
        excerpt: 'When a seller breaches a deal, what are your legal options in Bangladesh? Our legal team breaks down the dispute resolution process.',
        author: 'Sadia Akter', authorInitials: 'SA', authorColor: '#dc2626',
        date: 'Feb 28, 2026', readTime: '6 min', reads: 2200,
        gradient: 'linear-gradient(135deg, #1a0a00 0%, #c2410c 100%)', letter: '⚖️',
    },
    {
        id: 10, cat: 'Fintech',
        title: 'How Escrow is Reshaping the Bangladesh Real Estate Market',
        excerpt: 'Property fraud is one of the most common high-value scams in Bangladesh. Escrow-backed real estate transactions are the new solution.',
        author: 'Asif Rahman', authorInitials: 'AR', authorColor: '#006A4E',
        date: 'Feb 25, 2026', readTime: '7 min', reads: 4350,
        gradient: 'linear-gradient(135deg, #042f2e 0%, #006A4E 100%)', letter: '🏘',
    },
    {
        id: 11, cat: 'Security',
        title: 'SIM-Swap Attacks: The Hidden Threat to Mobile Banking in BD',
        excerpt: 'Cybercriminals are hijacking phone numbers to drain mobile wallets. How to protect your bKash and Nagad accounts from SIM-swap fraud.',
        author: 'Dev Team', authorInitials: 'DT', authorColor: '#0369a1',
        date: 'Feb 22, 2026', readTime: '5 min', reads: 5800,
        gradient: 'linear-gradient(135deg, #0c4a6e 0%, #1d4ed8 100%)', letter: '📱',
    },
    {
        id: 12, cat: 'Usage',
        title: 'Using Migratepay for Freelance Project Milestones',
        excerpt: 'Milestone-based escrow is the safest way to manage freelance projects. Here is how to set it up step-by-step on Migratepay.',
        author: 'Nusrat Jahan', authorInitials: 'NJ', authorColor: '#7c3aed',
        date: 'Feb 19, 2026', readTime: '6 min', reads: 2900,
        gradient: 'linear-gradient(135deg, #3b0764 0%, #7c3aed 100%)', letter: '🎯',
    },
    {
        id: 13, cat: 'Updates',
        title: 'Migratepay App Version 2.5: New Features Breakdown',
        excerpt: 'Real-time push notifications, multi-party deals, and an AI-assisted fraud score — everything new in Migratepay version 2.5.',
        author: 'Dev Team', authorInitials: 'DT', authorColor: '#0369a1',
        date: 'Feb 16, 2026', readTime: '4 min', reads: 8100,
        gradient: 'linear-gradient(135deg, #082f49 0%, #0284c7 100%)', letter: '🚀',
    },
    {
        id: 14, cat: 'Guides',
        title: 'How to Write a Secure Deal Agreement on Migratepay',
        excerpt: 'The terms of your escrow deal determine how disputes are resolved. Here is how to write airtight deal conditions that protect both parties.',
        author: 'Asif Rahman', authorInitials: 'AR', authorColor: '#006A4E',
        date: 'Feb 13, 2026', readTime: '8 min', reads: 3450,
        gradient: 'linear-gradient(135deg, #052e16 0%, #16a34a 100%)', letter: '📝',
    },
    {
        id: 15, cat: 'Awareness',
        title: 'The Rise of Facebook Marketplace Fraud in Bangladesh',
        excerpt: 'Facebook Marketplace has become a prime hunting ground for scammers. How victims are targeted and what escrow can do to stop the cycle.',
        author: 'Sadia Akter', authorInitials: 'SA', authorColor: '#dc2626',
        date: 'Feb 10, 2026', readTime: '9 min', reads: 9200,
        gradient: 'linear-gradient(135deg, #3b0000 0%, #991b1b 100%)', letter: '⚠️',
    },
    {
        id: 16, cat: 'Compliance',
        title: 'Bangladesh Data Protection Act: What Fintech Must Do Now',
        excerpt: 'The newly passed Data Protection Act imposes strict requirements on financial data processors. A compliance checklist for digital platforms.',
        author: 'Sadia Akter', authorInitials: 'SA', authorColor: '#dc2626',
        date: 'Feb 7, 2026', readTime: '7 min', reads: 1500,
        gradient: 'linear-gradient(135deg, #1a0000 0%, #b91c1c 100%)', letter: '🔏',
    },
    {
        id: 17, cat: 'Legal',
        title: 'Can a Seller Sue a Buyer for Refusing to Release Escrow?',
        excerpt: 'An exploration of the legal landscape in Bangladesh regarding unjustified hold of escrow funds and what remedies sellers have available.',
        author: 'Rafiqul Islam', authorInitials: 'RI', authorColor: '#d97706',
        date: 'Feb 4, 2026', readTime: '6 min', reads: 1900,
        gradient: 'linear-gradient(135deg, #1c0a00 0%, #b45309 100%)', letter: '🏛',
    },
    {
        id: 18, cat: 'Fintech',
        title: "CBDC in Bangladesh: How Digital Taka Will Change Payments",
        excerpt: "Bangladesh Bank's Central Bank Digital Currency pilot is underway. What does a digital taka mean for escrow, mobile banking, and merchants?",
        author: 'Asif Rahman', authorInitials: 'AR', authorColor: '#006A4E',
        date: 'Feb 1, 2026', readTime: '8 min', reads: 6700,
        gradient: 'linear-gradient(135deg, #001a12 0%, #047857 100%)', letter: '💴',
    },
    {
        id: 19, cat: 'Security',
        title: 'Zero-Trust Security: How Migratepay Protects Your Funds',
        excerpt: 'Our Zero-Trust architecture means no single point of failure. An inside look at the multi-layered security systems behind every Migratepay transaction.',
        author: 'Dev Team', authorInitials: 'DT', authorColor: '#0369a1',
        date: 'Jan 28, 2026', readTime: '6 min', reads: 4100,
        gradient: 'linear-gradient(135deg, #0f172a 0%, #1e40af 100%)', letter: '🔒',
    },
    {
        id: 20, cat: 'Guides',
        title: 'How to Resolve a Dispute Step-by-Step on Migratepay',
        excerpt: 'When deals go wrong, our dispute resolution system ensures fair outcomes. Full walkthrough of the evidence submission and mediation process.',
        author: 'Nusrat Jahan', authorInitials: 'NJ', authorColor: '#7c3aed',
        date: 'Jan 25, 2026', readTime: '7 min', reads: 5500,
        gradient: 'linear-gradient(135deg, #2e1065 0%, #6d28d9 100%)', letter: '⚡',
    },
];

let CATEGORIES = [
    { name: 'Security', icon: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>', bg: '#dcfce7', color: '#15803d' },
    { name: 'Fintech', icon: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/></svg>', bg: '#ede9fe', color: '#7c3aed' },
    { name: 'Usage', icon: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>', bg: '#fef3c7', color: '#d97706' },
    { name: 'Compliance', icon: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>', bg: '#fee2e2', color: '#dc2626' },
    { name: 'Updates', icon: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="23 4 23 10 17 10"/><polyline points="1 20 1 14 7 14"/><path d="M3.51 9a9 9 0 0114.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0020.49 15"/></svg>', bg: '#dbeafe', color: '#1d4ed8' },
    { name: 'Awareness', icon: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>', bg: '#fce7f3', color: '#be185d' },
    { name: 'Guides', icon: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 3h6a4 4 0 014 4v14a3 3 0 00-3-3H2z"/><path d="M22 3h-6a4 4 0 00-4 4v14a3 3 0 013-3h7z"/></svg>', bg: '#ecfdf5', color: '#059669' },
    { name: 'Legal', icon: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><line x1="9" y1="12" x2="15" y2="12"/><line x1="12" y1="9" x2="12" y2="15"/></svg>', bg: '#fff7ed', color: '#ea580c' }
];

// OVERRIDE WITH ADMIN DATA IF AVAILABLE
(function(){
    try {
        var adminDataRaw = localStorage.getItem('migratepay_blog_data');
        if (adminDataRaw) {
            var adminData = JSON.parse(adminDataRaw);
            
            // Only show published posts globally on blog page
            var adminPosts = adminData.posts.filter(p => !p.status || p.status === 'Published'); 

            if (adminPosts && adminPosts.length > 0) {
                // Formatting admin posts to include standard extra data for blog cards
                ALL_POSTS = adminPosts.map(p => {
                    var isArr = Array.isArray(p.categories);
                    var catVal = p.categories;
                    var catText = 'Uncategorized';
                    
                    if (isArr && catVal.length > 0) {
                        catText = catVal[0];
                    } else if (typeof catVal === 'string' && catVal.length > 0) {
                        catText = catVal.split(',')[0].trim();
                    }

                    return {
                        id: p.id,
                        cat: catText,
                        title: p.title || 'Untitled Segment',
                        excerpt: p.excerpt || (p.content ? p.content.substring(0, 50).replace(/<[^>]+>/g, '') + '...' : 'No content available.'),
                        author: p.author || 'Admin',
                        authorInitials: (p.author || 'A').substring(0, 2).toUpperCase(),
                        authorColor: '#006A4E', // default
                        date: p.date || new Date().toISOString().split('T')[0],
                        readTime: p.readTime || '4 min',
                        reads: p.reads || Math.floor(Math.random() * 5000),
                        gradient: 'linear-gradient(135deg, #002147 0%, #006A4E 100%)', // Default fallback style
                        letter: '📄',
                        featuredImage: p.featuredImage,
                        content: p.content,
                        slug: p.slug
                    };
                });
            }

            if (adminData.categories && adminData.categories.length > 0) {
                // If there's an exact match in icons, fallback to random color
                var bgColors = ['#dcfce7', '#ede9fe', '#fef3c7', '#fee2e2'];
                var txtColors = ['#15803d', '#7c3aed', '#d97706', '#dc2626'];
                CATEGORIES = adminData.categories.map((c, i) => {
                    return {
                        name: c.name,
                        icon: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>', // Default icon
                        bg: bgColors[i % bgColors.length],
                        color: txtColors[i % txtColors.length]
                    };
                });
            }
        }
    } catch(e) {
        console.error("Failed to parse admin data", e);
    }
})();

const POSTS_PER_PAGE = 10;
let currentPage = 1;
let activeFilter = 'all';

// ========================
// RENDER CATEGORIES SIDEBAR
// ========================
function renderCategories() {
    var container = document.getElementById('categoryList');
    if (!container) return;
    var counts = {};
    ALL_POSTS.forEach(function(p) { counts[p.cat] = (counts[p.cat] || 0) + 1; });
    container.innerHTML = CATEGORIES.map(function(cat) {
        return '<div class="category-item ' + (activeFilter === cat.name ? 'active' : '') + '" onclick="setFilter(\'' + cat.name + '\')">' +
            '<span class="category-item-name">' +
                '<span class="category-icon" style="background:' + cat.bg + '; color:' + cat.color + '">' + cat.icon + '</span>' +
                cat.name +
            '</span>' +
            '<span class="category-count">' + (counts[cat.name] || 0) + '</span>' +
        '</div>';
    }).join('');
}

// ========================
// RENDER RECENT / MOST READ
// ========================
function renderSidebars() {
    var mostRead = ALL_POSTS.slice().sort(function(a, b) { return b.reads - a.reads; }).slice(0, 5);
    var recentPosts = ALL_POSTS.slice().slice(0, 5);

    var makeSidebarItem = function(posts, container) {
        if (!container) return;
        container.innerHTML = posts.map(function(p, i) {
            return '<div class="recent-post-item">' +
                '<div class="recent-post-num">' + String(i + 1).padStart(2, '0') + '</div>' +
                '<div class="recent-post-info">' +
                    '<h4>' + p.title + '</h4>' +
                    '<div class="recent-post-meta">' + p.author + ' · ' + p.date + '</div>' +
                '</div>' +
            '</div>';
        }).join('');
    };

    makeSidebarItem(mostRead, document.getElementById('mostReadList'));
    makeSidebarItem(recentPosts, document.getElementById('recentPostList'));
}

// ========================
// RENDER BLOG GRID
// ========================
function getFilteredPosts() {
    if (activeFilter === 'all') return ALL_POSTS;
    return ALL_POSTS.filter(p => p.cat === activeFilter);
}

function renderGrid() {
    var filtered = getFilteredPosts();
    var totalPages = Math.ceil(filtered.length / POSTS_PER_PAGE);
    if (currentPage > totalPages) currentPage = 1;

    var start = (currentPage - 1) * POSTS_PER_PAGE;
    var pagePosts = filtered.slice(start, start + POSTS_PER_PAGE);

    var grid = document.getElementById('blogGrid');
    var label = document.getElementById('gridLabel');
    var countEl = document.getElementById('postCountLabel');

    if (label) label.textContent = activeFilter === 'all' ? 'Latest Posts' : activeFilter + ' Articles';
    if (countEl) countEl.textContent = filtered.length + ' Post' + (filtered.length !== 1 ? 's' : '');

    if (!grid) return;

    if (pagePosts.length === 0) {
        grid.innerHTML = '<div class="no-posts">' +
                '<svg viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="1.5"><path d="M2 3h6a4 4 0 014 4v14a3 3 0 00-3-3H2z"/><path d="M22 3h-6a4 4 0 00-4 4v14a3 3 0 013-3h7z"/></svg>' +
                '<h3>No posts found</h3>' +
                '<p>Try selecting a different category.</p>' +
            '</div>';
        var pagWrap = document.getElementById('paginationWrap');
        if (pagWrap) pagWrap.innerHTML = '';
        return;
    }

    grid.innerHTML = pagePosts.map(function(p) {
        var thumbContent = p.featuredImage 
            ? '<img src="' + p.featuredImage + '" style="width:100%; height:100%; object-fit:cover;" />' 
            : '<span style="font-size:52px; line-height:1;">' + p.letter + '</span>';

        return '<div class="post-card" onclick="location.href=\'/blog?slug=' + p.slug + '\'">' +
            '<div class="post-thumbnail">' +
                '<div class="post-thumbnail-inner" style="background: ' + (p.featuredImage ? 'none' : p.gradient) + '">' +
                    thumbContent +
                '</div>' +
                '<span class="post-cat-badge">' + p.cat + '</span>' +
            '</div>' +
            '<div class="post-card-body">' +
                '<h3>' + p.title + '</h3>' +
                '<p>' + p.excerpt + '</p>' +
                '<div class="post-card-footer">' +
                    '<div class="post-author-info">' +
                        '<div class="post-author-avatar" style="background:' + p.authorColor + '">' + p.authorInitials + '</div>' +
                        '<div>' +
                            '<div class="post-author-name">' + p.author + '</div>' +
                            '<div class="post-date">' + p.date + ' · ' + p.readTime + ' read</div>' +
                        '</div>' +
                    '</div>' +
                    '<span class="post-read-link">' +
                        'Read ' +
                        '<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="M5 12h14M12 5l7 7-7 7"/></svg>' +
                    '</span>' +
                '</div>' +
            '</div>' +
        '</div>';
    }).join('');

    renderPagination(filtered.length, totalPages);
}

// ========================
// RENDER PAGINATION
// ========================
function renderPagination(totalPosts, totalPages) {
    var wrap = document.getElementById('paginationWrap');
    if (!wrap || totalPages <= 1) { if(wrap) wrap.innerHTML = ''; return; }

    var btns = '';

    // Prev
    btns += '<button class="page-btn" ' + (currentPage === 1 ? 'disabled' : '') + ' onclick="goToPage(' + (currentPage - 1) + ')">' +
        '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="15 18 9 12 15 6"/></svg>' +
    '</button>';

    var range = [];
    for (var i = 1; i <= totalPages; i++) {
        if (i === 1 || i === totalPages || (i >= currentPage - 1 && i <= currentPage + 1)) {
            range.push(i);
        } else if (range[range.length - 1] !== '...') {
            range.push('...');
        }
    }

    range.forEach(function(item) {
        if (item === '...') {
            btns += '<button class="page-btn dots">···</button>';
        } else {
            btns += '<button class="page-btn ' + (item === currentPage ? 'active' : '') + '" onclick="goToPage(' + item + ')">' + item + '</button>';
        }
    });

    // Next
    btns += '<button class="page-btn" ' + (currentPage === totalPages ? 'disabled' : '') + ' onclick="goToPage(' + (currentPage + 1) + ')">' +
        '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="9 18 15 12 9 6"/></svg>' +
    '</button>';

    wrap.innerHTML = btns;
}

// ========================
// SINGLE POST RENDERER
// ========================
function renderSinglePost(post) {
    const archive = document.getElementById('archiveView');
    const single = document.getElementById('singlePostView');
    const content = document.getElementById('postSingleContent');
    const headerDate = document.getElementById('newspaperDate');
    
    if (!archive || !single || !content) return;
    
    archive.classList.add('hidden');
    single.classList.remove('hidden');
    
    // Set date in header
    if (headerDate) {
        const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
        headerDate.textContent = new Date().toLocaleDateString('en-US', options);
    }
    
    // Breadcrumbs
    const breadcrumbs = `
        <div class="post-breadcrumbs" style="font-family:'Lexend', sans-serif; text-transform:none; color:#777; font-weight:400; font-size:12px;">
            <a href="/">Home</a> <span class="separator">›</span>
            <a href="/blog?cat=${post.cat}">${post.cat}</a> <span class="separator">›</span>
            <span>${post.title}</span>
        </div>
    `;
    
    // Meta
    const meta = `
        <div class="post-meta-detailed" style="border:none; margin-bottom:10px; padding:0; font-size:13px;">
            <div style="background:${post.authorColor}; width:30px; height:30px; border-radius:50%; display:flex; align-items:center; justify-content:center; color:#fff; font-size:12px; font-weight:900;">${post.authorInitials}</div>
            <span>By <span class="author-name" style="font-weight:800; color:#000;">${post.author}</span></span>
            <span style="color:#ccc;">-</span>
            <span>${post.date}</span>
            <div style="margin-left:auto; display:flex; gap:15px; color:#999; font-size:11px; font-weight:600;">
                <span style="display:flex; align-items:center; gap:5px;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                    ${post.reads}
                </span>
                <span style="display:flex; align-items:center; gap:5px;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 11.5a8.38 8.38 0 01-.9 3.8 8.5 8.5 0 11-7.6-7.6 8.38 8.38 0 013.8.9h.5a2.5 2.5 0 012.5 2.5v.5z"/></svg>
                    0
                </span>
            </div>
        </div>
    `;

    // Share Buttons Row
    const shareHTML = `
        <div class="share-buttons-container" style="display:flex; align-items:center; gap:10px; margin-bottom:30px;">
            <div style="background:#fff; border:1px solid #eee; padding:5px 15px; display:flex; align-items:center; gap:10px; border-radius:4px; font-size:11px; font-weight:800; color:#999; text-transform:uppercase;">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="M4 12v8a2 2 0 002 2h12a2 2 0 002-2v-8"/><polyline points="16 6 12 2 8 6"/><line x1="12" y1="2" x2="12" y2="15"/></svg>
                Share
            </div>
            <div style="background:#3b5998; width:35px; height:35px; border-radius:4px; display:flex; align-items:center; justify-content:center; cursor:pointer;"><svg width="16" height="16" fill="#fff" viewBox="0 0 24 24"><path d="M18 2h-3a5 5 0 00-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 011-1h3z"/></svg></div>
            <div style="background:#00acee; width:35px; height:35px; border-radius:4px; display:flex; align-items:center; justify-content:center; cursor:pointer;"><svg width="16" height="16" fill="#fff" viewBox="0 0 24 24"><path d="M23 3a10.9 10.9 0 01-3.14 1.53 4.48 4.48 0 00-7.86 3v1A10.66 10.66 0 013 4s-4 9 5 13a11.64 11.64 0 01-7 2c9 5 20 0 20-11.5a4.5 4.5 0 00-.08-.83A7.72 7.72 0 0023 3z"/></svg></div>
            <div style="background:#bd081c; width:35px; height:35px; border-radius:4px; display:flex; align-items:center; justify-content:center; cursor:pointer;"><svg width="16" height="16" fill="#fff" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12c0 4.27 2.68 7.9 6.46 9.39.29.05.39-.12.39-.28v-1c-2.78.6-3.37-1.34-3.37-1.34-.45-1.15-1.1-1.46-1.1-1.46-.9-.62.07-.61.07-.61 1 .07 1.53 1.03 1.53 1.03.9 1.54 2.35 1.1 2.92.84.09-.65.35-1.1.64-1.35-2.22-.25-4.55-1.1-4.55-4.93 0-1.09.39-1.98 1.03-2.68-.1-.25-.45-1.27.1-2.64 0 0 .84-.27 2.75 1.02a9.58 9.58 0 015 0c1.91-1.29 2.75-1.02 2.75-1.02.55 1.37.2 2.39.1 2.64.64.7 1.03 1.59 1.03 2.68 0 3.84-2.34 4.68-4.57 4.93.36.31.68.92.68 1.85v2.75c0 .16.1.34.4.28C19.32 19.9 22 16.27 22 12c0-5.52-4.48-10-10-10z"/></svg></div>
            <div style="background:#25d366; width:35px; height:35px; border-radius:4px; display:flex; align-items:center; justify-content:center; cursor:pointer;"><svg width="16" height="16" fill="#fff" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg></div>
        </div>
    `;
    
    // Content body
    const body = post.content || `<p>${post.excerpt}</p><p>Full content for this post is currently being processed by the Migratepay editorial team. Stay tuned for deeper insights into digital trust and escrow security in the Bangladeshi market.</p>`;
    
    // Related
    const relatedPosts = ALL_POSTS.filter(p => p.id !== post.id && p.cat === post.cat).slice(0, 3);
    const relatedHTML = relatedPosts.map(r => `
        <div class="related-item">
            <a href="/blog?slug=${r.slug}">
                <div style="height:140px; background:#f1f1f1; overflow:hidden;">
                    ${r.featuredImage ? `<img src="${r.featuredImage}" style="width:100%; height:100%; object-fit:cover;"/>` : `<div style="width:100%; height:100%; display:flex; align-items:center; justify-content:center; font-size:40px;">${r.letter}</div>`}
                </div>
                <h4>${r.title}</h4>
            </a>
        </div>
    `).join('');

    // Popular in Sidebar
    const popular = ALL_POSTS.slice().sort((a,b) => b.reads - a.reads).slice(0, 5);
    const popularHTML = popular.map(p => `
        <div class="popular-post-item">
            <div class="popular-post-thumb">
                ${p.featuredImage ? `<img src="${p.featuredImage}"/>` : `<div style="width:100%; height:100%; display:flex; align-items:center; justify-content:center; font-size:20px;">${p.letter}</div>`}
            </div>
            <div class="popular-post-info">
                <a href="/blog?slug=${p.slug}"><h4>${p.title}</h4></a>
                <div class="popular-post-date">${p.date}</div>
            </div>
        </div>
    `).join('');

    content.innerHTML = `
        <div class="post-main-content">
            ${breadcrumbs}
            <div class="post-header" style="margin-top:15px;">
                <span class="post-header-cat" style="background:#d0021b; font-size:11px; font-weight:700; border-radius:2px; margin-bottom:15px;">${post.cat}</span>
                <h1 style="font-size:40px; font-weight:800; line-height:1.2; margin-bottom:20px; font-family:'Lexend', sans-serif;">${post.title}</h1>
                ${meta}
                ${shareHTML}
            </div>
            
            <div class="post-featured-image" style="margin-top:10px;">
                ${post.featuredImage ? `<img src="${post.featuredImage}" />` : `<div style="height:400px; background:${post.gradient}; display:flex; align-items:center; justify-content:center; font-size:120px;">${post.letter}</div>`}
            </div>
            
            <div class="post-body-text">
                ${body}
            </div>
            
            <div class="post-footer-tags">
                <span>Tags:</span>
                <div class="post-tag">#Escrow</div>
                <div class="post-tag">#Fintech</div>
                <div class="post-tag">#${post.cat}</div>
            </div>
            
            <div class="author-bio-card">
                <div class="author-bio-avatar" style="background:${post.authorColor}; display:flex; align-items:center; justify-content:center; font-size:32px; font-weight:900; color:#fff;">${post.authorInitials}</div>
                <div class="author-bio-info">
                    <h3>${post.author}</h3>
                    <p>Expert in Bangladeshi digital commerce and fintech security. Leading the charge in making online transactions safe for everyone through advanced escrow solutions.</p>
                    <div class="author-socials">
                        <a href="#">Follow on Twitter</a> | <a href="#">LinkedIn Profile</a>
                    </div>
                </div>
            </div>
            
            <div class="related-articles">
                <div class="related-articles-header"><h3>Related Articles</h3></div>
                <div class="related-grid">${relatedHTML}</div>
            </div>
            
            <div class="comments-section">
                <div class="comments-header"><h3>Leave a Response</h3></div>
                <div class="comment-form">
                    <textarea placeholder="Write your thoughts..."></textarea>
                    <button class="comment-submit">Post Comment</button>
                    <p style="font-size:11px; color:#999; margin-top:10px;">Your email address will not be published. Required fields are marked *</p>
                </div>
            </div>
        </div>
        
        <aside class="post-sidebar">
            <div class="sidebar-widget">
                <div class="widget-title" style="background:#000; padding:0;"><h3 style="background:#000; color:#fff; padding:8px 15px; font-size:12px;">MOST POPULAR</h3></div>
                ${popularHTML}
            </div>
        </aside>
    `;

    // Update Page Title
    document.title = post.title + " | News12Paper";
    
    // Scroll to top
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function handleRouting() {
    const params = new URLSearchParams(window.location.search);
    const slug = params.get('slug');
    const cat = params.get('cat');
    
    if (slug) {
        const post = ALL_POSTS.find(p => p.slug === slug);
        if (post) {
            renderSinglePost(post);
            return;
        }
    }
    
    // Normal Archive view
    document.getElementById('archiveView').classList.remove('hidden');
    document.getElementById('singlePostView').classList.add('hidden');
    
    if (cat) {
        setFilter(cat);
    } else {
        renderGrid();
    }
}

// ========================
// ACTIONS
// ========================
function goToPage(page) {
    currentPage = page;
    renderGrid();
    // Smooth scroll to grid top
    document.querySelector('.blog-center-col')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
}

function setFilter(cat) {
    activeFilter = cat;
    currentPage = 1;

    // Update filter chips
    document.querySelectorAll('.filter-chip').forEach(chip => {
        chip.classList.toggle('active', chip.dataset.cat === cat);
    });

    renderGrid();
    renderCategories();
}

// ========================
// FILTER CHIPS
// ========================
document.getElementById('filterChips').addEventListener('click', (e) => {
    const chip = e.target.closest('.filter-chip');
    if (!chip) return;
    setFilter(chip.dataset.cat);
});

// ========================
// INIT — run directly since script is after DOM elements
// ========================
(function init() {
    renderCategories();
    renderSidebars();
    handleRouting();
})();
@endverbatim
</script>

@endsection
