<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Migratepay | Login & KYC Registration</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        :root {
            /* Color Palette */
            --navy-900: #002147;
            --emerald-500: #006A4E;
            --emerald-400: #008763;
            --sunset-orange: #FF4F00;
            --white: #FFFFFF;
            --gray-50: #F8FAFC;
            --gray-100: #F1F5F9;
            --gray-300: #CBD5E1;
            --gray-500: #64748B;
            --gray-800: #1E293B;
            
            /* Form states */
            --bg-body: #F4F7F6;
            --success-green: #2D5A27;
            --error-red: #D32F2F;
            --error-bg: #FFEBEE;
            
            --font-family: 'Inter', sans-serif;
            --shadow-md: 0 10px 25px -5px rgba(0, 0, 0, 0.05);
            --transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: var(--font-family);
            background-color: var(--bg-body);
            color: var(--gray-800);
            -webkit-font-smoothing: antialiased;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        a {
            text-decoration: none;
            color: inherit;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 24px;
        }

        /* Nav exact copy structure */
        header {
            padding: 24px 0;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            transition: var(--transition);
            background-color: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(255,255,255,0.3);
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        }
        
        .navbar { display: flex; justify-content: space-between; align-items: center; }
        .logo { font-size: 24px; font-weight: 800; color: var(--navy-900); display: flex; align-items: center; gap: 10px; z-index: 1001;}
        .logo svg { width: 32px; height: 32px; fill: var(--emerald-500); }
        .nav-links { display: flex; gap: 32px; align-items: center; }
        .nav-item { position: relative; font-weight: 600; color: var(--navy-900); transition: var(--transition); display: flex; align-items: center; gap: 4px; padding: 8px 0; cursor: pointer; }
        .nav-item:hover { color: var(--sunset-orange); }
        .nav-item svg { width: 14px; height: 14px; transition: transform 0.3s ease; }
        .nav-item:hover svg { transform: rotate(180deg); }
        .dropdown-menu { position: absolute; top: 100%; left: -16px; background: var(--white); border-radius: 12px; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1); padding: 12px; min-width: 220px; opacity: 0; visibility: hidden; transform: translateY(10px); transition: var(--transition); border: 1px solid var(--gray-100); display: flex; flex-direction: column; gap: 4px; }
        .nav-item:hover .dropdown-menu { opacity: 1; visibility: visible; transform: translateY(0); }
        .dropdown-link { display: flex; align-items: center; gap: 12px; padding: 10px 16px; border-radius: 8px; color: var(--navy-900); font-weight: 500; font-size: 15px; transition: var(--transition); }
        .dropdown-link svg { width: 18px; height: 18px; color: var(--emerald-500); }
        .dropdown-link:hover { background-color: var(--gray-50); color: var(--emerald-500); }
        @media (max-width: 1024px) { .nav-links { display: none; } }

        /* Auth Form Area */
        .auth-wrapper {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 120px 24px 60px;
            position: relative;
        }

        .auth-card {
            background-color: var(--white);
            border-radius: 20px;
            box-shadow: var(--shadow-md);
            width: 100%;
            max-width: 500px;
            padding: 48px;
            position: relative;
            overflow: hidden;
        }

        .back-link {
            position: absolute;
            top: 24px;
            left: 24px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: var(--gray-500);
            font-weight: 600;
            font-size: 14px;
            transition: var(--transition);
        }

        .back-link:hover {
            color: var(--navy-900);
        }

        .auth-header {
            text-align: center;
            margin-bottom: 32px;
            margin-top: 16px;
        }

        .auth-header h1 {
            font-size: 28px;
            font-weight: 800;
            color: var(--navy-900);
            margin-bottom: 8px;
        }

        .auth-header p {
            color: var(--gray-500);
            font-size: 15px;
        }

        .form-group {
            margin-bottom: 24px;
            position: relative;
        }

        .form-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            font-size: 14px;
            color: var(--navy-900);
            display: flex;
            justify-content: space-between;
        }

        .form-control {
            width: 100%;
            padding: 14px 16px;
            border: 1px solid var(--gray-300);
            border-radius: 10px;
            font-family: inherit;
            font-size: 15px;
            color: var(--navy-900);
            transition: var(--transition);
            background-color: var(--white);
        }

        .form-control:focus {
            outline: none;
            border-color: var(--navy-900);
            box-shadow: 0 0 0 3px rgba(0, 33, 71, 0.1);
        }
        
        .form-control.error {
            background-color: var(--error-bg);
            border-color: var(--error-red);
        }
        .form-control.success {
            border-color: var(--success-green);
        }

        .icon-right {
            position: absolute;
            right: 16px;
            top: 42px;
            color: var(--gray-500);
            cursor: pointer;
        }
        
        .success-icon {
            position: absolute;
            right: -24px;
            top: 42px;
            color: var(--success-green);
            opacity: 0;
            transition: var(--transition);
        }

        .success-icon.show {
            opacity: 1;
        }

        /* Mobile Input Specific */
        .mobile-input-group {
            display: flex;
            align-items: stretch;
        }

        .mobile-prefix {
            background-color: var(--gray-50);
            border: 1px solid var(--gray-300);
            border-right: none;
            padding: 0 16px;
            display: flex;
            align-items: center;
            border-top-left-radius: 10px;
            border-bottom-left-radius: 10px;
            color: var(--gray-500);
            font-weight: 600;
        }

        .mobile-input-group .form-control {
            border-top-left-radius: 0;
            border-bottom-left-radius: 0;
        }

        textarea.form-control {
            resize: vertical;
            min-height: 80px;
        }

        /* Drops */
        .flex-row {
            display: flex;
            gap: 16px;
        }
        .flex-row .form-group {
            flex: 1;
        }

        /* Buttons */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 14px 24px;
            font-weight: 600;
            font-size: 16px;
            cursor: pointer;
            transition: var(--transition);
            text-align: center;
            border: none;
            border-radius: 10px;
            width: 100%;
        }

        .btn-primary {
            background-color: var(--emerald-500);
            color: var(--white);
            box-shadow: 0 4px 10px -2px rgba(0, 106, 78, 0.3);
        }

        .btn-primary:hover {
            background-color: var(--emerald-400);
            transform: translateY(-2px);
        }
        
        .btn-ghost {
            background-color: var(--gray-50);
            color: var(--navy-900);
            font-size: 14px;
            padding: 8px 16px;
            border: 1px dashed var(--gray-300);
            margin-top: 8px;
            width: auto;
        }
        
        .btn-ghost:hover {
            background-color: var(--gray-100);
            border-color: var(--navy-900);
        }

        /* Auth Footer */
        .auth-footer {
            text-align: center;
            margin-top: 32px;
            font-size: 14px;
            color: var(--gray-500);
        }

        .auth-footer button {
            color: var(--navy-900);
            font-weight: 700;
            background: none;
            border: none;
            cursor: pointer;
            font-size: inherit;
        }
        .auth-footer button:hover {
            text-decoration: underline;
        }

        /* View Panels (Slide In micro-interactions) */
        .view-panel {
            display: none;
            animation: slideIn 0.4s ease forwards;
        }

        .view-panel.active {
            display: block;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateX(20px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        /* Steps Indicator */
        .steps-indicator {
            display: flex;
            justify-content: space-between;
            margin-bottom: 32px;
            position: relative;
        }
        .steps-indicator::before {
            content: '';
            position: absolute;
            top: 14px;
            left: 0;
            width: 100%;
            height: 2px;
            background-color: var(--gray-100);
            z-index: 0;
        }
        .step-dot {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background-color: var(--gray-100);
            color: var(--gray-500);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 14px;
            position: relative;
            z-index: 1;
            transition: var(--transition);
        }
        .step-dot.active {
            background-color: var(--navy-900);
            color: var(--white);
            box-shadow: 0 0 0 4px rgba(0, 33, 71, 0.1);
        }
        .step-dot.completed {
            background-color: var(--emerald-500);
            color: var(--white);
        }

        /* Password Strength */
        .strength-meter {
            height: 4px;
            border-radius: 2px;
            background-color: var(--gray-100);
            margin-top: 8px;
            display: flex;
            gap: 4px;
        }
        .strength-bar {
            height: 100%;
            flex: 1;
            border-radius: 2px;
            transition: all 0.3s;
        }
        
        .tooltip-icon {
            color: var(--gray-300);
            cursor: help;
            position: relative;
        }

        /* Loading Spinner */
        .spinner {
            display: none;
            width: 20px;
            height: 20px;
            border: 2px solid rgba(255,255,255,0.3);
            border-top-color: white;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        @keyframes spin { 100% { transform: rotate(360deg); } }
        
        .loading span { display: none; }
        .loading .spinner { display: inline-block; }

        /* --- ADMIN UI REDESIGN --- */
        .admin-login-mode {
            --bg-body: #050b14;
            --white: #ffffff;
            --gray-800: #f1f5f9;
            --navy-900: #f8fafc;
            --gray-500: #94a3b8;
            
            background: radial-gradient(circle at 10% 20%, #06112a 0%, #000000 100%);
            position: relative;
        }
        .admin-login-mode::before {
            content: ''; position: absolute; top: 0; left: 0; right: 0; bottom: 0;
            background: radial-gradient(circle at 80% 80%, rgba(59, 130, 246, 0.15) 0%, transparent 40%),
                        radial-gradient(circle at 20% 20%, rgba(99, 102, 241, 0.1) 0%, transparent 40%);
            z-index: 0; pointer-events: none;
        }

        .admin-login-mode header { background-color: rgba(5, 11, 20, 0.6); border-bottom: 1px solid rgba(255, 255, 255, 0.05); backdrop-filter: blur(20px); }
        .admin-login-mode .nav-item { color: var(--gray-300); }
        .admin-login-mode .nav-item:hover { color: #3b82f6; }
        
        .admin-login-mode .auth-wrapper { position: relative; z-index: 1; }
        .auth-split-layout { display: flex; align-items: center; justify-content: center; width: 100%; max-width: 1400px; margin: 0 auto; gap: 40px; }

        .admin-left-branding, .admin-right-visual { display: none; }
        .admin-login-mode .admin-left-branding { display: flex; flex-direction: column; width: 35%; animation: fadeUp 0.8s ease-out; }
        .admin-login-mode .admin-right-visual { display: flex; align-items: center; justify-content: center; width: 25%; animation: floatSlow 6s ease-in-out infinite; }
        
        @keyframes fadeUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes floatSlow { 0% { transform: translateY(0px); } 50% { transform: translateY(-15px); } 100% { transform: translateY(0px); } }

        .admin-login-mode .auth-card {
            width: 40%; max-width: 500px;
            background: rgba(15, 23, 42, 0.4); border: 1px solid rgba(59, 130, 246, 0.2);
            backdrop-filter: blur(16px); -webkit-backdrop-filter: blur(16px);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5), inset 0 0 0 1px rgba(255, 255, 255, 0.05);
            border-radius: 24px; position: relative; animation: fadeUp 0.6s ease-out;
            padding: 48px;
        }

        .admin-login-mode .form-control { background: rgba(0, 0, 0, 0.3); border: 1px solid rgba(255, 255, 255, 0.1); color: #fff; border-radius: 14px; }
        .admin-login-mode .form-control:focus { background: rgba(0, 0, 0, 0.5); border-color: #3b82f6; box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.15); }
        .admin-login-mode .btn-primary { background: linear-gradient(135deg, #1d4ed8 0%, #3b82f6 100%); border: none; box-shadow: 0 4px 14px 0 rgba(59, 130, 246, 0.39); border-radius: 12px; }
        .admin-login-mode .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(59, 130, 246, 0.5); }
        
        .admin-login-mode .back-link { position: relative; top: auto; left: auto; margin-bottom: 24px; color: #94a3b8; }
        .admin-login-mode .back-link:hover { color: #3b82f6; transform: translateX(-4px); }

        .admin-feature-box { display: flex; align-items: center; gap: 16px; margin-top: 24px; padding: 16px; background: rgba(255, 255, 255, 0.03); border: 1px solid rgba(255, 255, 255, 0.05); border-radius: 16px; transition: all 0.3s; }
        .admin-feature-box:hover { background: rgba(59, 130, 246, 0.05); border-color: rgba(59, 130, 246, 0.2); }
        .admin-feature-icon { width: 40px; height: 40px; border-radius: 10px; background: rgba(59, 130, 246, 0.1); color: #3b82f6; display: flex; align-items: center; justify-content: center; }
        .admin-feature-text h4 { color: #f8fafc; font-size: 15px; margin-bottom: 2px; font-weight: 700; }
        .admin-feature-text p { color: #94a3b8; font-size: 13px; }

        .admin-badge-ui { display: inline-flex; align-items: center; gap: 6px; background: rgba(59, 130, 246, 0.1); border: 1px solid rgba(59, 130, 246, 0.3); color: #3b82f6; padding: 6px 12px; border-radius: 20px; font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 24px; width: fit-content; }
        .admin-login-mode .admin-badge { background: linear-gradient(135deg, #1d4ed8, #4f46e5) !important; box-shadow: 0 4px 15px rgba(59, 130, 246, 0.4) !important; color: white !important; }

        @media (max-width: 1024px) {
            .admin-login-mode .admin-right-visual { display: none; }
            .admin-login-mode .admin-left-branding { width: 45%; }
            .admin-login-mode .auth-card { width: 55%; }
        }
        @media (max-width: 768px) {
            .auth-split-layout { flex-direction: column; }
            .admin-login-mode .admin-left-branding, .admin-login-mode .auth-card { width: 100%; }
            .admin-login-mode .admin-left-branding { margin-bottom: 32px; text-align: center; align-items: center; }
            .admin-login-mode .admin-feature-box { text-align: left; width: 100%; max-width: 400px; }
        }

        /* --- USER UI REDESIGN --- */
        .user-login-mode {
            --bg-body: #f8fafc;
            --white: #ffffff;
            --navy-900: #0f172a;
            --gray-500: #64748b;
            position: relative;
            background: linear-gradient(135deg, #e0f2fe 0%, #f3e8ff 100%);
            overflow-x: hidden;
        }
        .user-login-mode::before, .user-login-mode::after {
            content: ''; position: absolute; border-radius: 50%; filter: blur(80px); z-index: 0; pointer-events: none; opacity: 0.6;
        }
        .user-login-mode::before { top: -10%; left: -10%; width: 50vw; height: 50vw; background: rgba(34, 211, 238, 0.3); animation: floatBlob 15s infinite alternate; }
        .user-login-mode::after { bottom: -10%; right: -10%; width: 60vw; height: 60vw; background: rgba(168, 85, 247, 0.25); animation: floatBlob 18s infinite alternate-reverse; }
        @keyframes floatBlob { 0% { transform: translate(0, 0) scale(1); } 100% { transform: translate(50px, 50px) scale(1.1); } }

        .user-login-mode header { background-color: rgba(255, 255, 255, 0.7); border-bottom: 1px solid rgba(255, 255, 255, 0.5); backdrop-filter: blur(20px); }
        .user-login-mode .nav-item { color: var(--navy-900); position: relative; }
        .user-login-mode .nav-item:hover { color: #8b5cf6; }
        .user-login-mode .nav-item::after { content: ''; position: absolute; bottom: 0; left: 0; width: 0%; height: 2px; background: #8b5cf6; transition: width 0.3s; }
        .user-login-mode .nav-item:hover::after { width: 100%; }
        
        .user-left-branding { display: none; }
        .user-login-mode .user-left-branding { display: flex; flex-direction: column; width: 50%; position: relative; z-index: 2; animation: fadeUp 0.8s ease-out; }
        .user-login-mode .auth-card { width: 50%; max-width: 480px; background: rgba(255, 255, 255, 0.9); border: 1px solid rgba(255, 255, 255, 0.5); backdrop-filter: blur(20px); box-shadow: 0 30px 60px rgba(0, 0, 0, 0.05); border-radius: 24px; position: relative; z-index: 2; animation: fadeUp 0.6s ease-out; padding: 48px; }
        
        .user-login-mode .form-control { background: #ffffff; border: 1px solid #e2e8f0; color: #0f172a; border-radius: 14px; transition: all 0.3s; }
        .user-login-mode .form-control:focus { background: #ffffff; border-color: #8b5cf6; box-shadow: 0 0 0 4px rgba(139, 92, 246, 0.15); }
        .user-login-mode .btn-primary { background: linear-gradient(135deg, #3b82f6 0%, #06b6d4 50%, #8b5cf6 100%); background-size: 200% auto; border: none; box-shadow: 0 10px 20px -5px rgba(139, 92, 246, 0.3); border-radius: 12px; transition: all 0.4s; }
        .user-login-mode .btn-primary:hover { background-position: right center; transform: translateY(-2px); box-shadow: 0 15px 25px -5px rgba(139, 92, 246, 0.4); }
        
        .user-login-mode .back-link { position: relative; top: auto; left: auto; margin-bottom: 24px; color: #64748b; font-weight: 600; }
        .user-login-mode .back-link:hover { color: #8b5cf6; transform: translateX(-4px); }

        .user-badge-ui { display: inline-flex; align-items: center; gap: 6px; background: #ffffff; box-shadow: 0 4px 10px rgba(0,0,0,0.05); color: #8b5cf6; padding: 8px 16px; border-radius: 20px; font-size: 13px; font-weight: 700; margin-bottom: 24px; width: fit-content; }
        .user-feature-box { display: flex; align-items: center; gap: 16px; margin-top: 20px; padding: 16px; background: #ffffff; box-shadow: 0 10px 20px rgba(0,0,0,0.02); border-radius: 16px; transition: all 0.3s; position: relative; z-index: 2; }
        .user-feature-box:hover { transform: translateY(-3px); box-shadow: 0 15px 30px rgba(139, 92, 246, 0.1); }
        .user-feature-icon { width: 44px; height: 44px; border-radius: 12px; background: linear-gradient(135deg, #e0e7ff, #f3e8ff); color: #8b5cf6; display: flex; align-items: center; justify-content: center; font-size: 20px; }
        .user-feature-text h4 { color: #0f172a; font-size: 15px; margin-bottom: 2px; font-weight: 700; }
        .user-feature-text p { color: #64748b; font-size: 13px; }

        .user-floating-card { position: absolute; background: white; padding: 12px 20px; border-radius: 16px; box-shadow: 0 15px 30px rgba(0,0,0,0.05); display: flex; align-items: center; gap: 12px; z-index: 1; animation: floatCard 8s ease-in-out infinite; font-size: 13px; font-weight: 700; color: #0f172a; }
        .user-fc-1 { top: 10%; right: -20px; animation-delay: 0s; transform: rotate(2deg); }
        .user-fc-2 { top: 25%; right: 20px; left: auto; bottom: auto; animation-delay: 2s; transform: rotate(-2deg); }
        .user-fc-icon { width: 30px; height: 30px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 14px; color: white; }
        @keyframes floatCard { 0% { transform: translateY(0px) rotate(0deg); } 50% { transform: translateY(-15px) rotate(2deg); } 100% { transform: translateY(0px) rotate(0deg); } }

        .user-login-mode .auth-footer button { color: #8b5cf6; font-weight: 700; background: none; border: none; cursor: pointer; transition: all 0.3s; display: inline-flex; align-items: center; gap: 4px; }
        .user-login-mode .auth-footer button::after { content: '→'; opacity: 0; transform: translateX(-10px); transition: all 0.3s; }
        .user-login-mode .auth-footer button:hover::after { opacity: 1; transform: translateX(0); }

        .user-security-msg { text-align: center; font-size: 12px; color: #94a3b8; font-weight: 600; margin-top: 24px; display: flex; justify-content: center; align-items: center; gap: 6px; }

        @media (max-width: 1024px) {
            .user-login-mode .user-floating-card { display: none; }
            .user-login-mode .user-left-branding { width: 45%; }
            .user-login-mode .auth-card { width: 55%; }
        }
        @media (max-width: 768px) {
            .user-login-mode .user-left-branding, .user-login-mode .auth-card { width: 100%; }
            .user-login-mode .user-left-branding { margin-bottom: 24px; text-align: center; align-items: center; }
            .user-login-mode .user-feature-box { text-align: left; width: 100%; max-width: 400px; }
            .user-login-mode .user-badge-ui { margin: 0 auto 24px auto; }
            .user-login-mode h1 { font-size: 2.2rem !important; }
        }
        /* Toast Notification */
        .toast-container {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        .toast {
            background-color: var(--white);
            border-left: 4px solid var(--red-500);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            padding: 16px 20px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            gap: 12px;
            min-width: 300px;
            transform: translateX(120%);
            opacity: 0;
            transition: all 0.3s cubic-bezier(0.68, -0.55, 0.265, 1.55);
        }
        .toast.show {
            transform: translateX(0);
            opacity: 1;
        }
        .toast.success {
            border-left-color: var(--emerald-500);
        }
        .toast.error {
            border-left-color: var(--red-500);
        }
        .toast-icon {
            width: 24px;
            height: 24px;
            flex-shrink: 0;
        }
        .toast.success .toast-icon {
            color: var(--emerald-500);
        }
        .toast.error .toast-icon {
            color: var(--red-500);
        }
        .toast-message {
            color: var(--navy-900);
            font-weight: 600;
            font-size: 14px;
            line-height: 1.4;
        }
    </style>
</head>
<body class="{{ request('role') == 'admin' ? 'admin-login-mode' : 'user-login-mode' }}">

    <!-- Shared Header -->
    <header>
        <div class="container navbar">
            <a href="/" class="logo" style="display:flex; align-items:center;">
                <img src="/assets/images/Migratepay logo.png" alt="Migratepay Logo" style="height: 60px; width: auto;" />
            </a>
            
            <nav class="nav-links">
                <a href="/" class="nav-item">Home</a>
                <div class="nav-item dropdown">
                    Our Services
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>
                    <div class="dropdown-menu">
                        <a href="#" class="dropdown-link">Individual Escrow</a>
                        <a href="#" class="dropdown-link">Business Solutions</a>
                    </div>
                </div>
                <a href="/about" class="nav-item">About Us</a>
            </nav>
        </div>
    </header>

    <!-- Auth Area -->
    <main class="auth-wrapper">
        <div class="auth-split-layout">
            
            <!-- USER LEFT BRANDING (Hidden natively, shown via CSS in user mode) -->
            <div class="user-left-branding">
                <div class="user-floating-card user-fc-1">
                    <div class="user-fc-icon" style="background: linear-gradient(135deg, #34d399, #10b981);">✓</div>
                    <div>
                        <div style="font-size: 10px; color: #64748b;">Payment Successful</div>
                        <div style="font-size: 14px;">৳25,000</div>
                    </div>
                </div>
                <div class="user-floating-card user-fc-2">
                    <div class="user-fc-icon" style="background: linear-gradient(135deg, #60a5fa, #3b82f6);">🛡</div>
                    <div>
                        <div style="font-size: 10px; color: #64748b;">Deal Protected</div>
                        <div style="font-size: 14px;">Funds Secured</div>
                    </div>
                </div>

                <div class="user-badge-ui">
                    ✦ Secure & Simple
                </div>
                <h1 style="font-size: 3rem; font-weight: 800; color: #0f172a; line-height: 1.1; margin-bottom: 16px;">
                    Welcome Back to<br/>
                    <span style="background: linear-gradient(135deg, #3b82f6, #8b5cf6); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">MigratePay</span>
                </h1>
                <p style="color: #64748b; font-size: 16px; line-height: 1.6; margin-bottom: 24px; max-width: 400px;">
                    Your trusted platform for secure and seamless digital transactions.
                </p>

                <div class="user-feature-box">
                    <div class="user-feature-icon">🔒</div>
                    <div class="user-feature-text">
                        <h4>Secure</h4>
                        <p>Protected transactions</p>
                    </div>
                </div>

                <div class="user-feature-box">
                    <div class="user-feature-icon">⚡</div>
                    <div class="user-feature-text">
                        <h4>Fast</h4>
                        <p>Quick & seamless experience</p>
                    </div>
                </div>

                <div class="user-feature-box">
                    <div class="user-feature-icon">✓</div>
                    <div class="user-feature-text">
                        <h4>Trusted</h4>
                        <p>Built for buyers & sellers</p>
                    </div>
                </div>
            </div>

            <!-- ADMIN LEFT BRANDING (Hidden natively, shown via CSS in admin mode) -->
            <div class="admin-left-branding">
                <div class="admin-badge-ui">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
                    Secure & Reliable
                </div>
                <h1 style="font-size: 3rem; font-weight: 800; color: #fff; line-height: 1.1; margin-bottom: 16px;">
                    Welcome Back<br/>to <span style="color: #3b82f6;">MigratePay</span>
                </h1>
                <p style="color: #94a3b8; font-size: 16px; line-height: 1.6; margin-bottom: 24px; max-width: 400px;">
                    Access your admin portal to manage your platform and keep everything running smoothly.
                </p>

                <div class="admin-feature-box">
                    <div class="admin-feature-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
                    </div>
                    <div class="admin-feature-text">
                        <h4>Secure Access</h4>
                        <p>Protected admin access</p>
                    </div>
                </div>

                <div class="admin-feature-box">
                    <div class="admin-feature-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon></svg>
                    </div>
                    <div class="admin-feature-text">
                        <h4>Fast & Reliable</h4>
                        <p>Built for performance</p>
                    </div>
                </div>

                <div class="admin-feature-box">
                    <div class="admin-feature-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 18v-6a9 9 0 0 1 18 0v6"></path><path d="M21 19a2 2 0 0 1-2 2h-1a2 2 0 0 1-2-2v-3a2 2 0 0 1 2-2h3zM3 19a2 2 0 0 0 2 2h1a2 2 0 0 0 2-2v-3a2 2 0 0 0-2-2H3z"></path></svg>
                    </div>
                    <div class="admin-feature-text">
                        <h4>24/7 Support</h4>
                        <p>Always here when you need us</p>
                    </div>
                </div>
            </div>

            <div class="auth-card">
                <a href="/" class="back-link">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
                Back to Home
            </a>

            <!-- ==============================
                 LOGIN PANEL
                 ============================== -->
            <div id="login-panel" class="view-panel active">
                <div class="auth-header">
                    <h1>Welcome Back</h1>
                    <p>Log in to access your secure vault.</p>
                </div>

                <form id="login-form">
                    <div class="form-group">
                        <label class="form-label">Email Address</label>
                        <input type="email" class="form-control" placeholder="name@example.com" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">
                            Password
                            <a href="#" onclick="switchPanel('forgot-password-panel')" style="font-size:12px; color:var(--emerald-500);">Forgot Password?</a>
                        </label>
                        <input type="password" id="login-password" class="form-control" placeholder="••••••••" required>
                        <svg class="icon-right" onclick="togglePasswordVisibility(this, 'login-password')" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                    </div>
                    
                    <div id="login-error-msg" style="display:none; background:#FFEBEE; color:#D32F2F; border:1px solid #FFCDD2; border-radius:8px; padding:10px 14px; font-size:13px; font-weight:600; margin-bottom:16px;"></div>
                    <button type="button" id="login-btn" class="btn btn-primary" onclick="doLogin(this)">
                        <span>Secure Login</span>
                        <div class="spinner"></div>
                    </button>
                </form>

                <div class="auth-footer">
                    New to MigratePay? <button type="button" onclick="switchPanel('signup-1')">Create Account</button>
                </div>
                <div class="user-security-msg" style="display: none;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                    Your account information is protected
                </div>
                <script>
                    if (!document.body.classList.contains('admin-login-mode')) {
                        document.querySelector('.user-security-msg').style.display = 'flex';
                    }
                </script>
            </div>
            <!-- ==============================
                 END LOGIN PANEL
                 ============================== -->

            <!-- ==============================
                 FORGOT PASSWORD PANEL
                 ============================== -->
            <div id="forgot-password-panel" class="view-panel">
                <div class="auth-header">
                    <h1>Recover Account</h1>
                    <p>Enter your email address and we'll send you a 6-digit OTP to reset your password.</p>
                </div>

                <form id="forgot-form">
                    <div class="form-group">
                        <label class="form-label">Email Address</label>
                        <input type="email" id="forgot-email" class="form-control" placeholder="name@example.com" required>
                    </div>
                    
                    <div id="forgot-error-msg" style="display:none; background:#FFEBEE; color:#D32F2F; border:1px solid #FFCDD2; border-radius:8px; padding:10px 14px; font-size:13px; font-weight:600; margin-bottom:16px;"></div>
                    <div id="forgot-success-msg" style="display:none; background:#E8F5E9; color:#2E7D32; border:1px solid #C8E6C9; border-radius:8px; padding:10px 14px; font-size:13px; font-weight:600; margin-bottom:16px;"></div>

                    <button type="button" id="forgot-btn" class="btn btn-primary" onclick="doForgotPassword(this)">
                        <span>Send OTP</span>
                        <div class="spinner"></div>
                    </button>
                </form>

                <div class="auth-footer">
                    Remembered your password? <button type="button" onclick="switchPanel('login-panel')">Log In</button>
                </div>
            </div>
            
            <!-- ==============================
                 RESET PASSWORD PANEL
                 ============================== -->
            <div id="reset-password-panel" class="view-panel">
                <div class="auth-header">
                    <h1 id="reset-title">Verify OTP</h1>
                    <p id="reset-subtitle">Enter the 6-digit OTP sent to your email.</p>
                </div>

                <form id="reset-form">
                    <div id="step-otp-section">
                        <div class="form-group">
                            <label class="form-label">6-Digit OTP</label>
                            <input type="text" id="reset-otp" class="form-control" placeholder="123456" maxlength="6" style="letter-spacing:4px; font-size:18px; text-align:center; font-weight:800;" required>
                        </div>
                        <button type="button" id="verify-otp-btn" class="btn btn-primary" onclick="doVerifyOtp(this)">
                            <span>Verify OTP</span>
                            <div class="spinner"></div>
                        </button>
                    </div>

                    <div id="step-password-section" style="display:none; animation: fadeUp 0.5s ease-out;">
                        <div class="form-group">
                            <label class="form-label">New Password</label>
                            <input type="password" id="reset-password" class="form-control" placeholder="••••••••" required>
                            <svg class="icon-right" onclick="togglePasswordVisibility(this, 'reset-password')" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Confirm New Password</label>
                            <input type="password" id="reset-password-confirm" class="form-control" placeholder="••••••••" required>
                            <svg class="icon-right" onclick="togglePasswordVisibility(this, 'reset-password-confirm')" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                        </div>
                        <button type="button" id="reset-btn" class="btn btn-primary" onclick="doResetPassword(this)">
                            <span>Update Password</span>
                            <div class="spinner"></div>
                        </button>
                    </div>
                    
                    <div id="reset-error-msg" style="display:none; background:#FFEBEE; color:#D32F2F; border:1px solid #FFCDD2; border-radius:8px; padding:10px 14px; font-size:13px; font-weight:600; margin-top:16px;"></div>
                    <div id="reset-success-msg" style="display:none; background:#E8F5E9; color:#2E7D32; border:1px solid #C8E6C9; border-radius:8px; padding:10px 14px; font-size:13px; font-weight:600; margin-top:16px;"></div>
                </form>

                <div class="auth-footer" id="reset-footer">
                    Need a new OTP? <button type="button" onclick="switchPanel('forgot-password-panel')">Go Back</button>
                </div>
            </div>
            
            <!-- (Signup panels remain here, completely untouched logic/styling outside admin mode) -->
            <div id="signup-1" class="view-panel">
                <div class="steps-indicator">
                    <div class="step-dot active">1</div>
                    <div class="step-dot">2</div>
                    <div class="step-dot">3</div>
                </div>
                
                <div class="auth-header" style="margin-top:0;">
                    <h1 style="font-size:24px;">Account Essentials</h1>
                    <p>Basic details to get started securely.</p>
                </div>

                <form>
                    <div class="form-group">
                        <label class="form-label">Full Name</label>
                        <input type="text" class="form-control" id="reg-name" placeholder="As per NID/Passport" oninput="validateInput(this, 'name')">
                        <svg class="success-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"></polyline></svg>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Username</label>
                        <input type="text" class="form-control" id="reg-username" placeholder="johndoe" oninput="validateInput(this, 'username')">
                        <svg class="success-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"></polyline></svg>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Email Address</label>
                        <input type="email" class="form-control" id="reg-email" placeholder="name@example.com" oninput="validateInput(this, 'email')">
                        <svg class="success-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"></polyline></svg>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Mobile Number</label>
                        <div class="mobile-input-group">
                            <div class="mobile-prefix">+880</div>
                            <input type="tel" class="form-control" id="reg-phone" placeholder="1XXXXXXXXX" maxlength="10" oninput="validateInput(this, 'phone')">
                        </div>
                        <svg class="success-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"></polyline></svg>
                    </div>

                    <button type="button" class="btn btn-primary" onclick="goToStep2()" style="margin-top:8px;">Continue to KYC</button>
                </form>
                
                <div class="auth-footer">
                    Already have an account? <button type="button" onclick="switchPanel('login-panel')">Log In</button>
                </div>
            </div>

            <!-- ==============================
                 SIGNUP STEP 2: Legal Identity
                 ============================== -->
            <div id="signup-2" class="view-panel">
                <div class="steps-indicator">
                    <div class="step-dot completed">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"></polyline></svg>
                    </div>
                    <div class="step-dot active">2</div>
                    <div class="step-dot">3</div>
                </div>
                
                <div class="auth-header" style="margin-top:0;">
                    <h1 style="font-size:24px;">Legal Identity</h1>
                    <p>Required for Bangladesh Bank KYC compliance.</p>
                </div>

                <form>
                    <div class="form-group">
                        <label class="form-label">
                            Date of Birth 
                            <span class="tooltip-icon" title="You must be 18+ to use Migratepay escrow services.">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>
                            </span>
                        </label>
                        <input type="date" class="form-control" id="reg-dob" onchange="validateInput(this, 'dob')" max="{{ date('Y-m-d', strtotime('-15 years')) }}">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Gender</label>
                        <select class="form-control" id="reg-gender" onchange="validateInput(this, 'select')">
                            <option value="" disabled selected>Select</option>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                        </select>
                    </div>



                    <div class="flex-row">
                        <div class="form-group">
                            <label class="form-label">Division</label>
                            <select class="form-control" id="reg-div" onchange="updateDistricts(); validateInput(this, 'select')">
                                <option value="" disabled selected>Select</option>
                                <option value="Barishal">Barishal</option>
                                <option value="Chattogram">Chattogram</option>
                                <option value="Dhaka">Dhaka</option>
                                <option value="Khulna">Khulna</option>
                                <option value="Mymensingh">Mymensingh</option>
                                <option value="Rajshahi">Rajshahi</option>
                                <option value="Rangpur">Rangpur</option>
                                <option value="Sylhet">Sylhet</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">District</label>
                            <select class="form-control" id="reg-dist" onchange="validateInput(this, 'select')">
                                <option value="" disabled selected>Select</option>
                            </select>
                        </div>
                    </div>

                    <div class="flex-row" style="margin-top: 16px;">
                        <button type="button" class="btn btn-outline" onclick="switchPanel('signup-1')" style="flex: 0.5;">Back</button>
                        <button type="button" class="btn btn-primary" onclick="goToStep3()" style="flex: 1;">Final Step</button>
                    </div>
                </form>
            </div>

            <!-- ==============================
                 SIGNUP STEP 3: Security
                 ============================== -->
            <div id="signup-3" class="view-panel">
                <div class="steps-indicator">
                    <div class="step-dot completed"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"></polyline></svg></div>
                    <div class="step-dot completed"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"></polyline></svg></div>
                    <div class="step-dot active">3</div>
                </div>
                
                <div class="auth-header" style="margin-top:0;">
                    <h1 style="font-size:24px;">Secure Your Account</h1>
                    <p>Create a strong password.</p>
                </div>

                <form>
                    <div class="form-group">
                        <label class="form-label">Create Password</label>
                        <input type="password" class="form-control" id="reg-pass" placeholder="Min 8 characters" oninput="checkStrength()">
                        <svg class="icon-right" onclick="togglePasswordVisibility(this, 'reg-pass')" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                        <div class="strength-meter">
                            <div class="strength-bar" id="str-1"></div>
                            <div class="strength-bar" id="str-2"></div>
                            <div class="strength-bar" id="str-3"></div>
                            <div class="strength-bar" id="str-4"></div>
                        </div>
                        <div style="font-size: 12px; color: var(--gray-500); margin-top: 12px; background-color: var(--gray-50); padding: 12px; border-radius: 8px; border: 1px solid var(--gray-100);">
                            <div style="font-weight: 600; color: var(--navy-900); margin-bottom: 6px;">Tips for a strong password:</div>
                            <ul style="margin-left: 18px; display: flex; flex-direction: column; gap: 4px;">
                                <li>At least 8 characters long</li>
                                <li>Include uppercase and lowercase letters</li>
                                <li>Include at least one number</li>
                                <li>Include at least one special character (e.g., @, #, $, %)</li>
                            </ul>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label" id="match-label">Confirm Password</label>
                        <input type="password" class="form-control" id="reg-confirm" placeholder="Repeat password" oninput="checkMatch()">
                        <svg class="icon-right" onclick="togglePasswordVisibility(this, 'reg-confirm')" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                    </div>

                    <div class="form-group" style="display:flex; gap:12px; margin-top:24px; font-size:14px; align-items:flex-start;">
                        <input type="checkbox" id="terms" style="margin-top:4px; width:16px; height:16px; accent-color:var(--emerald-500);">
                        <label for="terms" style="color:var(--gray-500); line-height:1.5;">
                            I agree to Migratepay's <a href="/terms-and-conditions" target="_blank" style="color:var(--navy-900); font-weight:600;">Terms of Service</a> and <a href="/privacy-policy" target="_blank" style="color:var(--navy-900); font-weight:600;">Privacy Policy</a>.
                        </label>
                    </div>

                    <div class="flex-row" style="margin-top: 24px;">
                        <button type="button" class="btn btn-outline" onclick="switchPanel('signup-2')" style="flex: 0.5;">Back</button>
                        <button type="button" id="register-btn" class="btn btn-primary" onclick="doRegister(this)" style="flex: 1;">
                            <span>Verify & Create Account</span>
                            <div class="spinner"></div>
                        </button>
                    </div>
                </form>
            </div>
            <!-- END AUTH CARD -->

            <!-- ADMIN RIGHT VISUAL (Hidden natively, shown via CSS in admin mode) -->
            <div class="admin-right-visual">
                <svg width="240" height="240" viewBox="0 0 200 200" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="100" cy="100" r="90" stroke="rgba(59, 130, 246, 0.1)" stroke-width="2" stroke-dasharray="8 8"/>
                    <circle cx="100" cy="100" r="70" stroke="rgba(99, 102, 241, 0.2)" stroke-width="1"/>
                    <path d="M100 25C100 25 150 45 150 100C150 155 100 175 100 175C100 175 50 155 50 100C50 45 100 25 100 25Z" fill="url(#paint0_linear)" fill-opacity="0.1" stroke="#3b82f6" stroke-width="2" stroke-linejoin="round"/>
                    <rect x="85" y="90" width="30" height="25" rx="3" fill="#3b82f6"/>
                    <path d="M90 90V80C90 74.4772 94.4772 70 100 70C105.523 70 110 74.4772 110 80V90" stroke="#3b82f6" stroke-width="4" stroke-linecap="round"/>
                    <circle cx="100" cy="102" r="3" fill="white"/>
                    <path d="M100 105V110" stroke="white" stroke-width="2" stroke-linecap="round"/>
                    <defs>
                        <linearGradient id="paint0_linear" x1="100" y1="25" x2="100" y2="175" gradientUnits="userSpaceOnUse">
                            <stop stop-color="#3b82f6"/>
                            <stop offset="1" stop-color="#4f46e5"/>
                        </linearGradient>
                    </defs>
                </svg>
            </div>
        </div>
    </main>

    <script>
        const bdDistricts = {
            "Barishal": ["Barguna", "Barishal", "Bhola", "Jhalokati", "Patuakhali", "Pirojpur"],
            "Chattogram": ["Bandarban", "Brahmanbaria", "Chandpur", "Chattogram", "Cumilla", "Cox's Bazar", "Feni", "Khagrachhari", "Lakshmipur", "Noakhali", "Rangamati"],
            "Dhaka": ["Dhaka", "Faridpur", "Gazipur", "Gopalganj", "Kishoreganj", "Madaripur", "Manikganj", "Munshiganj", "Narayanganj", "Narsingdi", "Rajbari", "Shariatpur", "Tangail"],
            "Khulna": ["Bagerhat", "Chuadanga", "Jashore", "Jhenaidah", "Khulna", "Kushtia", "Magura", "Meherpur", "Narail", "Satkhira"],
            "Mymensingh": ["Jamalpur", "Mymensingh", "Netrokona", "Sherpur"],
            "Rajshahi": ["Bogura", "Joypurhat", "Naogaon", "Natore", "Chapainawabganj", "Pabna", "Rajshahi", "Sirajganj"],
            "Rangpur": ["Dinajpur", "Gaibandha", "Kurigram", "Lalmonirhat", "Nilphamari", "Panchagarh", "Rangpur", "Thakurgaon"],
            "Sylhet": ["Habiganj", "Moulvibazar", "Sunamganj", "Sylhet"]
        };

        function updateDistricts() {
            const divSelect = document.getElementById('reg-div');
            const distSelect = document.getElementById('reg-dist');
            const selectedDiv = divSelect.value;
            
            distSelect.innerHTML = '<option value="" disabled selected>Select</option>';
            
            if (selectedDiv && bdDistricts[selectedDiv]) {
                bdDistricts[selectedDiv].forEach(dist => {
                    const option = document.createElement('option');
                    option.value = dist;
                    option.textContent = dist;
                    distSelect.appendChild(option);
                });
            }
            
            validateInput(distSelect, 'select');
        }

        // ─── Panel Switcher ───────────────────────────────────────────────────────
        function switchPanel(panelId) {
            document.querySelectorAll('.view-panel').forEach(p => p.classList.remove('active'));
            document.getElementById(panelId).classList.add('active');
        }

        // Toggle Password Vis
        function togglePasswordVisibility(iconElement, inputId) {
            const input = document.getElementById(inputId);
            if (!input) return;
            if (input.type === 'password') {
                input.type = 'text';
                iconElement.innerHTML = '<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line>';
                iconElement.style.color = 'var(--emerald-500)';
            } else {
                input.type = 'password';
                iconElement.innerHTML = '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle>';
                iconElement.style.color = '';
            }
        }

        // ─── Toast Notification ───────────────────────────────────────────────────
        function showToast(message, type = 'error') {
            let container = document.getElementById('toast-container');
            if (!container) {
                container = document.createElement('div');
                container.id = 'toast-container';
                container.className = 'toast-container';
                document.body.appendChild(container);
            }

            const toast = document.createElement('div');
            toast.className = `toast ${type}`;
            
            let iconSvg = '';
            if (type === 'success') {
                iconSvg = `<svg class="toast-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>`;
            } else {
                iconSvg = `<svg class="toast-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>`;
            }

            toast.innerHTML = `
                ${iconSvg}
                <div class="toast-message">${message}</div>
            `;
            
            container.appendChild(toast);
            
            // Trigger reflow
            void toast.offsetWidth;
            
            toast.classList.add('show');
            
            setTimeout(() => {
                toast.classList.remove('show');
                setTimeout(() => toast.remove(), 300);
            }, 4000);
        }

        let validationTimeout = {};
        async function validateInput(element, type) {
            let isValid = false;
            if (type === 'name') isValid = element.value.length > 2;
            if (type === 'username') isValid = element.value.length > 2;
            if (type === 'email') isValid = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(element.value);
            if (type === 'phone') {
                element.value = element.value.replace(/[^0-9]/g, '');
                isValid = element.value.length === 10;
            }
            if (type === 'nid') {
                element.value = element.value.replace(/[^0-9]/g, '');
                isValid = element.value.length === 10 || element.value.length === 17;
            }
            if (type === 'select') isValid = element.value !== '';
            if (type === 'dob') {
                const birth = new Date(element.value);
                const today = new Date();
                let age = today.getFullYear() - birth.getFullYear();
                if (today.getMonth() < birth.getMonth() || (today.getMonth() === birth.getMonth() && today.getDate() < birth.getDate())) age--;
                isValid = age >= 18;
            }

            const existingMsg = element.parentNode.querySelector('.unique-err-msg');
            if(existingMsg) existingMsg.remove();

            if (isValid && (type === 'username' || type === 'email')) {
                // Debounce API calls
                clearTimeout(validationTimeout[type]);
                
                return new Promise((resolve) => {
                    validationTimeout[type] = setTimeout(async () => {
                        try {
                            const payload = {};
                            if (type === 'username') payload.username = element.value;
                            if (type === 'email') payload.email = element.value;
                            
                            const res = await fetch(getBasePath() + '/api/check-availability', {
                                method: 'POST',
                                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': getCsrf(), 'Accept': 'application/json' },
                                body: JSON.stringify(payload)
                            });
                            const data = await res.json();
                            
                            if (!data.success) {
                                isValid = false;
                                let errMsg = document.createElement('div');
                                errMsg.className = 'unique-err-msg';
                                errMsg.style.color = 'var(--red-500)';
                                errMsg.style.fontSize = '12px';
                                errMsg.style.marginTop = '6px';
                                errMsg.style.fontWeight = '600';
                                errMsg.innerText = data.message;
                                element.parentNode.appendChild(errMsg);
                            }
                        } catch (e) {
                            console.error(e);
                        }
                        
                        applyValidationUI(element, isValid);
                        resolve(isValid);
                    }, 500); // 500ms debounce
                });
            } else {
                applyValidationUI(element, isValid);
                return isValid;
            }
        }

        function applyValidationUI(element, isValid) {
            if(element.value.length > 0) {
                if (isValid) {
                    element.classList.remove('error');
                    element.classList.add('success');
                    if (element.nextElementSibling && element.nextElementSibling.classList.contains('success-icon')) {
                        element.nextElementSibling.classList.add('show');
                    }
                } else {
                    element.classList.remove('success');
                    element.classList.add('error');
                    if (element.nextElementSibling && element.nextElementSibling.classList.contains('success-icon')) {
                        element.nextElementSibling.classList.remove('show');
                    }
                }
            } else {
                element.classList.remove('success', 'error');
            }
        }

        function checkStrength() {
            const val = document.getElementById('reg-pass').value;
            const bars = [document.getElementById('str-1'), document.getElementById('str-2'), document.getElementById('str-3'), document.getElementById('str-4')];
            let strength = 0;
            
            bars.forEach(b => b.style.backgroundColor = 'var(--gray-100)');

            if (val.length > 5) strength++;
            if (val.length >= 8) strength++;
            if (/[A-Z]/.test(val) && /[0-9]/.test(val)) strength++;
            if (/[^A-Za-z0-9]/.test(val)) strength++;

            for(let i=0; i<strength; i++) {
                if(strength <= 2) bars[i].style.backgroundColor = 'var(--error-red)';
                else if(strength === 3) bars[i].style.backgroundColor = '#F59E0B'; // Amber
                else bars[i].style.backgroundColor = 'var(--emerald-500)';
            }
            checkMatch();
        }

        function checkMatch() {
            const pass = document.getElementById('reg-pass').value;
            const confirm = document.getElementById('reg-confirm');
            const label = document.getElementById('match-label');

            if (confirm.value.length > 0) {
                if (pass === confirm.value) {
                    confirm.classList.remove('error');
                    confirm.classList.add('success');
                    label.innerHTML = 'Confirm Password <span style="color:var(--success-green); font-size:12px; font-weight:700;">(Matched)</span>';
                } else {
                    confirm.classList.add('error');
                    confirm.classList.remove('success');
                    label.innerHTML = 'Confirm Password <span style="color:var(--error-red); font-size:12px; font-weight:700;">(No Match)</span>';
                }
            } else {
                label.innerHTML = 'Confirm Password';
                confirm.classList.remove('error', 'success');
            }
        }

        // ─── Helper ───────────────────────────────────────────────────────────────
        function getBasePath() {
            return window.location.pathname.replace('/auth', '').replace(/\/$/, '');
        }
        function getCsrf() {
            const t = document.querySelector('meta[name="csrf-token"]');
            if (t && t.content) return t.content;
            const match = document.cookie.match(/XSRF-TOKEN=([^;]+)/);
            if (match) return decodeURIComponent(match[1]);
            return '';
        }
        function showLoginError(msg, isSuccess = false) {
            const el = document.getElementById('login-error-msg');
            if (!el) return;
            el.style.display = 'block';
            if (isSuccess) {
                el.style.background = '#E8F5E9';
                el.style.color = '#2D5A27';
                el.style.borderColor = '#C8E6C9';
            } else {
                el.style.background = '#FFEBEE';
                el.style.color = '#D32F2F';
                el.style.borderColor = '#FFCDD2';
            }
            el.textContent = msg;
        }
        function hideLoginError() {
            const el = document.getElementById('login-error-msg');
            if (el) el.style.display = 'none';
        }

        // ─── Forgot & Reset Password ───────────────────────────────────────────────
        let resetEmailStore = '';

        async function doForgotPassword(btn) {
            const emailInput = document.getElementById('forgot-email');
            const email = emailInput.value.trim();
            if (!email) {
                showToast('Please enter your email address.');
                return;
            }

            btn.classList.add('loading');
            document.getElementById('forgot-error-msg').style.display = 'none';
            document.getElementById('forgot-success-msg').style.display = 'none';

            try {
                const res = await fetch(getBasePath() + '/api/forgot-password', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': getCsrf(), 'Accept': 'application/json' },
                    body: JSON.stringify({ email })
                });
                const result = await res.json();
                btn.classList.remove('loading');

                if (res.ok && result.success) {
                    resetEmailStore = email;
                    const successMsg = document.getElementById('forgot-success-msg');
                    successMsg.style.display = 'block';
                    successMsg.textContent = result.message;
                    
                    setTimeout(() => {
                        switchPanel('reset-password-panel');
                    }, 2000);
                } else {
                    const errorMsg = document.getElementById('forgot-error-msg');
                    errorMsg.style.display = 'block';
                    errorMsg.textContent = result.message || 'Something went wrong.';
                }
            } catch (e) {
                btn.classList.remove('loading');
                const errorMsg = document.getElementById('forgot-error-msg');
                errorMsg.style.display = 'block';
                errorMsg.textContent = 'Network error. Please try again.';
            }
        }

        async function doVerifyOtp(btn) {
            const otp = document.getElementById('reset-otp').value.trim();
            if (!otp || otp.length !== 6) {
                showToast('Please enter the 6-digit OTP.');
                return;
            }

            btn.classList.add('loading');
            document.getElementById('reset-error-msg').style.display = 'none';
            document.getElementById('reset-success-msg').style.display = 'none';

            try {
                const res = await fetch(getBasePath() + '/api/verify-otp', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': getCsrf(), 'Accept': 'application/json' },
                    body: JSON.stringify({ email: resetEmailStore, otp })
                });
                const result = await res.json();
                btn.classList.remove('loading');

                if (res.ok && result.success) {
                    const successMsg = document.getElementById('reset-success-msg');
                    successMsg.style.display = 'block';
                    successMsg.textContent = result.message;
                    
                    document.getElementById('step-otp-section').style.display = 'none';
                    document.getElementById('step-password-section').style.display = 'block';
                    document.getElementById('reset-title').innerText = 'Set New Password';
                    document.getElementById('reset-subtitle').innerText = 'Create a secure new password for your account.';
                    document.getElementById('reset-footer').style.display = 'none';
                    successMsg.style.display = 'none'; // hide it so password form is clean
                } else {
                    const errorMsg = document.getElementById('reset-error-msg');
                    errorMsg.style.display = 'block';
                    errorMsg.textContent = result.message || 'Invalid OTP.';
                }
            } catch (e) {
                btn.classList.remove('loading');
                const errorMsg = document.getElementById('reset-error-msg');
                errorMsg.style.display = 'block';
                errorMsg.textContent = 'Network error. Please try again.';
            }
        }

        async function doResetPassword(btn) {
            const otp = document.getElementById('reset-otp').value.trim();
            const password = document.getElementById('reset-password').value;
            const password_confirmation = document.getElementById('reset-password-confirm').value;

            if (!otp || otp.length !== 6) {
                showToast('Please enter the 6-digit OTP.');
                return;
            }
            if (password.length < 8) {
                showToast('Password must be at least 8 characters.');
                return;
            }
            if (password !== password_confirmation) {
                showToast('Passwords do not match.');
                return;
            }

            btn.classList.add('loading');
            document.getElementById('reset-error-msg').style.display = 'none';
            document.getElementById('reset-success-msg').style.display = 'none';

            try {
                const res = await fetch(getBasePath() + '/api/reset-password', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': getCsrf(), 'Accept': 'application/json' },
                    body: JSON.stringify({ email: resetEmailStore, otp, password, password_confirmation })
                });
                const result = await res.json();
                btn.classList.remove('loading');

                if (res.ok && result.success) {
                    const successMsg = document.getElementById('reset-success-msg');
                    successMsg.style.display = 'block';
                    successMsg.textContent = result.message;
                    
                    setTimeout(() => {
                        switchPanel('login-panel');
                        const loginEmailInput = document.querySelector('#login-form input[type="email"]');
                        if(loginEmailInput) loginEmailInput.value = resetEmailStore;
                        showLoginError('Password reset successful. Please log in with your new password.', true);
                    }, 2000);
                } else {
                    const errorMsg = document.getElementById('reset-error-msg');
                    errorMsg.style.display = 'block';
                    errorMsg.textContent = result.message || 'Something went wrong.';
                }
            } catch (e) {
                btn.classList.remove('loading');
                const errorMsg = document.getElementById('reset-error-msg');
                errorMsg.style.display = 'block';
                errorMsg.textContent = 'Network error. Please try again.';
            }
        }

        // ─── Registration ─────────────────────────────────────────────────────────
        async function goToStep2() {
            const btn = document.querySelector('#signup-1 button.btn-primary');
            const name  = (document.getElementById('reg-name')?.value  || '').trim();
            const username = (document.getElementById('reg-username')?.value || '').trim().toLowerCase();
            const email = (document.getElementById('reg-email')?.value || '').trim().toLowerCase();
            const phone = (document.getElementById('reg-phone')?.value || '').trim();

            if (!name || name.length < 3)  { showToast('Please enter your full name (min 3 characters).'); return; }
            if (!username || username.length < 3) { showToast('Please enter a username (min 3 characters).'); return; }
            if (!email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) { showToast('Please enter a valid email address.'); return; }
            if (!phone || phone.length < 10) { showToast('Please enter a valid 10-digit phone number.'); return; }

            const originalText = btn.innerText;
            btn.innerText = 'Checking...';
            btn.disabled = true;

            try {
                const res = await fetch(getBasePath() + '/api/check-availability', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': getCsrf(), 'Accept': 'application/json' },
                    body: JSON.stringify({ email, username })
                });
                const data = await res.json();
                
                if (!data.success) {
                    showToast(data.message);
                    btn.innerText = originalText;
                    btn.disabled = false;
                    return;
                }
            } catch (e) {
                console.error(e);
            }

            btn.innerText = originalText;
            btn.disabled = false;
            switchPanel('signup-2');
        }

        function goToStep3() {
            const dob   = document.getElementById('reg-dob')?.value  || null;
            const gender = document.getElementById('reg-gender')?.value || null;
            const div   = document.getElementById('reg-div')?.value  || null;
            const dist  = document.getElementById('reg-dist')?.value || null;

            if (!dob) { showToast('Please enter your Date of Birth.'); return; }
            if (!gender) { showToast('Please select your Gender.'); return; }
            if (!div) { showToast('Please select your Division.'); return; }
            if (!dist) { showToast('Please select your District.'); return; }

            switchPanel('signup-3');
        }

        async function doRegister(btn) {
            const name  = (document.getElementById('reg-name')?.value  || '').trim();
            const username = (document.getElementById('reg-username')?.value || '').trim().toLowerCase();
            const email = (document.getElementById('reg-email')?.value || '').trim().toLowerCase();
            const phone = (document.getElementById('reg-phone')?.value || '').trim();
            const pass  = (document.getElementById('reg-pass')?.value  || '').trim();
            const conf  = (document.getElementById('reg-confirm')?.value || '').trim();
            const dob   = document.getElementById('reg-dob')?.value  || null;
            const gender = document.getElementById('reg-gender')?.value || null;

            const div   = document.getElementById('reg-div')?.value  || null;
            const dist  = document.getElementById('reg-dist')?.value || null;
            const terms = document.getElementById('terms')?.checked;

            // Client-side validation
            if (!name || name.length < 3)  { showToast('Please enter your full name (min 3 characters).'); return; }
            if (!username || username.length < 3) { showToast('Please enter a username (min 3 characters).'); return; }
            if (!email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) { showToast('Please enter a valid email address.'); return; }
            if (!phone || phone.length < 10) { showToast('Please enter a valid 10-digit phone number.'); return; }
            if (!pass  || pass.length < 8)  { showToast('Password must be at least 8 characters.'); return; }
            if (pass !== conf)              { showToast('Passwords do not match.'); return; }
            if (!terms)                     { showToast('You must agree to the Terms of Service.'); return; }

            btn.classList.add('loading');

            const payload = { name, username, email, phone: '+880' + phone, password: pass };
            if (dob)  payload.date_of_birth = dob;
            if (gender) payload.gender      = gender;
            if (div)  payload.division      = div;
            if (dist) payload.district      = dist;

            try {
                payload._token = getCsrf();
                const res    = await fetch(getBasePath() + '/api/register', {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': getCsrf(), 'Accept': 'application/json' },
                    body: JSON.stringify(payload)
                });
                const result = await res.json();
                btn.classList.remove('loading');

                if (res.ok && result.success) {
                    // ✅ Success — take user to login panel with a green message
                    switchPanel('login-panel');
                    // Pre-fill the email for convenience
                    const loginEmailInput = document.querySelector('#login-form input[type="email"]');
                    if (loginEmailInput) loginEmailInput.value = email;
                    showLoginError('✓ Account created! Log in with your new credentials.', true);
                } else {
                    let errMsg = result.message || 'Registration failed. Please check your details.';
                    if (result.errors) {
                        const firstKey = Object.keys(result.errors)[0];
                        errMsg = result.errors[firstKey][0];
                    }
                    showToast('Registration failed: ' + errMsg);
                }
            } catch (e) {
                btn.classList.remove('loading');
                showToast('Network error. Please check your connection and try again.');
            }
        }

        // ─── Login ────────────────────────────────────────────────────────────────
        async function doLogin(btn) {
            const emailInput = document.querySelector('#login-form input[type="email"]');
            const passInput  = document.getElementById('login-password');
            const email = (emailInput?.value || '').trim().toLowerCase();
            const pass  =  passInput?.value || '';

            hideLoginError();
            if (!email) { showLoginError('Please enter your email address.'); return; }
            if (!pass)  { showLoginError('Please enter your password.'); return; }

            btn.classList.add('loading');

            try {
                let csrfToken = getCsrf();
                let res = await fetch(getBasePath() + '/api/login', {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                    body: JSON.stringify({ email, password: pass, _token: csrfToken })
                });

                // Auto-recover if CSRF token expired in background
                if (res.status === 419) {
                    try {
                        const tokenRes = await fetch(getBasePath() + '/api/csrf-token', { credentials: 'same-origin' });
                        const tokenData = await tokenRes.json();
                        if (tokenData && tokenData.csrf_token) {
                            csrfToken = tokenData.csrf_token;
                            const metaTag = document.querySelector('meta[name="csrf-token"]');
                            if (metaTag) metaTag.setAttribute('content', csrfToken);
                            res = await fetch(getBasePath() + '/api/login', {
                                method: 'POST',
                                credentials: 'same-origin',
                                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                                body: JSON.stringify({ email, password: pass, _token: csrfToken })
                            });
                        }
                    } catch (tokenErr) {
                        console.error('Failed to auto-refresh CSRF token:', tokenErr);
                    }
                }

                const result = await res.json();
                btn.classList.remove('loading');

                if (res.ok && result.success) {
                    // Sync email and wallet balance to localStorage for React dashboards
                    if (result.role === 'admin') {
                        localStorage.setItem('migratepay_admin_auth', 'true');
                    } else {
                        localStorage.setItem('migratepay_user_auth', 'true');
                    }
                    localStorage.setItem('migratepay_current_user_email', result.user.email.toLowerCase());
                    const balances = JSON.parse(localStorage.getItem('migratepay_wallet_balances') || '{}');
                    balances[result.user.email.toLowerCase()] = parseFloat(result.user.wallet_balance) || 0;
                    localStorage.setItem('migratepay_wallet_balances', JSON.stringify(balances));

                    // Redirect based on role — server tells us where to go
                    window.location.href = getBasePath() + (result.redirect || '/dashboard');
                } else {
                    if (emailInput) emailInput.classList.add('error');
                    if (passInput)  passInput.classList.add('error');
                    showLoginError(result.message || 'Invalid email or password. Please try again.');
                }
            } catch (e) {
                btn.classList.remove('loading');
                showLoginError('Network error. Please check your connection.');
            }
        }

        // --- ADMIN UI ADAPTATION ---
        document.addEventListener('DOMContentLoaded', () => {
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('role') === 'admin') {
                const headerH1 = document.querySelector('.auth-header h1');
                const headerP = document.querySelector('.auth-header p');
                const submitBtnSpan = document.querySelector('.btn-primary span');
                const footer = document.querySelector('.auth-footer');
                const backLink = document.querySelector('.back-link');

                if (headerH1) headerH1.innerText = 'Admin Portal Access';
                if (headerP) headerP.innerText = 'Authorize via secure administrative credentials.';
                if (submitBtnSpan) submitBtnSpan.innerText = 'Establish Admin Link';
                if (footer) footer.style.display = 'none';
                
                if (backLink) {
                    backLink.innerHTML = `<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"></polyline></svg> Return to Public Site`;
                    backLink.href = window.location.pathname.replace('/auth', '');
                }

                // Add a subtle admin badge
                const card = document.querySelector('.auth-card');
                const badge = document.createElement('div');
                badge.className = 'admin-badge';
                badge.innerHTML = 'ROOT ACCESS';
                badge.style = `
                    position: absolute;
                    top: 24px;
                    right: -30px;
                    background: #CF1322;
                    color: white;
                    font-size: 8px;
                    font-weight: 900;
                    padding: 4px 40px;
                    transform: rotate(45deg);
                    box-shadow: 0 2px 4px rgba(0,0,0,0.2);
                    letter-spacing: 1px;
                `;
                card.appendChild(badge);
                card.style.overflow = 'hidden';
            }
        });
    </script>
    @include('partials.ai-chat-widget')
</body>
</html>
