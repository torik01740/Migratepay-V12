@extends('layouts.frontend')
@section('title', 'About Us | Migratepay')

@section('content')
<style>
    /* Custom Styling for About Page - Colorful Visual Redesign */
    .about-page {
        background-color: #F5F8FC; /* Light Blue-Gray */
        color: #334155;
        font-family: 'Inter', sans-serif;
        overflow: hidden;
    }

    .about-page h1, .about-page h2, .about-page h3, .about-page h4, .about-page h5 {
        font-family: 'Lexend', sans-serif;
        color: #1e293b;
    }

    .text-accent { color: #3b82f6; } /* Blue accent */
    .text-gradient {
        background: linear-gradient(135deg, #3b82f6, #8b5cf6);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }
    .text-muted { color: #64748b; }

    /* Keyframes for animations */
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(30px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }

    @keyframes floatVertical {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-20px); }
    }

    @keyframes pulseSoft {
        0%, 100% { transform: scale(1); opacity: 0.8; }
        50% { transform: scale(1.05); opacity: 1; }
    }
    
    @keyframes drawLine {
        from { stroke-dashoffset: 1000; }
        to { stroke-dashoffset: 0; }
    }

    .reveal-elem {
        opacity: 0;
        transform: translateY(30px);
        transition: opacity 1s ease, transform 1s cubic-bezier(0.16, 1, 0.3, 1);
    }
    .reveal-elem.in-view {
        opacity: 1;
        transform: translateY(0);
    }

    /* 1. Hero Section */
    .about-hero {
        padding: 200px 24px 120px;
        position: relative;
        text-align: center;
        background: #F5F8FC;
    }
    
    .hero-bg-orbs {
        position: absolute;
        inset: 0;
        z-index: 0;
        overflow: hidden;
        pointer-events: none;
    }
    .orb-1 {
        position: absolute;
        width: 600px;
        height: 600px;
        background: radial-gradient(circle, rgba(59,130,246,0.1) 0%, transparent 70%);
        top: -200px;
        left: -100px;
        border-radius: 50%;
        animation: floatVertical 10s ease-in-out infinite;
    }
    .orb-2 {
        position: absolute;
        width: 500px;
        height: 500px;
        background: radial-gradient(circle, rgba(139,92,246,0.08) 0%, transparent 70%);
        bottom: -150px;
        right: -50px;
        border-radius: 50%;
        animation: floatVertical 12s ease-in-out infinite reverse;
    }
    .hero-dots {
        position: absolute;
        inset: 0;
        background-image: radial-gradient(#cbd5e1 1px, transparent 1px);
        background-size: 40px 40px;
        opacity: 0.3;
        z-index: 0;
    }

    .hero-content {
        position: relative;
        z-index: 1;
        max-width: 900px;
        margin: 0 auto;
    }

    .hero-label {
        font-size: 12px;
        font-weight: 800;
        letter-spacing: 0.2em;
        text-transform: uppercase;
        color: #3b82f6;
        margin-bottom: 32px;
        display: inline-block;
        background: rgba(59,130,246,0.1);
        padding: 8px 16px;
        border-radius: 99px;
        animation: fadeIn 1s ease forwards;
    }

    .hero-title {
        font-size: clamp(3rem, 7vw, 5.5rem);
        font-weight: 900;
        line-height: 1.05;
        letter-spacing: -0.04em;
        margin-bottom: 32px;
        color: #0f172a;
        animation: fadeInUp 1s ease forwards 0.2s;
        opacity: 0;
    }

    .hero-desc {
        font-size: clamp(1.1rem, 2vw, 1.4rem);
        color: #475569;
        line-height: 1.6;
        max-width: 600px;
        margin: 0 auto;
        animation: fadeInUp 1s ease forwards 0.4s;
        opacity: 0;
    }

    /* 2. Our Story */
    .story-section-wrapper {
        background: #ffffff;
        position: relative;
        z-index: 2;
    }
    .story-section {
        padding: 160px 24px;
        max-width: 1200px;
        margin: 0 auto;
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 80px;
        align-items: center;
    }

    .story-content h2 {
        font-size: clamp(2.5rem, 5vw, 4rem);
        font-weight: 800;
        margin-bottom: 32px;
        letter-spacing: -0.02em;
        color: #0f172a;
    }

    .story-content p {
        font-size: 1.15rem;
        line-height: 1.8;
        color: #475569;
        margin-bottom: 24px;
    }
    
    .story-visual {
        position: relative;
        width: 100%;
        height: 400px;
        background: #F5F8FC;
        border-radius: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        box-shadow: 0 20px 40px rgba(0,0,0,0.02);
    }
    
    .visual-nodes {
        display: flex;
        align-items: center;
        justify-content: space-between;
        width: 80%;
        position: relative;
        z-index: 2;
    }
    .v-node {
        width: 60px;
        height: 60px;
        background: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 10px 20px rgba(0,0,0,0.05);
        font-weight: bold;
        color: #3b82f6;
        z-index: 3;
    }
    .v-node.center {
        width: 100px;
        height: 100px;
        background: linear-gradient(135deg, #3b82f6, #8b5cf6);
        color: white;
        font-size: 24px;
        box-shadow: 0 15px 30px rgba(59,130,246,0.3);
        animation: pulseSoft 3s infinite;
    }
    
    .v-line {
        position: absolute;
        top: 50%;
        left: 30px;
        right: 30px;
        height: 4px;
        background: #e2e8f0;
        transform: translateY(-50%);
        z-index: 1;
        border-radius: 2px;
    }
    .v-line-progress {
        position: absolute;
        top: 0;
        left: 0;
        height: 100%;
        width: 0%;
        background: linear-gradient(90deg, #3b82f6, #8b5cf6);
        border-radius: 2px;
        transition: width 1.5s ease-out;
    }
    .story-section.in-view .v-line-progress {
        width: 100%;
    }
    .v-orb {
        position: absolute;
        width: 300px;
        height: 300px;
        background: radial-gradient(circle, rgba(139,92,246,0.1) 0%, transparent 70%);
        top: -50px;
        right: -50px;
        border-radius: 50%;
    }

    /* 3. Mission & Vision */
    .mission-vision-wrapper {
        background: #EEF5FF; /* Soft Blue */
    }
    .mission-vision-section {
        max-width: 1200px;
        margin: 0 auto;
        padding: 120px 24px;
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 40px;
    }

    .mv-card {
        background: white;
        padding: 60px 48px;
        border-radius: 32px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.03);
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }
    
    .mv-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 20px 40px rgba(0,0,0,0.06);
    }
    
    .mv-bg {
        position: absolute;
        top: -50%;
        right: -50%;
        width: 100%;
        height: 100%;
        border-radius: 50%;
        opacity: 0.4;
        transition: all 0.5s ease;
        z-index: 0;
    }
    .mv-mission .mv-bg { background: radial-gradient(circle, rgba(6,182,212,0.15) 0%, transparent 70%); }
    .mv-vision .mv-bg { background: radial-gradient(circle, rgba(139,92,246,0.15) 0%, transparent 70%); }
    
    .mv-card:hover .mv-bg { transform: scale(1.2); opacity: 0.6; }

    .mv-label {
        font-size: 13px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.15em;
        margin-bottom: 24px;
        display: flex;
        align-items: center;
        gap: 12px;
        position: relative;
        z-index: 1;
    }
    
    .mv-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        transition: transform 0.3s ease;
    }
    .mv-card:hover .mv-icon { transform: scale(1.1); }
    
    .mv-mission .mv-label, .mv-mission .mv-icon { color: #0891b2; } /* Cyan */
    .mv-mission .mv-icon { background: rgba(6,182,212,0.1); }
    
    .mv-vision .mv-label, .mv-vision .mv-icon { color: #7c3aed; } /* Violet */
    .mv-vision .mv-icon { background: rgba(139,92,246,0.1); }

    .mv-text {
        font-size: clamp(1.5rem, 3vw, 2rem);
        font-weight: 400;
        line-height: 1.5;
        color: #1e293b;
        font-family: 'Lexend', sans-serif;
        position: relative;
        z-index: 1;
    }
    
    .mv-text strong {
        font-weight: 700;
        color: #0f172a;
    }

    /* 4. What We Believe */
    .beliefs-section {
        background: #ffffff;
        padding: 160px 24px;
    }

    .beliefs-container {
        max-width: 1200px;
        margin: 0 auto;
    }

    .beliefs-header {
        text-align: center;
        margin-bottom: 80px;
    }
    
    .beliefs-header h2 {
        font-size: clamp(2rem, 4vw, 3rem);
        font-weight: 800;
        letter-spacing: -0.02em;
        color: #0f172a;
    }

    .beliefs-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 32px;
    }

    .belief-item {
        background: #F8FAFC;
        padding: 40px;
        border-radius: 24px;
        transition: all 0.3s ease;
        border: 1px solid transparent;
        position: relative;
        overflow: hidden;
    }
    
    .belief-item::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 4px;
        opacity: 0;
        transition: opacity 0.3s ease;
    }
    
    /* Accent variations */
    .belief-trust::before { background: #3b82f6; }
    .belief-innovation::before { background: #8b5cf6; }
    .belief-simplicity::before { background: #f59e0b; }
    .belief-people::before { background: #ec4899; }
    .belief-security::before { background: #10b981; }
    .belief-transparency::before { background: #06b6d4; }

    .belief-item:hover {
        transform: translateY(-4px);
        background: white;
        box-shadow: 0 15px 35px rgba(0,0,0,0.04);
        border-color: #e2e8f0;
    }
    .belief-item:hover::before { opacity: 1; }
    
    .b-icon {
        width: 56px;
        height: 56px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        margin-bottom: 24px;
        background: white;
        box-shadow: 0 4px 10px rgba(0,0,0,0.05);
        transition: all 0.3s ease;
    }
    .belief-item:hover .b-icon {
        color: white;
    }
    
    .belief-trust:hover .b-icon { background: #3b82f6; }
    .belief-innovation:hover .b-icon { background: #8b5cf6; }
    .belief-simplicity:hover .b-icon { background: #f59e0b; }
    .belief-people:hover .b-icon { background: #ec4899; }
    .belief-security:hover .b-icon { background: #10b981; }
    .belief-transparency:hover .b-icon { background: #06b6d4; }

    .belief-item h3 {
        font-size: 1.25rem;
        font-weight: 700;
        margin-bottom: 12px;
        color: #0f172a;
    }

    .belief-item p {
        font-size: 1rem;
        line-height: 1.6;
        color: #64748b;
    }

    /* 5. How We Work */
    .workflow-section {
        padding: 160px 24px;
        max-width: 1200px;
        margin: 0 auto;
        background: #ECFAFC; /* Soft Cyan */
        border-radius: 40px;
        margin-bottom: 80px;
    }

    .workflow-header h2 {
        font-size: clamp(2.5rem, 4vw, 3.5rem);
        font-weight: 800;
        margin-bottom: 80px;
        letter-spacing: -0.02em;
        text-align: center;
    }

    .workflow-timeline {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 40px;
        position: relative;
        padding: 0 40px;
    }

    .workflow-line {
        position: absolute;
        top: 32px;
        left: 60px;
        right: 60px;
        height: 2px;
        background: #cbd5e1;
        z-index: 0;
    }
    
    .workflow-line-active {
        position: absolute;
        top: 32px;
        left: 60px;
        height: 2px;
        background: #06b6d4; /* Cyan */
        z-index: 1;
        width: 0%;
        transition: width 1.5s ease-out;
        box-shadow: 0 0 10px rgba(6,182,212,0.5);
    }
    .workflow-section.in-view .workflow-line-active { width: 66%; }

    .workflow-step {
        position: relative;
        z-index: 2;
        text-align: center;
        padding: 0 20px;
    }

    .step-number {
        width: 64px;
        height: 64px;
        background: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        font-weight: 800;
        color: #94a3b8;
        margin: 0 auto 24px;
        border: 2px solid #cbd5e1;
        transition: all 0.5s ease;
    }
    
    .workflow-section.in-view .workflow-step:nth-child(1) .step-number {
        border-color: #06b6d4;
        color: #06b6d4;
        box-shadow: 0 0 20px rgba(6,182,212,0.2);
    }
    .workflow-section.in-view .workflow-step:nth-child(2) .step-number {
        border-color: #06b6d4;
        color: #06b6d4;
        box-shadow: 0 0 20px rgba(6,182,212,0.2);
        transition-delay: 0.5s;
    }
    .workflow-section.in-view .workflow-step:nth-child(3) .step-number {
        border-color: #06b6d4;
        color: #06b6d4;
        box-shadow: 0 0 20px rgba(6,182,212,0.2);
        transition-delay: 1s;
    }

    .step-label {
        font-size: 12px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        color: #06b6d4;
        margin-bottom: 12px;
    }

    .step-title {
        font-size: 1.5rem;
        font-weight: 700;
        margin-bottom: 16px;
        color: #0f172a;
    }

    .step-desc {
        color: #475569;
        line-height: 1.6;
    }
    
    /* 5.5 Ecosystem */
    .ecosystem-section {
        background: #ffffff;
        padding: 80px 24px;
        max-width: 1200px;
        margin: 0 auto;
    }
    .eco-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 24px;
    }
    .eco-card {
        padding: 32px;
        background: #F8FAFC;
        border-radius: 24px;
        text-align: center;
        transition: transform 0.3s ease;
    }
    .eco-card:hover { transform: translateY(-4px); background: #F1F5F9; }
    .eco-icon { font-size: 32px; margin-bottom: 16px; color: #3b82f6; }
    .eco-card h4 { font-size: 1.1rem; font-weight: 700; margin-bottom: 8px; color: #1e293b; }
    .eco-card p { font-size: 0.95rem; color: #64748b; }

    /* 6. Partners / People */
    .partners-preview {
        padding: 120px 24px;
        background: #F6F0FF; /* Soft Purple */
        text-align: center;
    }

    .partners-preview h2 {
        font-size: clamp(2rem, 4vw, 3rem);
        font-weight: 800;
        margin-bottom: 16px;
        letter-spacing: -0.02em;
        color: #0f172a;
    }
    
    .partners-preview p {
        color: #475569;
        margin-bottom: 60px;
        font-size: 1.125rem;
        max-width: 600px;
        margin-left: auto;
        margin-right: auto;
    }

    .avatars-wrap {
        display: flex;
        justify-content: center;
        gap: 24px;
        margin-bottom: 40px;
    }

    .avatar-card {
        background: white;
        padding: 24px;
        border-radius: 24px;
        box-shadow: 0 10px 20px rgba(0,0,0,0.02);
        display: flex;
        flex-direction: column;
        align-items: center;
        transition: transform 0.3s ease;
    }
    
    .avatar-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 15px 30px rgba(139,92,246,0.1);
    }

    .avatar {
        width: 90px;
        height: 90px;
        border-radius: 50%;
        object-fit: cover;
        margin-bottom: 16px;
    }

    .avatar-name { font-weight: 700; color: #0f172a; font-size: 1.1rem; }
    .avatar-role { color: #8b5cf6; font-size: 0.9rem; font-weight: 600; }
    
    .simple-link {
        display: inline-block;
        margin-top: 32px;
        color: #8b5cf6;
        font-weight: 700;
        font-size: 1rem;
        letter-spacing: 0.05em;
        transition: color 0.3s ease;
    }
    .simple-link:hover { color: #6d28d9; }

    /* Stats Section */
    .stats-section {
        padding: 100px 24px;
        background: linear-gradient(135deg, #EFFBF5, #ECFAFC);
        border-top: 1px solid rgba(255,255,255,0.5);
        border-bottom: 1px solid rgba(255,255,255,0.5);
    }
    
    .stats-container {
        max-width: 1200px;
        margin: 0 auto;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 40px;
        text-align: center;
    }

    .stat-val {
        font-size: clamp(2.5rem, 4vw, 3.5rem);
        font-weight: 900;
        color: #0f172a;
        margin-bottom: 8px;
        line-height: 1;
    }
    .stat-val span {
        background: linear-gradient(135deg, #3b82f6, #10b981);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    .stat-lbl {
        font-size: 12px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        color: #64748b;
    }

    /* 7. Closing CTA */
    .closing-cta {
        padding: 160px 24px;
        text-align: center;
        position: relative;
        overflow: hidden;
        background: linear-gradient(135deg, #1e3a8a, #4c1d95);
        color: white;
    }

    .cta-glow {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 800px;
        height: 800px;
        background: radial-gradient(circle, rgba(139,92,246,0.4) 0%, transparent 60%);
        z-index: 0;
        pointer-events: none;
    }

    .closing-cta h2 {
        font-size: clamp(3rem, 6vw, 4.5rem);
        font-weight: 900;
        line-height: 1.1;
        letter-spacing: -0.02em;
        margin-bottom: 24px;
        position: relative;
        z-index: 1;
        color: white;
    }
    
    .closing-cta p {
        font-size: 1.25rem;
        color: #c4b5fd;
        margin-bottom: 48px;
        max-width: 600px;
        margin-left: auto;
        margin-right: auto;
        position: relative;
        z-index: 1;
    }

    .btn-solid {
        display: inline-flex;
        align-items: center;
        gap: 12px;
        padding: 20px 48px;
        background: #ffffff;
        color: #4c1d95;
        border-radius: 99px;
        font-weight: 800;
        font-size: 16px;
        transition: all 0.3s ease;
        position: relative;
        z-index: 1;
        box-shadow: 0 10px 25px rgba(0,0,0,0.1);
    }

    .btn-solid:hover {
        transform: translateY(-4px);
        box-shadow: 0 15px 35px rgba(0,0,0,0.2);
        background: #f8fafc;
    }

    /* Responsive */
    @media (max-width: 992px) {
        .story-section {
            grid-template-columns: 1fr;
            gap: 40px;
            padding: 100px 24px;
        }
        .mission-vision-section {
            grid-template-columns: 1fr;
            padding: 80px 24px;
        }
        .beliefs-grid {
            grid-template-columns: 1fr 1fr;
        }
        .workflow-timeline {
            grid-template-columns: 1fr;
            gap: 60px;
        }
        .workflow-line, .workflow-line-active {
            display: none; /* Hide horizontal line on mobile */
        }
        .workflow-step {
            padding: 0;
            text-align: left;
            display: flex;
            flex-direction: column;
            align-items: flex-start;
        }
        .step-number {
            margin: 0 0 16px 0;
        }
        .eco-grid {
            grid-template-columns: 1fr 1fr;
        }
        .stats-grid {
            grid-template-columns: 1fr 1fr;
            row-gap: 60px;
        }
        .avatars-wrap {
            flex-direction: column;
            align-items: center;
        }
        .avatar-card {
            width: 100%;
            max-width: 300px;
        }
    }
    
    @media (max-width: 576px) {
        .beliefs-grid, .stats-grid, .eco-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (prefers-reduced-motion: reduce) {
        *, ::before, ::after {
            animation-duration: 0.01ms !important;
            animation-iteration-count: 1 !important;
            transition-duration: 0.01ms !important;
            scroll-behavior: auto !important;
        }
    }
</style>

<div class="about-page">
    <!-- 1. Hero Section -->
    <div class="about-hero">
        <div class="hero-bg-orbs">
            <div class="orb-1"></div>
            <div class="orb-2"></div>
        </div>
        <div class="hero-dots"></div>
        <div class="hero-content">
            <span class="hero-label">About Migratepay</span>
            <h1 class="hero-title">Building a More Trusted Way to <span class="text-gradient">Move Forward</span></h1>
            <p class="hero-desc">Founded on the absolute right to financial safety. We provide the institutional-grade infrastructure required for the modern digital marketplace.</p>
        </div>
    </div>

    <!-- 2. Our Story -->
    <div class="story-section-wrapper">
        <div class="story-section reveal-elem">
            <div class="story-content">
                <h2>Our Story</h2>
                <p>Migratepay was born out of a stark realization: the digital economy is expanding rapidly, yet the underlying trust infrastructure hasn't kept pace. We saw individuals and businesses holding back on high-value transactions simply because they couldn't confidently trust the other party.</p>
                <p>We set out to engineer a solution that removes this friction entirely. By building a high-integrity, bank-grade escrow architecture, we are not just securing payments—we are actively enabling commerce, fostering partnerships, and allowing people to transact with absolute certainty in an evolving digital world.</p>
            </div>
            <div class="story-visual">
                <div class="v-orb"></div>
                <div class="v-line"></div>
                <div class="v-line-progress"></div>
                <div class="visual-nodes">
                    <div class="v-node">Buy</div>
                    <div class="v-node center"><i class="fa fa-shield-alt"></i></div>
                    <div class="v-node">Sell</div>
                </div>
            </div>
        </div>
    </div>

    <!-- 3. Mission & Vision -->
    <div class="mission-vision-wrapper">
        <div class="mission-vision-section reveal-elem">
            <div class="mv-card mv-mission">
                <div class="mv-bg"></div>
                <div class="mv-label"><div class="mv-icon"><i class="fa fa-bullseye"></i></div> Our Mission</div>
                <div class="mv-text">
                    To eradicate digital fraud by providing a <strong>high-integrity escrow architecture</strong> that empowers the honest majority.
                </div>
            </div>
            <div class="mv-card mv-vision">
                <div class="mv-bg"></div>
                <div class="mv-label"><div class="mv-icon"><i class="fa fa-eye"></i></div> Our Vision</div>
                <div class="mv-text">
                    To become the <strong>global sovereign standard</strong> for digital trust, where every individual can exchange capital with absolute certainty.
                </div>
            </div>
        </div>
    </div>

    <!-- 4. What We Believe -->
    <div class="beliefs-section">
        <div class="beliefs-container reveal-elem">
            <div class="beliefs-header">
                <h2>What We Believe</h2>
            </div>
            <div class="beliefs-grid">
                <div class="belief-item belief-trust">
                    <div class="b-icon"><i class="fa fa-handshake"></i></div>
                    <h3>Trust</h3>
                    <p>Building experiences people can rely on unequivocally. Trust isn't requested; it's architected into every layer.</p>
                </div>
                <div class="belief-item belief-simplicity">
                    <div class="b-icon"><i class="fa fa-magic"></i></div>
                    <h3>Simplicity</h3>
                    <p>Making complex processes feel effortless. True sophistication lies in abstracting complexity.</p>
                </div>
                <div class="belief-item belief-innovation">
                    <div class="b-icon"><i class="fa fa-lightbulb"></i></div>
                    <h3>Innovation</h3>
                    <p>Continuously challenging the conventional. Leveraging modern cryptographic and financial tech.</p>
                </div>
                <div class="belief-item belief-people">
                    <div class="b-icon"><i class="fa fa-users"></i></div>
                    <h3>People First</h3>
                    <p>Technology should serve people, not complicate their lives. We design for human intuition.</p>
                </div>
                <div class="belief-item belief-transparency">
                    <div class="b-icon"><i class="fa fa-search"></i></div>
                    <h3>Transparency</h3>
                    <p>Clear processes and understandable charges. No hidden fees or opaque settlement timelines.</p>
                </div>
                <div class="belief-item belief-security">
                    <div class="b-icon"><i class="fa fa-lock"></i></div>
                    <h3>Security</h3>
                    <p>Protecting every transaction with enterprise-grade encryption and rigorous compliance standards.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- 5. How We Work -->
    <div class="workflow-section reveal-elem">
        <div class="workflow-header">
            <h2>How We Work</h2>
        </div>
        <div class="workflow-timeline">
            <div class="workflow-line"></div>
            <div class="workflow-line-active"></div>
            
            <div class="workflow-step">
                <div class="step-number">01</div>
                <div class="step-label">Understand</div>
                <h3 class="step-title">Identify Friction</h3>
                <p class="step-desc">We begin by understanding real-world problems before writing a single line of code. We analyze where trust breaks down.</p>
            </div>
            <div class="workflow-step">
                <div class="step-number">02</div>
                <div class="step-label">Build</div>
                <h3 class="step-title">Engineer Solutions</h3>
                <p class="step-desc">We create practical, scalable, and highly secure infrastructure designed to eliminate risk and streamline the journey.</p>
            </div>
            <div class="workflow-step">
                <div class="step-number">03</div>
                <div class="step-label">Improve</div>
                <h3 class="step-title">Iterate & Refine</h3>
                <p class="step-desc">Technology never stands still, and neither do threats. We continuously learn, refine our models, and move forward.</p>
            </div>
        </div>
    </div>
    
    <!-- 5.5 Ecosystem -->
    <div class="ecosystem-section reveal-elem">
        <div class="eco-grid">
            <div class="eco-card">
                <div class="eco-icon"><i class="fa fa-shield-check"></i></div>
                <h4>Secure Transactions</h4>
                <p>Protecting the entire transaction process end-to-end.</p>
            </div>
            <div class="eco-card">
                <div class="eco-icon"><i class="fa fa-receipt"></i></div>
                <h4>Transparent Charges</h4>
                <p>Clear, upfront, and understandable pricing always.</p>
            </div>
            <div class="eco-card">
                <div class="eco-icon"><i class="fa fa-id-badge"></i></div>
                <h4>User Profiles</h4>
                <p>Helping people discover and verify trusted users.</p>
            </div>
            <div class="eco-card">
                <div class="eco-icon"><i class="fa fa-plug"></i></div>
                <h4>Digital Services</h4>
                <p>Connecting users with useful verified services.</p>
            </div>
        </div>
    </div>

    <!-- Stats Section -->
    <div class="stats-section reveal-elem">
        <div class="stats-container">
            <div class="stats-grid">
                <div>
                    <div class="stat-val"><span class="counter" data-target="15000">0</span></div>
                    <div class="stat-lbl">Trusted Users</div>
                </div>
                <div>
                    <div class="stat-val"><span class="counter" data-target="7.5" data-decimals="1">0</span>M+</div>
                    <div class="stat-lbl">Transaction Value</div>
                </div>
                <div>
                    <div class="stat-val"><span class="counter" data-target="99.8" data-decimals="1">0</span>%</div>
                    <div class="stat-lbl">Success Rate</div>
                </div>
                <div>
                    <div class="stat-val"><span class="counter" data-target="24">0</span>/7</div>
                    <div class="stat-lbl">Dedicated Support</div>
                </div>
            </div>
        </div>
    </div>

    <!-- 6. Partners / People -->
    <div class="partners-preview reveal-elem">
        <h2>Built Around People, Not Just Transactions</h2>
        <p>Migratepay is designed to make digital transactions safer, simpler, and more accessible by a visionary team of fintech architects.</p>
        
        <div class="avatars-wrap">
            <div class="avatar-card">
                <img src="/assets/images/Md%20Torikul%20islam%20Torik.png" alt="Torik" class="avatar" onerror="this.src='https://ui-avatars.com/api/?name=Torik&background=3b82f6&color=fff'">
                <div class="avatar-name">Md Torikul Islam Torik</div>
                <div class="avatar-role">Founder & CEO</div>
            </div>
            <div class="avatar-card">
                <img src="/assets/images/H.%20M%20Faisal%20Arafin.png" alt="Faisal" class="avatar" onerror="this.src='https://ui-avatars.com/api/?name=Faisal&background=8b5cf6&color=fff'">
                <div class="avatar-name">H. M. Faisal Arafin</div>
                <div class="avatar-role">Co-Founder & CTO</div>
            </div>
        </div>
        
        <a href="/our-teams" class="simple-link">Meet Our Full Team &rarr;</a>
    </div>

    <!-- 7. Closing CTA -->
    <div class="closing-cta reveal-elem">
        <div class="cta-glow"></div>
        <h2>Ready to Move Forward With Migratepay?</h2>
        <p>Join a platform built around secure transactions, transparency and better digital experiences.</p>
        <div style="display: flex; gap: 16px; justify-content: center;">
            <a href="/register" class="btn-solid">Get Started &rarr;</a>
            <a href="/" class="btn-solid" style="background: rgba(255,255,255,0.1); color: white; border: 1px solid rgba(255,255,255,0.2);">Explore Migratepay</a>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        // Scroll Reveal
        const observerOptions = {
            root: null,
            rootMargin: '0px',
            threshold: 0.1
        };

        const observer = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('in-view');
                    
                    // Trigger counters if it's the stats section
                    if (entry.target.classList.contains('stats-section')) {
                        const counters = entry.target.querySelectorAll('.counter');
                        counters.forEach(counter => {
                            const target = parseFloat(counter.getAttribute('data-target'));
                            const decimals = parseInt(counter.getAttribute('data-decimals') || 0);
                            const duration = 1500; // ms
                            const fps = 60;
                            const steps = duration / (1000 / fps);
                            const increment = target / steps;
                            let current = 0;
                            
                            const updateCounter = setInterval(() => {
                                current += increment;
                                if (current >= target) {
                                    counter.innerText = target.toFixed(decimals) + (target === 15000 ? '+' : '');
                                    clearInterval(updateCounter);
                                } else {
                                    counter.innerText = current.toFixed(decimals);
                                }
                            }, 1000 / fps);
                        });
                    }
                    
                    observer.unobserve(entry.target);
                }
            });
        }, observerOptions);

        document.querySelectorAll('.reveal-elem').forEach(elem => {
            observer.observe(elem);
        });
    });
</script>
@endsection
