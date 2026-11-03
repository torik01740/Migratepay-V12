@extends('layouts.frontend')
@section('title', 'Meet Our Partners | Migratepay')

@section('content')
<style>
    /* Custom Styling for Partners Page */
    .partners-page {
        background-color: #0a0a0c; /* Deep Charcoal */
        color: #f1f5f9;
        font-family: 'Inter', sans-serif;
        overflow: hidden;
    }

    .partners-page h1, .partners-page h2, .partners-page h3, .partners-page h4, .partners-page h5 {
        font-family: 'Lexend', sans-serif;
    }

    /* Typography */
    .text-accent { color: #006A4E; }
    .text-muted { color: #9ca3af; }

    /* Hero Section */
    .partners-hero {
        padding: 200px 24px 100px;
        text-align: center;
        position: relative;
    }

    .hero-label {
        font-size: 12px;
        font-weight: 800;
        letter-spacing: 0.2em;
        text-transform: uppercase;
        color: #006A4E;
        margin-bottom: 24px;
        display: inline-block;
        animation: fadeInDown 1s ease forwards;
    }

    .partners-hero h1 {
        font-size: clamp(3rem, 8vw, 6rem);
        font-weight: 900;
        line-height: 1;
        letter-spacing: -0.04em;
        margin-bottom: 24px;
        text-transform: uppercase;
        animation: fadeInUp 1s ease forwards;
    }

    .partners-hero p {
        font-size: clamp(1.2rem, 2vw, 1.5rem);
        color: #9ca3af;
        max-width: 600px;
        margin: 0 auto;
        animation: fadeInUp 1.2s ease forwards;
    }

    /* Partner Sections */
    .partner-section {
        padding: 120px 24px;
        max-width: 1400px;
        margin: 0 auto;
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 80px;
        align-items: center;
        position: relative;
    }

    .partner-section.reverse {
        grid-template-columns: 1fr 1fr;
    }

    /* Image Treatment */
    .partner-image-wrapper {
        position: relative;
        aspect-ratio: 4/5;
        overflow: hidden;
        border-radius: 4px;
        box-shadow: 0 30px 60px rgba(0,0,0,0.4);
        transform: translateY(40px);
        opacity: 0;
        transition: transform 1s cubic-bezier(0.16, 1, 0.3, 1), opacity 1s ease;
    }

    .partner-image-wrapper.in-view {
        transform: translateY(0);
        opacity: 1;
    }

    .partner-image-wrapper img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 1.5s cubic-bezier(0.16, 1, 0.3, 1);
        filter: contrast(1.1) saturate(0.9);
    }

    .partner-image-wrapper:hover img {
        transform: scale(1.05);
    }

    /* Partner Details */
    .partner-details {
        transform: translateY(40px);
        opacity: 0;
        transition: transform 1s cubic-bezier(0.16, 1, 0.3, 1) 0.2s, opacity 1s ease 0.2s;
    }

    .partner-details.in-view {
        transform: translateY(0);
        opacity: 1;
    }

    .watermark {
        position: absolute;
        font-size: 20rem;
        font-weight: 900;
        color: rgba(255, 255, 255, 0.02);
        top: -100px;
        left: -50px;
        z-index: 0;
        pointer-events: none;
        line-height: 1;
    }

    .reverse .watermark {
        left: auto;
        right: -50px;
    }

    .partner-details-content {
        position: relative;
        z-index: 1;
    }

    .partner-role {
        font-size: 11px;
        font-weight: 800;
        letter-spacing: 0.15em;
        text-transform: uppercase;
        color: #006A4E;
        margin-bottom: 16px;
        display: block;
    }

    .partner-name {
        font-size: clamp(2.5rem, 5vw, 4rem);
        font-weight: 800;
        letter-spacing: -0.02em;
        margin-bottom: 32px;
        line-height: 1.1;
    }

    .partner-bio {
        font-size: 1.125rem;
        line-height: 1.8;
        color: #9ca3af;
        margin-bottom: 40px;
    }

    .partner-stats {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 24px;
        margin-bottom: 40px;
        padding-top: 40px;
        border-top: 1px solid rgba(255,255,255,0.1);
    }

    .stat-label {
        font-size: 10px;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        color: #64748B;
        margin-bottom: 8px;
        display: block;
    }

    .stat-value {
        font-size: 16px;
        font-weight: 600;
        color: #f1f5f9;
    }

    .partner-social {
        display: flex;
        gap: 16px;
    }

    .social-link {
        width: 48px;
        height: 48px;
        border: 1px solid rgba(255,255,255,0.1);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #f1f5f9;
        transition: all 0.3s ease;
    }

    .social-link:hover {
        background: #006A4E;
        border-color: #006A4E;
        transform: translateY(-4px);
    }

    /* Rest of the Squad */
    .squad-section {
        background: #FFFFFF; /* Transitioning back to light for the rest of the site context */
        color: #002147;
        padding: 120px 0;
    }

    .squad-header {
        text-align: center;
        margin-bottom: 80px;
    }

    .squad-header h2 {
        font-size: 2.5rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: -0.02em;
        margin-bottom: 16px;
    }

    .squad-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 40px;
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 24px;
    }

    .squad-card {
        padding: 40px 32px;
        background: #F8FAFC;
        border-radius: 20px;
        text-align: center;
        transition: all 0.4s ease;
        border: 1px solid #F1F5F9;
    }

    .squad-card:hover {
        transform: translateY(-8px);
        background: #FFFFFF;
        box-shadow: 0 20px 40px rgba(0,0,0,0.05);
        border-color: #006A4E;
    }

    .squad-initials {
        width: 64px;
        height: 64px;
        background: #006A4E;
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        font-weight: 800;
        margin: 0 auto 24px;
        font-family: 'Lexend', sans-serif;
    }

    .squad-card h3 {
        font-size: 18px;
        font-weight: 700;
        margin-bottom: 8px;
    }

    .squad-card p {
        font-size: 11px;
        font-weight: 700;
        color: #006A4E;
        text-transform: uppercase;
        letter-spacing: 0.1em;
    }

    /* CTA Section */
    .cta-section {
        background: #002147;
        color: white;
        text-align: center;
        padding: 120px 24px;
    }

    .cta-section h2 {
        font-size: clamp(2rem, 4vw, 3.5rem);
        font-weight: 900;
        margin-bottom: 24px;
    }

    .cta-btn {
        display: inline-flex;
        align-items: center;
        gap: 12px;
        padding: 20px 40px;
        background: #006A4E;
        color: white;
        border-radius: 99px;
        font-weight: 700;
        font-size: 16px;
        transition: all 0.3s ease;
        margin-top: 40px;
    }

    .cta-btn:hover {
        background: #00503A;
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(0, 106, 78, 0.3);
    }

    /* Keyframes */
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(30px); }
        to { opacity: 1; transform: translateY(0); }
    }
    @keyframes fadeInDown {
        from { opacity: 0; transform: translateY(-30px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* Responsive */
    @media (max-width: 992px) {
        .partner-section {
            grid-template-columns: 1fr;
            gap: 40px;
            padding: 80px 24px;
        }
        .partner-section.reverse {
            grid-template-columns: 1fr;
        }
        .reverse .partner-details {
            order: 1;
        }
        .reverse .partner-image-wrapper {
            order: 0;
        }
        .watermark { display: none; }
    }
    @media (prefers-reduced-motion: reduce) {
        *, ::before, ::after {
            animation-delay: -1ms !important;
            animation-duration: 1ms !important;
            animation-iteration-count: 1 !important;
            background-attachment: initial !important;
            scroll-behavior: auto !important;
            transition-duration: 0s !important;
            transition-delay: 0s !important;
        }
    }
</style>

<div class="partners-page">
    <!-- Hero Section -->
    <div class="partners-hero">
        <span class="hero-label">Meet Our Partners</span>
        <h1>The Visionaries</h1>
        <p>Architects behind Bangladesh's most secure and innovative escrow system. Combining deep fintech expertise with uncompromising security.</p>
    </div>

    <!-- Partner 01 -->
    <div class="partner-section" id="partner-1">
        <div class="watermark">01</div>
        <div class="partner-image-wrapper reveal-elem">
            <img src="/assets/images/Md%20Torikul%20islam%20Torik.png" alt="Md Torikul Islam Torik - Managing Partner" onerror="this.src='https://images.unsplash.com/photo-1556157382-97eda2d62296?auto=format&fit=crop&w=800&q=80'">
        </div>
        <div class="partner-details reveal-elem">
            <div class="partner-details-content">
                <span class="partner-role">Managing Partner & Co-Founder</span>
                <h2 class="partner-name">Md Torikul Islam Torik</h2>
                <p class="partner-bio">With a modern mindset and a strong belief in the power of technology, he co-founded Migratepay to build a more seamless and trustworthy digital experience. His focus lies in identifying real-world challenges, developing practical solutions, and shaping ideas that can create lasting impact in the evolving digital economy.</p>
                
                <div class="partner-stats">
                    <div>
                        <span class="stat-label">Focus</span>
                        <span class="stat-value">Strategic Vision, Product Architecture</span>
                    </div>
                    <div>
                        <span class="stat-label">Previous</span>
                        <span class="stat-value">Director at FinTech BD</span>
                    </div>
                </div>

                <div class="partner-social">
                    <a href="#" class="social-link" aria-label="LinkedIn">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/></svg>
                    </a>
                    <a href="mailto:torik@migratepay.com" class="social-link" aria-label="Email">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Partner 02 -->
    <div class="partner-section reverse" id="partner-2">
        <div class="watermark">02</div>
        <div class="partner-details reveal-elem">
            <div class="partner-details-content">
                <span class="partner-role">Managing Partner & Co-Founder</span>
                <h2 class="partner-name">H.M Faisal Arafin</h2>
                <p class="partner-bio">Driven by curiosity, innovation, and a forward-looking perspective, he co-founded Migratepay with a vision to simplify the way people and businesses interact in the digital space. He brings a thoughtful approach to every challenge, turning emerging ideas into meaningful solutions designed for a rapidly changing world.</p>
                
                <div class="partner-stats">
                    <div>
                        <span class="stat-label">Focus</span>
                        <span class="stat-value">Cyber Defense, Escrow Encryption</span>
                    </div>
                    <div>
                        <span class="stat-label">Previous</span>
                        <span class="stat-value">Lead Security Eng, Global Bank</span>
                    </div>
                </div>

                <div class="partner-social">
                    <a href="#" class="social-link" aria-label="LinkedIn">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/></svg>
                    </a>
                    <a href="mailto:faisal@migratepay.com" class="social-link" aria-label="Email">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
                    </a>
                </div>
            </div>
        </div>
        <div class="partner-image-wrapper reveal-elem">
            <img src="/assets/images/H.%20M%20Faisal%20Arafin.png" alt="H.M Faisal Arafin - Managing Partner" onerror="this.src='https://images.unsplash.com/photo-1560250097-0b93528c311a?auto=format&fit=crop&w=800&q=80'">
        </div>
    </div>
</div>

<!-- Rest of the Squad (Preserving Data) -->
<div class="squad-section">
    <div class="squad-header">
        <h2>The Core Squad</h2>
        <p class="text-muted">The brilliant minds powering our operations and customer success.</p>
    </div>
    
    <div class="squad-grid">
        @php
            $squad = [
                ['Sadia Akter', 'HEAD OF DISPUTE OPS'],
                ['Tahmina Akter', 'LEAD FINTECH ARCHITECT'],
                ['Farhana Rahman', 'CHIEF COMPLIANCE OFFICER'],
                ['Mahfuza Akter', 'CUSTOMER SUCCESS HEAD'],
                ['Shahadat Hossain', 'HEAD OF LEGAL'],
                ['Fahim Shahriar', 'SENIOR SYSTEMS ENGINEER']
            ];
        @endphp

        @foreach($squad as $member)
            <div class="squad-card">
                <div class="squad-initials">
                    {{ substr($member[0], 0, 1) }}{{ substr(explode(' ', $member[0])[1] ?? '', 0, 1) }}
                </div>
                <h3>{{ $member[0] }}</h3>
                <p>{{ $member[1] }}</p>
            </div>
        @endforeach
    </div>
</div>

<!-- CTA -->
<div class="cta-section">
    <h2>Great things start with a conversation.</h2>
    <p style="color: #9ca3af; font-size: 1.125rem; max-width: 600px; margin: 0 auto;">Whether you're looking to integrate our escrow system or join our growing team, we'd love to hear from you.</p>
    <a href="/contact" class="cta-btn">
        Start a Conversation
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"></path><path d="M12 5l7 7-7 7"></path></svg>
    </a>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        // Intersection Observer for scroll animations
        const observerOptions = {
            root: null,
            rootMargin: '0px',
            threshold: 0.15
        };

        const observer = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('in-view');
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
