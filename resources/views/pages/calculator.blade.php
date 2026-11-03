@extends('layouts.frontend')
@section('title', 'Charge Calculator | Migratepay')

@section('content')
<style>
    :root {
        --bg-main: #F5F8FC;
        --bg-white: #FFFFFF;
        --text-heading-1: #17233D;
        --text-heading-2: #243552;
        --text-body: #5F6B7D;
        --text-muted: #8994A6;
        --brand-primary: #3B82F6;
        --brand-purple: #8B5CF6;
        --brand-cyan: #06b6d4;
        --brand-green: #10B981;
        --border-soft: rgba(0,0,0,0.06);
        --shadow-card: 0 15px 35px -5px rgba(0,0,0,0.05), 0 5px 15px -5px rgba(0,0,0,0.02);
        --transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
    }

    .calc-page {
        background-color: var(--bg-main);
        color: var(--text-body);
        font-family: 'Inter', sans-serif;
        padding: 120px 24px;
        min-height: 100vh;
        position: relative;
        overflow: hidden;
    }

    /* Decorative Elements */
    .calc-decor {
        position: absolute;
        border-radius: 50%;
        filter: blur(80px);
        z-index: 0;
        opacity: 0.4;
        animation: float 20s ease-in-out infinite;
    }
    .decor-1 { top: 10%; left: -10%; width: 400px; height: 400px; background: rgba(59, 130, 246, 0.15); }
    .decor-2 { bottom: 10%; right: -5%; width: 500px; height: 500px; background: rgba(139, 92, 246, 0.12); }
    
    @keyframes float {
        0%, 100% { transform: translateY(0) scale(1); }
        50% { transform: translateY(-30px) scale(1.05); }
    }

    .calc-container {
        max-width: 1100px;
        margin: 0 auto;
        position: relative;
        z-index: 10;
    }

    /* Header */
    .calc-header {
        text-align: center;
        margin-bottom: 60px;
        animation: fadeDown 0.8s ease forwards;
    }
    .calc-label {
        font-size: 13px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        color: var(--brand-primary);
        margin-bottom: 16px;
        display: inline-block;
        background: rgba(59, 130, 246, 0.1);
        padding: 6px 16px;
        border-radius: 100px;
    }
    .calc-title {
        font-size: clamp(2.5rem, 5vw, 3.5rem);
        font-weight: 900;
        color: var(--text-heading-1);
        margin-bottom: 20px;
        line-height: 1.1;
        letter-spacing: -0.03em;
    }
    .calc-desc {
        font-size: 1.125rem;
        color: var(--text-body);
        max-width: 500px;
        margin: 0 auto;
    }

    /* Calculator Interface */
    .calc-interface {
        background: var(--bg-white);
        border-radius: 24px;
        box-shadow: var(--shadow-card);
        border: 1px solid var(--border-soft);
        display: grid;
        grid-template-columns: 1fr 1fr;
        overflow: hidden;
        animation: slideUp 0.8s ease forwards 0.2s;
        opacity: 0;
        transform: translateY(30px);
    }

    .calc-left {
        padding: 48px;
        border-right: 1px solid var(--border-soft);
    }
    .calc-right {
        padding: 48px;
        background: linear-gradient(180deg, rgba(245,248,252,0) 0%, rgba(245,248,252,0.5) 100%);
        position: relative;
    }

    /* Input Fields */
    .input-group {
        margin-bottom: 32px;
    }
    .input-label {
        display: block;
        font-size: 15px;
        font-weight: 700;
        color: var(--text-heading-2);
        margin-bottom: 12px;
    }
    .select-wrapper {
        position: relative;
    }
    .calc-select, .calc-input {
        width: 100%;
        background: var(--bg-main);
        border: 1px solid transparent;
        padding: 16px 20px;
        font-size: 16px;
        font-weight: 600;
        color: var(--text-heading-1);
        border-radius: 16px;
        transition: var(--transition);
        outline: none;
        appearance: none;
    }
    .calc-select:focus, .calc-input:focus {
        background: var(--bg-white);
        border-color: var(--brand-primary);
        box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
    }
    .select-icon {
        position: absolute;
        right: 20px;
        top: 50%;
        transform: translateY(-50%);
        pointer-events: none;
        color: var(--text-muted);
    }
    .amount-wrapper {
        position: relative;
        display: flex;
        align-items: center;
    }
    .currency-badge {
        position: absolute;
        left: 20px;
        font-weight: 800;
        color: var(--text-heading-2);
        font-size: 18px;
    }
    .calc-input.with-currency {
        padding-left: 60px;
        font-size: 24px;
        font-weight: 800;
    }

    /* Results */
    .empty-state {
        text-align: center;
        padding: 40px 0;
        color: var(--text-muted);
        transition: var(--transition);
    }
    .empty-state svg {
        width: 64px;
        height: 64px;
        margin-bottom: 24px;
        color: var(--border-soft);
    }
    .empty-state h3 {
        font-size: 18px;
        font-weight: 700;
        color: var(--text-heading-2);
        margin-bottom: 8px;
    }

    .result-state {
        display: none;
        animation: fadeIn 0.5s ease forwards;
    }
    .result-state.active {
        display: block;
    }
    
    .result-header {
        font-size: 20px;
        font-weight: 800;
        color: var(--text-heading-1);
        margin-bottom: 24px;
        padding-bottom: 16px;
        border-bottom: 1px solid var(--border-soft);
    }

    .charge-cards {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
        margin-bottom: 24px;
    }
    .charge-card {
        background: var(--bg-white);
        padding: 20px;
        border-radius: 16px;
        border: 1px solid var(--border-soft);
        box-shadow: 0 4px 12px rgba(0,0,0,0.02);
        transition: var(--transition);
        position: relative;
        overflow: hidden;
    }
    .charge-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 24px rgba(0,0,0,0.06);
    }
    .charge-card.buyer { border-top: 3px solid var(--brand-primary); }
    .charge-card.seller { border-top: 3px solid var(--brand-cyan); }
    
    .card-label {
        font-size: 13px;
        font-weight: 700;
        color: var(--text-body);
        margin-bottom: 8px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .card-label span.split-badge {
        font-size: 10px;
        background: var(--bg-main);
        padding: 2px 6px;
        border-radius: 4px;
    }
    .card-amount {
        font-size: 24px;
        font-weight: 900;
        color: var(--text-heading-1);
    }

    .summary-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 16px 0;
        border-bottom: 1px dashed var(--border-soft);
    }
    .summary-row:last-child {
        border-bottom: none;
    }
    .summary-label {
        font-size: 15px;
        font-weight: 600;
        color: var(--text-body);
    }
    .summary-val {
        font-size: 16px;
        font-weight: 800;
        color: var(--text-heading-1);
    }
    .summary-val.highlight {
        color: var(--brand-primary);
        font-size: 18px;
    }

    /* Tiers Dropdown */
    .tiers-accordion {
        margin-top: 24px;
        border: 1px solid var(--border-soft);
        border-radius: 12px;
        overflow: hidden;
    }
    .tiers-toggle {
        width: 100%;
        padding: 16px;
        background: var(--bg-white);
        border: none;
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 14px;
        font-weight: 700;
        color: var(--text-heading-2);
        cursor: pointer;
        transition: var(--transition);
    }
    .tiers-toggle:hover { background: var(--bg-main); }
    .tiers-content {
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.4s ease;
        background: var(--bg-main);
    }
    .tiers-content.open {
        max-height: 300px;
    }
    .tier-row {
        display: flex;
        justify-content: space-between;
        padding: 12px 16px;
        font-size: 13px;
        border-top: 1px solid var(--border-soft);
        color: var(--text-body);
        font-weight: 600;
    }

    /* Animation States */
    .calculating-overlay {
        position: absolute;
        inset: 0;
        background: rgba(255,255,255,0.8);
        backdrop-filter: blur(4px);
        z-index: 20;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        opacity: 0;
        visibility: hidden;
        transition: var(--transition);
    }
    .calculating-overlay.active {
        opacity: 1;
        visibility: visible;
    }
    .loader {
        width: 32px;
        height: 32px;
        border: 3px solid var(--border-soft);
        border-top-color: var(--brand-primary);
        border-radius: 50%;
        animation: spin 1s linear infinite;
        margin-bottom: 12px;
    }

    /* Info & CTA */
    .info-section {
        text-align: center;
        margin-top: 40px;
        animation: slideUp 0.8s ease forwards 0.4s;
        opacity: 0;
        transform: translateY(30px);
    }
    .info-section p {
        font-size: 14px;
        font-weight: 600;
        color: var(--text-body);
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
    .info-section p svg {
        color: var(--brand-green);
    }

    .cta-section {
        margin-top: 60px;
        text-align: center;
        padding: 60px;
        background: linear-gradient(135deg, #0f172a, #1d4ed8);
        border-radius: 24px;
        color: white;
        box-shadow: var(--shadow-card);
        animation: slideUp 0.8s ease forwards 0.6s;
        opacity: 0;
        transform: translateY(30px);
    }
    .cta-section h2 {
        font-size: 32px;
        font-weight: 900;
        margin-bottom: 24px;
    }
    .btn-white {
        background: white;
        color: #0f172a;
        padding: 16px 32px;
        border-radius: 100px;
        font-weight: 800;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        text-decoration: none;
        transition: var(--transition);
    }
    .btn-white:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.2);
    }

    /* Animations */
    @keyframes fadeDown {
        from { opacity: 0; transform: translateY(-20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    @keyframes slideUp {
        from { opacity: 0; transform: translateY(30px); }
        to { opacity: 1; transform: translateY(0); }
    }
    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
    @keyframes spin {
        to { transform: rotate(360deg); }
    }

    /* Responsive */
    @media (max-width: 900px) {
        .calc-interface {
            grid-template-columns: 1fr;
        }
        .calc-left {
            border-right: none;
            border-bottom: 1px solid var(--border-soft);
            padding: 32px;
        }
        .calc-right {
            padding: 32px;
        }
        .calc-title {
            font-size: 2rem;
        }
        .cta-section {
            padding: 40px 20px;
        }
    }
    @media (max-width: 500px) {
        .charge-cards {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="calc-page">
    <div class="calc-decor decor-1"></div>
    <div class="calc-decor decor-2"></div>

    <div class="calc-container">
        <!-- Header -->
        <div class="calc-header">
            <span class="calc-label">Transaction Fees</span>
            <h1 class="calc-title">Calculate Your Migratepay Charges</h1>
            <p class="calc-desc">Enter your transaction details to see exactly how the applicable charges are calculated instantly.</p>
        </div>

        <!-- Calculator -->
        <div class="calc-interface">
            <!-- Left: Inputs -->
            <div class="calc-left">
                <div class="input-group">
                    <label class="input-label">Deal Category <span style="color: #ef4444;">*</span></label>
                    <div class="select-wrapper">
                        <select class="calc-select" id="dealCategory">
                            <option value="" disabled selected>Loading categories...</option>
                        </select>
                        <svg class="select-icon" width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </div>
                </div>

                <div class="input-group">
                    <label class="input-label">Deal Amount</label>
                    <div class="amount-wrapper">
                        <span class="currency-badge">৳</span>
                        <input type="number" id="dealAmount" class="calc-input with-currency" placeholder="0.00" min="0" step="100">
                    </div>
                </div>

                <div class="input-group" style="margin-bottom: 0;">
                    <label class="input-label">Charge Distribution</label>
                    <div class="select-wrapper">
                        <select class="calc-select" id="chargePayer">
                            <option value="shared_50" selected>50% Split (Shared equally)</option>
                            <option value="buyer_100">100% Buyer Pays</option>
                            <option value="seller_100">100% Seller Pays</option>
                            <option value="custom">Custom Percentage</option>
                        </select>
                        <svg class="select-icon" width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </div>
                </div>

                <div id="customPctWrapper" style="display: none; grid-template-columns: 1fr 1fr; gap: 16px; margin-top: 16px;">
                    <div>
                        <label class="input-label" style="font-size:13px;">Buyer %</label>
                        <input type="number" id="buyerPct" class="calc-input" placeholder="e.g. 60" min="0" max="100">
                    </div>
                    <div>
                        <label class="input-label" style="font-size:13px;">Seller %</label>
                        <input type="number" id="sellerPct" class="calc-input" placeholder="e.g. 40" min="0" max="100">
                    </div>
                </div>

                <div style="margin-top: 24px;">
                    <button id="calculateBtn" class="calc-select" style="background: var(--brand-primary); color: white; cursor: pointer; text-align: center;">Calculate Charges</button>
                </div>
            </div>

            <!-- Right: Results -->
            <div class="calc-right">
                <div class="calculating-overlay" id="calcOverlay">
                    <div class="loader"></div>
                    <span style="font-weight:700; color:var(--text-heading-2)">Calculating...</span>
                </div>

                <!-- Empty State -->
                <div class="empty-state" id="emptyState">
                    <svg fill="currentColor" viewBox="0 0 24 24"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-7 9h-2V7h-2v5H6v2h2v2h2v-2h2v-2zm4 4h-2v-2h2v2zm0-4h-2v-2h2v2z"/></svg>
                    <h3>Enter Transaction Details</h3>
                    <p style="font-size:14px;">Select a deal category, enter the amount, and click Calculate.</p>
                </div>

                <!-- Result State -->
                <div class="result-state" id="resultState">
                    <h3 class="result-header">Transaction Breakdown</h3>
                    
                    <div class="charge-cards">
                        <div class="charge-card buyer">
                            <div class="card-label">Buyer Pays <span class="split-badge">50% Split</span></div>
                            <div style="font-size: 13px; color: var(--text-body); margin-top: 12px; margin-bottom: 8px;">
                                <div style="display: flex; justify-content: space-between; margin-bottom: 4px;">
                                    <span>Deal Amount</span>
                                    <span>৳<span id="resDealAmtB">0</span></span>
                                </div>
                                <div style="display: flex; justify-content: space-between; margin-bottom: 8px; border-bottom: 1px solid var(--border-soft); padding-bottom: 8px;">
                                    <span>Buyer Charge</span>
                                    <span>+ ৳<span id="resBuyerChargeB">0</span></span>
                                </div>
                            </div>
                            <div class="card-amount" style="display: flex; justify-content: space-between; align-items: center;">
                                <span style="font-size: 14px; font-weight: 700; color: var(--text-heading-2);">Total</span>
                                <span>৳<span class="anim-num" id="resBuyerSettlement">0</span></span>
                            </div>
                        </div>
                        <div class="charge-card seller">
                            <div class="card-label">Seller Gets <span class="split-badge">50% Split</span></div>
                            <div style="font-size: 13px; color: var(--text-body); margin-top: 12px; margin-bottom: 8px;">
                                <div style="display: flex; justify-content: space-between; margin-bottom: 4px;">
                                    <span>Deal Amount</span>
                                    <span>৳<span id="resDealAmtS">0</span></span>
                                </div>
                                <div style="display: flex; justify-content: space-between; margin-bottom: 8px; border-bottom: 1px solid var(--border-soft); padding-bottom: 8px;">
                                    <span>Seller Charge</span>
                                    <span>- ৳<span id="resSellerChargeS">0</span></span>
                                </div>
                            </div>
                            <div class="card-amount" style="display: flex; justify-content: space-between; align-items: center;">
                                <span style="font-size: 14px; font-weight: 700; color: var(--text-heading-2);">Total</span>
                                <span>৳<span class="anim-num" id="resSellerSettlement">0</span></span>
                            </div>
                        </div>
                    </div>

                    <div class="summary-row">
                        <span class="summary-label">Deal Amount</span>
                        <span class="summary-val">৳<span class="anim-num" id="resDealAmount">0</span></span>
                    </div>
                    <div class="summary-row">
                        <span class="summary-label">Platform Total Charge</span>
                        <span class="summary-val highlight">৳<span class="anim-num" id="resTotalCharge">0</span> (<span id="resChargePct">0</span>%)</span>
                    </div>
                    <div class="summary-row" style="background: rgba(59, 130, 246, 0.05); padding: 12px; border-radius: 8px; margin-top: 12px; border: 1px solid rgba(59, 130, 246, 0.1);">
                        <span class="summary-label" style="color: var(--brand-primary);">Total Calculation</span>
                        <span class="summary-val highlight">৳<span class="anim-num" id="resTotalAmountCalc">0</span></span>
                    </div>

                    <div class="tiers-accordion">
                        <button class="tiers-toggle" id="tiersToggle">
                            View Applicable Charge Structure
                            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        <div class="tiers-content" id="tiersContent">
                            <!-- Populated by JS -->
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Info Section -->
        <div class="info-section">
            <p>
                <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                Transparent Charges. No Surprises. The exact split between buyer and seller can be customized when creating the deal.
            </p>
        </div>

        <!-- Final CTA -->
        <div class="cta-section">
            <h2>Ready to Start Your Transaction?</h2>
            <a href="/auth" class="btn-white">
                Get Started
                <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
            </a>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const categorySelect = document.getElementById('dealCategory');
    const amountInput = document.getElementById('dealAmount');
    const chargePayer = document.getElementById('chargePayer');
    const customPctWrapper = document.getElementById('customPctWrapper');
    const buyerPctInput = document.getElementById('buyerPct');
    const sellerPctInput = document.getElementById('sellerPct');
    const emptyState = document.getElementById('emptyState');
    const resultState = document.getElementById('resultState');
    const calcOverlay = document.getElementById('calcOverlay');
    const tiersToggle = document.getElementById('tiersToggle');
    const tiersContent = document.getElementById('tiersContent');
    
    let debounceTimer;

    // Fetch Categories
    fetch('/api/public/categories')
        .then(res => res.json())
        .then(data => {
            if(data.success && data.categories) {
                categorySelect.innerHTML = '<option value="" disabled selected>Select a deal category</option>';
                data.categories.forEach(cat => {
                    categorySelect.innerHTML += `<option value="${cat.id}">${cat.name}</option>`;
                });
            }
        }).catch(err => {
            categorySelect.innerHTML = '<option value="">Default Category</option>';
        });

    // Accordion Toggle
    tiersToggle.addEventListener('click', () => {
        tiersContent.classList.toggle('open');
        const icon = tiersToggle.querySelector('svg');
        if(tiersContent.classList.contains('open')){
            icon.style.transform = 'rotate(180deg)';
        } else {
            icon.style.transform = 'rotate(0deg)';
        }
    });

    // Number Animation Helper
    const animateNumber = (elementId, start, end, duration) => {
        const el = document.getElementById(elementId);
        if(!el) return;
        
        let startTimestamp = null;
        const step = (timestamp) => {
            if (!startTimestamp) startTimestamp = timestamp;
            const progress = Math.min((timestamp - startTimestamp) / duration, 1);
            const current = Math.floor(progress * (end - start) + start);
            
            // Format with commas
            el.innerText = current.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
            
            if (progress < 1) {
                window.requestAnimationFrame(step);
            } else {
                el.innerText = end.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
            }
        };
        window.requestAnimationFrame(step);
    };

    // Toggle Custom Percentages
    chargePayer.addEventListener('change', () => {
        if (chargePayer.value === 'custom') {
            customPctWrapper.style.display = 'grid';
        } else {
            customPctWrapper.style.display = 'none';
        }
    });

    // Auto-fill custom percentages
    buyerPctInput.addEventListener('input', () => {
        let val = parseFloat(buyerPctInput.value);
        if (!isNaN(val) && val >= 0 && val <= 100) {
            sellerPctInput.value = 100 - val;
        }
    });
    sellerPctInput.addEventListener('input', () => {
        let val = parseFloat(sellerPctInput.value);
        if (!isNaN(val) && val >= 0 && val <= 100) {
            buyerPctInput.value = 100 - val;
        }
    });

    const calculateBtn = document.getElementById('calculateBtn');

    // Calculate Charge
    calculateBtn.addEventListener('click', () => {
        const amount = parseFloat(amountInput.value);
        const categoryId = categorySelect.value;
        const payer = chargePayer.value;
        const sPct = sellerPctInput.value || 0;
        const bPct = buyerPctInput.value || 0;

        if(!categoryId) {
            alert("Please select a Deal Category first.");
            return;
        }

        if(!amount || amount <= 0) {
            emptyState.style.display = 'block';
            resultState.classList.remove('active');
            return;
        }

        calcOverlay.classList.add('active');

        let url = `/api/public/calculate-charge?amount=${amount}&category_id=${categoryId}&charge_payer=${payer}`;
        if (payer === 'custom') {
            url += `&buyer_pct=${bPct}&seller_pct=${sPct}`;
        }

        fetch(url)
            .then(res => res.json())
            .then(data => {
                if(data.success) {
                    emptyState.style.display = 'none';
                    resultState.classList.add('active');
                    
                    const resData = data.data;
                    
                    // Update labels
                    const buyerLabel = document.querySelector('.charge-card.buyer .split-badge');
                    const sellerLabel = document.querySelector('.charge-card.seller .split-badge');
                    
                    if (payer === 'shared_50') {
                        buyerLabel.innerText = '50% Split';
                        sellerLabel.innerText = '50% Split';
                    } else if (payer === 'buyer_100') {
                        buyerLabel.innerText = '100% Split';
                        sellerLabel.innerText = '0% Split';
                    } else if (payer === 'seller_100') {
                        buyerLabel.innerText = '0% Split';
                        sellerLabel.innerText = '100% Split';
                    } else if (payer === 'custom') {
                        buyerLabel.innerText = bPct + '% Split';
                        sellerLabel.innerText = sPct + '% Split';
                    }

                    // Animate numbers
                    animateNumber('resBuyerSettlement', 0, resData.buyer_settlement, 800);
                    animateNumber('resSellerSettlement', 0, resData.seller_settlement, 800);
                    animateNumber('resDealAmount', 0, resData.amount, 800);
                    animateNumber('resTotalCharge', 0, resData.total_charge, 800);
                    animateNumber('resTotalAmountCalc', 0, resData.amount + resData.total_charge, 800);
                    document.getElementById('resChargePct').innerText = resData.charge_percentage;

                    // Breakdown
                    document.getElementById('resDealAmtB').innerText = resData.amount.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
                    document.getElementById('resBuyerChargeB').innerText = resData.buyer_charge.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
                    document.getElementById('resDealAmtS').innerText = resData.amount.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
                    document.getElementById('resSellerChargeS').innerText = resData.seller_charge.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");

                    // Build tiers structure
                    if(resData.tiers) {
                        tiersContent.innerHTML = '';
                        resData.tiers.forEach(tier => {
                            let isCurrent = tier.charge === resData.charge_percentage + '%';
                            tiersContent.innerHTML += `
                                <div class="tier-row" style="${isCurrent ? 'background:rgba(59, 130, 246, 0.05); color:var(--brand-primary);' : ''}">
                                    <span>${tier.range}</span>
                                    <span>${tier.charge}</span>
                                </div>
                            `;
                        });
                    }
                }
            })
            .finally(() => {
                setTimeout(() => { calcOverlay.classList.remove('active'); }, 300);
            });
    });
});
</script>
@endsection
