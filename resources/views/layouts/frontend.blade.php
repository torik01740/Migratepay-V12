<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Migratepay | Safe Payments')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Lexend:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --navy-900: #002147;
            --navy-800: #001833;
            --emerald-500: #006A4E;
            --emerald-600: #00503A;
            --sunset-orange: #FF4F00;
            --white: #FFFFFF;
            --gray-50: #F8FAFC;
            --gray-100: #F1F5F9;
            --gray-500: #64748B;
            --gray-800: #1E293B;
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background-color: var(--white); color: var(--gray-800); line-height: 1.6; }
        h1, h2, h3, h4, h5, h6 { font-family: 'Lexend', sans-serif; }
        a { text-decoration: none; color: inherit; }
        .container { max-width: 1200px; margin: 0 auto; padding: 0 24px; }

        /* New Header Styles */
        header { position: fixed; top: 0; left: 0; right: 0; z-index: 1000; transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1); }
        
        .pre-header { background: linear-gradient(90deg, #0f172a, #3b82f6, #8b5cf6); color: white; padding: 10px 0; font-size: 12px; font-weight: 600; transition: height 0.3s ease, opacity 0.3s ease, padding 0.3s ease; overflow: hidden; }
        .pre-header-inner { display: flex; justify-content: space-between; align-items: center; }
        .pre-header-left { display: flex; align-items: center; gap: 12px; }
        .social-icons-header { display: flex; gap: 8px; }
        .social-icons-header a { width: 28px; height: 28px; background: rgba(255,255,255,0.15); border-radius: 50%; display: flex; align-items: center; justify-content: center; transition: all 0.3s ease; color: white; }
        .social-icons-header a:hover { background: rgba(255,255,255,0.4); transform: scale(1.1); box-shadow: 0 0 10px rgba(255,255,255,0.2); color: white; }
        .social-icons-header svg { width: 14px; height: 14px; fill: currentColor; }
        .social-icons-header a:last-child svg { fill: none; stroke: currentColor; stroke-width: 2; }
        
        .pre-header-right { display: flex; gap: 24px; align-items: center; }
        .pre-header-item { display: flex; align-items: center; gap: 6px; opacity: 0.9; }
        .pre-header-item svg { width: 14px; height: 14px; stroke: currentColor; fill: none; stroke-width: 2; stroke-linecap: round; stroke-linejoin: round; }

        .main-header-wrapper { padding: 16px 24px; transition: padding 0.3s ease; }
        header.scrolled .pre-header { height: 0; opacity: 0; padding: 0; }
        header.scrolled .main-header-wrapper { padding: 10px 24px; }
        
        .main-header { 
            display: flex; justify-content: space-between; align-items: center; 
            background: rgba(255, 255, 255, 0.9); 
            backdrop-filter: blur(16px); -webkit-backdrop-filter: blur(16px);
            border-radius: 100px; 
            padding: 10px 24px; 
            box-shadow: 0 10px 30px rgba(0,0,0,0.05); 
            border: 1px solid rgba(255,255,255,0.8);
            transition: all 0.3s ease;
            position: relative;
        }
        header.scrolled .main-header { box-shadow: 0 15px 40px rgba(0,0,0,0.1); background: rgba(255, 255, 255, 0.95); }

        .logo { font-size: 22px; font-weight: 900; color: #0f172a; display: flex; align-items: center; gap: 10px; letter-spacing: -0.03em; }
        .logo svg { width: 28px; height: 28px; fill: #3b82f6; }

        .nav-links { display: flex; gap: 4px; align-items: center; }
        
        .nav-item { font-weight: 800; color: #0f172a; transition: all 0.3s ease; font-size: 13px; position: relative; padding: 10px 16px; border-radius: 100px; letter-spacing: 0.05em; display: flex; align-items: center; gap: 4px; cursor: pointer; }
        .nav-item::after { content: ''; position: absolute; bottom: 6px; left: 16px; right: 16px; height: 2px; background: linear-gradient(90deg, #3b82f6, #ec4899); border-radius: 2px; transform: scaleX(0); transition: transform 0.3s ease; transform-origin: center; }
        .nav-item:hover { color: #3b82f6; background: rgba(59,130,246,0.05); }
        .nav-item:hover::after { transform: scaleX(1); }
        
        .dropdown { position: relative; }
        .dropdown-menu { 
            position: absolute; top: calc(100% + 15px); left: 50%; transform: translateX(-50%) translateY(15px); 
            background: #ffffff; border-radius: 20px; box-shadow: 0 20px 50px rgba(0,0,0,0.1); padding: 12px; min-width: 220px; 
            opacity: 0; visibility: hidden; transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1); 
            border: 1px solid rgba(226, 232, 240, 0.8); z-index: 100;
        }
        .dropdown-menu::before {
            content: ''; position: absolute; top: -1px; left: 24px; right: 24px; height: 2px; background: linear-gradient(90deg, #3b82f6, #8b5cf6, #ec4899); border-radius: 2px;
        }
        .dropdown:hover .dropdown-menu { opacity: 1; visibility: visible; transform: translateX(-50%) translateY(0); }
        
        .dropdown-link { 
            display: block; padding: 12px 16px; border-radius: 12px; font-size: 13px; font-weight: 700; color: #475569; transition: all 0.2s ease;
        }
        .dropdown-link:hover { background: rgba(248, 250, 252, 0.8); color: #3b82f6; transform: translateX(4px); }

        .btn-login-header {
            background: linear-gradient(135deg, #3b82f6, #8b5cf6);
            background-size: 200% auto;
            color: white !important;
            padding: 12px 28px;
            border-radius: 100px;
            font-weight: 800;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
            box-shadow: 0 8px 20px rgba(139, 92, 246, 0.3);
            border: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-left: 12px;
        }
        .btn-login-header::after { display: none; }
        .btn-login-header:hover {
            background-position: right center;
            transform: translateY(-2px);
            box-shadow: 0 12px 25px rgba(139, 92, 246, 0.4);
            color: white;
        }

        .mobile-menu-toggle { display: none; background: none; border: none; cursor: pointer; flex-direction: column; gap: 5px; padding: 10px; z-index: 1001; }
        .mobile-menu-toggle span { display: block; width: 24px; height: 2px; background: #0f172a; transition: all 0.3s; border-radius: 2px; }
        .mobile-nav-panel { position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(255,255,255,0.95); z-index: 999; display: flex; flex-direction: column; justify-content: center; align-items: center; opacity: 0; visibility: hidden; transition: all 0.4s ease; backdrop-filter: blur(20px); }
        .mobile-nav-panel.active { opacity: 1; visibility: visible; }
        .mobile-nav-inner { display: flex; flex-direction: column; gap: 24px; align-items: center; width: 100%; max-width: 300px; transform: translateY(30px); transition: transform 0.4s ease; }
        .mobile-nav-panel.active .mobile-nav-inner { transform: translateY(0); }
        
        .trust-badge { position: absolute; bottom: -30px; right: 40px; background: white; padding: 6px 16px; border-radius: 100px; box-shadow: 0 10px 20px rgba(0,0,0,0.05); font-size: 10px; font-weight: 800; color: #023047; text-transform: uppercase; letter-spacing: 0.1em; display: flex; align-items: center; gap: 6px; border: 1px solid #f1f5f9; z-index: -1; animation: slideDownBadge 1s forwards 0.5s; opacity: 0; pointer-events: none; }
        @keyframes slideDownBadge { from { transform: translateY(-30px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
        
        @media (max-width: 1024px) {
            .nav-links { display: none; }
            .mobile-menu-toggle { display: flex; }
            .pre-header { display: none; }
            .trust-badge { display: none; }
            .main-header-wrapper { padding: 16px 20px; }
            header.scrolled .main-header-wrapper { padding: 10px 20px; }
        }

        .btn { padding: 10px 24px; border-radius: 50px; font-weight: 700; transition: all 0.3s ease; cursor: pointer; display: inline-flex; align-items: center; gap: 8px; font-size: 14px; }
        .btn-primary { background: #3b82f6; color: white; }
        .btn-primary:hover { background: #2563eb; transform: translateY(-2px); box-shadow: 0 10px 20px rgba(59,130,246, 0.2); }

        .page-header { padding: 160px 0 80px; background: linear-gradient(135deg, var(--navy-900) 0%, var(--emerald-600) 100%); color: white; text-align: center; }
        .page-header h1 { font-size: 48px; font-weight: 800; margin-bottom: 16px; letter-spacing: -1px; }
        .page-header p { font-size: 18px; opacity: 0.8; max-width: 600px; margin: 0 auto; }

        .section { padding: 100px 0; }
        .content-card { background: white; border-radius: 24px; border: 1px solid var(--gray-100); padding: 48px; shadow: 0 4px 6px rgba(0,0,0,0.02); }

        footer { background: var(--navy-900); color: white; padding: 80px 0 40px; }
        .footer-grid { display: grid; grid-template-columns: 2fr 1fr 1fr 1fr; gap: 48px; margin-bottom: 60px; }
        .footer-logo { font-size: 24px; font-weight: 800; margin-bottom: 24px; display: flex; align-items: center; gap: 10px; }
        .footer-desc { opacity: 0.6; font-size: 14px; margin-bottom: 24px; line-height: 1.8; }
        .footer-title { font-size: 16px; font-weight: 700; margin-bottom: 24px; color: var(--emerald-500); }
        .footer-links { list-style: none; display: flex; flex-direction: column; gap: 12px; }
        .footer-link { opacity: 0.7; font-size: 14px; transition: var(--transition); }
        .footer-link:hover { opacity: 1; color: var(--emerald-500); transform: translateX(5px); }
        .footer-bottom { border-top: 1px solid rgba(255,255,255,0.05); pt: 40px; text-align: center; font-size: 13px; opacity: 0.5; }

        /* Visual Builder Styles */
        #builder-toolbar {
            position: fixed;
            bottom: 30px;
            left: 50%;
            transform: translateX(-50%);
            background: #111827;
            border: 1px solid #374151;
            padding: 12px 24px;
            border-radius: 99px;
            z-index: 9999;
            box-shadow: 0 25px 50px -12px rgba(0,0,0,0.5);
            display: none;
            align-items: center;
            gap: 20px;
            color: white;
            animation: slideUp 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        }

        @keyframes slideUp { from { transform: translate(-50%, 100px); } }

        .builder-btn {
            background: #059669;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 99px;
            font-[10px];
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            cursor: pointer;
            transition: all 0.2s;
        }

        .builder-btn:hover { background: #10b981; }
        .builder-btn.secondary { background: #374151; }

        .editing [contenteditable="true"] {
            outline: 2px dashed #059669;
            outline-offset: 4px;
            border-radius: 4px;
        }

        .section-hover { position: relative; }
        .section-controls {
            position: absolute;
            top: 10px;
            right: 10px;
            display: none;
            gap: 5px;
            z-index: 100;
            background: #111827;
            padding: 4px;
            border-radius: 8px;
        }
        .editing .section-hover:hover .section-controls { display: flex; }
        .control-btn {
            width: 32px;
            height: 32px;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            border: none;
            color: white;
        }
    </style>
</head>
<body class="site-body">
    <div id="builder-toolbar">
        <span style="font-size: 10px; font-weight: 800; color: #9ca3af; text-transform: uppercase; letter-spacing: 0.1em;">BUILDER MODE</span>
        <div style="width: 1px; height: 20px; background: #374151;"></div>
        <button id="save-page" class="builder-btn">SAVE CHANGES</button>
        <button id="exit-builder" class="builder-btn secondary">EXIT</button>
    </div>
    <header id="site-header">
        <div class="pre-header">
            <div class="container pre-header-inner">
                <div class="pre-header-left">
                    <span>Follow Us:</span>
                    <div class="social-icons-header">
                        <a href="#" aria-label="Facebook"><svg viewBox="0 0 24 24"><path d="M18 2h-3a5 5 0 00-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 011-1h3z"/></svg></a>
                        <a href="#" aria-label="Twitter / X"><svg viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg></a>
                        <a href="#" aria-label="LinkedIn"><svg viewBox="0 0 24 24"><path d="M16 8a6 6 0 016 6v7h-4v-7a2 2 0 00-2-2 2 2 0 00-2 2v7h-4v-7a6 6 0 016-6zM2 9h4v12H2z"/><circle cx="4" cy="4" r="2"/></svg></a>
                        <a href="#" aria-label="YouTube"><svg viewBox="0 0 24 24"><path d="M22.54 6.42a2.78 2.78 0 0 0-1.94-2C18.88 4 12 4 12 4s-6.88 0-8.6.46a2.78 2.78 0 0 0-1.94 2A29 29 0 0 0 1 11.75a29 29 0 0 0 .46 5.33 2.78 2.78 0 0 0 1.94 2c1.72.46 8.6.46 8.6.46s6.88 0 8.6-.46a2.78 2.78 0 0 0 1.94-2 29 29 0 0 0 .46-5.33 29 29 0 0 0-.46-5.33z"/><polygon points="9.75 15.02 15.5 11.75 9.75 8.48 9.75 15.02"/></svg></a>
                        <a href="#" aria-label="Instagram"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1112.63 8 4 4 0 0116 11.37z"/><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/></svg></a>
                    </div>
                </div>
                <div class="pre-header-right">
                    <div class="pre-header-item"><svg viewBox="0 0 24 24"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg> support@migratepay.com</div>
                    <div class="pre-header-item"><svg viewBox="0 0 24 24"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg> +880 1712 345 678</div>
                    <div class="pre-header-item"><svg viewBox="0 0 24 24"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg> Dhaka, Bangladesh</div>
                </div>
            </div>
        </div>
        
        <div class="main-header-wrapper">
            <div class="container main-header">
                <a href="/" class="logo">
                    <img src="/assets/images/Migratepay logo.png" alt="Migratepay Logo" style="height: 60px; width: auto;" />
                </a>
                
                <nav class="nav-links" id="dynamic-nav-container">
                    <!-- Navigation items will be injected here by JavaScript -->
                    <a href="/auth" class="btn-login-header">
                        Login
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                    </a>
                </nav>

                <button class="mobile-menu-toggle" id="mobile-menu-toggle">
                    <span></span><span></span><span></span>
                </button>
            </div>
        </div>
    </header>

    <!-- Mobile Nav Panel -->
    <div class="mobile-nav-panel" id="mobile-nav-panel">
        <div class="mobile-nav-inner" id="mobile-nav-inner">
            <!-- Items injected by JS -->
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const rawMenus = localStorage.getItem('migratepay_menus');
            let headerItems = [];
            
            if (rawMenus) {
                try {
                    const menus = JSON.parse(rawMenus);
                    const headerMenuObj = menus.find(m => m.name.toLowerCase().includes('header')) || menus[0];
                    if (headerMenuObj && headerMenuObj.items) {
                        headerItems = headerMenuObj.items.filter(i => i.visible);
                    }
                } catch(e) {}
            }
            
            if (headerItems.length === 0) {
                headerItems = [
                    { id: 'm1', label: 'HOME', href: '/', visible: true },
                    { id: 'm2', label: 'GET TO KNOW US', href: '#', visible: true, children: [
                        { id: 'c1', label: 'About Us', href: '/about', visible: true },
                        { id: 'c2', label: 'Our Story', href: '/about#story', visible: true }
                    ]},
                    { id: 'm3', label: 'BLOG', href: '/blog', visible: true },
                    { id: 'm4', label: 'OUR PARTNERS', href: '/our-teams', visible: true },
                    { id: 'm5', label: 'LEGACY', href: '#', visible: true, children: [
                        { id: 'c3', label: 'Our Legacy', href: '/legacy', visible: true },
                        { id: 'c4', label: 'History', href: '/history', visible: true }
                    ]}
                ];
            }

            const navContainer = document.getElementById('dynamic-nav-container');
            const loginBtn = navContainer.querySelector('.btn-login-header');
            const mobileNavInner = document.getElementById('mobile-nav-inner');
            
            // Stagger animation for desktop load
            const staggerDelay = 100;
            let currentDelay = 100;

            const renderDesktopNode = (item, depth = 0) => {
                if(item.children && item.children.length > 0) {
                    const childHtml = item.children.filter(c=>c.visible).map(c => renderDesktopNode(c, depth + 1).outerHTML).join('');
                    
                    const dropdown = document.createElement('div');
                    dropdown.className = 'nav-item dropdown';
                    
                    if (depth === 0) {
                        dropdown.style.animation = `slideDownBadge 0.6s forwards ${currentDelay}ms`;
                        dropdown.style.opacity = '0';
                        currentDelay += staggerDelay;
                    }
                    
                    dropdown.innerHTML = `
                        <span style="display:flex; align-items:center;">
                            ${item.label}
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="width:14px; height:14px; margin-left:6px;"><polyline points="6 9 12 15 18 9"></polyline></svg>
                        </span>
                        <div class="dropdown-menu">
                            ${childHtml}
                        </div>
                    `;
                    return dropdown;
                } else {
                    const a = document.createElement('a');
                    a.href = item.href;
                    if (depth === 0) {
                        a.className = 'nav-item';
                        a.textContent = item.label;
                        a.style.animation = `slideDownBadge 0.6s forwards ${currentDelay}ms`;
                        a.style.opacity = '0';
                        currentDelay += staggerDelay;
                    } else {
                        a.className = 'dropdown-link';
                        a.textContent = item.label;
                    }
                    return a;
                }
            };
            
            const renderMobileNode = (item) => {
                const a = document.createElement('a');
                a.href = item.href;
                a.textContent = item.label;
                a.style.fontSize = '24px';
                a.style.fontWeight = '900';
                a.style.color = '#0f172a';
                a.style.textDecoration = 'none';
                return a;
            };

            // Build Desktop Nav
            headerItems.forEach(item => {
                navContainer.insertBefore(renderDesktopNode(item, 0), loginBtn);
            });
            
            // Build Mobile Nav
            headerItems.forEach(item => {
                mobileNavInner.appendChild(renderMobileNode(item));
            });
            const mobileLogin = loginBtn.cloneNode(true);
            mobileLogin.style.marginTop = '20px';
            mobileLogin.style.fontSize = '16px';
            mobileNavInner.appendChild(mobileLogin);

            // Sticky Header Logic
            const header = document.getElementById('site-header');
            window.addEventListener('scroll', () => {
                if (window.scrollY > 50) {
                    header.classList.add('scrolled');
                } else {
                    header.classList.remove('scrolled');
                }
            });
            
            // Mobile Menu Toggle
            const toggleBtn = document.getElementById('mobile-menu-toggle');
            const mobilePanel = document.getElementById('mobile-nav-panel');
            let menuOpen = false;
            
            toggleBtn.addEventListener('click', () => {
                menuOpen = !menuOpen;
                if(menuOpen) {
                    mobilePanel.classList.add('active');
                    toggleBtn.querySelector('span:nth-child(1)').style.transform = 'translateY(7px) rotate(45deg)';
                    toggleBtn.querySelector('span:nth-child(2)').style.opacity = '0';
                    toggleBtn.querySelector('span:nth-child(3)').style.transform = 'translateY(-7px) rotate(-45deg)';
                } else {
                    mobilePanel.classList.remove('active');
                    toggleBtn.querySelector('span:nth-child(1)').style.transform = 'none';
                    toggleBtn.querySelector('span:nth-child(2)').style.opacity = '1';
                    toggleBtn.querySelector('span:nth-child(3)').style.transform = 'none';
                }
            });
        });
    </script>

    @yield('content')

    @include('partials.footer')

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const isAdmin = localStorage.getItem('migratepay_admin_auth');
            const urlParams = new URLSearchParams(window.location.search);
            const builderMode = urlParams.get('edit_mode') === 'true' && isAdmin;

            if (builderMode) {
                document.body.classList.add('editing');
                document.getElementById('builder-toolbar').style.display = 'flex';
                
                // Make all headings and paragraphs editable
                document.querySelectorAll('h1, h2, h3, h4, p, a.dropdown-link, a.footer-link').forEach(el => {
                    el.setAttribute('contenteditable', 'true');
                });

                // Inject section controls
                document.querySelectorAll('.section, .page-header, .hero, .trust-bar, .problem, .how-it-works, .features, .metrics, .integrations, section').forEach(section => {
                    if (section.classList.contains('section-hover')) return;
                    section.classList.add('section-hover');
                    const controls = document.createElement('div');
                    controls.className = 'section-controls';
                    controls.innerHTML = `
                        <button class="control-btn" style="background: #3b82f6;" title="Move Up">↑</button>
                        <button class="control-btn" style="background: #3b82f6;" title="Move Down">↓</button>
                        <button class="control-btn" style="background: #ef4444;" title="Settings">⚙</button>
                    `;
                    section.appendChild(controls);

                    controls.querySelectorAll('button')[0].onclick = () => {
                        const prev = section.previousElementSibling;
                        if (prev && prev.tagName !== 'HEADER') section.parentNode.insertBefore(section, prev);
                    };
                    controls.querySelectorAll('button')[1].onclick = () => {
                        const next = section.nextElementSibling;
                        if (next && next.tagName !== 'FOOTER') section.parentNode.insertBefore(next, section);
                    };
                });

                document.getElementById('save-page').onclick = () => {
                    // Logic to capture DOM and save it (Mock for now)
                    alert('Structure saved to virtual local database successfully!');
                    window.location.href = window.location.pathname;
                };

                document.getElementById('exit-builder').onclick = () => {
                    if (confirm('Exit builder mode? Any unsaved changes will be lost.')) {
                        window.location.href = window.location.pathname;
                    }
                };
            }
        });
    </script>

    <!-- Cookie Consent Banner -->
    <div id="cookie-banner" class="cookie-banner hidden">
        <div class="cookie-content">
            <h3 class="cookie-title">We use Cookies</h3>
            <p class="cookie-text">Migratepay uses necessary Cookies to operate our Platform, maintain security and facilitate transactions. With your permission, we may also use optional Cookies for analytics, preferences and marketing. You can accept all Cookies, reject optional Cookies, or manage your preferences.</p>
            <div class="cookie-buttons">
                <button id="cookie-accept" class="cookie-btn cookie-btn-primary">Accept All</button>
                <button id="cookie-reject" class="cookie-btn cookie-btn-outline">Reject Optional</button>
                <button id="cookie-manage" class="cookie-btn cookie-btn-text">Manage Preferences</button>
            </div>
        </div>
    </div>

    <!-- Cookie Preferences Modal -->
    <div id="cookie-modal" class="cookie-modal-overlay hidden">
        <div class="cookie-modal-content">
            <div class="cookie-modal-header">
                <h3>Manage Preferences</h3>
                <button id="cookie-modal-close" class="cookie-modal-close">&times;</button>
            </div>
            <div class="cookie-modal-body">
                <div class="cookie-option">
                    <div class="cookie-option-info">
                        <h4>Necessary Cookies</h4>
                        <p>Required for essential Platform functionality.</p>
                    </div>
                    <div class="cookie-status always-active">Always Active</div>
                </div>
                <div class="cookie-option">
                    <div class="cookie-option-info">
                        <h4>Security Cookies</h4>
                        <p>Required to protect against unauthorized activity.</p>
                    </div>
                    <div class="cookie-status always-active">Always Active</div>
                </div>
                <div class="cookie-option">
                    <div class="cookie-option-info">
                        <h4>Functional Cookies</h4>
                        <p>Used to remember preferences and improve functionality.</p>
                    </div>
                    <label class="toggle-switch">
                        <input type="checkbox" id="cookie-functional" checked>
                        <span class="slider"></span>
                    </label>
                </div>
                <div class="cookie-option">
                    <div class="cookie-option-info">
                        <h4>Analytics Cookies</h4>
                        <p>Used to understand website usage and performance.</p>
                    </div>
                    <label class="toggle-switch">
                        <input type="checkbox" id="cookie-analytics" checked>
                        <span class="slider"></span>
                    </label>
                </div>
                <div class="cookie-option">
                    <div class="cookie-option-info">
                        <h4>Marketing Cookies</h4>
                        <p>Used for marketing and advertising purposes.</p>
                    </div>
                    <label class="toggle-switch">
                        <input type="checkbox" id="cookie-marketing" checked>
                        <span class="slider"></span>
                    </label>
                </div>
            </div>
            <div class="cookie-modal-footer">
                <button id="cookie-save-preferences" class="cookie-btn cookie-btn-primary" style="width: 100%;">Save Preferences</button>
            </div>
        </div>
    </div>

    <style>
    .cookie-banner { position: fixed; bottom: 24px; right: 24px; width: 400px; max-width: calc(100vw - 48px); background: white; border-radius: 20px; box-shadow: 0 20px 40px rgba(0,0,0,0.1); border: 1px solid var(--gray-100); z-index: 9999; transform: translateY(150%); transition: transform 0.6s cubic-bezier(0.16, 1, 0.3, 1); }
    .cookie-banner.show { transform: translateY(0); }
    .cookie-banner.hidden { display: none; }
    .cookie-content { padding: 32px; }
    .cookie-title { font-size: 20px; font-weight: 800; color: var(--navy-900); margin-bottom: 12px; }
    .cookie-text { font-size: 14px; color: var(--gray-500); line-height: 1.6; margin-bottom: 24px; }
    .cookie-buttons { display: flex; flex-direction: column; gap: 10px; }
    .cookie-btn { padding: 12px 16px; border-radius: 12px; font-size: 14px; font-weight: 700; cursor: pointer; transition: all 0.3s ease; text-align: center; border: none; }
    .cookie-btn-primary { background: var(--emerald-500); color: white; box-shadow: 0 4px 10px rgba(0, 106, 78, 0.2); }
    .cookie-btn-primary:hover { background: var(--emerald-600); transform: translateY(-2px); box-shadow: 0 8px 15px rgba(0, 106, 78, 0.3); }
    .cookie-btn-outline { background: white; color: var(--gray-800); border: 1px solid #e2e8f0; }
    .cookie-btn-outline:hover { background: var(--gray-50); color: var(--navy-900); }
    .cookie-btn-text { background: transparent; color: var(--gray-500); text-decoration: underline; font-size: 13px; }
    .cookie-btn-text:hover { color: var(--emerald-500); }

    /* Modal Styles */
    .cookie-modal-overlay { position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0, 24, 51, 0.5); backdrop-filter: blur(4px); z-index: 10000; display: flex; align-items: center; justify-content: center; opacity: 0; visibility: hidden; transition: all 0.3s ease; }
    .cookie-modal-overlay.show { opacity: 1; visibility: visible; }
    .cookie-modal-overlay.hidden { display: none; }
    .cookie-modal-content { background: white; width: 500px; max-width: 90%; border-radius: 24px; box-shadow: 0 25px 50px rgba(0,0,0,0.15); transform: translateY(20px); transition: transform 0.3s ease; display: flex; flex-direction: column; max-height: 90vh; }
    .cookie-modal-overlay.show .cookie-modal-content { transform: translateY(0); }
    .cookie-modal-header { padding: 24px 32px; border-bottom: 1px solid var(--gray-100); display: flex; justify-content: space-between; align-items: center; }
    .cookie-modal-header h3 { font-size: 20px; font-weight: 800; color: var(--navy-900); margin: 0; }
    .cookie-modal-close { background: none; border: none; font-size: 28px; color: var(--gray-500); cursor: pointer; line-height: 1; padding: 0; }
    .cookie-modal-close:hover { color: var(--navy-900); }
    .cookie-modal-body { padding: 32px; overflow-y: auto; }
    .cookie-option { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; padding-bottom: 24px; border-bottom: 1px solid var(--gray-100); }
    .cookie-option:last-child { margin-bottom: 0; padding-bottom: 0; border-bottom: none; }
    .cookie-option-info h4 { font-size: 16px; font-weight: 700; color: var(--navy-900); margin-bottom: 4px; font-family: 'Inter', sans-serif; }
    .cookie-option-info p { font-size: 13px; color: var(--gray-500); margin: 0; max-width: 280px; }
    .cookie-status.always-active { font-size: 12px; font-weight: 700; color: var(--emerald-500); background: rgba(0, 106, 78, 0.1); padding: 6px 12px; border-radius: 99px; }
    
    /* Toggle Switch */
    .toggle-switch { position: relative; display: inline-block; width: 44px; height: 24px; }
    .toggle-switch input { opacity: 0; width: 0; height: 0; }
    .slider { position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: #cbd5e1; transition: .4s; border-radius: 34px; }
    .slider:before { position: absolute; content: ""; height: 18px; width: 18px; left: 3px; bottom: 3px; background-color: white; transition: .4s; border-radius: 50%; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
    input:checked + .slider { background-color: var(--emerald-500); }
    input:checked + .slider:before { transform: translateX(20px); }
    
    .cookie-modal-footer { padding: 24px 32px; border-top: 1px solid var(--gray-100); background: var(--gray-50); border-radius: 0 0 24px 24px; }
    </style>
    <script>
    document.addEventListener('DOMContentLoaded', () => {
        const banner = document.getElementById('cookie-banner');
        const modal = document.getElementById('cookie-modal');
        
        // Show banner if no consent
        if (!localStorage.getItem('migratepay_cookies_accepted')) {
            banner.classList.remove('hidden');
            setTimeout(() => banner.classList.add('show'), 500);
        }

        const hideBanner = () => {
            banner.classList.remove('show');
            setTimeout(() => banner.classList.add('hidden'), 600);
        };

        const openModal = () => {
            modal.classList.remove('hidden');
            // small delay to allow display block to apply before opacity transition
            setTimeout(() => modal.classList.add('show'), 10);
            
            // Set current preferences if they exist
            try {
                const prefs = JSON.parse(localStorage.getItem('migratepay_cookie_prefs'));
                if (prefs) {
                    document.getElementById('cookie-functional').checked = prefs.functional;
                    document.getElementById('cookie-analytics').checked = prefs.analytics;
                    document.getElementById('cookie-marketing').checked = prefs.marketing;
                }
            } catch(e) {}
        };

        const closeModal = () => {
            modal.classList.remove('show');
            setTimeout(() => modal.classList.add('hidden'), 300);
        };

        // Banner Buttons
        document.getElementById('cookie-accept').onclick = () => {
            localStorage.setItem('migratepay_cookies_accepted', 'all');
            localStorage.setItem('migratepay_cookie_prefs', JSON.stringify({ functional: true, analytics: true, marketing: true }));
            hideBanner();
        };

        document.getElementById('cookie-reject').onclick = () => {
            localStorage.setItem('migratepay_cookies_accepted', 'necessary');
            localStorage.setItem('migratepay_cookie_prefs', JSON.stringify({ functional: false, analytics: false, marketing: false }));
            hideBanner();
        };

        document.getElementById('cookie-manage').onclick = () => {
            openModal();
        };

        // Modal Buttons
        document.getElementById('cookie-modal-close').onclick = closeModal;
        
        document.getElementById('cookie-save-preferences').onclick = () => {
            const prefs = {
                functional: document.getElementById('cookie-functional').checked,
                analytics: document.getElementById('cookie-analytics').checked,
                marketing: document.getElementById('cookie-marketing').checked
            };
            localStorage.setItem('migratepay_cookies_accepted', 'custom');
            localStorage.setItem('migratepay_cookie_prefs', JSON.stringify(prefs));
            closeModal();
            hideBanner();
        };
    });
    </script>
    @if(\App\Models\AiSetting::getVal('ai_widget_active', '1') == '1')
        @include('partials.ai-chat-widget')
    @endif
</body>
</html>
