<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MigratePay — Secure Escrow & Deal Management in Bangladesh</title>
    <meta name="description" content="MigratePay is Bangladesh's trusted third-party mediator, protecting buyers and sellers at every step of the transaction. Secure escrow and deal management.">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        :root {
            /* Color Palette */
            --primary: #2563EB; /* Vibrant Blue */
            --primary-hover: #1D4ED8;
            --secondary: #10B981; /* Emerald Green */
            --dark: #0A1128; /* Deeper Navy */
            --dark-light: #162447;
            --gray-100: #F8FAFC;
            --gray-200: #E2E8F0;
            --gray-300: #CBD5E1;
            --gray-500: #64748B;
            --gray-800: #1E293B;
            --white: #FFFFFF;
            
            /* Gradients */
            --gradient-primary: linear-gradient(135deg, #2563EB 0%, #3B82F6 50%, #1D4ED8 100%);
            --gradient-dark: linear-gradient(135deg, #0A1128 0%, #162447 100%);
            --gradient-glass: linear-gradient(135deg, rgba(255,255,255,0.9) 0%, rgba(255,255,255,0.6) 100%);
            --gradient-subtle: linear-gradient(to right bottom, #F8FAFC, #EFF6FF);
            
            /* Shadows */
            --shadow-sm: 0 2px 4px rgba(0,0,0,0.02);
            --shadow-md: 0 10px 30px -10px rgba(0,0,0,0.05);
            --shadow-lg: 0 20px 40px -15px rgba(0,0,0,0.08);
            --shadow-xl: 0 25px 50px -12px rgba(0,0,0,0.15);
            --shadow-primary: 0 15px 35px -10px rgba(37,99,235,0.4);
            --shadow-glow: 0 0 20px rgba(37,99,235,0.15);
            
            /* Typography */
            --font-body: 'Inter', sans-serif;
            --font-heading: 'Plus Jakarta Sans', sans-serif;
            
            /* Layout */
            --max-width: 1200px;
            --transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
            --transition-bounce: all 0.5s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        /* Base Reset */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: var(--font-body);
            color: var(--gray-800);
            background-color: var(--gray-100);
            line-height: 1.6;
            overflow-x: hidden;
            -webkit-font-smoothing: antialiased;
        }
        
        h1, h2, h3, h4, h5, h6 { font-family: var(--font-heading); font-weight: 700; color: var(--dark); line-height: 1.2; letter-spacing: -0.02em; }
        a { text-decoration: none; color: inherit; transition: var(--transition); }
        img { max-width: 100%; height: auto; display: block; }
        ul { list-style: none; }
        
        .container { max-width: var(--max-width); margin: 0 auto; padding: 0 24px; }
        
        /* Typography Utilities */
        .text-primary { color: var(--primary); }
        .text-secondary { color: var(--secondary); }
        .text-gray { color: var(--gray-500); }
        .text-center { text-align: center; }
        
        /* Buttons */
        .btn {
            display: inline-flex; align-items: center; justify-content: center;
            padding: 16px 32px; border-radius: 100px; font-weight: 700; font-size: 15px;
            cursor: pointer; transition: var(--transition-bounce); border: none; gap: 8px;
            position: relative; overflow: hidden; z-index: 1; text-transform: uppercase; letter-spacing: 0.5px;
        }
        .btn::before {
            content: ''; position: absolute; top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(255,255,255,0.1); transform: translateX(-100%) skewX(-15deg);
            transition: transform 0.5s ease; z-index: -1;
        }
        .btn:hover::before { transform: translateX(100%) skewX(-15deg); }
        .btn-primary { background: var(--gradient-primary); color: var(--white); box-shadow: var(--shadow-primary); }
        .btn-primary:hover { transform: translateY(-3px); box-shadow: 0 20px 40px -10px rgba(37, 99, 235, 0.6); }
        .btn-outline { background: var(--white); color: var(--dark); border: 2px solid var(--gray-200); box-shadow: var(--shadow-sm); }
        .btn-outline:hover { border-color: var(--primary); color: var(--primary); transform: translateY(-3px); box-shadow: var(--shadow-md); }
        .btn-white { background: var(--white); color: var(--primary); box-shadow: var(--shadow-md); }
        .btn-white:hover { transform: translateY(-3px); box-shadow: var(--shadow-xl); color: var(--primary-hover); }

        /* Sections */
        .section { padding: 80px 0; position: relative; }
        .section-light { 
            position: relative;
            overflow: hidden;
            background: linear-gradient(160deg, #EEF3FB 0%, #F0F5FF 25%, #EBF4FF 50%, #F3F0FF 75%, #ECF5FB 100%);
        }
        .section-light::before {
            content: ''; position: absolute; width: 900px; height: 900px;
            background: radial-gradient(circle, rgba(59, 130, 246, 0.12) 0%, transparent 70%);
            top: -200px; left: -200px; border-radius: 50%; filter: blur(50px);
            pointer-events: none; z-index: 0; animation: float 25s infinite alternate;
        }
        .section-light::after {
            content: ''; position: absolute; width: 700px; height: 700px;
            background: radial-gradient(circle, rgba(99, 102, 241, 0.08) 0%, transparent 70%);
            bottom: -100px; right: -150px; border-radius: 50%; filter: blur(50px);
            pointer-events: none; z-index: 0; animation: float-slow 30s infinite alternate-reverse;
        }
        .section-light > .container { position: relative; z-index: 1; }
        .section-header { text-align: center; max-width: 700px; margin: 0 auto 50px; }
        .section-title { font-size: 42px; margin-bottom: 24px; }
        .section-subtitle { font-size: 18px; color: var(--gray-500); line-height: 1.7; }
        .badge {
            display: inline-flex; align-items: center; padding: 8px 16px; border-radius: 100px;
            font-size: 13px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px;
            margin-bottom: 20px; background: rgba(37, 99, 235, 0.08); color: var(--primary);
            border: 1px solid rgba(37, 99, 235, 0.15); box-shadow: var(--shadow-glow);
        }

        /* Animations */
        @keyframes float { 0% { transform: translateY(0px); } 50% { transform: translateY(-20px); } 100% { transform: translateY(0px); } }
        @keyframes float-slow { 0% { transform: translateY(0px) rotate(0deg); } 50% { transform: translateY(-15px) rotate(2deg); } 100% { transform: translateY(0px) rotate(0deg); } }
        @keyframes pulse-glow { 0% { box-shadow: 0 0 0 0 rgba(37, 99, 235, 0.4); } 70% { box-shadow: 0 0 0 15px rgba(37, 99, 235, 0); } 100% { box-shadow: 0 0 0 0 rgba(37, 99, 235, 0); } }
        @keyframes scaleIn { 0% { opacity: 0; transform: scale(0.95); } 100% { opacity: 1; transform: scale(1); } }
        
        .reveal { opacity: 0; transform: translateY(40px); transition: all 0.8s cubic-bezier(0.16, 1, 0.3, 1); }
        .reveal.active { opacity: 1; transform: translateY(0); }
        .reveal-left { opacity: 0; transform: translateX(-40px); transition: all 0.8s cubic-bezier(0.16, 1, 0.3, 1); }
        .reveal-left.active { opacity: 1; transform: translateX(0); }
        .reveal-right { opacity: 0; transform: translateX(40px); transition: all 0.8s cubic-bezier(0.16, 1, 0.3, 1); }
        .reveal-right.active { opacity: 1; transform: translateX(0); }
        
        .delay-100 { transition-delay: 0.1s; }
        .delay-200 { transition-delay: 0.2s; }
        .delay-300 { transition-delay: 0.3s; }
        .delay-400 { transition-delay: 0.4s; }

        /* New Header Styles from Global Nav */
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
        
        .main-header { display: flex; justify-content: space-between; align-items: center; background: rgba(255, 255, 255, 0.9); backdrop-filter: blur(16px); -webkit-backdrop-filter: blur(16px); border-radius: 100px; padding: 10px 24px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); border: 1px solid rgba(255,255,255,0.8); transition: all 0.3s ease; position: relative; }
        header.scrolled .main-header { box-shadow: 0 15px 40px rgba(0,0,0,0.1); background: rgba(255, 255, 255, 0.95); }

        .logo { font-size: 22px; font-weight: 900; color: #0f172a; display: flex; align-items: center; gap: 10px; letter-spacing: -0.03em; }
        .logo svg { width: 28px; height: 28px; fill: #3b82f6; }

        .nav-links { display: flex; gap: 4px; align-items: center; }
        .nav-item { font-weight: 800; color: #0f172a; transition: all 0.3s ease; font-size: 13px; position: relative; padding: 10px 16px; border-radius: 100px; letter-spacing: 0.05em; display: flex; align-items: center; gap: 4px; cursor: pointer; }
        .nav-item::after { content: ''; position: absolute; bottom: 6px; left: 16px; right: 16px; height: 2px; background: linear-gradient(90deg, #3b82f6, #ec4899); border-radius: 2px; transform: scaleX(0); transition: transform 0.3s ease; transform-origin: center; }
        .nav-item:hover { color: #3b82f6; background: rgba(59,130,246,0.05); }
        .nav-item:hover::after { transform: scaleX(1); }
        
        .dropdown { position: relative; }
        .dropdown-menu { position: absolute; top: calc(100% + 15px); left: 50%; transform: translateX(-50%) translateY(15px); background: #ffffff; border-radius: 20px; box-shadow: 0 20px 50px rgba(0,0,0,0.1); padding: 12px; min-width: 220px; opacity: 0; visibility: hidden; transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1); border: 1px solid rgba(226, 232, 240, 0.8); z-index: 100; }
        .dropdown-menu::before { content: ''; position: absolute; top: -1px; left: 24px; right: 24px; height: 2px; background: linear-gradient(90deg, #3b82f6, #8b5cf6, #ec4899); border-radius: 2px; }
        .dropdown:hover .dropdown-menu { opacity: 1; visibility: visible; transform: translateX(-50%) translateY(0); }
        .dropdown-link { display: block; padding: 12px 16px; border-radius: 12px; font-size: 13px; font-weight: 700; color: #475569; transition: all 0.2s ease; }
        .dropdown-link:hover { background: rgba(248, 250, 252, 0.8); color: #3b82f6; transform: translateX(4px); }

        .btn-login-header { background: linear-gradient(135deg, #3b82f6, #8b5cf6); background-size: 200% auto; color: white !important; padding: 12px 28px; border-radius: 100px; font-weight: 800; font-size: 13px; text-transform: uppercase; letter-spacing: 0.05em; transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1); box-shadow: 0 8px 20px rgba(139, 92, 246, 0.3); border: none; display: inline-flex; align-items: center; gap: 8px; margin-left: 12px; }
        .btn-login-header::after { display: none; }
        .btn-login-header:hover { background-position: right center; transform: translateY(-2px); box-shadow: 0 12px 25px rgba(139, 92, 246, 0.4); color: white; }

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

        /* ================= Hero Section ================= */
        .hero { padding: 180px 0 120px; background: var(--gradient-subtle); position: relative; overflow: hidden; min-height: 100vh; display: flex; align-items: center; }
        .hero-bg-shape-1 { position: absolute; top: -20%; right: -10%; width: 60%; height: 120%; background: radial-gradient(circle, rgba(37,99,235,0.06) 0%, transparent 60%); z-index: 0; pointer-events: none; animation: float 15s ease-in-out infinite; }
        .hero-bg-shape-2 { position: absolute; bottom: -10%; left: -10%; width: 50%; height: 80%; background: radial-gradient(circle, rgba(16,185,129,0.04) 0%, transparent 60%); z-index: 0; pointer-events: none; animation: float-slow 20s ease-in-out infinite reverse; }
        
        .hero-container { display: grid; grid-template-columns: 1fr 1.2fr; gap: 60px; align-items: center; position: relative; z-index: 1; }
        .hero-content { animation: scaleIn 1s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
        .hero-content h1 { font-size: 56px; line-height: 1.1; margin-bottom: 24px; letter-spacing: -0.03em; color: var(--dark); text-shadow: 0 4px 10px rgba(0,0,0,0.02); }
        .hero-content h1 span { background: var(--gradient-primary); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
        .hero-content p { font-size: 18px; color: var(--gray-500); margin-bottom: 40px; max-width: 500px; line-height: 1.7; }
        .hero-actions { display: flex; gap: 20px; }
        
        /* ================= Hero Animation Container ================= */
        .hero-animation-container {
            position: relative;
            width: 100%;
            height: 520px;
            background: rgba(255,255,255,0.7);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-radius: 30px;
            box-shadow: var(--shadow-xl);
            border: 1px solid rgba(226, 232, 240, 0.8);
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            animation: scaleIn 1.2s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }

        .anim-char {
            position: absolute;
            display: flex;
            flex-direction: column;
            align-items: center;
            transition: all 0.8s cubic-bezier(0.16, 1, 0.3, 1);
            z-index: 10;
        }
        .anim-char-buyer { left: 40px; top: 130px; }
        .anim-char-seller { right: 40px; top: 230px; }

        .char-avatar { width: 64px; height: 64px; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: 800; font-size: 24px; box-shadow: var(--shadow-md); }
        .char-label { margin-top: 10px; font-weight: 700; font-size: 15px; color: var(--gray-500); }

        .anim-scene {
            position: absolute;
            inset: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.5s ease, transform 0.5s ease;
            transform: scale(0.95);
        }

        /* Scene States */
        #heroAnim[data-scene="1"] .anim-scene-1,
        #heroAnim[data-scene="2"] .anim-scene-2,
        #heroAnim[data-scene="3"] .anim-scene-3,
        #heroAnim[data-scene="4"] .anim-scene-4,
        #heroAnim[data-scene="5"] .anim-scene-5,
        #heroAnim[data-scene="6"] .anim-scene-6,
        #heroAnim[data-scene="7"] .anim-scene-7,
        #heroAnim[data-scene="8"] .anim-scene-8,
        #heroAnim[data-scene="9"] .anim-scene-9 {
            opacity: 1;
            transform: scale(1);
        }

        /* Character Positioning across scenes */
        #heroAnim[data-scene="5"] .anim-char-buyer, #heroAnim[data-scene="6"] .anim-char-buyer,
        #heroAnim[data-scene="7"] .anim-char-buyer, #heroAnim[data-scene="8"] .anim-char-buyer,
        #heroAnim[data-scene="9"] .anim-char-buyer { left: 20px; }

        #heroAnim[data-scene="5"] .anim-char-seller, #heroAnim[data-scene="6"] .anim-char-seller,
        #heroAnim[data-scene="7"] .anim-char-seller, #heroAnim[data-scene="8"] .anim-char-seller,
        #heroAnim[data-scene="9"] .anim-char-seller { right: 20px; }

        /* Scene 1 Styles */
        .anim-chat { background: white; padding: 14px 22px; border-radius: 20px; font-weight: 600; font-size: 15px; max-width: 220px; position: absolute; box-shadow: var(--shadow-md); border: 1px solid var(--gray-200); color: var(--dark); z-index: 10; }
        .chat-left { left: 120px; top: 130px; border-bottom-left-radius: 4px; }
        .chat-right { right: 120px; top: 250px; border-bottom-right-radius: 4px; background: var(--gradient-primary); color: white; border: none; }
        .anim-deal-amount { position: absolute; bottom: 80px; background: rgba(37,99,235,0.05); color: var(--primary); font-weight: 800; padding: 12px 28px; border-radius: 100px; border: 1px solid rgba(37,99,235,0.15); box-shadow: var(--shadow-glow); }

        /* Scene 2 Styles */
        .anim-question { position: absolute; font-size: 48px; font-weight: 900; color: #F59E0B; animation: float 2.5s infinite; filter: drop-shadow(0 4px 6px rgba(245, 158, 11, 0.3)); z-index: 5; }
        .anim-q-left { left: 140px; top: 60px; }
        .anim-q-right { right: 140px; top: 150px; }
        .anim-thought { position: absolute; background: white; border: 1px solid var(--gray-200); padding: 16px; border-radius: 20px; font-weight: 700; font-size: 14px; max-width: 200px; box-shadow: var(--shadow-lg); text-align: center; color: var(--dark); z-index: 10; }
        .thought-sub { font-size: 12px; color: #EF4444; font-weight: 600; display: block; margin-top: 8px; background: rgba(239, 68, 68, 0.05); padding: 6px; border-radius: 8px; }
        .anim-thought-left { left: 120px; top: 120px; border-bottom-left-radius: 4px; }
        .anim-thought-right { right: 120px; top: 250px; border-bottom-right-radius: 4px; }

        /* Scene 3 Styles */
        .anim-risk-card { background: white; border: 2px dashed #EF4444; padding: 34px; border-radius: 24px; text-align: center; width: 280px; box-shadow: 0 10px 30px rgba(239, 68, 68, 0.15); animation: pulse-red 2s infinite; }
        @keyframes pulse-red { 0% { box-shadow: 0 0 0 0 rgba(239,68,68,0.4); } 70% { box-shadow: 0 0 0 20px rgba(239,68,68,0); } 100% { box-shadow: 0 0 0 0 rgba(239,68,68,0); } }
        .risk-amount { font-size: 28px; font-weight: 800; color: var(--dark); margin-bottom: 12px; }
        .risk-arrow { font-size: 15px; font-weight: 700; color: var(--gray-500); margin-bottom: 24px; display: flex; align-items: center; justify-content: center; gap: 8px; }
        .risk-arrow::before { content: '→'; color: #EF4444; }
        .risk-arrow::after { content: '←'; color: #EF4444; }
        .risk-warnings { display: flex; flex-direction: column; gap: 10px; }
        .risk-badge { background: var(--gray-800); color: white; font-size: 12px; font-weight: 800; padding: 8px 14px; border-radius: 8px; letter-spacing: 1px; }
        .risk-badge-red { background: #EF4444; }

        /* Scene 4 Styles */
        .anim-mp-layer { position: absolute; left: 50%; transform: translateX(-50%); top: 50px; display: flex; flex-direction: column; align-items: center; z-index: 5; transition: all 0.6s cubic-bezier(0.16, 1, 0.3, 1); }
        .anim-mp-layer.active-layer { top: 30px; transform: translateX(-50%) scale(0.85); }
        .mp-logo { background: var(--dark); color: white; padding: 12px 28px; border-radius: 100px; font-weight: 800; font-size: 20px; display: flex; align-items: center; gap: 10px; box-shadow: var(--shadow-xl); border: 1px solid rgba(255,255,255,0.1); }
        .mp-logo::before { content: ''; display: block; width: 18px; height: 18px; background: var(--primary); border-radius: 4px; }
        .mp-msg { margin-top: 16px; background: rgba(37,99,235,0.1); color: var(--primary); padding: 8px 20px; border-radius: 100px; font-weight: 700; font-size: 14px; }

        .anim-structured-deal { margin-top: 120px; background: white; border: 1px solid var(--gray-200); padding: 28px; border-radius: 24px; width: 280px; box-shadow: var(--shadow-xl); }
        .sd-title { font-weight: 800; font-size: 18px; margin-bottom: 18px; color: var(--dark); border-bottom: 1px solid var(--gray-100); padding-bottom: 14px; }
        .sd-row { display: flex; justify-content: space-between; font-size: 15px; color: var(--gray-500); font-weight: 500; margin-bottom: 10px; }
        .sd-row span { font-weight: 700; color: var(--dark); }
        .sd-status { margin-top: 20px; background: rgba(16,185,129,0.1); color: #10B981; padding: 10px; border-radius: 10px; font-weight: 700; font-size: 14px; text-align: center; }

        /* Scene 5 Styles */
        .anim-payment-flow { margin-top: 90px; display: flex; flex-direction: column; align-items: center; gap: 14px; }
        .flow-step { background: white; border: 1px solid var(--gray-200); padding: 14px 28px; border-radius: 14px; font-weight: 700; font-size: 15px; box-shadow: var(--shadow-md); width: 220px; text-align: center; color: var(--dark); }
        .flow-buyer { border-left: 5px solid var(--primary); }
        .flow-mp { background: var(--dark); color: white; display: flex; align-items: center; justify-content: center; gap: 10px; border: none; }
        .flow-status { border-left: 5px solid #10B981; background: rgba(16,185,129,0.05); }
        .flow-arrow { font-weight: 900; color: var(--gray-300); }

        /* Scene 6 Styles */
        .anim-seller-working { position: absolute; right: 70px; top: 160px; background: var(--gray-800); color: white; padding: 14px 24px; border-radius: 100px; font-weight: 700; font-size: 15px; box-shadow: var(--shadow-lg); border-bottom-right-radius: 4px; z-index: 10; }
        .anim-progress-card { margin-top: 90px; background: white; border: 1px solid var(--gray-200); padding: 28px; border-radius: 24px; width: 300px; box-shadow: var(--shadow-xl); display: flex; flex-direction: column; gap: 20px; }
        .prog-step { display: flex; align-items: center; font-size: 15px; font-weight: 600; color: var(--gray-500); }
        .prog-step::before { content: ''; display: block; width: 18px; height: 18px; border-radius: 50%; border: 2px solid var(--gray-300); margin-right: 14px; }
        .prog-step.done { color: var(--dark); }
        .prog-step.done::before { background: var(--primary); border-color: var(--primary); }
        .prog-step.active { color: var(--primary); }
        .prog-step.active::before { border-color: var(--primary); border-width: 5px; }
        .loader-dots { animation: pulse-dots 1.5s infinite; }

        /* Scene 8 Styles */
        .anim-review-card { position: absolute; left: 120px; top: 100px; background: white; border: 1px solid var(--primary); padding: 24px; border-radius: 20px; box-shadow: var(--shadow-xl); width: 220px; text-align: center; z-index: 10; }
        .rev-status { font-weight: 800; color: var(--dark); margin-bottom: 12px; font-size: 15px; }
        .rev-action { font-size: 14px; font-weight: 600; color: var(--primary); background: rgba(37,99,235,0.1); padding: 8px; border-radius: 8px; }

        /* Scene 8 Styles */
        .anim-success-card { margin-top: 70px; background: var(--gradient-primary); padding: 40px; border-radius: 24px; color: white; text-align: center; box-shadow: var(--shadow-primary); }
        .success-icon { width: 70px; height: 70px; background: rgba(255,255,255,0.2); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 36px; font-weight: 900; margin: 0 auto 20px; }
        .success-title { font-size: 24px; font-weight: 800; }
        .anim-protected { position: absolute; background: rgba(16,185,129,0.1); color: #10B981; font-weight: 800; font-size: 13px; padding: 8px 16px; border-radius: 100px; display: flex; align-items: center; gap: 6px; border: 1px solid rgba(16,185,129,0.3); }
        .anim-protected::before { content: '✓'; }
        .p-buyer { left: 10px; top: 240px; }
        .p-seller { right: 10px; top: 340px; }

        /* Pagination Dots */
        .hero-pagination { position: absolute; bottom: 30px; left: 50%; transform: translateX(-50%); display: flex; gap: 8px; z-index: 20; }
        .hero-pagination .dot { width: 8px; height: 8px; border-radius: 50%; background: var(--gray-300); cursor: pointer; transition: var(--transition); }
        .hero-pagination .dot.active { width: 24px; border-radius: 4px; background: var(--primary); }

        /* ================= Social Media Hook ================= */
        .social-deal-section { background: var(--gradient-dark); padding: 100px 0; color: white; overflow: hidden; position: relative; }
        .social-deal-section::before { content: ''; position: absolute; inset: 0; background: radial-gradient(circle at top right, rgba(139,92,246,0.15) 0%, transparent 60%); pointer-events: none; }
        .social-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 60px; align-items: center; position: relative; z-index: 1; }
        .social-content h2 { font-size: 48px; margin-bottom: 24px; line-height: 1.1; font-weight: 900; color: white; }
        .social-content p { font-size: 18px; color: var(--gray-300); margin-bottom: 40px; line-height: 1.8; max-width: 500px; }
        .social-content .badge { background: rgba(139,92,246,0.2); color: #c4b5fd; border-color: rgba(139,92,246,0.3); }
        .social-visual { position: relative; height: 480px; display: flex; align-items: center; justify-content: center; }
        .sv-card { position: absolute; background: rgba(255,255,255,0.05); backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px); border: 1px solid rgba(255,255,255,0.1); padding: 24px; border-radius: 20px; box-shadow: 0 20px 40px rgba(0,0,0,0.3); width: 260px; animation: floatCard 6s infinite ease-in-out; }
        .sv-card-fb { top: 0; left: 0; animation-delay: 0s; border-top: 4px solid #1877F2; }
        .sv-card-li { bottom: 0; right: 0; animation-delay: 3s; border-top: 4px solid #0A66C2; }
        .sv-header { display: flex; align-items: center; gap: 12px; margin-bottom: 16px; }
        .sv-avatar { width: 48px; height: 48px; border-radius: 50%; background: var(--gray-800); display: flex; align-items: center; justify-content: center; overflow: hidden; }
        .sv-avatar svg { width: 24px; height: 24px; color: white; }
        .sv-info h4 { font-size: 16px; font-weight: 700; margin-bottom: 4px; color: white; }
        .sv-info p { font-size: 12px; color: var(--gray-400); margin: 0; }
        .sv-msg { background: rgba(255,255,255,0.1); padding: 12px 16px; border-radius: 12px; font-size: 14px; color: var(--gray-200); line-height: 1.5; font-style: italic; }
        .sv-connector { position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); display: flex; flex-direction: column; align-items: center; gap: 12px; z-index: 10; }
        .sv-mp-logo { width: 80px; height: 80px; border-radius: 24px; background: var(--white); display: flex; align-items: center; justify-content: center; box-shadow: 0 0 40px rgba(37,99,235,0.4); z-index: 2; position: relative; }
        .sv-mp-logo svg { width: 40px; height: 40px; }
        .sv-line { width: 2px; height: 80px; background: linear-gradient(to bottom, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0.6) 50%, rgba(255,255,255,0.1) 100%); animation: pulse-line 2s infinite; }
        @keyframes pulse-line { 0% { opacity: 0.3; transform: scaleY(0.8); } 50% { opacity: 1; transform: scaleY(1); } 100% { opacity: 0.3; transform: scaleY(0.8); } }

        @media (max-width: 992px) {
            .social-grid { grid-template-columns: 1fr; gap: 40px; }
            .social-visual { height: 400px; transform: scale(0.9); }
            .sv-card-fb { left: 10%; }
            .sv-card-li { right: 10%; }
        }
        @media (max-width: 768px) {
            .social-visual { transform: scale(0.8); height: 350px; }
            .sv-card-fb { left: 0; }
            .sv-card-li { right: 0; }
        }
        .problem-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 80px; align-items: center; }
        .problem-content h2 { font-size: 44px; margin-bottom: 24px; line-height: 1.1; }
        .problem-content p { font-size: 18px; color: var(--gray-500); margin-bottom: 40px; line-height: 1.8; }
        .problem-list { display: flex; flex-direction: column; gap: 20px; }
        .problem-item { display: flex; gap: 20px; align-items: flex-start; background: var(--white); padding: 20px; border-radius: 16px; border: 1px solid var(--gray-200); transition: var(--transition); }
        .problem-item:hover { box-shadow: var(--shadow-md); transform: translateY(-2px); border-color: rgba(239, 68, 68, 0.3); }
        .problem-icon { width: 32px; height: 32px; background: rgba(239, 68, 68, 0.1); color: #EF4444; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
        .problem-item p { font-size: 16px; color: var(--dark); margin: 0; font-weight: 500; }
        
        .solution-box { background: var(--gradient-dark); padding: 50px; border-radius: 30px; position: relative; overflow: hidden; box-shadow: var(--shadow-xl); }
        .solution-box::before { content: ''; position: absolute; top: -50%; right: -50%; width: 100%; height: 200%; background: radial-gradient(circle, rgba(37,99,235,0.2) 0%, transparent 60%); pointer-events: none; }
        .solution-icon { width: 64px; height: 64px; background: rgba(255,255,255,0.1); border-radius: 20px; display: flex; align-items: center; justify-content: center; margin-bottom: 30px; color: var(--primary); backdrop-filter: blur(10px); }
        .solution-box h3 { color: var(--white); margin-bottom: 20px; font-size: 32px; position: relative; z-index: 1; }
        .solution-box p { color: var(--gray-300); font-size: 18px; margin-bottom: 40px; line-height: 1.8; position: relative; z-index: 1; }
        .solution-box .btn { width: 100%; position: relative; z-index: 1; }

        /* ================= Why MigratePay ================= */
        .benefits-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 30px; perspective: 1000px; }
        .benefit-card { padding: 40px; background: var(--white); border-radius: 24px; border: 1px solid var(--gray-200); box-shadow: var(--shadow-sm); transition: var(--transition-bounce); position: relative; z-index: 1; overflow: hidden; }
        .benefit-card::after { content: ''; position: absolute; top: 0; left: 0; width: 100%; height: 4px; background: var(--gradient-primary); transform: scaleX(0); transition: transform 0.4s ease; transform-origin: left; }
        .benefit-card:hover { transform: translateY(-10px); box-shadow: var(--shadow-lg); border-color: transparent; }
        .benefit-card:hover::after { transform: scaleX(1); }
        .benefit-icon { width: 70px; height: 70px; border-radius: 20px; background: rgba(37, 99, 235, 0.05); color: var(--primary); display: flex; align-items: center; justify-content: center; margin-bottom: 30px; transition: var(--transition-bounce); }
        .benefit-card:hover .benefit-icon { background: var(--primary); color: var(--white); transform: scale(1.1) rotate(5deg); box-shadow: var(--shadow-primary); }
        .benefit-card h3 { font-size: 22px; margin-bottom: 16px; }
        .benefit-card p { color: var(--gray-500); font-size: 16px; line-height: 1.7; }

        /* ================= How It Works ================= */
        /* ================= Process Slider ================= */
        .process-slider-section { position: relative; padding: 100px 0; overflow: hidden; background: linear-gradient(135deg, #f8fafc 0%, #eff6ff 50%, #e0e7ff 100%); }
        .process-slider-section::before { content: ''; position: absolute; inset: 0; background: radial-gradient(circle at 80% 20%, rgba(34,211,238,0.1), transparent 40%), radial-gradient(circle at 20% 80%, rgba(99,102,241,0.05), transparent 40%); pointer-events: none; }
        .process-slider-container { position: relative; max-width: 1200px; margin: 60px auto 0; z-index: 2; }
        
        .process-connector { position: absolute; top: 24px; left: 10%; right: 10%; height: 4px; background: rgba(203, 213, 225, 0.4); border-radius: 4px; z-index: 1; }
        .process-connector-fill { position: absolute; top: 0; left: 0; height: 100%; background: var(--gradient-primary); border-radius: 4px; transition: width 0.6s cubic-bezier(0.16, 1, 0.3, 1); box-shadow: 0 0 10px rgba(37,99,235,0.4); }
        
        .process-slider-track { display: flex; gap: 20px; position: relative; z-index: 2; align-items: stretch; }
        
        .process-card { flex: 1; background: rgba(255, 255, 255, 0.6); backdrop-filter: blur(8px); border-radius: 20px; padding: 30px 24px; border: 1px solid rgba(255, 255, 255, 0.9); box-shadow: 0 4px 15px rgba(0,0,0,0.02); opacity: 0.6; transform: translateY(0); transition: all 0.5s cubic-bezier(0.16, 1, 0.3, 1); cursor: pointer; text-align: center; position: relative; display: flex; flex-direction: column; align-items: center; }
        
        .process-card.active { opacity: 1; transform: translateY(-8px); background: linear-gradient(180deg, #ffffff 0%, #f0f9ff 100%); border-color: rgba(37,99,235,0.3); box-shadow: 0 20px 40px rgba(37,99,235,0.12), inset 0 2px 0 rgba(37,99,235,0.5); z-index: 10; }
        
        .process-step-num { width: 48px; height: 48px; background: #f1f5f9; color: var(--gray-400); font-size: 18px; font-weight: 800; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-bottom: 24px; transition: all 0.5s cubic-bezier(0.16, 1, 0.3, 1); border: 2px solid white; box-shadow: 0 4px 10px rgba(0,0,0,0.05); position: relative; z-index: 2; flex-shrink: 0; }
        .process-card.active .process-step-num { background: var(--gradient-primary); color: white; transform: scale(1.15); box-shadow: 0 0 20px rgba(37,99,235,0.4); border-color: white; }
        
        .process-card h3 { font-size: 18px; font-weight: 800; color: var(--gray-500); margin-bottom: 12px; transition: all 0.4s; line-height: 1.3; }
        .process-card.active h3 { color: var(--dark); }
        
        .process-card p { font-size: 14px; color: var(--gray-400); line-height: 1.6; transition: all 0.4s; margin: 0; }
        .process-card.active p { color: var(--gray-600); }
        
        @media (max-width: 1024px) {
            .process-slider-track { overflow-x: auto; scroll-snap-type: x mandatory; padding-bottom: 30px; margin: 0 -20px; padding-left: 20px; padding-right: 20px; scroll-behavior: smooth; -webkit-overflow-scrolling: touch; }
            .process-slider-track::-webkit-scrollbar { display: none; }
            .process-card { flex: 0 0 280px; scroll-snap-align: center; }
            .process-connector { display: none; }
        }
        @media (prefers-reduced-motion) {
            .process-slider-section::before { display: none; }
            .process-card, .process-step-num, .process-connector-fill { transition: none !important; transform: none !important; }
        }


        /* ================= Use Cases ================= */
        .usecases-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 30px; }
        .usecase-card { padding: 40px 24px; text-align: center; background: var(--white); border-radius: 24px; border: 1px solid var(--gray-200); transition: var(--transition-bounce); }
        .usecase-card:hover { transform: translateY(-10px); box-shadow: var(--shadow-lg); border-color: var(--primary); }
        .usecase-icon { width: 70px; height: 70px; margin: 0 auto 24px; background: var(--gray-100); border-radius: 20px; display: flex; align-items: center; justify-content: center; color: var(--primary); font-size: 32px; transition: var(--transition); }
        .usecase-card:hover .usecase-icon { background: var(--primary); color: var(--white); transform: scale(1.1); box-shadow: var(--shadow-primary); }
        .usecase-card h4 { font-size: 18px; }

        /* ================= What If Something Goes Wrong ================= */
        .dispute-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 80px; align-items: center; }
        .dispute-img { border-radius: 30px; overflow: hidden; box-shadow: var(--shadow-xl); border: 1px solid var(--gray-200); position: relative; background: var(--white); }
        .dispute-list { margin-top: 40px; display: flex; flex-direction: column; gap: 30px; }
        .dispute-item { display: flex; gap: 24px; background: var(--white); padding: 24px; border-radius: 20px; border: 1px solid var(--gray-200); transition: var(--transition); }
        .dispute-item:hover { box-shadow: var(--shadow-md); transform: translateX(10px); border-color: rgba(37,99,235,0.2); }
        .dispute-icon { width: 60px; height: 60px; border-radius: 16px; background: rgba(37,99,235,0.08); color: var(--primary); display: flex; align-items: center; justify-content: center; flex-shrink: 0; font-size: 24px; }
        .dispute-text h4 { font-size: 20px; margin-bottom: 10px; }
        .dispute-text p { color: var(--gray-500); font-size: 16px; margin: 0; line-height: 1.7; }
        
        .mock-ui { padding: 40px; background: var(--gray-100); }
        .mock-card { background: white; padding: 24px; border-radius: 16px; box-shadow: var(--shadow-sm); margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center; transition: var(--transition); border: 1px solid var(--gray-200); }
        .mock-card:hover { transform: scale(1.02); box-shadow: var(--shadow-md); }

        /* ================= Testimonials ================= */
        .testimonials-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 30px; }
        .testimonial-card { background: var(--white); padding: 40px; border-radius: 24px; border: 1px solid var(--gray-200); box-shadow: var(--shadow-sm); transition: var(--transition); position: relative; overflow: hidden; }
        .testimonial-card::before { content: '"'; position: absolute; top: 10px; right: 20px; font-size: 120px; font-family: serif; color: var(--gray-100); z-index: 0; line-height: 1; opacity: 0.5; }
        .testimonial-card:hover { transform: translateY(-10px); box-shadow: var(--shadow-lg); }
        .stars { color: #F59E0B; margin-bottom: 24px; display: flex; gap: 4px; position: relative; z-index: 1; }
        .testimonial-text { font-size: 16px; color: var(--dark); font-weight: 500; margin-bottom: 30px; line-height: 1.8; position: relative; z-index: 1; }
        .testimonial-author { display: flex; align-items: center; gap: 16px; position: relative; z-index: 1; }
        .author-avatar { width: 56px; height: 56px; border-radius: 50%; background: var(--gradient-primary); display: flex; align-items: center; justify-content: center; font-weight: 800; color: var(--white); font-size: 20px; box-shadow: var(--shadow-sm); }
        .author-info h4 { font-size: 18px; margin-bottom: 4px; }
        .author-info p { font-size: 14px; color: var(--gray-500); margin: 0; font-weight: 500; }

        /* ================= FAQ ================= */
        .faq-container { max-width: 1200px; margin: 0 auto; display: grid; grid-template-columns: repeat(2, 1fr); gap: 24px; align-items: start; }
        @media (max-width: 768px) { .faq-container { grid-template-columns: 1fr; } }
        .faq-item { border-radius: 20px; background: var(--white); overflow: hidden; transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1); box-shadow: 0 4px 15px rgba(0,0,0,0.03); border: 1px solid rgba(0,0,0,0.03); position: relative; z-index: 1; }
        .faq-item::before { content: ''; position: absolute; left: 0; top: 0; bottom: 0; width: 0; background: var(--gradient-primary); transition: width 0.4s cubic-bezier(0.16, 1, 0.3, 1); z-index: 0; }
        .faq-item:hover { box-shadow: 0 10px 25px rgba(0,0,0,0.06); transform: translateY(-2px); }
        .faq-item.is-open { box-shadow: 0 20px 40px rgba(37,99,235,0.12); border-color: rgba(37,99,235,0.2); transform: scale(1.02); z-index: 10; }
        .faq-item.is-open::before { width: 6px; }
        
        .faq-question { padding: 28px 32px; display: flex; justify-content: space-between; align-items: center; cursor: pointer; font-weight: 800; font-size: 19px; color: var(--dark); transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1); position: relative; z-index: 1; }
        .faq-item.is-open .faq-question { color: var(--primary); padding-left: 40px; }
        
        .faq-question svg { transition: all 0.5s cubic-bezier(0.68, -0.55, 0.265, 1.55); color: var(--gray-400); background: var(--gray-100); border-radius: 50%; padding: 6px; width: 36px; height: 36px; flex-shrink: 0; margin-left: 20px; }
        .faq-item:hover .faq-question svg { color: var(--primary); background: rgba(37,99,235,0.1); }
        .faq-item.is-open .faq-question svg { transform: rotate(180deg); background: var(--primary); color: var(--white); box-shadow: 0 5px 15px rgba(37,99,235,0.4); }
        
        .faq-answer { padding: 0 32px; max-height: 0; overflow: hidden; transition: all 0.5s cubic-bezier(0.16, 1, 0.3, 1); color: var(--gray-500); font-size: 16px; line-height: 1.8; position: relative; z-index: 1; opacity: 0; transform: translateY(-10px); }
        .faq-item.is-open .faq-answer { padding: 0 40px 32px; max-height: 500px; opacity: 1; transform: translateY(0); }

        /* ================= CTA Section ================= */
        .cta-section { background: var(--gradient-primary); padding: 80px 0; text-align: center; position: relative; overflow: hidden; }
        .cta-bg-shape { position: absolute; inset: 0; background: radial-gradient(circle at 50% 50%, rgba(255,255,255,0.1) 0%, transparent 60%); pointer-events: none; }
        .cta-container { max-width: 800px; margin: 0 auto; position: relative; z-index: 1; }
        .cta-section h2 { color: var(--white); font-size: 52px; margin-bottom: 24px; }
        .cta-section p { color: rgba(255,255,255,0.9); font-size: 22px; margin-bottom: 40px; line-height: 1.6; }
        .cta-actions { display: flex; gap: 20px; justify-content: center; }
        
        /* ================= Footer ================= */
        footer { background: var(--dark); color: var(--gray-300); padding: 100px 0 40px; position: relative; overflow: hidden; }
        footer::before { content: ''; position: absolute; top: 0; left: 0; width: 100%; height: 4px; background: var(--gradient-primary); }
        .footer-grid { display: grid; grid-template-columns: 2fr 1fr 1fr 1fr; gap: 80px; margin-bottom: 80px; position: relative; z-index: 1; }
        .footer-brand .logo { color: var(--white); margin-bottom: 24px; font-size: 28px; }
        .footer-brand p { font-size: 16px; margin-bottom: 30px; max-width: 350px; line-height: 1.8; }
        .footer-heading { color: var(--white); font-size: 20px; margin-bottom: 30px; font-weight: 700; }
        .footer-links { display: flex; flex-direction: column; gap: 16px; }
        .footer-links a { color: var(--gray-300); font-size: 16px; transition: var(--transition); display: inline-flex; width: fit-content; }
        .footer-links a:hover { color: var(--primary); transform: translateX(5px); }
        .footer-bottom { border-top: 1px solid rgba(255,255,255,0.1); padding-top: 40px; display: flex; justify-content: space-between; align-items: center; font-size: 15px; position: relative; z-index: 1; }
        
        .social-icons { display: flex; gap: 16px; }
        .social-icons a { width: 44px; height: 44px; border-radius: 50%; background: rgba(255,255,255,0.05); display: flex; align-items: center; justify-content: center; color: var(--white); transition: var(--transition-bounce); }
        .social-icons a:hover { background: var(--primary); transform: translateY(-5px); box-shadow: var(--shadow-primary); }

        /* ================= The Comparison Matrix ================= */
        .comparison-section {
            background: linear-gradient(180deg, var(--gray-100) 0%, #FFFFFF 100%);
            position: relative;
        }
        .comparison-cards-grid {
            display: grid;
            grid-template-columns: 1fr 1fr 1.15fr;
            gap: 24px;
            align-items: stretch;
            margin-top: 50px;
        }
        .compare-col {
            background: var(--white);
            border-radius: 24px;
            border: 1px solid var(--gray-200);
            padding: 36px 28px;
            display: flex;
            flex-direction: column;
            transition: var(--transition);
            position: relative;
            box-shadow: var(--shadow-sm);
        }
        .compare-col:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-md);
        }
        .compare-col.danger-col {
            background: linear-gradient(180deg, #FFF5F5 0%, #FFFFFF 100%);
            border-color: #FED7D7;
        }
        .compare-col.warning-col {
            background: linear-gradient(180deg, #F8FAFC 0%, #FFFFFF 100%);
            border-color: var(--gray-300);
        }
        .compare-col.featured-col {
            background: linear-gradient(180deg, #EFF6FF 0%, #FFFFFF 100%);
            border: 2px solid var(--primary);
            box-shadow: 0 20px 40px -10px rgba(37,99,235,0.22);
            transform: scale(1.03);
            z-index: 2;
        }
        .compare-col.featured-col:hover {
            transform: scale(1.05) translateY(-5px);
            box-shadow: 0 25px 50px -10px rgba(37,99,235,0.32);
        }
        .compare-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            padding: 6px 12px;
            border-radius: 100px;
            width: fit-content;
            margin-bottom: 16px;
        }
        .pill-danger { background: rgba(239, 68, 68, 0.12); color: #DC2626; }
        .pill-warning { background: rgba(245, 158, 11, 0.15); color: #D97706; }
        .pill-featured { background: var(--gradient-primary); color: #FFFFFF; box-shadow: 0 4px 12px rgba(37,99,235,0.3); }
        
        .compare-col h3 {
            font-size: 24px;
            margin-bottom: 8px;
            color: var(--dark);
        }
        .compare-subtitle {
            font-size: 14px;
            color: var(--gray-500);
            margin-bottom: 24px;
            min-height: 42px;
            line-height: 1.5;
        }
        .compare-fee-tag {
            background: rgba(0,0,0,0.03);
            border-radius: 12px;
            padding: 12px 16px;
            margin-bottom: 24px;
            font-size: 14px;
            font-weight: 700;
        }
        .featured-col .compare-fee-tag {
            background: rgba(37,99,235,0.08);
            color: var(--primary);
        }
        .compare-features-list {
            display: flex;
            flex-direction: column;
            gap: 14px;
            margin-bottom: 30px;
            flex-grow: 1;
        }
        .compare-item {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            font-size: 14px;
            line-height: 1.5;
            color: var(--gray-800);
        }
        .compare-icon-bad {
            color: #EF4444;
            flex-shrink: 0;
            margin-top: 2px;
        }
        .compare-icon-mid {
            color: #F59E0B;
            flex-shrink: 0;
            margin-top: 2px;
        }
        .compare-icon-good {
            color: #10B981;
            flex-shrink: 0;
            margin-top: 2px;
        }

        /* ================= The 3 Guarantees ================= */
        .guarantees-section {
            background: var(--dark);
            color: var(--white);
            position: relative;
            overflow: hidden;
            padding: 100px 0;
        }
        .guarantees-section::before {
            content: '';
            position: absolute;
            top: -20%;
            left: 50%;
            transform: translateX(-50%);
            width: 800px;
            height: 800px;
            background: radial-gradient(circle, rgba(37,99,235,0.18) 0%, transparent 70%);
            pointer-events: none;
        }
        .guarantees-section .section-title {
            color: var(--white);
        }
        .guarantees-section .section-subtitle {
            color: var(--gray-300);
        }
        .guarantees-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 28px;
            position: relative;
            z-index: 2;
        }
        .guarantee-card {
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border-radius: 24px;
            padding: 38px 32px;
            transition: var(--transition-bounce);
            position: relative;
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }
        .guarantee-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: var(--gradient-primary);
            opacity: 0;
            transition: opacity 0.4s ease;
        }
        .guarantee-card:hover {
            transform: translateY(-8px);
            border-color: rgba(37, 99, 235, 0.5);
            background: rgba(255, 255, 255, 0.07);
            box-shadow: 0 20px 40px rgba(0,0,0,0.4);
        }
        .guarantee-card:hover::before {
            opacity: 1;
        }
        .guarantee-icon-box {
            width: 64px;
            height: 64px;
            border-radius: 18px;
            background: rgba(37, 99, 235, 0.15);
            color: #60A5FA;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 24px;
            border: 1px solid rgba(37, 99, 235, 0.3);
            transition: var(--transition);
        }
        .guarantee-card:hover .guarantee-icon-box {
            transform: scale(1.1);
            background: var(--primary);
            color: #FFFFFF;
            box-shadow: 0 0 25px rgba(37, 99, 235, 0.6);
        }
        .guarantee-card h3 {
            color: var(--white);
            font-size: 22px;
            margin-bottom: 14px;
        }
        .guarantee-card p {
            color: var(--gray-300);
            font-size: 15px;
            line-height: 1.7;
            margin-bottom: 24px;
            flex-grow: 1;
        }
        .guarantee-tag {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 12px;
            font-weight: 700;
            color: #10B981;
            background: rgba(16, 185, 129, 0.12);
            border: 1px solid rgba(16, 185, 129, 0.25);
            padding: 6px 14px;
            border-radius: 100px;
            width: fit-content;
        }

        /* ================= Interactive Calculator ================= */
        .calculator-section {
            background: var(--white);
            position: relative;
        }
        .calc-home-wrapper {
            max-width: 1050px;
            margin: 0 auto;
            background: var(--gray-100);
            border-radius: 28px;
            border: 1px solid var(--gray-200);
            box-shadow: var(--shadow-lg);
            overflow: hidden;
            display: grid;
            grid-template-columns: 1.2fr 1fr;
            align-items: stretch;
        }
        .calc-home-controls {
            padding: 36px 36px;
            display: flex;
            flex-direction: column;
            gap: 20px;
            justify-content: space-between;
        }
        .calc-input-label {
            font-size: 14px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: var(--dark);
            margin-bottom: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .calc-input-wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }
        .calc-input-prefix {
            position: absolute;
            left: 20px;
            font-size: 24px;
            font-weight: 800;
            color: var(--dark);
        }
        .calc-amount-input {
            width: 100%;
            padding: 14px 20px 14px 50px;
            font-size: 24px;
            font-weight: 800;
            font-family: var(--font-heading);
            color: var(--dark);
            background: var(--white);
            border: 2px solid var(--gray-300);
            border-radius: 16px;
            outline: none;
            transition: var(--transition);
        }
        .calc-amount-input:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.15);
        }
        .calc-presets-row {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            margin-top: 10px;
        }
        .calc-preset-chip {
            padding: 7px 14px;
            background: var(--white);
            border: 1px solid var(--gray-300);
            border-radius: 100px;
            font-size: 13px;
            font-weight: 700;
            color: var(--dark);
            cursor: pointer;
            transition: var(--transition);
        }
        .calc-preset-chip:hover {
            border-color: var(--primary);
            color: var(--primary);
            transform: translateY(-2px);
        }
        .calc-preset-chip.active {
            background: var(--primary);
            border-color: var(--primary);
            color: var(--white);
            box-shadow: 0 4px 10px rgba(37,99,235,0.3);
        }
        .calc-payer-tabs {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 8px;
            background: var(--white);
            padding: 6px;
            border-radius: 14px;
            border: 1px solid var(--gray-200);
        }
        .calc-payer-btn {
            padding: 10px 8px;
            text-align: center;
            border-radius: 10px;
            font-size: 12px;
            font-weight: 700;
            cursor: pointer;
            border: none;
            background: transparent;
            color: var(--gray-500);
            transition: var(--transition);
        }
        .calc-payer-btn.active {
            background: var(--gradient-primary);
            color: var(--white);
            box-shadow: 0 4px 12px rgba(37,99,235,0.25);
        }

        /* Fee Schedule Box (Fills empty space) */
        .calc-schedule-box {
            background: var(--white);
            border: 1px solid var(--gray-200);
            border-radius: 16px;
            padding: 16px 18px;
            display: flex;
            flex-direction: column;
            gap: 12px;
            box-shadow: var(--shadow-sm);
        }
        .calc-schedule-title {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 12px;
            font-weight: 800;
            color: var(--dark);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .calc-schedule-sub {
            font-size: 12px;
            font-weight: 700;
            color: var(--primary);
            text-transform: none;
            letter-spacing: 0;
        }
        .calc-schedule-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 8px;
        }
        .calc-tier-card {
            background: var(--gray-100);
            border: 1.5px solid var(--gray-200);
            border-radius: 12px;
            padding: 10px 6px;
            text-align: center;
            cursor: pointer;
            transition: var(--transition);
        }
        .calc-tier-card:hover {
            border-color: var(--primary);
            transform: translateY(-2px);
        }
        .calc-tier-card.active {
            background: rgba(37, 99, 235, 0.08);
            border-color: var(--primary);
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.15);
            transform: translateY(-2px);
        }
        .tier-range {
            font-size: 11px;
            font-weight: 700;
            color: var(--gray-500);
            margin-bottom: 2px;
        }
        .calc-tier-card.active .tier-range {
            color: var(--primary);
        }
        .tier-pct {
            font-size: 16px;
            font-weight: 900;
            color: var(--dark);
            font-family: var(--font-heading);
        }
        .calc-tier-card.active .tier-pct {
            color: var(--primary);
        }
        .tier-best {
            color: #10B981;
        }
        .calc-extra-info {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 11.5px;
            font-weight: 600;
            color: var(--gray-500);
            padding-top: 2px;
        }
        .calc-extra-info svg {
            color: #10B981;
            flex-shrink: 0;
        }

        .calc-home-result {
            background: var(--gradient-dark);
            color: var(--white);
            padding: 36px 36px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
            overflow: hidden;
        }
        .calc-home-result::before {
            content: '';
            position: absolute;
            top: -40%;
            right: -40%;
            width: 300px;
            height: 300px;
            background: radial-gradient(circle, rgba(37,99,235,0.3) 0%, transparent 70%);
            pointer-events: none;
        }
        .calc-res-header {
            font-size: 13px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #93C5FD;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .calc-res-breakdown {
            display: flex;
            flex-direction: column;
            gap: 14px;
            margin-bottom: 22px;
        }
        .calc-res-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 15px;
            color: var(--gray-300);
        }
        .calc-res-row span:last-child {
            font-weight: 700;
            color: var(--white);
        }
        .calc-res-divider {
            height: 1px;
            background: rgba(255, 255, 255, 0.12);
            margin: 4px 0;
        }
        .calc-highlight-box {
            background: rgba(255, 255, 255, 0.06);
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: 16px;
            padding: 16px 18px;
            display: flex;
            flex-direction: column;
            gap: 10px;
            backdrop-filter: blur(10px);
        }
        .calc-highlight-item {
            display: flex;
            justify-content: space-between;
            align-items: baseline;
        }
        .calc-highlight-item .label {
            font-size: 13px;
            color: var(--gray-300);
            font-weight: 600;
        }
        .calc-highlight-item .val {
            font-size: 22px;
            font-weight: 900;
            color: #10B981;
            font-family: var(--font-heading);
        }
        .calc-highlight-item.buyer-val .val {
            color: #60A5FA;
        }
        .calc-guarantee-note {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 12px;
            color: var(--gray-300);
            margin-top: 14px;
            padding: 10px 14px;
            background: rgba(16, 185, 129, 0.1);
            border-radius: 10px;
            border: 1px solid rgba(16, 185, 129, 0.2);
        }
        .calc-guarantee-note svg {
            color: #10B981;
            flex-shrink: 0;
        }

        /* ================= Security Credentials Strip ================= */
        .security-strip-section {
            background: var(--dark);
            border-top: 1px solid rgba(255,255,255,0.06);
            border-bottom: 1px solid rgba(255,255,255,0.06);
            padding: 40px 0;
        }
        .security-strip-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 30px;
        }
        .security-strip-item {
            display: flex;
            align-items: center;
            gap: 16px;
        }
        .security-strip-icon {
            width: 48px;
            height: 48px;
            border-radius: 14px;
            background: rgba(37,99,235,0.12);
            color: #60A5FA;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            border: 1px solid rgba(37,99,235,0.25);
        }
        .security-strip-text h4 {
            font-size: 15px;
            font-weight: 700;
            color: var(--white);
            margin-bottom: 2px;
        }
        .security-strip-text p {
            font-size: 12px;
            color: var(--gray-400);
            margin: 0;
        }

        /* ================= Responsive ================= */
        @media (max-width: 1200px) {
            .hero-content h1 { font-size: 52px; }
            .problem-content h2, .cta-section h2 { font-size: 40px; }
            .dispute-grid { gap: 40px; }
            .footer-grid { gap: 40px; }
            .hero-container { grid-template-columns: 1fr 1fr; gap: 40px; }
        }

        @media (max-width: 1024px) {
            .hero-container, .problem-grid, .comparison-container, .dispute-grid { grid-template-columns: 1fr; }
            .benefits-grid, .usecases-grid, .testimonials-grid { grid-template-columns: repeat(2, 1fr); }
            .comparison-cards-grid { grid-template-columns: 1fr; gap: 30px; }
            .compare-col.featured-col { transform: none; }
            .compare-col.featured-col:hover { transform: translateY(-5px); }
            .guarantees-grid { grid-template-columns: 1fr; }
            .calc-home-wrapper { grid-template-columns: 1fr; }
            .security-strip-grid { grid-template-columns: repeat(2, 1fr); gap: 24px; }
            .hero { padding-top: 150px; text-align: center; }
            .hero-content p { margin: 0 auto 40px; }
            .hero-actions { justify-content: center; }
            .hero-animation-container { height: 450px; margin-top: 40px; transform: scale(0.9); transform-origin: top center; }
            .anim-char-buyer { left: 10px; }
            .anim-char-seller { right: 10px; }
            .chat-left { left: 60px; }
            .chat-right { right: 60px; }
            .anim-q-left { left: 70px; }
            .anim-q-right { right: 70px; }
            .anim-thought-left { left: 40px; }
            .anim-thought-right { right: 40px; }
            .anim-review-card { left: 40px; }
            .anim-seller-working { right: 40px; }
            .footer-grid { grid-template-columns: 1fr 1fr; }
            .problem-content { text-align: center; }
            .problem-item { text-align: left; }
        }
        /* ================= Our Partners Section ================= */
        .partners-section {
            padding: 100px 0;
            position: relative;
            background: linear-gradient(135deg, #eff6ff 0%, #e0e7ff 50%, #cffafe 100%);
            overflow: hidden;
            z-index: 1;
        }
        .partners-bg-orbs {
            position: absolute;
            top: 0; left: 0; width: 100%; height: 100%;
            pointer-events: none; z-index: -1;
            overflow: hidden;
        }
        .partners-bg-orbs::before, .partners-bg-orbs::after {
            content: '';
            position: absolute;
            width: 600px;
            height: 600px;
            border-radius: 50%;
            filter: blur(100px);
            opacity: 0.4;
            animation: float 20s infinite alternate ease-in-out;
        }
        .partners-bg-orbs::before {
            background: rgba(167, 139, 250, 0.4);
            top: -200px; left: -100px;
        }
        .partners-bg-orbs::after {
            background: rgba(56, 189, 248, 0.4);
            bottom: -200px; right: -100px;
            animation-delay: -10s;
        }
        .partners-header {
            text-align: center;
            margin-bottom: 60px;
            position: relative;
            z-index: 2;
        }
        .partners-eyebrow {
            display: inline-block;
            font-size: 13px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: var(--primary);
            background: rgba(37, 99, 235, 0.1);
            padding: 6px 16px;
            border-radius: 100px;
            margin-bottom: 16px;
            border: 1px solid rgba(37, 99, 235, 0.2);
        }
        .partners-header h2 {
            font-size: 42px;
            margin-bottom: 20px;
            color: var(--dark);
        }
        .partners-header p {
            font-size: 18px;
            color: var(--gray-500);
            max-width: 600px;
            margin: 0 auto;
        }
        
        .partners-marquee-container {
            position: relative;
            max-width: 1400px;
            margin: 0 auto;
            overflow: hidden;
            mask-image: linear-gradient(to right, transparent, black 10%, black 90%, transparent);
            -webkit-mask-image: linear-gradient(to right, transparent, black 10%, black 90%, transparent);
            padding: 20px 0;
            z-index: 2;
        }
        .partners-marquee {
            display: flex;
            gap: 30px;
            width: max-content;
            animation: marqueeScroll 40s linear infinite;
        }
        .partners-marquee:hover {
            animation-play-state: paused;
        }
        @keyframes marqueeScroll {
            0% { transform: translateX(0); }
            100% { transform: translateX(calc(-50% - 15px)); }
        }
        
        .partner-card {
            width: 260px;
            height: 120px;
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.9);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 15px rgba(0,0,0,0.02);
            transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
            cursor: pointer;
            flex-shrink: 0;
            padding: 20px;
        }
        .partner-card:hover {
            transform: translateY(-5px) scale(1.02);
            box-shadow: 0 15px 30px rgba(37,99,235,0.08);
            border-color: rgba(37,99,235,0.2);
            background: rgba(255, 255, 255, 0.9);
        }
        .partner-placeholder {
            font-weight: 800;
            font-size: 20px;
            color: var(--gray-400);
            font-family: var(--font-heading);
            letter-spacing: -0.02em;
            text-transform: uppercase;
            transition: color 0.3s ease;
        }
        .partner-card:hover .partner-placeholder {
            color: var(--primary);
        }
        
        @media (max-width: 768px) {
            .partners-section { padding: 60px 0; }
            .partners-header h2 { font-size: 32px; }
            .partner-card { width: 200px; height: 100px; }
            .partner-placeholder { font-size: 16px; }
        }
        @media (max-width: 480px) {
            .partners-header h2 { font-size: 28px; }
            .partner-card { width: 160px; height: 80px; }
            .partner-placeholder { font-size: 14px; }
        }
        @media (prefers-reduced-motion: reduce) {
            .partners-marquee { animation-duration: 120s; }
        }

        @media (max-width: 768px) {
            .nav-menu, .header-actions { display: none; }
            .mobile-menu-btn { display: block; }
            .benefits-grid, .usecases-grid, .testimonials-grid, .footer-grid { grid-template-columns: 1fr; }
            .calc-home-controls, .calc-home-result { padding: 28px 20px; }
            .calc-payer-tabs { grid-template-columns: 1fr; }
            .security-strip-grid { grid-template-columns: 1fr; gap: 20px; }
            .section { padding: 50px 0; }
            .section-header { margin-bottom: 40px; }
            .cta-section { padding: 60px 0; }
            .hero-content h1 { font-size: 36px; line-height: 1.15; }
            .section-title { font-size: 28px; line-height: 1.25; }
            .problem-content h2 { font-size: 32px; line-height: 1.2; }
            .solution-box h3 { font-size: 26px; }
            .cta-content h2 { font-size: 32px; line-height: 1.25; }
            .compare-card h2 { font-size: 32px; }
            .benefit-card h3 { font-size: 20px; }
            
            .cta-actions { flex-direction: column; }
            .hero-animation-container { transform: scale(0.75); margin-top: 20px; margin-bottom: -100px; border: none; box-shadow: none; background: transparent; }
            @media (prefers-reduced-motion) {
                .hero-animation-container { display: none; }
            }
            /* Process slider mobile */
            .process-slider-section { padding: 60px 0; }
            .process-slider-container { margin-top: 40px; }
            .compare-card { padding: 40px 30px; }
            .footer-bottom { flex-direction: column; gap: 20px; text-align: center; }
        }

        /* --- Micro Mobile Adjustments (< 480px) --- */
        @media (max-width: 480px) {
            .hero-content h1 { font-size: 30px; line-height: 1.2; word-break: break-word; }
            .section-title { font-size: 24px; line-height: 1.3; word-break: break-word; }
            .problem-content h2 { font-size: 26px; line-height: 1.25; word-break: break-word; }
            .solution-box h3 { font-size: 22px; word-break: break-word; }
            .cta-content h2 { font-size: 26px; line-height: 1.3; }
            .compare-card h2 { font-size: 26px; line-height: 1.25; }
            .section { padding: 40px 0; }
            .container { padding: 0 16px; }
            .process-step-num { width: 36px; height: 36px; font-size: 14px; margin-bottom: 16px; }
            .process-card h3 { font-size: 16px; }
            .btn { padding: 12px 24px; font-size: 13px; }
        }
    </style>
</head>
<body>

    <!-- PHP Banner Logic (Preserved) -->
    @if(isset($activeBanner) && $activeBanner)
    @php
        $design = is_array($activeBanner->design) ? $activeBanner->design : json_decode($activeBanner->design, true) ?? [];
        $elements = is_array($activeBanner->elements) ? $activeBanner->elements : json_decode($activeBanner->elements, true) ?? [];
        $bgColor = $design['bg_color'] ?? '#0f172a';
        $textColor = $design['text_color'] ?? '#ffffff';
    @endphp
    <div id="marketing-banner" style="background-color: {{ $bgColor }}; color: {{ $textColor }}; display: flex; align-items: center; justify-content: center; gap: 24px; padding: 12px 24px; position: relative; transition: all 0.3s ease; flex-wrap: wrap; z-index: 2000;">
        @foreach($elements as $element)
            @if($element['type'] === 'heading')
                <strong style="font-size: {{ $element['fontSize'] ?? 16 }}px; font-weight: {{ $element['fontWeight'] ?? 800 }}; letter-spacing: 0.05em; text-transform: uppercase;">{{ $element['content'] }}</strong>
            @elseif($element['type'] === 'description')
                <span style="font-size: {{ $element['fontSize'] ?? 14 }}px; font-weight: 500;">{{ $element['content'] }}</span>
            @elseif($element['type'] === 'button')
                <a href="{{ $element['link'] ?? '#' }}" style="background: {{ $textColor }}; color: {{ $bgColor }}; padding: 6px 20px; border-radius: 100px; font-weight: 800; font-size: 13px; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); transition: transform 0.2s;">
                    {{ str_replace('→', '', $element['content']) }}
                    @if(str_contains($element['content'], '→') || str_contains($element['content'], '➔'))
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                    @endif
                </a>
            @endif
        @endforeach
        
        @if($design['show_close'] ?? true)
            <button onclick="document.getElementById('marketing-banner').style.display='none'; document.getElementById('site-header').style.top='0';" style="position: absolute; right: 24px; top: 50%; transform: translateY(-50%); background: none; border: none; color: {{ $textColor }}; cursor: pointer; font-size: 20px; opacity: 0.7; padding: 5px; display: flex; align-items: center; justify-content: center; transition: opacity 0.2s;" onmouseover="this.style.opacity='1'" onmouseout="this.style.opacity='0.7'">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
            </button>
        @endif
    </div>
    @endif

    <!-- Header -->
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
                    <a href="{{ route('auth') }}" class="btn-login-header">
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

    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-bg-shape-1"></div>
        <div class="hero-bg-shape-2"></div>
        <div class="container hero-container">
            <div class="hero-content">
                <div class="badge">Bangladesh's Smarter Way to Handle Online Deals</div>
                <h1>Don't Just Trust the Deal.<br><span>Protect It.</span></h1>
                <p>Buying from a stranger? Hiring someone online? Selling to a new customer? MigratePay helps both sides manage the deal through a structured escrow and mediation process.</p>
                <div class="hero-actions">
                    <a href="{{ route('auth') }}" class="btn btn-primary">Start a Deal <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="margin-left: 8px;"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg></a>
                    <a href="#how-it-works" class="btn btn-outline">See How It Works</a>
                </div>
            </div>
            
            <div class="hero-animation-container" id="heroAnim" data-scene="1">
                <!-- Character Left (Buyer) -->
                <div class="anim-char anim-char-buyer">
                    <div class="char-avatar" style="background: var(--primary);">B</div>
                    <div class="char-label">Buyer</div>
                </div>
                
                <!-- Character Right (Seller) -->
                <div class="anim-char anim-char-seller">
                    <div class="char-avatar" style="background: var(--secondary);">S</div>
                    <div class="char-label">Seller</div>
                </div>

                <!-- Scene 1: Stranger Deal -->
                <div class="anim-scene anim-scene-1">
                    <div class="anim-chat chat-left">Can you build this website for me?</div>
                    <div class="anim-chat chat-right">Sure. I'll deliver it in 7 days.</div>
                    <div class="anim-deal-amount">Deal Amount: ৳30,000</div>
                </div>

                <!-- Scene 2: Confusion -->
                <div class="anim-scene anim-scene-2">
                    <div class="anim-question anim-q-left">?</div>
                    <div class="anim-question anim-q-right">?</div>
                    <div class="anim-thought anim-thought-left">Should I pay first?<br><span class="thought-sub">What if they disappear after I pay?</span></div>
                    <div class="anim-thought anim-thought-right">What if I finish the work and don't get paid?<br><span class="thought-sub">What if they refuse to pay?</span></div>
                </div>

                <!-- Scene 3: Risk -->
                <div class="anim-scene anim-scene-3">
                    <div class="anim-risk-card">
                        <div class="risk-amount">৳30,000</div>
                        <div class="risk-arrow">Direct Payment</div>
                        <div class="risk-warnings">
                            <div class="risk-badge">UNKNOWN SELLER</div>
                            <div class="risk-badge">NO STRUCTURED DEAL</div>
                            <div class="risk-badge risk-badge-red">WHO GOES FIRST?</div>
                        </div>
                    </div>
                </div>

                <!-- Scene 4: The Proposal -->
                <div class="anim-scene anim-scene-4">
                    <div class="anim-chat chat-left" style="top: 100px;">Let's complete the deal in MigratePay.</div>
                    <div class="anim-chat chat-right" style="top: 200px; max-width: 250px; border-radius: 20px; border-bottom-right-radius: 4px;">Sure! It's highly secure and much cheaper than freelancing sites.</div>
                </div>

                <!-- Scene 5: MigratePay Appears -->
                <div class="anim-scene anim-scene-5">
                    <div class="anim-mp-layer">
                        <div class="mp-logo">MigratePay</div>
                        <div class="mp-msg">Let's structure the deal.</div>
                    </div>
                    <div class="anim-structured-deal">
                        <div class="sd-title">Website Development</div>
                        <div class="sd-row">Amount: <span>৳30,000</span></div>
                        <div class="sd-row">Delivery: <span>7 Days</span></div>
                        <div class="sd-status">Status: Deal Created</div>
                    </div>
                </div>

                <!-- Scene 6: Payment Secured -->
                <div class="anim-scene anim-scene-6">
                    <div class="anim-mp-layer active-layer">
                        <div class="mp-logo">MigratePay</div>
                    </div>
                    <div class="anim-payment-flow">
                        <div class="flow-step flow-buyer">Buyer Payment</div>
                        <div class="flow-arrow">↓</div>
                        <div class="flow-step flow-mp">MigratePay Vault <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg></div>
                        <div class="flow-arrow">↓</div>
                        <div class="flow-step flow-status">Deal in Progress</div>
                    </div>
                </div>

                <!-- Scene 7: Seller Works -->
                <div class="anim-scene anim-scene-7">
                    <div class="anim-mp-layer active-layer">
                        <div class="mp-logo">MigratePay</div>
                    </div>
                    <div class="anim-seller-working">
                        <div class="seller-laptop">💻 Working...</div>
                    </div>
                    <div class="anim-progress-card">
                        <div class="prog-step done">Deal Started</div>
                        <div class="prog-step active">Work in Progress <span class="loader-dots">...</span></div>
                        <div class="prog-step">Delivered</div>
                    </div>
                </div>

                <!-- Scene 8: Delivery & Review -->
                <div class="anim-scene anim-scene-8">
                    <div class="anim-mp-layer active-layer">
                        <div class="mp-logo">MigratePay</div>
                    </div>
                    <div class="anim-chat chat-right">Project delivered.</div>
                    <div class="anim-review-card">
                        <div class="rev-status">Delivery Received</div>
                        <div class="rev-action">Reviewing Deal...</div>
                    </div>
                </div>

                <!-- Scene 9: Successful Deal -->
                <div class="anim-scene anim-scene-9">
                    <div class="anim-mp-layer active-layer">
                        <div class="mp-logo">MigratePay</div>
                    </div>
                    <div class="anim-success-card">
                        <div class="success-icon">✓</div>
                        <div class="success-title">Deal Completed</div>
                    </div>
                    <div class="anim-protected p-buyer">Protected</div>
                    <div class="anim-protected p-seller">Protected</div>
                </div>
                
                <!-- Scene 10 is an empty state to transition back seamlessly -->
                <div class="anim-scene anim-scene-10"></div>
                
                <div class="hero-pagination" id="heroDots">
                    <div class="dot active" data-target="1"></div>
                    <div class="dot" data-target="2"></div>
                    <div class="dot" data-target="3"></div>
                    <div class="dot" data-target="4"></div>
                    <div class="dot" data-target="5"></div>
                    <div class="dot" data-target="6"></div>
                    <div class="dot" data-target="7"></div>
                    <div class="dot" data-target="8"></div>
                    <div class="dot" data-target="9"></div>
                </div>
            </div>
        </div>
    </section>

    <!-- Social Media Hook -->
    <section class="section social-deal-section">
        <div class="container">
            <div class="social-grid">
                <div class="social-content reveal-left">
                    <div class="badge">Create Demand</div>
                    <h2>Found a Match on Social Media?</h2>
                    <p>You've connected with the right expert or the perfect client on Facebook or LinkedIn. But how do you handle the payment without risking your money or your hard work?</p>
                    <p style="color: var(--gray-400); font-size: 16px; margin-bottom: 30px;">Direct bank transfers offer no protection. Escrow platforms charge enormous fees. Don't let a great connection turn into a risky transaction.</p>
                    <a href="{{ route('auth') }}" class="btn btn-primary" style="background: var(--white); color: var(--dark);">Bring the Deal to MigratePay</a>
                </div>
                
                <div class="social-visual reveal-right delay-200">
                    <div class="sv-card sv-card-fb">
                        <div class="sv-header">
                            <div class="sv-avatar" style="background: #1877F2;">
                                <svg viewBox="0 0 24 24" fill="currentColor"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"></path></svg>
                            </div>
                            <div class="sv-info">
                                <h4>Expert Found</h4>
                                <p>Facebook Group</p>
                            </div>
                        </div>
                        <div class="sv-msg">"I can fix your website issue today. My fee is ৳10,000 in advance."</div>
                    </div>
                    
                    <div class="sv-connector">
                        <div class="sv-line"></div>
                        <div class="sv-mp-logo">
                            <svg viewBox="0 0 24 24" fill="none" stroke="#2563EB" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                                <polyline points="9 12 11 14 15 10"></polyline>
                            </svg>
                        </div>
                        <div class="sv-line"></div>
                    </div>
                    
                    <div class="sv-card sv-card-li">
                        <div class="sv-header">
                            <div class="sv-avatar" style="background: #0A66C2;">
                                <svg viewBox="0 0 24 24" fill="currentColor"><path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z"></path><rect x="2" y="9" width="4" height="12"></rect><circle cx="4" cy="4" r="2"></circle></svg>
                            </div>
                            <div class="sv-info">
                                <h4>Client Found</h4>
                                <p>LinkedIn Messages</p>
                            </div>
                        </div>
                        <div class="sv-msg">"We love your portfolio. We are ready to hire you for this project."</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Comparison Matrix Section -->
    <section class="section comparison-section" id="comparison">
        <div class="container">
            <div class="section-header reveal">
                <div class="badge">Direct Comparison</div>
                <h2 class="section-title">The Smartest Way to Close Online Deals</h2>
                <p class="section-subtitle">Direct transfers leave you exposed to scams. Freelance marketplaces charge 20% fees and restrict your communication. MigratePay gives you total security with minimal cost.</p>
            </div>
            
            <div class="comparison-cards-grid">
                <!-- Direct Transfer Card -->
                <div class="compare-col danger-col reveal delay-100">
                    <span class="compare-pill pill-danger">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                        High Risk
                    </span>
                    <h3>Direct Transfer</h3>
                    <p class="compare-subtitle">bKash, Nagad, Bank Wire, or Crypto sent directly to a stranger online.</p>
                    <div class="compare-fee-tag" style="color: #DC2626;">Hidden Cost: 100% Capital at Risk</div>
                    <div class="compare-features-list">
                        <div class="compare-item">
                            <span class="compare-icon-bad"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg></span>
                            <span><strong>No Escrow Holding:</strong> Buyer pays in advance, seller can vanish instantly.</span>
                        </div>
                        <div class="compare-item">
                            <span class="compare-icon-bad"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg></span>
                            <span><strong>Unpaid Deliveries:</strong> Seller delivers work, but buyer blocks without paying.</span>
                        </div>
                        <div class="compare-item">
                            <span class="compare-icon-bad"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg></span>
                            <span><strong>Zero Recourse:</strong> Bank or MFS will not refund peer-to-peer transfers.</span>
                        </div>
                        <div class="compare-item">
                            <span class="compare-icon-bad"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg></span>
                            <span><strong>Constant Hesitation:</strong> "Who sends first?" deadlock delays or ruins deals.</span>
                        </div>
                    </div>
                    <div style="font-size: 13px; color: #DC2626; font-weight: 600; text-align: center; margin-top: auto; padding-top: 10px;">
                        ⚠️ Over 40% of internet deals report fraud or payment disputes.
                    </div>
                </div>

                <!-- Traditional Marketplaces Card -->
                <div class="compare-col warning-col reveal delay-200">
                    <span class="compare-pill pill-warning">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path></svg>
                        Expensive & Rigid
                    </span>
                    <h3>Fiverr / Upwork</h3>
                    <p class="compare-subtitle">Traditional freelance marketplaces designed for hiring strangers inside their walled garden.</p>
                    <div class="compare-fee-tag" style="color: #D97706;">Platform Cost: Up to 20% Fee</div>
                    <div class="compare-features-list">
                        <div class="compare-item">
                            <span class="compare-icon-mid"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg></span>
                            <span><strong>Excessive Cut:</strong> 20% cut taken from earnings on deals you found yourself.</span>
                        </div>
                        <div class="compare-item">
                            <span class="compare-icon-mid"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg></span>
                            <span><strong>Strict Policies:</strong> Bringing clients from Facebook/LinkedIn can get you permanently banned.</span>
                        </div>
                        <div class="compare-item">
                            <span class="compare-icon-mid"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg></span>
                            <span><strong>14-Day Payout Delay:</strong> Mandatory holding periods before withdrawing your hard-earned funds.</span>
                        </div>
                        <div class="compare-item">
                            <span class="compare-icon-mid"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg></span>
                            <span><strong>Automated Bots:</strong> Frustrating canned support when dealing with misunderstandings.</span>
                        </div>
                    </div>
                    <div style="font-size: 13px; color: var(--gray-500); text-align: center; margin-top: auto; padding-top: 10px;">
                        Great for finding new clients, terrible for deals you already negotiated.
                    </div>
                </div>

                <!-- MigratePay Card (Featured) -->
                <div class="compare-col featured-col reveal delay-300">
                    <span class="compare-pill pill-featured">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
                        The Golden Standard
                    </span>
                    <h3>MigratePay</h3>
                    <p class="compare-subtitle">A neutral deal vault for transactions agreed on Facebook, LinkedIn, WhatsApp, or anywhere.</p>
                    <div class="compare-fee-tag">Escrow Fee: From Only 3% (Tiered)</div>
                    <div class="compare-features-list">
                        <div class="compare-item">
                            <span class="compare-icon-good"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg></span>
                            <span><strong>100% Vault Security:</strong> Funds locked safely before work starts; released only upon approval.</span>
                        </div>
                        <div class="compare-item">
                            <span class="compare-icon-good"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg></span>
                            <span><strong>Keep 97%+ of Your Earnings:</strong> A tiny fraction of freelance marketplace commissions.</span>
                        </div>
                        <div class="compare-item">
                            <span class="compare-icon-good"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg></span>
                            <span><strong>Bring Any Outside Client:</strong> Welcome deals from Facebook, Telegram, WhatsApp, or X.</span>
                        </div>
                        <div class="compare-item">
                            <span class="compare-icon-good"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg></span>
                            <span><strong>Real Human Dispute Review:</strong> Real moderators inspect files and evidence when issues arise.</span>
                        </div>
                        <div class="compare-item">
                            <span class="compare-icon-good"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg></span>
                            <span><strong>Instant Local Payouts:</strong> Withdraw straight to bKash, Nagad, or Bank Wire.</span>
                        </div>
                    </div>
                    <a href="{{ route('auth') }}" class="btn btn-primary" style="width: 100%; margin-top: auto;">Start Protected Deal</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Problem Statement -->
    <section class="section section-light">
        <div class="container">
            <div class="problem-grid">
                <div class="problem-content reveal-left">
                    <h2>Online Deals Shouldn't Be a Matter of Trusting a Stranger.</h2>
                    <p>Completing transactions with someone you met online is risky. Relying on verbal promises and screenshots often leads to stress and financial loss.</p>
                    
                    <div class="problem-list">
                        <div class="problem-item">
                            <div class="problem-icon"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg></div>
                            <p>Buyer pays in advance, but the seller disappears.</p>
                        </div>
                        <div class="problem-item">
                            <div class="problem-icon"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg></div>
                            <p>Freelancers complete work but struggle to collect payment.</p>
                        </div>
                        <div class="problem-item">
                            <div class="problem-icon"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg></div>
                            <p>Disputes become impossible to resolve without a neutral party.</p>
                        </div>
                    </div>
                </div>
                
                <div class="solution-box reveal-right delay-200">
                    <div class="solution-icon">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
                    </div>
                    <h3>The MigratePay Solution</h3>
                    <p>We sit securely between the buyer and seller. The buyer deposits funds into our platform. The seller delivers the agreed work. Once approved, we release the funds. It's that simple.</p>
                    <a href="{{ route('auth') }}" class="btn btn-primary">Protect Your Next Deal</a>
                </div>
            </div>
        </div>
    </section>

    <!-- 3 Guarantees Section -->
    <section class="guarantees-section" id="guarantees">
        <div class="container">
            <div class="section-header reveal">
                <div class="badge" style="background: rgba(37, 99, 235, 0.2); color: #93C5FD; border-color: rgba(37, 99, 235, 0.4);">Our Ironclad Promise</div>
                <h2 class="section-title">The 3 Guarantees Backing Every Transaction</h2>
                <p class="section-subtitle">MigratePay acts as an unyielding neutral referee. Here is how we ensure that neither buyers nor sellers can ever be cheated.</p>
            </div>
            
            <div class="guarantees-grid">
                <!-- Guarantee 1 -->
                <div class="guarantee-card reveal delay-100">
                    <div class="guarantee-icon-box">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                            <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                            <circle cx="12" cy="16" r="1.5"></circle>
                        </svg>
                    </div>
                    <h3>1. Neutral Escrow Vault</h3>
                    <p>Buyer money never goes directly to the seller until you inspect the delivered work. At the same time, the seller never begins work until funds are confirmed inside our isolated holding vault. Neither party can run away.</p>
                    <div class="guarantee-tag">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                        Zero Advance Payment Risk
                    </div>
                </div>

                <!-- Guarantee 2 -->
                <div class="guarantee-card reveal delay-200">
                    <div class="guarantee-icon-box">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                        </svg>
                    </div>
                    <h3>2. Human Dispute Tribunal</h3>
                    <p>When disagreements happen, you aren't evaluated by mindless AI or canned bot responses. Our dedicated human dispute team analyzes original deal terms, work submissions, and chat screenshots within hours for a fair outcome.</p>
                    <div class="guarantee-tag">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                        Fair Evidence-Based Arbitration
                    </div>
                </div>

                <!-- Guarantee 3 -->
                <div class="guarantee-card reveal delay-300">
                    <div class="guarantee-icon-box">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                            <polyline points="9 12 11 14 15 10"></polyline>
                        </svg>
                    </div>
                    <h3>3. Anti-Chargeback Protection</h3>
                    <p>Once a buyer reviews and approves the delivered work, the funds are irrevocably released to the seller. Freelancers and service providers are 100% immune to malicious bank chargebacks or buyer regret weeks later.</p>
                    <div class="guarantee-tag">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                        100% Protected Seller Payouts
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Why MigratePay (Benefits) -->
    <section class="section" id="benefits">
        <div class="container">
            <div class="section-header reveal">
                <div class="badge">Platform Benefits</div>
                <h2 class="section-title">Why Use MigratePay?</h2>
                <p class="section-subtitle">A structured platform designed to protect both parties and ensure fair online transactions.</p>
            </div>
            
            <div class="benefits-grid">
                <div class="benefit-card reveal delay-100">
                    <div class="benefit-icon"><svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg></div>
                    <h3>Buyer Protection</h3>
                    <p>Reduce the risk of sending money directly to an unknown seller. Funds are held until terms are met.</p>
                </div>
                <div class="benefit-card reveal delay-200">
                    <div class="benefit-icon"><svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg></div>
                    <h3>Seller Protection</h3>
                    <p>Give sellers confidence that a buyer has committed funds according to the deal terms before work begins.</p>
                </div>
                <div class="benefit-card reveal delay-300">
                    <div class="benefit-icon"><svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg></div>
                    <h3>Neutral Mediation</h3>
                    <p>MigratePay sits between both parties instead of representing only the buyer or seller.</p>
                </div>
                <div class="benefit-card reveal delay-100">
                    <div class="benefit-icon"><svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg></div>
                    <h3>Clear Deal Terms</h3>
                    <p>Both parties agree on the exact transaction conditions and delivery requirements before proceeding.</p>
                </div>
                <div class="benefit-card reveal delay-200">
                    <div class="benefit-icon"><svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg></div>
                    <h3>Live Deal Tracking</h3>
                    <p>Users can see the progress and current status of their deal organized in a structured dashboard.</p>
                </div>
                <div class="benefit-card reveal delay-300">
                    <div class="benefit-icon"><svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="12 2 2 7 12 12 22 7 12 2"></polygon><polyline points="2 17 12 22 22 17"></polyline><polyline points="2 12 17 22 12"></polyline></svg></div>
                    <h3>Dispute Handling</h3>
                    <p>If disagreements arise, we provide a structured process for handling them according to platform rules.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- How It Works -->
    <section class="process-slider-section" id="how-it-works">
        <div class="container">
            <div class="section-header reveal">
                <div class="badge">Simple Process</div>
                <h2 class="section-title">How MigratePay Works</h2>
                <p class="section-subtitle">A transparent, step-by-step escrow process that eliminates payment anxiety.</p>
            </div>
            
            <div class="process-slider-container">
                <div class="process-connector">
                    <div class="process-connector-fill" id="processConnector" style="width: 10%;"></div>
                </div>
                
                <div class="process-slider-track" id="processTrack">
                    <!-- Step 1 -->
                    <div class="process-card active" data-step="1">
                        <div class="process-step-num">01</div>
                        <h3>Create a Deal</h3>
                        <p>Buyer and seller agree on the transaction terms, delivery timeline, and create a deal through MigratePay.</p>
                    </div>
                    
                    <!-- Step 2 -->
                    <div class="process-card" data-step="2">
                        <div class="process-step-num">02</div>
                        <h3>Buyer Secures the Payment</h3>
                        <p>The buyer deposits the agreed amount. MigratePay securely holds these funds.</p>
                    </div>
                    
                    <!-- Step 3 -->
                    <div class="process-card" data-step="3">
                        <div class="process-step-num">03</div>
                        <h3>Seller Completes the Work</h3>
                        <p>Knowing the funds are secured, the seller delivers the agreed product, service, or work.</p>
                    </div>
                    
                    <!-- Step 4 -->
                    <div class="process-card" data-step="4">
                        <div class="process-step-num">04</div>
                        <h3>Buyer Reviews</h3>
                        <p>The buyer checks the delivery to ensure it meets the agreed deal conditions.</p>
                    </div>
                    
                    <!-- Step 5 -->
                    <div class="process-card" data-step="5">
                        <div class="process-step-num">05</div>
                        <h3>Deal Completed</h3>
                        <p>Once the buyer is satisfied, MigratePay releases the funds to the seller. Transaction complete!</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Interactive Escrow Calculator Section -->
    <section class="section calculator-section" id="calculator">
        <div class="container">
            <div class="section-header reveal">
                <div class="badge">Transparent Pricing</div>
                <h2 class="section-title">Calculate Your Deal Protection Fee</h2>
                <p class="section-subtitle">No hidden charges, no surprises. See exactly what the buyer deposits and what the seller takes home with bank-grade escrow security.</p>
            </div>
            
            <div class="calc-home-wrapper reveal">
                <!-- Controls Left -->
                <div class="calc-home-controls">
                    <div>
                        <div class="calc-input-label">
                            <span>Deal Amount (BDT)</span>
                            <span style="font-size: 12px; color: var(--primary); font-weight: 700;">Tiered Rates: 3% - 5%</span>
                        </div>
                        <div class="calc-input-wrapper">
                            <span class="calc-input-prefix">৳</span>
                            <input type="number" id="calcHomeAmount" class="calc-amount-input" value="30000" min="1000" step="500">
                        </div>
                        <div class="calc-presets-row">
                            <button type="button" class="calc-preset-chip" data-amount="5000">৳5,000</button>
                            <button type="button" class="calc-preset-chip" data-amount="10000">৳10,000</button>
                            <button type="button" class="calc-preset-chip" data-amount="15000">৳15,000</button>
                            <button type="button" class="calc-preset-chip active" data-amount="30000">৳30,000</button>
                            <button type="button" class="calc-preset-chip" data-amount="60000">৳60,000</button>
                        </div>
                    </div>

                    <div>
                        <div class="calc-input-label">
                            <span>Who Covers The Escrow Fee?</span>
                        </div>
                        <div class="calc-payer-tabs">
                            <button type="button" class="calc-payer-btn active" data-payer="shared_50">Split 50/50 (Standard)</button>
                            <button type="button" class="calc-payer-btn" data-payer="buyer_100">Buyer Pays 100%</button>
                            <button type="button" class="calc-payer-btn" data-payer="seller_100">Seller Pays 100%</button>
                        </div>
                    </div>

                    <div class="calc-schedule-box">
                        <div class="calc-schedule-title">
                            <span>Platform Fee Schedule</span>
                            <span class="calc-schedule-sub" id="calcTierText">Applied: 3% (deals over ৳15k)</span>
                        </div>
                        <div class="calc-schedule-grid">
                            <div class="calc-tier-card" id="tierCard1" data-amount="3000">
                                <div class="tier-range">৳1k – ৳5k</div>
                                <div class="tier-pct">5.0%</div>
                            </div>
                            <div class="calc-tier-card" id="tierCard2" data-amount="8000">
                                <div class="tier-range">৳5k – ৳10k</div>
                                <div class="tier-pct">4.5%</div>
                            </div>
                            <div class="calc-tier-card" id="tierCard3" data-amount="12000">
                                <div class="tier-range">৳10k – ৳15k</div>
                                <div class="tier-pct">4.0%</div>
                            </div>
                            <div class="calc-tier-card active" id="tierCard4" data-amount="30000">
                                <div class="tier-range">৳15k+</div>
                                <div class="tier-pct tier-best">3.0%</div>
                            </div>
                        </div>
                        <div class="calc-extra-info">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                            <span>No hidden charges · Free human dispute arbitration · Instant MFS payouts</span>
                        </div>
                    </div>
                </div>

                <!-- Live Results Right -->
                <div class="calc-home-result">
                    <div>
                        <div class="calc-res-header">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
                            Deal Settlement Breakdown
                        </div>

                        <div class="calc-res-breakdown">
                            <div class="calc-res-row">
                                <span>Agreed Deal Value</span>
                                <span id="calcValAmount">৳30,000</span>
                            </div>
                            <div class="calc-res-row">
                                <span>Total Escrow Fee (<span id="calcValRate">3%</span>)</span>
                                <span id="calcValFee">৳900</span>
                            </div>
                            <div class="calc-res-divider"></div>
                        </div>

                        <div class="calc-highlight-box">
                            <div class="calc-highlight-item buyer-val">
                                <div>
                                    <div class="label">Buyer Deposits</div>
                                    <small style="color: var(--gray-400); font-size: 11px;" id="calcBuyerSub">Deal + 50% fee</small>
                                </div>
                                <div class="val" id="calcBuyerPays">৳30,450</div>
                            </div>
                            <div style="height: 1px; background: rgba(255,255,255,0.08);"></div>
                            <div class="calc-highlight-item">
                                <div>
                                    <div class="label">Seller Takes Home</div>
                                    <small style="color: var(--gray-400); font-size: 11px;" id="calcSellerSub">Deal - 50% fee</small>
                                </div>
                                <div class="val" id="calcSellerGets">৳29,550</div>
                            </div>
                        </div>

                        <div class="calc-guarantee-note">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                            <span>Funds securely locked until buyer approves delivery.</span>
                        </div>
                    </div>

                    <a href="{{ route('auth') }}" class="btn btn-primary" style="margin-top: 28px; width: 100%; background: var(--white); color: var(--dark);">
                        Start This Deal Now
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Security Credentials Strip -->
    <section class="security-strip-section">
        <div class="container">
            <div class="security-strip-grid reveal">
                <div class="security-strip-item">
                    <div class="security-strip-icon">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                    </div>
                    <div class="security-strip-text">
                        <h4>Bank-Grade 256-Bit SSL</h4>
                        <p>End-to-end encrypted transactions</p>
                    </div>
                </div>

                <div class="security-strip-item">
                    <div class="security-strip-icon">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path><polyline points="9 12 11 14 15 10"></polyline></svg>
                    </div>
                    <div class="security-strip-text">
                        <h4>Dual-Key Release</h4>
                        <p>Payout only with mutual confirmation</p>
                    </div>
                </div>

                <div class="security-strip-item">
                    <div class="security-strip-icon">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="5" width="20" height="14" rx="2"></rect><line x1="2" y1="10" x2="22" y2="10"></line></svg>
                    </div>
                    <div class="security-strip-text">
                        <h4>Instant Local Payouts</h4>
                        <p>bKash, Nagad, Cards & Bank Wire</p>
                    </div>
                </div>

                <div class="security-strip-item">
                    <div class="security-strip-icon">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="8.5" cy="7" r="4"></circle><polyline points="17 11 19 13 23 9"></polyline></svg>
                    </div>
                    <div class="security-strip-text">
                        <h4>KYC Verified Profiles</h4>
                        <p>Verified deal counterparties</p>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <!-- Use Cases (Digital Only) -->
    <section class="section section-light" id="use-cases">
        <div class="container">
            <div class="section-header reveal">
                <div class="badge">Versatile Escrow</div>
                <h2 class="section-title">What Can MigratePay Be Used For?</h2>
                <p class="section-subtitle">Our platform protects a wide variety of agreed digital transactions and services in Bangladesh.</p>
            </div>
            
            <div class="usecases-grid">
                <div class="usecase-card reveal delay-100">
                    <div class="usecase-icon">💻</div>
                    <h4>Freelance Projects</h4>
                </div>
                <div class="usecase-card reveal delay-200">
                    <div class="usecase-icon">🌐</div>
                    <h4>Website Development</h4>
                </div>
                <div class="usecase-card reveal delay-300">
                    <div class="usecase-icon">🎨</div>
                    <h4>Graphic Design</h4>
                </div>
                <div class="usecase-card reveal delay-400">
                    <div class="usecase-icon">📱</div>
                    <h4>App Development</h4>
                </div>
                <div class="usecase-card reveal delay-100">
                    <div class="usecase-icon">📝</div>
                    <h4>Copywriting</h4>
                </div>
                <div class="usecase-card reveal delay-200">
                    <div class="usecase-icon">📈</div>
                    <h4>SEO Services</h4>
                </div>
                <div class="usecase-card reveal delay-300">
                    <div class="usecase-icon">🔗</div>
                    <h4>Domain Transactions</h4>
                </div>
                <div class="usecase-card reveal delay-400">
                    <div class="usecase-icon">🤝</div>
                    <h4>B2B Service Deals</h4>
                </div>
            </div>
            <p class="text-center text-gray reveal delay-400" style="margin-top: 40px; font-size: 14px; font-weight: 500;">* All transactions are subject to MigratePay's terms and deal rules.</p>
        </div>
    </section>


    <!-- Testimonials -->
    <section class="section section-light">
        <div class="container">
            <div class="section-header reveal">
                <div class="badge">Success Stories</div>
                <h2 class="section-title">Trusted by Users in Bangladesh</h2>
                <p class="section-subtitle">See what our users have to say about securing their digital deals with MigratePay.</p>
            </div>
            
            <div class="testimonials-grid">
                <div class="testimonial-card reveal delay-100">
                    <div class="stars">★★★★★</div>
                    <p class="testimonial-text">"As a freelance web developer, I used to worry about not getting paid after finishing a project. MigratePay fixed that. Now I only start working once the funds are secured in escrow."</p>
                    <div class="testimonial-author">
                        <div class="author-avatar">T</div>
                        <div class="author-info">
                            <h4>Tanvir Hasan</h4>
                            <p>Freelance Developer</p>
                        </div>
                    </div>
                </div>
                <div class="testimonial-card reveal delay-200">
                    <div class="stars">★★★★★</div>
                    <p class="testimonial-text">"I bought a premium domain name from a Facebook group. Instead of sending money blindly, we used MigratePay. It gave me the peace of mind to verify the domain transfer before the seller received the money."</p>
                    <div class="testimonial-author">
                        <div class="author-avatar" style="background: linear-gradient(135deg, #10B981, #059669);">N</div>
                        <div class="author-info">
                            <h4>Nusrat Jahan</h4>
                            <p>Digital Buyer</p>
                        </div>
                    </div>
                </div>
                <div class="testimonial-card reveal delay-300">
                    <div class="stars">★★★★★</div>
                    <p class="testimonial-text">"Our agency uses MigratePay for all new client deals. It looks professional, builds trust instantly, and keeps our transaction records perfectly organized in one dashboard."</p>
                    <div class="testimonial-author">
                        <div class="author-avatar" style="background: linear-gradient(135deg, #8B5CF6, #6D28D9);">S</div>
                        <div class="author-info">
                            <h4>Saif Rahman</h4>
                            <p>Agency Owner</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ -->
    <section class="section" id="faq">
        <div class="container">
            <div class="section-header reveal">
                <div class="badge">Got Questions?</div>
                <h2 class="section-title">Frequently Asked Questions</h2>
            </div>
            
            <div class="faq-container">
                <div class="faq-item reveal delay-100">
                    <div class="faq-question">What is MigratePay? <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"></polyline></svg></div>
                    <div class="faq-answer">MigratePay is a secure escrow and deal-management mediation platform operating in Bangladesh. We act as a trusted third party that holds funds temporarily while a transaction takes place between a buyer and a seller.</div>
                </div>
                <div class="faq-item reveal delay-200">
                    <div class="faq-question">How does the escrow process protect me? <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"></polyline></svg></div>
                    <div class="faq-answer">If you are a buyer, your money is safe because the seller doesn't get paid until they deliver the agreed work. If you are a seller, your time is safe because you know the buyer has already deposited the funds before you start working.</div>
                </div>
                <div class="faq-item reveal delay-300">
                    <div class="faq-question">When is the payment released? <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"></polyline></svg></div>
                    <div class="faq-answer">Funds are released to the seller only after the seller submits the work and the buyer reviews and approves the delivery, according to the agreed terms of the deal.</div>
                </div>
                <div class="faq-item reveal delay-400">
                    <div class="faq-question">What happens if there is a dispute? <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"></polyline></svg></div>
                    <div class="faq-answer">If the buyer and seller cannot agree, either party can report an issue. Our administrative team will review the deal terms, messages, and delivery evidence to make a fair resolution based on our platform rules.</div>
                </div>
                <div class="faq-item reveal delay-500">
                    <div class="faq-question">Can we communicate on WhatsApp or Messenger? <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"></polyline></svg></div>
                    <div class="faq-answer">Yes, you can communicate on any external platform. <strong>However, you must share all critical proofs, work submissions, and revisions via the MigratePay deal chatbox.</strong> If a dispute or conflict arises, the MigratePay team can only mediate and resolve issues based on the evidence provided inside the official deal chatbox.</div>
                </div>
                <div class="faq-item reveal delay-600">
                    <div class="faq-question">Can I use MigratePay for freelance work? <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"></polyline></svg></div>
                    <div class="faq-answer">Absolutely. MigratePay is highly suited for freelance projects, digital services, website development, and other custom orders where payment security is needed.</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Our Partners Section -->
    <section class="partners-section">
        <div class="partners-bg-orbs"></div>
        <div class="container">
            <div class="partners-header reveal">
                <span class="partners-eyebrow">Trusted Partners</span>
                <h2>Our Partners</h2>
                <p>Building stronger digital transactions through trusted partnerships.</p>
            </div>
        </div>
        
        <!-- Marquee Slider -->
        <div class="partners-marquee-container reveal delay-200">
            <div class="partners-marquee">
                <!-- Original Set -->
                <div class="partner-card"><span class="partner-placeholder">Partner Logo 1</span></div>
                <div class="partner-card"><span class="partner-placeholder">Partner Logo 2</span></div>
                <div class="partner-card"><span class="partner-placeholder">Partner Logo 3</span></div>
                <div class="partner-card"><span class="partner-placeholder">Partner Logo 4</span></div>
                <div class="partner-card"><span class="partner-placeholder">Partner Logo 5</span></div>
                
                <!-- Duplicate Set for Infinite Scroll -->
                <div class="partner-card" aria-hidden="true"><span class="partner-placeholder">Partner Logo 1</span></div>
                <div class="partner-card" aria-hidden="true"><span class="partner-placeholder">Partner Logo 2</span></div>
                <div class="partner-card" aria-hidden="true"><span class="partner-placeholder">Partner Logo 3</span></div>
                <div class="partner-card" aria-hidden="true"><span class="partner-placeholder">Partner Logo 4</span></div>
                <div class="partner-card" aria-hidden="true"><span class="partner-placeholder">Partner Logo 5</span></div>
            </div>
        </div>
    </section>

    <!-- Final CTA -->
    <section class="cta-section">
        <div class="cta-bg-shape"></div>
        <div class="container cta-container reveal">
            <h2>Ready to Make Your Next Deal Safer?</h2>
            <p>Whether you're buying or selling, MigratePay helps you manage the transaction with greater clarity and confidence.</p>
            <div class="cta-actions">
                <a href="{{ route('auth') }}" class="btn btn-white">Start a Deal Now</a>
                <a href="{{ route('calculator') }}" class="btn btn-outline" style="color: white; border-color: rgba(255,255,255,0.3); background: transparent;">Calculate Charges</a>
            </div>
        </div>
    </section>

    @include('partials.footer')

    <!-- Interactive Scripts -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Scroll Reveal Animation (Intersection Observer)
            const revealElements = document.querySelectorAll('.reveal, .reveal-left, .reveal-right');
            
            const revealObserver = new IntersectionObserver((entries, observer) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('active');
                    }
                });
            }, {
                root: null,
                threshold: 0.15,
                rootMargin: '0px 0px -50px 0px'
            });
            
            revealElements.forEach(el => revealObserver.observe(el));

            // FAQ Accordion
            const faqItems = document.querySelectorAll('.faq-item');
            faqItems.forEach(item => {
                const question = item.querySelector('.faq-question');
                question.addEventListener('click', () => {
                    const isOpen = item.classList.contains('is-open');
                    faqItems.forEach(i => i.classList.remove('is-open'));
                    if (!isOpen) {
                        item.classList.add('is-open');
                    }
                });
            });

            // Smooth Scroll
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function (e) {
                    e.preventDefault();
                    const targetId = this.getAttribute('href');
                    if(targetId === '#') return;
                    const targetElement = document.querySelector(targetId);
                    if(targetElement) {
                        targetElement.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                    }
                });
            });

            // Header Scroll Effect
            const header = document.getElementById('site-header');
            window.addEventListener('scroll', () => {
                if (window.scrollY > 50) {
                    header.classList.add('scrolled');
                } else {
                    header.classList.remove('scrolled');
                }
            });

            // Menu logic
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
            if (navContainer) {
                headerItems.forEach(item => {
                    navContainer.insertBefore(renderDesktopNode(item, 0), loginBtn);
                });
            }
            
            // Build Mobile Nav
            if (mobileNavInner) {
                headerItems.forEach(item => {
                    mobileNavInner.appendChild(renderMobileNode(item));
                });
                const mobileLogin = loginBtn.cloneNode(true);
                mobileLogin.style.marginTop = '20px';
                mobileLogin.style.fontSize = '16px';
                mobileNavInner.appendChild(mobileLogin);
            }

            // Mobile Menu Toggle
            const toggleBtn = document.getElementById('mobile-menu-toggle');
            const mobilePanel = document.getElementById('mobile-nav-panel');
            let menuOpen = false;
            
            if (toggleBtn && mobilePanel) {
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
            }

            // Interactive Process Slider
            const processTrack = document.getElementById('processTrack');
            const processCards = document.querySelectorAll('.process-card');
            const processConnector = document.getElementById('processConnector');
            
            if (processTrack && processCards.length > 0) {
                let activeIndex = 0;
                let autoplayTimer;
                
                function updateSlider(index) {
                    activeIndex = index;
                    processCards.forEach((card, i) => {
                        if (i === index) {
                            card.classList.add('active');
                        } else {
                            card.classList.remove('active');
                        }
                    });
                    
                    if (processConnector) {
                        const percentage = 10 + (index * 20);
                        processConnector.style.width = percentage + '%';
                    }
                }
                
                function startAutoplay() {
                    stopAutoplay();
                    autoplayTimer = setInterval(() => {
                        let nextIndex = (activeIndex + 1) % processCards.length;
                        updateSlider(nextIndex);
                        
                        if (window.innerWidth <= 1024 && processCards[nextIndex]) {
                            processTrack.scrollTo({
                                left: processCards[nextIndex].offsetLeft - 20,
                                behavior: 'smooth'
                            });
                        }
                    }, 4000);
                }
                
                function stopAutoplay() {
                    if (autoplayTimer) clearInterval(autoplayTimer);
                }
                
                processCards.forEach((card, i) => {
                    card.addEventListener('mouseenter', () => {
                        if (window.innerWidth > 1024) {
                            stopAutoplay();
                            updateSlider(i);
                        }
                    });
                    card.addEventListener('click', () => {
                        stopAutoplay();
                        updateSlider(i);
                        if (window.innerWidth <= 1024) {
                            processTrack.scrollTo({
                                left: card.offsetLeft - 20,
                                behavior: 'smooth'
                            });
                        }
                    });
                });
                
                processTrack.addEventListener('mouseenter', stopAutoplay);
                processTrack.addEventListener('mouseleave', () => {
                    if (window.innerWidth > 1024) startAutoplay();
                });
                
                if (window.innerWidth <= 1024) {
                    let isScrolling;
                    processTrack.addEventListener('scroll', () => {
                        window.clearTimeout(isScrolling);
                        isScrolling = setTimeout(function() {
                            let closestIndex = 0;
                            let minDistance = Infinity;
                            const trackCenter = processTrack.scrollLeft + (processTrack.clientWidth / 2);
                            
                            processCards.forEach((card, i) => {
                                const cardCenter = card.offsetLeft + (card.clientWidth / 2);
                                const dist = Math.abs(trackCenter - cardCenter);
                                if (dist < minDistance) {
                                    minDistance = dist;
                                    closestIndex = i;
                                }
                            });
                            
                            if (closestIndex !== activeIndex) {
                                updateSlider(closestIndex);
                            }
                        }, 66);
                    }, { passive: true });
                }
                
                updateSlider(0);
                startAutoplay();
            }

            // Hero Animation Sequencer
            const heroAnim = document.getElementById('heroAnim');
            if(heroAnim) {
                let currentScene = 1;
                let animTimeout;
                
                const sceneDurations = {
                    1: 3000, 2: 3500, 3: 3500, 4: 3500, 
                    5: 3000, 6: 3000, 7: 3500, 8: 3000, 9: 3500, 10: 1000  
                };

                const dots = document.querySelectorAll('#heroDots .dot');

                function updateDots(scene) {
                    dots.forEach((dot, index) => {
                        if(index + 1 === scene) dot.classList.add('active');
                        else dot.classList.remove('active');
                    });
                }

                function playNextScene() {
                    let duration = sceneDurations[currentScene] || 2000;
                    
                    if (currentScene === 10) {
                        currentScene = 1;
                        heroAnim.setAttribute('data-scene', currentScene);
                        updateDots(currentScene);
                        animTimeout = setTimeout(playNextScene, sceneDurations[1]);
                    } else {
                        currentScene++;
                        heroAnim.setAttribute('data-scene', currentScene);
                        if(currentScene < 10) updateDots(currentScene);
                        animTimeout = setTimeout(playNextScene, sceneDurations[currentScene]);
                    }
                }
                
                dots.forEach(dot => {
                    dot.addEventListener('click', function() {
                        clearTimeout(animTimeout);
                        currentScene = parseInt(this.getAttribute('data-target'));
                        heroAnim.setAttribute('data-scene', currentScene);
                        updateDots(currentScene);
                        animTimeout = setTimeout(playNextScene, sceneDurations[currentScene]);
                    });
                });

                animTimeout = setTimeout(playNextScene, sceneDurations[1]);
            }

            // Homepage Escrow Calculator Logic
            const calcAmountInput = document.getElementById('calcHomeAmount');
            const calcPresets = document.querySelectorAll('.calc-preset-chip');
            const calcPayerBtns = document.querySelectorAll('.calc-payer-btn');
            
            let currentPayer = 'shared_50';

            function getChargePct(amount) {
                if (amount >= 1000 && amount <= 5000) return 5;
                if (amount > 5000 && amount <= 10000) return 4.5;
                if (amount > 10000 && amount <= 15000) return 4;
                if (amount > 15000) return 3;
                return 5;
            }

            function formatBDT(num) {
                return '৳' + Math.round(num).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
            }

            function updateCalculator() {
                if(!calcAmountInput) return;
                let amount = parseFloat(calcAmountInput.value) || 0;
                if(amount < 1000) amount = 1000;

                const pct = getChargePct(amount);
                const totalFee = amount * (pct / 100);
                
                let buyerFee = 0;
                let sellerFee = 0;
                let buyerSubText = '';
                let sellerSubText = '';

                if (currentPayer === 'shared_50') {
                    buyerFee = totalFee / 2;
                    sellerFee = totalFee / 2;
                    buyerSubText = 'Deal + 50% escrow fee';
                    sellerSubText = 'Deal - 50% escrow fee';
                } else if (currentPayer === 'buyer_100') {
                    buyerFee = totalFee;
                    sellerFee = 0;
                    buyerSubText = 'Deal + 100% escrow fee';
                    sellerSubText = 'Full deal amount (0% fee)';
                } else if (currentPayer === 'seller_100') {
                    buyerFee = 0;
                    sellerFee = totalFee;
                    buyerSubText = 'Exact deal amount (0% fee)';
                    sellerSubText = 'Deal - 100% escrow fee';
                }

                const buyerPays = amount + buyerFee;
                const sellerGets = Math.max(0, amount - sellerFee);

                // Update UI elements
                const elTierText = document.getElementById('calcTierText');
                const elValAmount = document.getElementById('calcValAmount');
                const elValRate = document.getElementById('calcValRate');
                const elValFee = document.getElementById('calcValFee');
                const elBuyerPays = document.getElementById('calcBuyerPays');
                const elSellerGets = document.getElementById('calcSellerGets');
                const elBuyerSub = document.getElementById('calcBuyerSub');
                const elSellerSub = document.getElementById('calcSellerSub');

                if (elTierText) {
                    let tierDesc = 'for deals over ৳15,000';
                    if (amount <= 5000) tierDesc = 'for deals ৳1,000 – ৳5,000';
                    else if (amount <= 10000) tierDesc = 'for deals ৳5,001 – ৳10,000';
                    else if (amount <= 15000) tierDesc = 'for deals ৳10,001 – ৳15,000';
                    elTierText.innerHTML = `Applied: <strong>৳{pct}%</strong> (${tierDesc})`;
                }

                // Highlight active tier card
                const t1 = document.getElementById('tierCard1');
                const t2 = document.getElementById('tierCard2');
                const t3 = document.getElementById('tierCard3');
                const t4 = document.getElementById('tierCard4');
                if (t1 && t2 && t3 && t4) {
                    [t1, t2, t3, t4].forEach(c => c.classList.remove('active'));
                    if (amount <= 5000) t1.classList.add('active');
                    else if (amount <= 10000) t2.classList.add('active');
                    else if (amount <= 15000) t3.classList.add('active');
                    else t4.classList.add('active');
                }

                if (elValAmount) elValAmount.innerText = formatBDT(amount);
                if (elValRate) elValRate.innerText = `${pct}%`;
                if (elValFee) elValFee.innerText = formatBDT(totalFee);
                if (elBuyerPays) elBuyerPays.innerText = formatBDT(buyerPays);
                if (elSellerGets) elSellerGets.innerText = formatBDT(sellerGets);
                if (elBuyerSub) elBuyerSub.innerText = buyerSubText;
                if (elSellerSub) elSellerSub.innerText = sellerSubText;
            }

            if (calcAmountInput) {
                calcAmountInput.addEventListener('input', () => {
                    calcPresets.forEach(chip => chip.classList.remove('active'));
                    updateCalculator();
                });

                calcPresets.forEach(chip => {
                    chip.addEventListener('click', function() {
                        calcPresets.forEach(c => c.classList.remove('active'));
                        this.classList.add('active');
                        calcAmountInput.value = this.getAttribute('data-amount');
                        updateCalculator();
                    });
                });

                const tierCards = document.querySelectorAll('.calc-tier-card');
                tierCards.forEach(card => {
                    card.addEventListener('click', function() {
                        const targetAmount = this.getAttribute('data-amount');
                        if (targetAmount && calcAmountInput) {
                            calcPresets.forEach(c => {
                                if (c.getAttribute('data-amount') === targetAmount) c.classList.add('active');
                                else c.classList.remove('active');
                            });
                            calcAmountInput.value = targetAmount;
                            updateCalculator();
                        }
                    });
                });

                calcPayerBtns.forEach(btn => {
                    btn.addEventListener('click', function() {
                        calcPayerBtns.forEach(b => b.classList.remove('active'));
                        this.classList.add('active');
                        currentPayer = this.getAttribute('data-payer');
                        updateCalculator();
                    });
                });

                // Initial calculation
                updateCalculator();
            }
        });
    </script>
    @include('partials.ai-chat-widget')
</body>
</html>
