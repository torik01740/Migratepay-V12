const puppeteer = require('puppeteer-core');
const fs = require('fs');
const path = require('path');

const CHROME_PATH = 'C:\\Program Files\\Google\\Chrome\\Application\\chrome.exe';
const SCREENSHOT_DIR = path.resolve(__dirname, '../docs/phase-10/screenshots');
const ARTIFACT_DIR = 'C:\\Users\\Torik\\.gemini\\antigravity\\brain\\4e7f5db7-39d3-4af8-ade5-1264caba9329\\screenshots';

if (!fs.existsSync(SCREENSHOT_DIR)) {
    fs.mkdirSync(SCREENSHOT_DIR, { recursive: true });
}
if (!fs.existsSync(ARTIFACT_DIR)) {
    fs.mkdirSync(ARTIFACT_DIR, { recursive: true });
}

async function run() {
    console.log('=== PHASE 10: END-TO-END AUTOMATION & VISUAL EVIDENCE CAPTURE ===\n');
    console.log('Using Chrome executable:', CHROME_PATH);

    const browser = await puppeteer.launch({
        executablePath: CHROME_PATH,
        headless: 'new',
        args: [
            '--no-sandbox',
            '--disable-setuid-sandbox',
            '--disable-dev-shm-usage',
            '--window-size=1440,900',
        ],
    });

    const capture = async (page, filename) => {
        const dest1 = path.join(SCREENSHOT_DIR, filename);
        const dest2 = path.join(ARTIFACT_DIR, filename);
        await page.screenshot({ path: dest1, fullPage: false });
        fs.copyFileSync(dest1, dest2);
        console.log(`[Captured] -> ${filename}`);
    };

    try {
        // =========================================================================
        // PART 1: PUBLIC PAGES (DESKTOP)
        // =========================================================================
        console.log('\n--- Part 1: Public Website Verification (Desktop 1440x900) ---');
        const pubContext = await browser.createBrowserContext();
        const pubPage = await pubContext.newPage();
        await pubPage.setViewport({ width: 1440, height: 900 });

        // 01_public_home.png
        console.log('Navigating to Public Home: http://127.0.0.1:8000/ ...');
        await pubPage.goto('http://127.0.0.1:8000/', { waitUntil: 'networkidle0' });
        await new Promise((r) => setTimeout(r, 1000));
        await capture(pubPage, '01_public_home.png');

        // 02_public_calculator.png
        console.log('Navigating to Fee Calculator: http://127.0.0.1:8000/charge-calculator ...');
        await pubPage.goto('http://127.0.0.1:8000/charge-calculator', { waitUntil: 'networkidle0' });
        await new Promise((r) => setTimeout(r, 1000));
        await capture(pubPage, '02_public_calculator.png');

        // 03_public_login.png
        console.log('Navigating to Login Portal: http://127.0.0.1:8000/auth ...');
        await pubPage.goto('http://127.0.0.1:8000/auth', { waitUntil: 'networkidle0' });
        await new Promise((r) => setTimeout(r, 1000));
        await capture(pubPage, '03_public_login.png');

        // 13_mobile_public.png (Public Mobile Viewport)
        console.log('Testing Mobile Public Homepage (375x812)...');
        await pubPage.setViewport({ width: 375, height: 812, isMobile: true });
        await pubPage.goto('http://127.0.0.1:8000/', { waitUntil: 'networkidle0' });
        await new Promise((r) => setTimeout(r, 1000));
        await capture(pubPage, '13_mobile_public.png');

        await pubContext.close();

        // =========================================================================
        // PART 2: USER DASHBOARD & MFA (DESKTOP & MOBILE)
        // =========================================================================
        console.log('\n--- Part 2: Customer User Dashboard Verification ---');
        const userContext = await browser.createBrowserContext();
        const userPage = await userContext.newPage();
        await userPage.setViewport({ width: 1440, height: 900 });

        console.log('Logging in as customer@migratepay.com ...');
        await userPage.goto('http://127.0.0.1:8000/auth', { waitUntil: 'networkidle0' });
        await new Promise((r) => setTimeout(r, 1000));
        await userPage.type('#login-form input[type="email"]', 'customer@migratepay.com');
        await userPage.type('#login-password', 'Password123!');
        await userPage.click('#login-btn');
        await userPage.waitForNavigation({ waitUntil: 'networkidle0', timeout: 15000 }).catch(() => {});
        await new Promise((r) => setTimeout(r, 2000));
        console.log('Current URL after customer login:', userPage.url());

        // 04_user_dashboard.png
        console.log('Capturing User Dashboard Overview...');
        await userPage.goto('http://127.0.0.1:8000/dashboard', { waitUntil: 'networkidle0' });
        await new Promise((r) => setTimeout(r, 1500));
        await capture(userPage, '04_user_dashboard.png');

        // 05_user_wallet.png
        console.log('Capturing User Activity & Transactions...');
        await userPage.goto('http://127.0.0.1:8000/dashboard/activity', { waitUntil: 'networkidle0' });
        await new Promise((r) => setTimeout(r, 1500));
        await capture(userPage, '05_user_wallet.png');

        // 06_user_payouts.png
        console.log('Capturing User Withdrawals...');
        await userPage.goto('http://127.0.0.1:8000/dashboard/withdraw', { waitUntil: 'networkidle0' });
        await new Promise((r) => setTimeout(r, 1500));
        await capture(userPage, '06_user_payouts.png');

        // 07_user_deals.png
        console.log('Capturing User Deals & Escrow Agreements...');
        await userPage.goto('http://127.0.0.1:8000/dashboard/deals', { waitUntil: 'networkidle0' });
        await new Promise((r) => setTimeout(r, 1500));
        await capture(userPage, '07_user_deals.png');

        // 08_user_mfa_setup.png (MFA Setup with Live QR Code)
        console.log('Navigating to User Profile -> 2FA Tab...');
        await userPage.goto('http://127.0.0.1:8000/dashboard/profile', { waitUntil: 'networkidle0' });
        await new Promise((r) => setTimeout(r, 1500));

        // Click "Two-Factor Auth (2FA)" button in tabs
        const mfaTabClicked = await userPage.evaluate(() => {
            const buttons = Array.from(document.querySelectorAll('button'));
            const btn = buttons.find((b) => b.textContent.includes('Two-Factor Auth'));
            if (btn) {
                btn.click();
                return true;
            }
            return false;
        });
        console.log('Clicked 2FA tab:', mfaTabClicked);
        await new Promise((r) => setTimeout(r, 1000));

        // Click "Enable 2FA" button to generate QR code
        const enableBtnClicked = await userPage.evaluate(() => {
            const buttons = Array.from(document.querySelectorAll('button'));
            const btn = buttons.find((b) => b.textContent.trim() === 'Enable 2FA');
            if (btn) {
                btn.click();
                return true;
            }
            return false;
        });
        console.log('Clicked Enable 2FA button:', enableBtnClicked);
        await new Promise((r) => setTimeout(r, 2000));
        await capture(userPage, '08_user_mfa_setup.png');

        // 14_mobile_user_dashboard.png
        console.log('Testing Mobile User Dashboard (375x812)...');
        await userPage.setViewport({ width: 375, height: 812, isMobile: true });
        await userPage.goto('http://127.0.0.1:8000/dashboard', { waitUntil: 'networkidle0' });
        await new Promise((r) => setTimeout(r, 1500));
        await capture(userPage, '14_mobile_user_dashboard.png');

        await userContext.close();

        // =========================================================================
        // PART 3: ADMIN DASHBOARD (DESKTOP & MOBILE)
        // =========================================================================
        console.log('\n--- Part 3: Administrative Dashboard Verification ---');
        const adminContext = await browser.createBrowserContext();
        const adminPage = await adminContext.newPage();
        await adminPage.setViewport({ width: 1440, height: 900 });

        console.log('Logging in as admin@migratepay.com ...');
        await adminPage.goto('http://127.0.0.1:8000/auth?role=admin', { waitUntil: 'networkidle0' });
        await new Promise((r) => setTimeout(r, 1000));
        await adminPage.type('#login-form input[type="email"]', 'admin@migratepay.com');
        await adminPage.type('#login-password', 'Faitor@911');
        await adminPage.click('#login-btn');
        await adminPage.waitForNavigation({ waitUntil: 'networkidle0', timeout: 15000 }).catch(() => {});
        await new Promise((r) => setTimeout(r, 2000));
        console.log('Current URL after admin login:', adminPage.url());

        // 09_admin_overview.png
        console.log('Capturing Admin Control Tower Overview...');
        await adminPage.goto('http://127.0.0.1:8000/admin', { waitUntil: 'networkidle0' });
        await new Promise((r) => setTimeout(r, 1500));
        await capture(adminPage, '09_admin_overview.png');

        // 10_admin_users_kyc.png
        console.log('Capturing Admin Users & KYC Governance...');
        await adminPage.goto('http://127.0.0.1:8000/admin/users', { waitUntil: 'networkidle0' });
        await new Promise((r) => setTimeout(r, 1500));
        await capture(adminPage, '10_admin_users_kyc.png');

        // 11_admin_finances.png
        console.log('Capturing Admin Deposits & Financial Management...');
        await adminPage.goto('http://127.0.0.1:8000/admin/deposits', { waitUntil: 'networkidle0' });
        await new Promise((r) => setTimeout(r, 1500));
        await capture(adminPage, '11_admin_finances.png');

        // 12_admin_security_policies.png
        console.log('Capturing Admin Security Policies & 2FA Governance...');
        await adminPage.goto('http://127.0.0.1:8000/admin/security', { waitUntil: 'networkidle0' });
        await new Promise((r) => setTimeout(r, 1500));
        await capture(adminPage, '12_admin_security_policies.png');

        // 15_mobile_admin_dashboard.png
        console.log('Testing Mobile Admin Dashboard (375x812)...');
        await adminPage.setViewport({ width: 375, height: 812, isMobile: true });
        await adminPage.goto('http://127.0.0.1:8000/admin', { waitUntil: 'networkidle0' });
        await new Promise((r) => setTimeout(r, 1500));
        await capture(adminPage, '15_mobile_admin_dashboard.png');

        await adminContext.close();

        console.log('\n============================================================');
        console.log('PHASE 10 VISUAL VERIFICATION: ALL 15 SCREENSHOTS CAPTURED');
        console.log('============================================================');
    } catch (err) {
        console.error('Browser verification failed with error:', err);
    } finally {
        await browser.close();
    }
}

run();
