@extends('layouts.frontend')
@section('title', 'Contact Us | Migratepay')

@section('content')
<style>
    .contact-page {
        background: linear-gradient(135deg, #f0f4f8 0%, #f4f0ff 50%, #fef8f5 100%);
        color: #0f172a;
        font-family: 'Inter', sans-serif;
        padding-bottom: 120px;
        overflow-x: hidden;
        position: relative;
    }

    h1, h2, h3, h4, h5 {
        font-family: 'Lexend', sans-serif;
    }

    /* Ambient Background Motion */
    .ambient-bg {
        position: absolute;
        inset: 0;
        overflow: hidden;
        z-index: 0;
        pointer-events: none;
    }

    .ambient-orb {
        position: absolute;
        border-radius: 50%;
        filter: blur(120px);
        opacity: 0.4;
        mix-blend-mode: multiply;
    }

    .orb-blue { top: -10%; left: 0%; width: 800px; height: 800px; background: #3b82f6; animation: drift1 25s infinite alternate ease-in-out; }
    .orb-purple { top: 30%; right: -10%; width: 700px; height: 700px; background: #8b5cf6; animation: drift2 30s infinite alternate ease-in-out; }
    .orb-cyan { bottom: -10%; left: 20%; width: 600px; height: 600px; background: #06b6d4; animation: drift3 28s infinite alternate ease-in-out; }
    .orb-pink { top: 50%; left: 50%; width: 500px; height: 500px; background: #ec4899; transform: translate(-50%, -50%); animation: drift4 35s infinite alternate ease-in-out; opacity: 0.3; }

    @keyframes drift1 { 0% { transform: translate(0, 0); } 100% { transform: translate(100px, 50px); } }
    @keyframes drift2 { 0% { transform: translate(0, 0); } 100% { transform: translate(-80px, 80px); } }
    @keyframes drift3 { 0% { transform: translate(0, 0); } 100% { transform: translate(60px, -100px); } }
    @keyframes drift4 { 0% { transform: translate(-50%, -50%); } 100% { transform: translate(-30%, -30%); } }

    @keyframes gradientFlow {
        0% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
        100% { background-position: 0% 50%; }
    }

    @keyframes pulseGlow {
        0% { box-shadow: 0 0 0 0 rgba(var(--pulse-rgb), 0.5); }
        70% { box-shadow: 0 0 0 15px rgba(var(--pulse-rgb), 0); }
        100% { box-shadow: 0 0 0 0 rgba(var(--pulse-rgb), 0); }
    }

    /* Scroll Animations */
    .animate-on-scroll { opacity: 0; transform: translateY(30px); transition: opacity 0.8s cubic-bezier(0.16, 1, 0.3, 1), transform 0.8s cubic-bezier(0.16, 1, 0.3, 1); }
    .animate-on-scroll.in-view { opacity: 1; transform: translateY(0); }
    .scale-on-scroll { opacity: 0; transform: scale(0.95); transition: opacity 0.8s cubic-bezier(0.16, 1, 0.3, 1), transform 0.8s cubic-bezier(0.16, 1, 0.3, 1); }
    .scale-on-scroll.in-view { opacity: 1; transform: scale(1); }
    .delay-1 { transition-delay: 0.1s; }
    .delay-2 { transition-delay: 0.2s; }
    .delay-3 { transition-delay: 0.3s; }

    /* Hero Section */
    .contact-hero {
        position: relative;
        padding: 200px 24px 80px;
        text-align: center;
        z-index: 2;
    }

    .contact-hero h1 {
        font-size: clamp(3rem, 7vw, 5rem);
        font-weight: 900;
        color: #0f172a;
        margin-bottom: 16px;
        letter-spacing: -0.04em;
        line-height: 1.1;
    }
    
    .contact-hero h1 span {
        background: linear-gradient(135deg, #3b82f6, #8b5cf6, #ec4899);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-size: 200% auto;
        animation: gradientFlow 6s infinite linear;
    }

    .contact-hero h2 {
        font-size: clamp(1.25rem, 3vw, 1.75rem);
        font-weight: 500;
        color: #475569;
        margin-bottom: 24px;
        letter-spacing: -0.01em;
    }

    /* Main Layout */
    .contact-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 24px;
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 80px;
        position: relative;
        z-index: 2;
    }

    /* Floating Connect */
    .floating-connect {
        position: absolute;
        left: 50%;
        top: 200px;
        transform: translate(-50%, -50%);
        background: rgba(255, 255, 255, 0.8);
        backdrop-filter: blur(16px);
        padding: 12px 24px;
        border-radius: 100px;
        box-shadow: 0 20px 40px rgba(139, 92, 246, 0.1);
        border: 1px solid rgba(255,255,255,0.6);
        display: flex;
        align-items: center;
        gap: 12px;
        font-weight: 800;
        font-size: 0.875rem;
        color: #0f172a;
        z-index: 10;
        animation: drift1 10s infinite alternate ease-in-out;
    }
    .floating-connect svg {
        color: #8b5cf6;
    }
    @media (max-width: 992px) { .floating-connect { display: none; } }

    /* Left Side */
    .contact-left { display: flex; flex-direction: column; gap: 60px; }

    .section-label {
        font-size: 0.75rem;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: 0.15em;
        color: #64748b;
        margin-bottom: 24px;
    }

    /* Contact Info Grid */
    .contact-info-grid { display: flex; flex-direction: column; gap: 20px; }

    .contact-info-item {
        position: relative;
        display: flex;
        align-items: center;
        gap: 24px;
        padding: 24px;
        background: rgba(255, 255, 255, 0.6);
        backdrop-filter: blur(12px);
        border-radius: 24px;
        border: 1px solid rgba(255,255,255,0.8);
        box-shadow: 0 10px 30px rgba(0,0,0,0.02);
        transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
        overflow: hidden;
        text-decoration: none;
    }

    .item-email { --accent-color: #3b82f6; --accent-rgb: 59, 130, 246; }
    .item-phone { --accent-color: #8b5cf6; --accent-rgb: 139, 92, 246; }
    .item-location { --accent-color: #ec4899; --accent-rgb: 236, 72, 153; }

    .contact-info-item:hover {
        transform: translateY(-4px) scale(1.01);
        background: rgba(255, 255, 255, 0.9);
        box-shadow: 0 20px 40px rgba(var(--accent-rgb), 0.1);
        border-color: rgba(var(--accent-rgb), 0.3);
    }

    .contact-info-item::after {
        content: '→';
        position: absolute;
        right: 24px;
        top: 50%;
        transform: translateY(-50%) translateX(10px);
        opacity: 0;
        color: var(--accent-color);
        font-size: 1.25rem;
        font-weight: bold;
        transition: all 0.3s ease;
    }
    .contact-info-item:hover::after { transform: translateY(-50%) translateX(0); opacity: 1; }

    .info-icon {
        width: 60px;
        height: 60px;
        border-radius: 18px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        flex-shrink: 0;
        background: linear-gradient(135deg, rgba(var(--accent-rgb), 0.1), rgba(var(--accent-rgb), 0.05));
        color: var(--accent-color);
        transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
    }
    .contact-info-item:hover .info-icon {
        background: linear-gradient(135deg, var(--accent-color), rgba(var(--accent-rgb), 0.8));
        color: white;
        transform: rotate(10deg) scale(1.05);
        box-shadow: 0 10px 20px rgba(var(--accent-rgb), 0.2);
    }

    .info-text { transition: transform 0.3s ease; }
    .contact-info-item:hover .info-text { transform: translateX(5px); }

    .info-text h4 { font-size: 0.875rem; font-weight: 800; color: #0f172a; margin-bottom: 4px; }
    .info-text p { font-size: 1rem; color: #64748b; font-weight: 500; }

    /* Form Container */
    .form-wrapper { position: relative; }

    .form-halo {
        position: absolute;
        top: -15%; left: -15%; right: -15%; bottom: -15%;
        background: radial-gradient(circle, rgba(59,130,246,0.15) 0%, rgba(139,92,246,0.15) 30%, rgba(236,72,153,0.1) 60%, transparent 80%);
        filter: blur(40px);
        z-index: 0;
        pointer-events: none;
    }

    .contact-form-container {
        position: relative;
        z-index: 1;
        background: rgba(255, 255, 255, 0.7);
        backdrop-filter: blur(24px);
        -webkit-backdrop-filter: blur(24px);
        border: 1px solid rgba(255, 255, 255, 0.9);
        box-shadow: 0 30px 60px rgba(139, 92, 246, 0.08), inset 0 0 0 1px rgba(255, 255, 255, 0.5);
        border-radius: 32px;
        padding: 48px;
    }

    .form-header {
        position: relative;
        margin-bottom: 32px;
        padding-bottom: 24px;
        border-bottom: 1px solid rgba(139, 92, 246, 0.1);
    }

    .form-header-bg {
        position: absolute;
        top: -48px; left: -48px; right: -48px; height: 140px;
        background: radial-gradient(circle at top right, rgba(139, 92, 246, 0.08) 0%, transparent 70%);
        border-radius: 32px 32px 0 0;
        z-index: -1;
    }

    .form-header h3 {
        font-size: 1.5rem;
        font-weight: 900;
        color: #0f172a;
        margin-bottom: 8px;
        letter-spacing: -0.02em;
    }
    
    .form-header p {
        font-size: 0.9375rem;
        color: #64748b;
    }

    .form-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 24px;
        margin-bottom: 32px;
    }

    .input-wrapper {
        position: relative;
        margin-bottom: 24px;
    }

    .input-wrapper.full-width { grid-column: 1 / -1; }

    .input-label {
        display: block;
        font-size: 0.75rem;
        font-weight: 800;
        color: #64748b;
        margin-bottom: 8px;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        transition: color 0.3s ease;
    }

    .input-wrapper:focus-within .input-label { color: #8b5cf6; }

    .input-box {
        position: relative;
        border-radius: 16px;
        background: rgba(248, 250, 252, 0.6);
        border: 1px solid rgba(226, 232, 240, 0.8);
        transition: all 0.3s ease;
        box-shadow: inset 0 2px 4px rgba(0,0,0,0.01);
    }

    .input-box::before {
        content: '';
        position: absolute;
        inset: -2px;
        border-radius: 18px;
        background: linear-gradient(135deg, #3b82f6, #8b5cf6, #ec4899);
        background-size: 200% auto;
        z-index: -1;
        opacity: 0;
        transition: opacity 0.4s ease;
        animation: gradientFlow 3s infinite linear;
    }

    .input-wrapper:hover .input-box {
        background: rgba(255, 255, 255, 0.9);
        border-color: rgba(139, 92, 246, 0.3);
    }

    .input-wrapper:focus-within .input-box {
        background: #ffffff;
        border-color: transparent;
        box-shadow: 0 10px 20px rgba(139, 92, 246, 0.05), inset 0 2px 4px rgba(0,0,0,0.01);
    }

    .input-wrapper:focus-within .input-box::before { opacity: 1; }

    .modern-input {
        width: 100%;
        background: transparent;
        border: none;
        padding: 16px 20px;
        border-radius: 16px;
        font-family: 'Inter', sans-serif;
        font-size: 0.9375rem;
        color: #0f172a;
        position: relative;
        z-index: 2;
    }

    .modern-input::placeholder { color: #94a3b8; }
    .modern-input:focus { outline: none; }

    textarea.modern-input {
        resize: vertical;
        min-height: 140px;
    }

    /* Submit Button */
    .btn-submit {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 12px;
        background: linear-gradient(135deg, #3b82f6, #8b5cf6, #ec4899, #3b82f6);
        background-size: 300% auto;
        color: #ffffff;
        padding: 20px 48px;
        border-radius: 100px;
        font-weight: 800;
        font-size: 1rem;
        border: none;
        cursor: pointer;
        transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
        width: 100%;
        box-shadow: 0 10px 25px rgba(139, 92, 246, 0.3);
    }

    .btn-submit:hover {
        background-position: right center;
        transform: translateY(-4px);
        box-shadow: 0 20px 40px rgba(139, 92, 246, 0.4);
    }
    
    .btn-submit:active { transform: translateY(0); }

    .btn-submit svg { transition: transform 0.3s ease; }
    .btn-submit:hover svg { transform: translateX(6px); }

    .btn-submit:disabled { opacity: 0.7; cursor: not-allowed; animation: none; transform: none; }
    
    .form-message {
        margin-top: 24px;
        padding: 16px;
        border-radius: 16px;
        font-size: 0.9375rem;
        font-weight: 600;
        display: none;
        animation: fadeInUp 0.4s ease;
        text-align: center;
    }
    .form-message.success { display: block; background: rgba(16, 185, 129, 0.1); color: #10b981; border: 1px solid rgba(16, 185, 129, 0.2); }

    /* Right Side: Map & Info */
    .contact-right { display: flex; flex-direction: column; gap: 40px; }

    .map-wrapper {
        position: relative;
        padding: 6px;
        border-radius: 32px;
        background: linear-gradient(135deg, #3b82f6, #06b6d4, #8b5cf6, #ec4899);
        background-size: 300% auto;
        animation: gradientFlow 15s infinite linear;
        box-shadow: 0 30px 60px rgba(139, 92, 246, 0.15);
        transition: transform 0.4s ease;
    }
    
    .map-wrapper:hover { transform: translateY(-5px); }

    .map-container {
        position: relative;
        width: 100%;
        height: 480px;
        border-radius: 26px;
        overflow: hidden;
        background: #ffffff;
    }

    .map-overlay-label {
        position: absolute;
        top: 24px;
        left: 24px;
        background: rgba(255, 255, 255, 0.9);
        backdrop-filter: blur(12px);
        padding: 12px 24px;
        border-radius: 100px;
        font-size: 0.8125rem;
        font-weight: 900;
        color: #0f172a;
        letter-spacing: 0.1em;
        box-shadow: 0 15px 30px rgba(0,0,0,0.1);
        border: 1px solid rgba(255,255,255,0.8);
        z-index: 10;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .map-overlay-label::before {
        content: '';
        display: block;
        width: 12px;
        height: 12px;
        background: #ec4899;
        border-radius: 50%;
        --pulse-rgb: 236, 72, 153;
        animation: pulseGlow 2s infinite;
    }

    .map-iframe {
        width: 100%;
        height: 100%;
        border: none;
        filter: contrast(1.1) saturation(1.1);
    }

    .right-info-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 24px;
    }

    .info-block {
        background: rgba(255, 255, 255, 0.6);
        backdrop-filter: blur(16px);
        padding: 32px 24px;
        border-radius: 24px;
        border: 1px solid rgba(255,255,255,0.8);
        box-shadow: 0 10px 30px rgba(139, 92, 246, 0.05);
    }

    .info-block h4 {
        font-size: 0.75rem;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: 0.15em;
        color: #64748b;
        margin-bottom: 16px;
    }

    .info-block p {
        font-size: 0.9375rem;
        color: #0f172a;
        font-weight: 600;
        line-height: 1.6;
    }

    .social-links-minimal {
        display: flex;
        gap: 12px;
    }

    .social-link {
        width: 48px;
        height: 48px;
        border-radius: 16px;
        background: rgba(255,255,255,0.9);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #475569;
        text-decoration: none;
        border: 1px solid rgba(255,255,255,0.8);
        transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        position: relative;
        overflow: hidden;
        box-shadow: 0 4px 10px rgba(0,0,0,0.02);
    }

    .social-link::before {
        content: '';
        position: absolute;
        bottom: 0; left: 0; right: 0; height: 3px;
        background: linear-gradient(90deg, #3b82f6, #8b5cf6, #ec4899);
        transform: scaleX(0);
        transition: transform 0.3s ease;
    }

    .social-link:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 30px rgba(139, 92, 246, 0.15);
        color: #8b5cf6;
        border-color: rgba(139, 92, 246, 0.2);
    }

    .social-link:hover::before { transform: scaleX(1); }

    /* Bottom CTA Section */
    .bottom-cta-section { max-width: 1200px; margin: 120px auto 0; padding: 0 24px; }

    .cta-inner {
        position: relative;
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
        border-radius: 40px;
        padding: 100px 40px;
        text-align: center;
        color: white;
        overflow: hidden;
        box-shadow: 0 40px 80px rgba(15, 23, 42, 0.2);
    }

    .cta-blob-1 {
        position: absolute; top: -50%; right: -10%; width: 600px; height: 600px;
        background: radial-gradient(circle, rgba(139, 92, 246, 0.3) 0%, transparent 70%);
        border-radius: 50%; filter: blur(80px); z-index: 1; animation: drift2 20s infinite alternate;
    }
    
    .cta-blob-2 {
        position: absolute; bottom: -50%; left: -10%; width: 500px; height: 500px;
        background: radial-gradient(circle, rgba(59, 130, 246, 0.3) 0%, transparent 70%);
        border-radius: 50%; filter: blur(80px); z-index: 1; animation: drift1 18s infinite alternate;
    }

    .cta-content { position: relative; z-index: 2; max-width: 800px; margin: 0 auto; }

    .cta-inner h2 { 
        font-size: clamp(2.5rem, 5vw, 4rem); 
        font-weight: 900; 
        margin-bottom: 24px; 
        line-height: 1.1;
        letter-spacing: -0.02em;
    }
    
    .cta-inner p { font-size: 1.25rem; color: #cbd5e1; margin-bottom: 48px; }

    .btn-cta {
        display: inline-flex;
        align-items: center;
        gap: 12px;
        background: linear-gradient(135deg, #ffffff, #f8fafc);
        color: #0f172a;
        padding: 20px 48px;
        border-radius: 100px;
        font-weight: 900;
        font-size: 1.125rem;
        text-decoration: none;
        transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
        box-shadow: 0 20px 40px rgba(0,0,0,0.1);
    }

    .btn-cta:hover { 
        transform: translateY(-4px) scale(1.02); 
        box-shadow: 0 25px 50px rgba(0,0,0,0.2), 0 0 0 4px rgba(255,255,255,0.1); 
    }

    @keyframes fadeInUp { to { opacity: 1; transform: translateY(0); } }

    /* Responsive */
    @media (max-width: 992px) {
        .contact-container { grid-template-columns: 1fr; gap: 60px; }
        .contact-hero { padding: 160px 24px 60px; }
        .right-info-grid { grid-template-columns: 1fr; }
    }
    @media (max-width: 640px) {
        .form-grid { grid-template-columns: 1fr; }
        .contact-form-container { padding: 32px 24px; }
        .cta-inner { padding: 60px 24px; border-radius: 32px; }
    }
</style>

<div class="contact-page">
    <div class="ambient-bg">
        <div class="ambient-orb orb-blue"></div>
        <div class="ambient-orb orb-purple"></div>
        <div class="ambient-orb orb-cyan"></div>
        <div class="ambient-orb orb-pink"></div>
    </div>

    <section class="contact-hero">
        <div class="contact-hero-content animate-on-scroll">
            <h1>Let's <span>Talk</span></h1>
            <h2>Have an idea? Let's make it happen.</h2>
            <p>Reach out to our team regarding partnerships, escrow deals, or general inquiries. We'll get back to you shortly.</p>
        </div>
    </section>

    <div class="contact-container">
        
        <div class="floating-connect">
            <span>LET'S CONNECT</span>
            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M12 5v14"></path><path d="M19 12l-7 7-7-7"></path></svg>
        </div>

        <!-- Left Side: Contact Info & Form -->
        <div class="contact-left">
            
            <div class="contact-info-top animate-on-scroll delay-1">
                <div class="section-label">Get in Touch</div>
                <div class="contact-info-grid">
                    <a href="mailto:support@migratepay.com" class="contact-info-item item-email">
                        <div class="info-icon">
                            <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
                        </div>
                        <div class="info-text">
                            <h4>Email Us</h4>
                            <p>support@migratepay.com</p>
                        </div>
                    </a>
                    <a href="tel:+8801712345678" class="contact-info-item item-phone">
                        <div class="info-icon">
                            <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
                        </div>
                        <div class="info-text">
                            <h4>Call Us</h4>
                            <p>+880 1712-345678</p>
                        </div>
                    </a>
                    <div class="contact-info-item item-location">
                        <div class="info-icon">
                            <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                        </div>
                        <div class="info-text">
                            <h4>Location</h4>
                            <p>12/A, Dhanmondi R/A, Dhaka-1209</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-wrapper animate-on-scroll delay-2">
                <div class="form-halo"></div>
                <div class="contact-form-container">
                    
                    <div class="form-header">
                        <div class="form-header-bg"></div>
                        <h3>Send Us a Message</h3>
                        <p>Fill out the form below and our team will get back to you promptly.</p>
                    </div>

                    <form id="contactForm" class="modern-form">
                        <div class="form-grid">
                            <div class="input-wrapper">
                                <label class="input-label" for="fullName">Full Name</label>
                                <div class="input-box">
                                    <input type="text" id="fullName" class="modern-input" placeholder="John Doe" required>
                                </div>
                            </div>
                            <div class="input-wrapper">
                                <label class="input-label" for="email">Email Address</label>
                                <div class="input-box">
                                    <input type="email" id="email" class="modern-input" placeholder="john@example.com" required>
                                </div>
                            </div>
                            <div class="input-wrapper full-width">
                                <label class="input-label" for="phone">Phone Number</label>
                                <div class="input-box">
                                    <input type="tel" id="phone" class="modern-input" placeholder="+880 1XXXXXXXXX">
                                </div>
                            </div>
                            <div class="input-wrapper full-width">
                                <label class="input-label" for="subject">Subject</label>
                                <div class="input-box">
                                    <input type="text" id="subject" class="modern-input" placeholder="How can we help?" required>
                                </div>
                            </div>
                            <div class="input-wrapper full-width">
                                <label class="input-label" for="message">Message</label>
                                <div class="input-box">
                                    <textarea id="message" class="modern-input" placeholder="Tell us about your project or inquiry..." required></textarea>
                                </div>
                            </div>
                        </div>
                        
                        <button type="submit" id="submitBtn" class="btn-submit">
                            <span>Send Message</span>
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                        </button>
                        
                        <div id="formStatus" class="form-message"></div>
                    </form>
                </div>
            </div>
            
        </div>

        <!-- Right Side: Map & Info -->
        <div class="contact-right">
            <div class="map-wrapper scale-on-scroll delay-1">
                <div class="map-container">
                    <div class="map-overlay-label">
                        WE ARE HERE
                    </div>
                    <!-- Google Maps Placeholder/Embed -->
                    <iframe class="map-iframe" src="https://maps.google.com/maps?q=Dhanmondi,Dhaka,Bangladesh&t=&z=14&ie=UTF8&iwloc=&output=embed" allowfullscreen="" loading="lazy"></iframe>
                </div>
            </div>

            <div class="right-info-grid animate-on-scroll delay-3">
                <div class="info-block">
                    <h4>Office Hours</h4>
                    <p>Sunday – Thursday<br>9:00 AM – 6:00 PM</p>
                </div>
                <div class="info-block">
                    <h4>Follow Us</h4>
                    <div class="social-links-minimal">
                        <a href="#" class="social-link" aria-label="LinkedIn">
                            <svg width="20" height="20" fill="currentColor" viewBox="0 0 24 24"><path d="M16 8a6 6 0 016 6v7h-4v-7a2 2 0 00-2-2 2 2 0 00-2 2v7h-4v-7a6 6 0 016-6zM2 9h4v12H2z"/><circle cx="4" cy="4" r="2"/></svg>
                        </a>
                        <a href="#" class="social-link" aria-label="X / Twitter">
                            <svg width="18" height="18" fill="currentColor" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                        </a>
                        <a href="#" class="social-link" aria-label="Facebook">
                            <svg width="20" height="20" fill="currentColor" viewBox="0 0 24 24"><path d="M18 2h-3a5 5 0 00-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 011-1h3z"/></svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Colorful Bottom CTA -->
    <section class="bottom-cta-section animate-on-scroll">
        <div class="cta-inner">
            <div class="cta-blob-1"></div>
            <div class="cta-blob-2"></div>
            <div class="cta-content">
                <h2>LET'S BUILD SOMETHING GREAT.</h2>
                <p>Start a conversation today and discover how Migratepay can transform your digital transactions with secure escrow solutions.</p>
                <a href="#contactForm" class="btn-cta">
                    Start a Conversation &rarr;
                </a>
            </div>
        </div>
    </section>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    
    // Intersection Observer for Scroll Animations
    const observerOptions = {
        root: null,
        rootMargin: '0px 0px -100px 0px',
        threshold: 0.1
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if(entry.isIntersecting) {
                entry.target.classList.add('in-view');
                observer.unobserve(entry.target);
            }
        });
    }, observerOptions);

    document.querySelectorAll('.animate-on-scroll, .scale-on-scroll').forEach(el => {
        observer.observe(el);
    });

    // Form Handling
    const form = document.getElementById('contactForm');
    const statusDiv = document.getElementById('formStatus');
    const submitBtn = document.getElementById('submitBtn');
    const btnText = submitBtn.querySelector('span');

    form.addEventListener('submit', (e) => {
        e.preventDefault();
        
        const fullName = document.getElementById('fullName').value.trim();
        const email = document.getElementById('email').value.trim();
        const phone = document.getElementById('phone').value.trim();
        const subject = document.getElementById('subject').value.trim();
        const message = document.getElementById('message').value.trim();

        submitBtn.disabled = true;
        btnText.textContent = 'Sending...';
        statusDiv.className = 'form-message';
        statusDiv.textContent = '';
        
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

        const payload = {
            guest_name: fullName,
            guest_email: email,
            subject: subject,
            message: phone ? `${message}\n\nPhone: ${phone}` : message,
            priority: 'medium'
        };

        fetch('/api/support-tickets', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify(payload)
        })
        .then(async res => {
            const data = await res.json().catch(() => ({}));
            if (res.ok && data.success) {
                const ticketNumber = data.ticket?.ticket_number || '';
                statusDiv.textContent = ticketNumber 
                    ? `Thank you! Your inquiry #${ticketNumber} has been received. Our team will get back to you shortly.`
                    : 'Thank you! Your message has been sent successfully. We will get back to you shortly.';
                statusDiv.className = 'form-message success';
                form.reset();
            } else {
                const errorMsg = data.message || Object.values(data.errors || {})[0]?.[0] || 'Unable to submit your message right now. Please try again or email us directly.';
                statusDiv.textContent = errorMsg;
                statusDiv.className = 'form-message error';
            }
        })
        .catch(err => {
            statusDiv.textContent = 'A network error occurred. Please verify your connection or email support@migratepay.com.';
            statusDiv.className = 'form-message error';
        })
        .finally(() => {
            submitBtn.disabled = false;
            btnText.textContent = 'Send Message';
        });
    });
});
</script>
@endsection
