<style>
/* ================================================================
   MIGRATEPAY PREMIUM FOOTER
   Deep navy environment, atmospheric lighting, glowing hover states
================================================================ */
.premium-footer {
    position: relative;
    background: #0A1128;
    color: #CBD5E1;
    overflow: hidden;
    padding: 100px 0 40px;
    font-family: 'Inter', sans-serif;
    border-top: 1px solid rgba(37, 99, 235, 0.2);
    z-index: 10;
}

/* Base gradients and floating glow orbs */
.premium-footer::before {
    content: '';
    position: absolute;
    inset: 0;
    background: linear-gradient(180deg, rgba(10,17,40,1) 0%, rgba(10,17,40,0.85) 100%);
    z-index: -3;
    pointer-events: none;
}

.pf-glow-blue {
    position: absolute;
    width: 700px;
    height: 700px;
    background: radial-gradient(circle, rgba(37, 99, 235, 0.12) 0%, transparent 60%);
    top: -200px;
    left: -150px;
    border-radius: 50%;
    filter: blur(60px);
    z-index: -2;
    animation: pf-drift 25s infinite alternate ease-in-out;
    pointer-events: none;
}

.pf-glow-indigo {
    position: absolute;
    width: 600px;
    height: 600px;
    background: radial-gradient(circle, rgba(99, 102, 241, 0.08) 0%, transparent 60%);
    bottom: -150px;
    right: -100px;
    border-radius: 50%;
    filter: blur(60px);
    z-index: -2;
    animation: pf-drift-reverse 30s infinite alternate-reverse ease-in-out;
    pointer-events: none;
}

.pf-glow-cyan {
    position: absolute;
    width: 450px;
    height: 450px;
    background: radial-gradient(circle, rgba(6, 182, 212, 0.06) 0%, transparent 60%);
    top: 30%;
    left: 35%;
    border-radius: 50%;
    filter: blur(50px);
    z-index: -2;
    animation: pf-drift-cyan 35s infinite alternate ease-in-out;
    pointer-events: none;
}

/* Top Divider Glow */
.pf-top-divider {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 2px;
    background: linear-gradient(90deg, transparent 0%, rgba(37,99,235,0.6) 50%, transparent 100%);
    opacity: 0.7;
    z-index: 1;
}

@keyframes pf-drift {
    0% { transform: translate(0, 0); }
    100% { transform: translate(120px, 80px); }
}
@keyframes pf-drift-reverse {
    0% { transform: translate(0, 0); }
    100% { transform: translate(-100px, -60px); }
}
@keyframes pf-drift-cyan {
    0% { transform: translate(0, 0); }
    100% { transform: translate(-70px, 90px); }
}

/* Floating Background Icons */
.pf-bg-icons {
    position: absolute;
    inset: 0;
    z-index: -1;
    overflow: hidden;
    pointer-events: none;
}
.pf-bg-icon {
    position: absolute;
    color: rgba(255, 255, 255, 0.02);
    animation: pf-float var(--dur, 20s) infinite ease-in-out alternate;
}
@keyframes pf-float {
    0% { transform: translateY(0) rotate(0deg); }
    100% { transform: translateY(-30px) rotate(15deg); }
}

/* Grid Layout */
.pf-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 24px;
    position: relative;
    z-index: 2;
}

.pf-grid {
    display: grid;
    grid-template-columns: 2fr 1fr 1fr 1fr;
    gap: 50px;
    margin-bottom: 80px;
}

/* Brand Section */
.pf-brand .pf-logo {
    display: flex;
    align-items: center;
    font-size: 26px;
    font-weight: 800;
    color: #FFFFFF;
    margin-bottom: 24px;
    letter-spacing: -0.02em;
    text-decoration: none;
}
.pf-brand .pf-logo svg {
    margin-right: 12px;
    filter: drop-shadow(0 0 15px rgba(37,99,235,0.6));
}
.pf-brand p {
    font-size: 15px;
    line-height: 1.8;
    color: #94A3B8;
    max-width: 320px;
    margin-bottom: 30px;
}

/* Social Icons */
.pf-socials {
    display: flex;
    gap: 16px;
}
.pf-social-btn {
    width: 44px;
    height: 44px;
    border-radius: 12px;
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 255, 255, 0.1);
    display: flex;
    align-items: center;
    justify-content: center;
    color: #CBD5E1;
    transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
    backdrop-filter: blur(10px);
}
.pf-social-btn:hover {
    background: linear-gradient(135deg, #2563EB, #6366F1);
    border-color: transparent;
    color: #FFFFFF;
    transform: translateY(-5px) scale(1.05);
    box-shadow: 0 10px 25px rgba(37, 99, 235, 0.5);
}
.pf-social-btn svg {
    width: 20px;
    height: 20px;
    fill: currentColor;
    transition: all 0.3s;
}

/* Links Columns */
.pf-col-title {
    color: #FFFFFF;
    font-size: 16px;
    font-weight: 800;
    margin-bottom: 28px;
    letter-spacing: 0.05em;
    text-transform: uppercase;
}
.pf-links {
    list-style: none;
    padding: 0;
    margin: 0;
    display: flex;
    flex-direction: column;
    gap: 16px;
}
.pf-links a {
    color: #94A3B8;
    font-size: 15px;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    transition: all 0.3s ease;
    position: relative;
}
.pf-links a::before {
    content: '→';
    position: absolute;
    left: -20px;
    color: #06B6D4;
    opacity: 0;
    transform: translateX(-10px);
    transition: all 0.3s ease;
    font-weight: bold;
}
.pf-links a:hover {
    color: #06B6D4;
    transform: translateX(18px);
    text-shadow: 0 0 10px rgba(6, 182, 212, 0.4);
}
.pf-links a:hover::before {
    opacity: 1;
    transform: translateX(0);
}

/* Trust Card in Legal Column */
.pf-trust-card {
    margin-top: 30px;
    background: rgba(255, 255, 255, 0.03);
    border: 1px solid rgba(255, 255, 255, 0.05);
    border-radius: 12px;
    padding: 16px;
    display: flex;
    align-items: center;
    gap: 12px;
    backdrop-filter: blur(10px);
    transition: all 0.3s ease;
}
.pf-trust-card:hover {
    background: rgba(255, 255, 255, 0.06);
    border-color: rgba(37, 99, 235, 0.3);
    box-shadow: 0 5px 15px rgba(37,99,235,0.1);
}
.pf-trust-icon {
    width: 32px;
    height: 32px;
    background: rgba(16, 185, 129, 0.1);
    color: #10B981;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
}
.pf-trust-text {
    font-size: 13px;
    font-weight: 600;
    color: #E2E8F0;
    line-height: 1.4;
}
.pf-trust-text span {
    display: block;
    color: #94A3B8;
    font-size: 11px;
    font-weight: 400;
}

/* Bottom Bar */
.pf-bottom {
    border-top: 1px solid rgba(255, 255, 255, 0.08);
    padding-top: 30px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 14px;
    color: #64748B;
}
.pf-bottom-links {
    display: flex;
    gap: 20px;
}
.pf-bottom-links a {
    color: #64748B;
    text-decoration: none;
    transition: color 0.3s;
}
.pf-bottom-links a:hover {
    color: #FFFFFF;
}

/* Responsive */
@media (max-width: 1024px) {
    .pf-grid { grid-template-columns: 1fr 1fr; gap: 40px; }
    .pf-brand { grid-column: span 2; }
    .pf-bottom { flex-direction: column; text-align: center; gap: 16px; }
}
@media (max-width: 768px) {
    .pf-grid { grid-template-columns: 1fr; gap: 40px; }
    .pf-brand { grid-column: span 1; }
    .pf-links a:hover { transform: translateX(10px); }
    .pf-links a:hover::before { display: none; }
}

@media (prefers-reduced-motion: reduce) {
    .pf-glow-blue, .pf-glow-indigo, .pf-glow-cyan, .pf-bg-icon {
        animation: none !important;
    }
    .pf-links a, .pf-social-btn {
        transition: none !important;
    }
    .pf-links a:hover {
        transform: none !important;
    }
    .pf-links a::before {
        display: none !important;
    }
}
</style>

<footer class="premium-footer">
    <!-- Atmospheric glows -->
    <div class="pf-glow-blue"></div>
    <div class="pf-glow-indigo"></div>
    <div class="pf-glow-cyan"></div>
    <div class="pf-top-divider"></div>

    <!-- Floating Background Elements -->
    <div class="pf-bg-icons">
        <svg class="pf-bg-icon" style="top: 20%; left: 10%; width: 120px; height: 120px; --dur: 25s;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
        <svg class="pf-bg-icon" style="top: 60%; right: 15%; width: 150px; height: 150px; --dur: 30s;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
        <svg class="pf-bg-icon" style="top: 10%; right: 40%; width: 80px; height: 80px; --dur: 20s;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1"><polyline points="20 6 9 17 4 12"></polyline></svg>
    </div>

    <div class="pf-container">
        <div class="pf-grid">
            
            <!-- Brand Column -->
            <div class="pf-brand">
                <a href="/" class="pf-logo" style="margin-bottom: 24px; display: block;">
                    <img src="/assets/images/Migratepay logo.png" alt="Migratepay Logo" style="height: 48px; width: auto; background: white; padding: 6px 12px; border-radius: 8px;" />
                </a>
                <p>The most secure escrow payment gateway in Bangladesh. Protecting every deal with bank-grade security and dispute resolution.</p>
                
                <div class="pf-socials">
                    <a href="#" aria-label="Facebook" class="pf-social-btn">
                        <svg viewBox="0 0 24 24"><path d="M18 2h-3a5 5 0 00-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 011-1h3z"/></svg>
                    </a>
                    <a href="#" aria-label="Twitter" class="pf-social-btn">
                        <svg viewBox="0 0 24 24"><path d="M23 3a10.9 10.9 0 01-3.14 1.53 4.48 4.48 0 00-7.86 3v1A10.66 10.66 0 013 4s-4 9 5 13a11.64 11.64 0 01-7 2c9 5 20 0 20-11.5a4.5 4.5 0 00-.08-.83A7.72 7.72 0 0023 3z"/></svg>
                    </a>
                    <a href="#" aria-label="LinkedIn" class="pf-social-btn">
                        <svg viewBox="0 0 24 24"><path d="M16 8a6 6 0 016 6v7h-4v-7a2 2 0 00-2-2 2 2 0 00-2 2v7h-4v-7a6 6 0 016-6zM2 9h4v12H2z"/><circle cx="4" cy="4" r="2"/></svg>
                    </a>
                </div>
            </div>

            <!-- Platform / Product -->
            <div>
                <h4 class="pf-col-title">Platform</h4>
                <ul class="pf-links">
                    <li><a href="{{ route('auth') ?? '/login' }}">Create a Deal</a></li>
                    <li><a href="/dashboard">User Dashboard</a></li>
                    <li><a href="{{ route('calculator') ?? '/calculator' }}">Charge Calculator</a></li>
                    <li><a href="/#how-it-works">How It Works</a></li>
                    <li><a href="/gallery">Gallery</a></li>
                </ul>
            </div>

            <!-- Company -->
            <div>
                <h4 class="pf-col-title">Company</h4>
                <ul class="pf-links">
                    <li><a href="/about">About Us</a></li>
                    <li><a href="/our-teams">Our Teams</a></li>
                    <li><a href="/blog">Our Blog</a></li>
                    <li><a href="/contact">Contact Center</a></li>
                </ul>
            </div>

            <!-- Legal & Trust -->
            <div>
                <h4 class="pf-col-title">Legal</h4>
                <ul class="pf-links">
                    <li><a href="/terms-and-conditions">Terms of Service</a></li>
                    <li><a href="/privacy-policy">Privacy Protection</a></li>
                    <li><a href="/cookies">Cookie Policies</a></li>
                    <li><a href="/documents">Trade Licenses</a></li>
                </ul>

                <!-- Subtle Trust Element -->
                <div class="pf-trust-card">
                    <div class="pf-trust-icon">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
                    </div>
                    <div class="pf-trust-text">
                        Secure Transactions
                        <span>Mediated & Protected</span>
                    </div>
                </div>
            </div>

        </div>
        
        <!-- Bottom Copyright -->
        <div class="pf-bottom">
            <div>&copy; {{ date('Y') }} Migratepay Secure Systems. All Rights Reserved. Verified by Bangladesh Digital Commerce Authority.</div>
            <div class="pf-bottom-links">
                <a href="/privacy-policy">Privacy</a>
                <a href="/terms-and-conditions">Terms</a>
            </div>
        </div>
    </div>
</footer>
