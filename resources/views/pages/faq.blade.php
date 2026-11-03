@extends('layouts.frontend')

@section('title', 'FAQ — Frequently Asked Questions | MigratePay')

@section('content')
<style>
.faq-page{--faq-primary:#2563EB;--faq-primary-dark:#1D4ED8;--faq-cyan:#06B6D4;--faq-indigo:#6366F1;--faq-violet:#8B5CF6;--faq-red-accent:#EF4444;--faq-teal:#14B8A6;--faq-white:#FFFFFF;--faq-text-dark:#0F172A;--faq-text-mid:#334155;--faq-text-muted:#64748B;--faq-card-bg:rgba(255,255,255,0.88);--faq-shadow-card:0 4px 24px rgba(37,99,235,0.07),0 1px 4px rgba(0,0,0,0.04);--faq-shadow-open:0 12px 40px rgba(37,99,235,0.14),0 2px 8px rgba(0,0,0,0.06);--faq-shadow-hover:0 8px 28px rgba(37,99,235,0.10);--faq-transition:all 0.4s cubic-bezier(0.16,1,0.3,1);--faq-bounce:all 0.5s cubic-bezier(0.34,1.56,0.64,1);font-family:'Inter',system-ui,-apple-system,sans-serif;color:var(--faq-text-dark);position:relative;overflow-x:hidden;}
.faq-page__bg{position:fixed;inset:0;z-index:0;pointer-events:none;overflow:hidden;}
.faq-page__bg::before{content:'';position:absolute;inset:0;background:linear-gradient(160deg,#EEF3FB 0%,#F0F5FF 25%,#EBF4FF 50%,#F3F0FF 75%,#ECF5FB 100%);}
.faq-bg-glow{position:absolute;border-radius:50%;filter:blur(50px);}
.faq-bg-blue{width:900px;height:900px;background:radial-gradient(circle,rgba(59,130,246,.18) 0%,rgba(37,99,235,.06) 45%,transparent 70%);top:-200px;right:-150px;animation:fglow1 22s ease-in-out infinite alternate;}
.faq-bg-cyan{width:700px;height:700px;background:radial-gradient(circle,rgba(6,182,212,.14) 0%,rgba(6,182,212,.04) 50%,transparent 70%);bottom:100px;left:-100px;animation:fglow2 28s ease-in-out infinite alternate;}
.faq-bg-indigo{width:600px;height:600px;background:radial-gradient(circle,rgba(99,102,241,.11) 0%,rgba(99,102,241,.03) 50%,transparent 70%);top:40%;left:25%;animation:fglow3 30s ease-in-out infinite alternate;filter:blur(60px);}
.faq-bg-violet{width:500px;height:500px;background:radial-gradient(circle,rgba(139,92,246,.09) 0%,transparent 65%);bottom:30%;right:10%;animation:fglow4 25s ease-in-out infinite alternate;filter:blur(55px);}
.faq-bg-red{width:300px;height:300px;background:radial-gradient(circle,rgba(239,68,68,.06) 0%,transparent 65%);top:60%;right:5%;filter:blur(50px);}
@keyframes fglow1{0%{transform:translate(0,0) scale(1);opacity:.8}50%{transform:translate(-60px,80px) scale(1.08);opacity:1}100%{transform:translate(40px,-40px) scale(.95);opacity:.7}}
@keyframes fglow2{0%{transform:translate(0,0) scale(1);opacity:.7}50%{transform:translate(70px,-50px) scale(1.1);opacity:.9}100%{transform:translate(-30px,60px) scale(.9);opacity:.6}}
@keyframes fglow3{0%{opacity:.8}100%{transform:translate(-50px,80px) scale(1.05);opacity:.6}}
@keyframes fglow4{0%{opacity:.7}100%{transform:translate(40px,-60px);opacity:.5}}
.faq-fi{position:fixed;pointer-events:none;z-index:1;opacity:0;animation:ffi-in 2s ease forwards,ffi-fl var(--fd,12s) ease-in-out var(--fdel,0s) infinite;}
@keyframes ffi-in{from{opacity:0}to{opacity:var(--fo,.09)}}
@keyframes ffi-fl{0%{transform:translateY(0) rotate(var(--rs,0deg))}50%{transform:translateY(var(--fy,-18px)) rotate(var(--re,4deg))}100%{transform:translateY(0) rotate(var(--rs,0deg))}}
.faq-page__content{position:relative;z-index:2;padding-top:140px;}
.faq-hero{padding:80px 0 60px;text-align:center;}
.faq-hero__badge{display:inline-flex;align-items:center;gap:8px;background:rgba(37,99,235,.08);border:1px solid rgba(37,99,235,.18);color:var(--faq-primary);padding:8px 20px;border-radius:100px;font-size:12px;font-weight:800;text-transform:uppercase;letter-spacing:1.2px;margin-bottom:28px;animation:faq-in .8s cubic-bezier(.16,1,.3,1) both;}
.faq-bdot{width:7px;height:7px;border-radius:50%;background:var(--faq-primary);animation:faq-dot 2s ease-in-out infinite;}
@keyframes faq-dot{0%,100%{transform:scale(1);opacity:1}50%{transform:scale(1.4);opacity:.6}}
.faq-hero__title{font-family:'Plus Jakarta Sans','Inter',sans-serif;font-size:clamp(40px,6vw,72px);font-weight:900;line-height:1.05;letter-spacing:-.03em;color:var(--faq-text-dark);margin-bottom:24px;animation:faq-in .9s cubic-bezier(.16,1,.3,1) .1s both;}
.faq-hero__title span{background:linear-gradient(135deg,#2563EB 0%,#06B6D4 50%,#6366F1 100%);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;}
.faq-hero__sub{font-size:clamp(16px,2vw,20px);color:var(--faq-text-muted);max-width:580px;margin:0 auto 48px;line-height:1.7;animation:faq-in .9s cubic-bezier(.16,1,.3,1) .2s both;}
@keyframes faq-in{from{opacity:0;transform:translateY(30px)}to{opacity:1;transform:translateY(0)}}
.faq-sw{position:relative;max-width:580px;margin:0 auto;animation:faq-in .9s cubic-bezier(.16,1,.3,1) .3s both;}
.faq-si{position:absolute;left:22px;top:50%;transform:translateY(-50%);color:var(--faq-text-muted);width:20px;height:20px;pointer-events:none;transition:color .3s;}
.faq-search-input{width:100%;padding:20px 24px 20px 56px;border-radius:16px;border:2px solid rgba(37,99,235,.12);background:rgba(255,255,255,.92);font-size:16px;font-weight:500;color:var(--faq-text-dark);font-family:'Inter',sans-serif;outline:none;box-shadow:0 8px 32px rgba(37,99,235,.08),0 2px 8px rgba(0,0,0,.04);transition:var(--faq-transition);backdrop-filter:blur(8px);}
.faq-search-input::placeholder{color:var(--faq-text-muted);font-weight:400;}
.faq-search-input:focus{border-color:var(--faq-primary);background:rgba(255,255,255,.98);box-shadow:0 0 0 4px rgba(37,99,235,.12),0 12px 40px rgba(37,99,235,.12);transform:translateY(-1px);}
.faq-sc{position:absolute;right:18px;top:50%;transform:translateY(-50%);background:rgba(100,116,139,.1);border:none;border-radius:50%;width:28px;height:28px;display:none;align-items:center;justify-content:center;cursor:pointer;color:var(--faq-text-muted);transition:var(--faq-transition);font-size:18px;line-height:1;}
.faq-sc:hover{background:rgba(239,68,68,.1);color:var(--faq-red-accent);}
.faq-sc.visible{display:flex;}
.faq-main{max-width:1200px;margin:0 auto;padding:0 24px 120px;}
.faq-hls{display:flex;justify-content:center;gap:12px;flex-wrap:wrap;margin-bottom:60px;animation:faq-in .9s cubic-bezier(.16,1,.3,1) .4s both;}
.faq-hp{display:flex;align-items:center;gap:8px;background:rgba(255,255,255,.85);border:1px solid rgba(37,99,235,.1);border-radius:100px;padding:10px 20px;font-size:13px;font-weight:700;color:var(--faq-text-mid);backdrop-filter:blur(8px);box-shadow:0 2px 12px rgba(37,99,235,.06);transition:var(--faq-bounce);}
.faq-hp:hover{transform:translateY(-3px);box-shadow:0 8px 24px rgba(37,99,235,.12);}
.faq-hpi{width:28px;height:28px;border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0;}
.hp-b{background:rgba(37,99,235,.1);color:var(--faq-primary);}
.hp-c{background:rgba(6,182,212,.1);color:var(--faq-cyan);}
.hp-t{background:rgba(20,184,166,.1);color:var(--faq-teal);}
.hp-i{background:rgba(99,102,241,.1);color:var(--faq-indigo);}
.faq-grid{display:grid;grid-template-columns:240px 1fr;gap:40px;align-items:start;}
.faq-sidebar{position:sticky;top:130px;}
.faq-sl{font-size:11px;font-weight:800;text-transform:uppercase;letter-spacing:1.5px;color:var(--faq-text-muted);margin-bottom:14px;padding-left:4px;}
.faq-cn{display:flex;flex-direction:column;gap:4px;}
.faq-cb{display:flex;align-items:center;gap:10px;padding:11px 16px;border-radius:12px;border:1px solid transparent;background:none;cursor:pointer;font-size:13px;font-weight:700;color:var(--faq-text-muted);text-align:left;transition:var(--faq-transition);font-family:'Inter',sans-serif;width:100%;}
.faq-cb:hover{background:rgba(37,99,235,.06);color:var(--faq-primary);border-color:rgba(37,99,235,.12);}
.faq-cb.active{background:linear-gradient(135deg,rgba(37,99,235,.12) 0%,rgba(99,102,241,.08) 100%);color:var(--faq-primary);border-color:rgba(37,99,235,.2);box-shadow:0 2px 12px rgba(37,99,235,.08);}
.faq-cc{margin-left:auto;font-size:11px;font-weight:700;background:rgba(37,99,235,.1);color:var(--faq-primary);padding:2px 8px;border-radius:100px;opacity:0;transition:opacity .3s;}
.faq-cb.active .faq-cc,.faq-cb:hover .faq-cc{opacity:1;}
.faq-sh{display:flex;align-items:center;gap:14px;margin-bottom:24px;padding-bottom:18px;border-bottom:1px solid rgba(37,99,235,.08);}
.faq-si2{width:38px;height:38px;border-radius:10px;background:linear-gradient(135deg,var(--faq-primary) 0%,var(--faq-indigo) 100%);display:flex;align-items:center;justify-content:center;color:white;flex-shrink:0;box-shadow:0 4px 12px rgba(37,99,235,.25);}
.faq-st{font-family:'Plus Jakarta Sans','Inter',sans-serif;font-size:22px;font-weight:800;color:var(--faq-text-dark);letter-spacing:-.02em;}
.faq-group{margin-bottom:56px;display:none;}
.faq-group.active{display:block;}
.faq-card{background:var(--faq-card-bg);border:1px solid rgba(37,99,235,.10);border-radius:18px;margin-bottom:12px;overflow:hidden;box-shadow:var(--faq-shadow-card);transition:var(--faq-transition);position:relative;backdrop-filter:blur(12px);}
.faq-card::before{content:'';position:absolute;left:0;top:0;bottom:0;width:4px;background:linear-gradient(180deg,var(--faq-primary) 0%,var(--faq-cyan) 100%);transform:scaleY(0);transform-origin:center;transition:transform .4s cubic-bezier(.16,1,.3,1);border-radius:4px 0 0 4px;}
.faq-card:hover{box-shadow:var(--faq-shadow-hover);border-color:rgba(37,99,235,.18);transform:translateY(-2px);}
.faq-card.is-open{border-color:rgba(37,99,235,.22);box-shadow:var(--faq-shadow-open);transform:translateY(-2px);background:rgba(255,255,255,.96);}
.faq-card.is-open::before{transform:scaleY(1);}
.faq-card[data-hidden="true"]{display:none;}
.faq-question{display:flex;align-items:center;gap:16px;padding:22px 24px 22px 28px;cursor:pointer;border:none;background:none;width:100%;text-align:left;font-family:'Inter',sans-serif;transition:var(--faq-transition);}
.faq-question:focus-visible{outline:2px solid var(--faq-primary);outline-offset:-2px;border-radius:16px;}
.faq-qn{font-size:12px;font-weight:800;color:rgba(37,99,235,.4);letter-spacing:.5px;min-width:28px;font-variant-numeric:tabular-nums;transition:color .3s;flex-shrink:0;}
.faq-card.is-open .faq-qn,.faq-card:hover .faq-qn{color:var(--faq-primary);}
.faq-qt{font-size:16px;font-weight:700;color:var(--faq-text-dark);line-height:1.4;flex:1;transition:color .3s;}
.faq-card.is-open .faq-qt{color:var(--faq-primary);}
.faq-qi{width:34px;height:34px;border-radius:10px;background:rgba(37,99,235,.06);border:1px solid rgba(37,99,235,.1);display:flex;align-items:center;justify-content:center;flex-shrink:0;transition:var(--faq-bounce);color:var(--faq-text-muted);}
.faq-card:hover .faq-qi{background:rgba(37,99,235,.10);color:var(--faq-primary);border-color:rgba(37,99,235,.2);}
.faq-card.is-open .faq-qi{background:linear-gradient(135deg,var(--faq-primary) 0%,var(--faq-indigo) 100%);color:white;border-color:transparent;transform:rotate(45deg);box-shadow:0 4px 14px rgba(37,99,235,.35);}
.faq-aw{display:grid;grid-template-rows:0fr;transition:grid-template-rows .45s cubic-bezier(.16,1,.3,1);}
.faq-card.is-open .faq-aw{grid-template-rows:1fr;}
.faq-ai{overflow:hidden;}
.faq-answer{padding:0 28px 26px 72px;font-size:15px;color:var(--faq-text-muted);line-height:1.8;opacity:0;transform:translateY(-8px);transition:opacity .35s ease .05s,transform .4s cubic-bezier(.16,1,.3,1) .05s;}
.faq-card.is-open .faq-answer{opacity:1;transform:translateY(0);}
.faq-answer strong{color:var(--faq-text-dark);font-weight:700;}
.faq-nr{display:none;text-align:center;padding:60px 24px;background:rgba(255,255,255,.7);border-radius:20px;border:1px solid rgba(37,99,235,.08);backdrop-filter:blur(8px);}
.faq-nr.visible{display:block;}
.faq-nr__icon{font-size:56px;margin-bottom:20px;opacity:.4;}
.faq-nr__title{font-size:20px;font-weight:800;color:var(--faq-text-dark);margin-bottom:10px;}
.faq-nr__sub{font-size:15px;color:var(--faq-text-muted);}
.faq-cta{margin-top:40px;border-radius:28px;background:linear-gradient(135deg,#1E40AF 0%,#2563EB 30%,#4F46E5 65%,#7C3AED 100%);background-size:200% 200%;animation:fcta-g 12s ease-in-out infinite;padding:64px 48px;text-align:center;position:relative;overflow:hidden;box-shadow:0 20px 60px rgba(37,99,235,.3);}
@keyframes fcta-g{0%{background-position:0% 50%}50%{background-position:100% 50%}100%{background-position:0% 50%}}
.faq-cta::before{content:'';position:absolute;inset:0;background:radial-gradient(circle at 30% 50%,rgba(255,255,255,.08) 0%,transparent 60%),radial-gradient(circle at 75% 20%,rgba(255,255,255,.06) 0%,transparent 50%);pointer-events:none;}
.faq-cfi{position:absolute;opacity:.12;animation:fcta-fl 10s ease-in-out infinite;color:white;font-size:36px;}
.faq-cfi:nth-child(1){top:15%;left:8%;animation-delay:0s;}
.faq-cfi:nth-child(2){top:60%;left:5%;font-size:28px;animation-delay:2s;}
.faq-cfi:nth-child(3){top:20%;right:8%;animation-delay:4s;}
.faq-cfi:nth-child(4){top:65%;right:6%;font-size:32px;animation-delay:1s;}
@keyframes fcta-fl{0%,100%{transform:translateY(0) rotate(0deg)}50%{transform:translateY(-16px) rotate(8deg)}}
.faq-cta__body{position:relative;z-index:1;max-width:520px;margin:0 auto;}
.faq-cta__title{font-family:'Plus Jakarta Sans','Inter',sans-serif;font-size:clamp(26px,4vw,38px);font-weight:900;color:white;margin-bottom:14px;letter-spacing:-.02em;line-height:1.15;}
.faq-cta__sub{font-size:16px;color:rgba(255,255,255,.80);margin-bottom:36px;line-height:1.7;}
.faq-cta__acts{display:flex;gap:14px;justify-content:center;flex-wrap:wrap;}
.faq-bw{display:inline-flex;align-items:center;gap:10px;padding:16px 32px;border-radius:100px;font-weight:800;font-size:15px;cursor:pointer;transition:var(--faq-bounce);border:none;text-decoration:none;background:white;color:var(--faq-primary);box-shadow:0 8px 24px rgba(0,0,0,.2);}
.faq-bw:hover{transform:translateY(-4px);box-shadow:0 14px 36px rgba(0,0,0,.25);color:var(--faq-primary-dark);}
.faq-bg2{display:inline-flex;align-items:center;gap:10px;padding:16px 32px;border-radius:100px;font-weight:800;font-size:15px;cursor:pointer;transition:var(--faq-bounce);border:2px solid rgba(255,255,255,.3);text-decoration:none;background:rgba(255,255,255,.08);color:white;backdrop-filter:blur(4px);}
.faq-bg2:hover{background:rgba(255,255,255,.18);border-color:rgba(255,255,255,.5);transform:translateY(-3px);color:white;}
.faq-reveal{opacity:0;transform:translateY(24px);transition:opacity .7s cubic-bezier(.16,1,.3,1),transform .7s cubic-bezier(.16,1,.3,1);}
.faq-reveal.is-vis{opacity:1;transform:translateY(0);}
@media(max-width:900px){.faq-grid{grid-template-columns:1fr;}.faq-sidebar{position:static;}.faq-cn{flex-direction:row;flex-wrap:nowrap;overflow-x:auto;gap:8px;padding-bottom:6px;scrollbar-width:none;}.faq-cn::-webkit-scrollbar{display:none;}.faq-cb{white-space:nowrap;border-radius:100px;padding:9px 18px;flex-shrink:0;}.faq-cc,.faq-sl{display:none;}.faq-cta{padding:48px 28px;}}
@media(max-width:600px){.faq-hero{padding:48px 0 40px;}.faq-page__content{padding-top:120px;}.faq-main{padding-bottom:80px;}.faq-question{padding:18px;gap:12px;}.faq-answer{padding:0 20px 22px 20px;}.faq-qn{display:none;}.faq-cta__acts{flex-direction:column;align-items:center;}.faq-bw,.faq-bg2{width:100%;justify-content:center;}.faq-fi{display:none;}}
@media(prefers-reduced-motion:reduce){.faq-bg-blue,.faq-bg-cyan,.faq-bg-indigo,.faq-bg-violet,.faq-fi,.faq-cfi{animation:none!important;}.faq-cta{animation:none;}.faq-hero__badge,.faq-hero__title,.faq-hero__sub,.faq-sw,.faq-hls{animation:none;opacity:1;}.faq-reveal{opacity:1;transform:none;}}
</style><div class="faq-page">
<div class="faq-page__bg" aria-hidden="true">
  <div class="faq-bg-glow faq-bg-blue"></div>
  <div class="faq-bg-glow faq-bg-cyan"></div>
  <div class="faq-bg-glow faq-bg-indigo"></div>
  <div class="faq-bg-glow faq-bg-violet"></div>
  <div class="faq-bg-glow faq-bg-red"></div>
</div>
<div class="faq-fi" aria-hidden="true" style="top:18%;left:4%;--fo:.08;--fd:14s;--fdel:0s;--fy:-20px;--rs:-8deg;--re:4deg;"><svg width="52" height="52" viewBox="0 0 24 24" fill="none" stroke="#2563EB" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg></div>
<div class="faq-fi" aria-hidden="true" style="top:35%;right:3%;--fo:.07;--fd:18s;--fdel:2s;--fy:-14px;--rs:5deg;--re:-4deg;"><svg width="44" height="44" viewBox="0 0 24 24" fill="none" stroke="#6366F1" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg></div>
<div class="faq-fi" aria-hidden="true" style="top:60%;left:3%;--fo:.07;--fd:20s;--fdel:5s;--fy:-18px;--rs:0deg;--re:6deg;"><svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#06B6D4" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg></div>
<div class="faq-fi" aria-hidden="true" style="top:80%;right:4%;--fo:.06;--fd:16s;--fdel:3s;--fy:-22px;--rs:-5deg;--re:3deg;"><svg width="46" height="46" viewBox="0 0 24 24" fill="none" stroke="#8B5CF6" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg></div>
<div class="faq-fi" aria-hidden="true" style="top:12%;right:8%;--fo:.06;--fd:22s;--fdel:1s;--fy:-16px;--rs:3deg;--re:-5deg;"><svg width="38" height="38" viewBox="0 0 24 24" fill="none" stroke="#2563EB" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg></div>
<div class="faq-fi" aria-hidden="true" style="top:70%;left:8%;--fo:.07;--fd:24s;--fdel:7s;--fy:-12px;--rs:-3deg;--re:5deg;"><svg width="42" height="42" viewBox="0 0 24 24" fill="none" stroke="#14B8A6" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg></div>
<div class="faq-page__content">
<div class="faq-hero container">
  <div class="faq-hero__badge"><span class="faq-bdot"></span>Help Center</div>
  <h1 class="faq-hero__title">Frequently Asked<br><span>Questions</span></h1>
  <p class="faq-hero__sub">Everything you need to know about secure online deals, payments, buyers, sellers, and MigratePay.</p>
  <div class="faq-sw">
    <svg class="faq-si" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
    <input type="search" id="faq-search" class="faq-search-input" placeholder="Search your question..." aria-label="Search FAQ questions" autocomplete="off">
    <button class="faq-sc" id="faq-sc" aria-label="Clear search">&#215;</button>
  </div>
</div>
<div class="faq-main">
  <div class="faq-hls">
    <div class="faq-hp"><span class="faq-hpi hp-b"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg></span>Secure Deals</div>
    <div class="faq-hp"><span class="faq-hpi hp-c"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg></span>Clear Deal Process</div>
    <div class="faq-hp"><span class="faq-hpi hp-t"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg></span>Transparent Payments</div>
    <div class="faq-hp"><span class="faq-hpi hp-i"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg></span>Dedicated Support</div>
  </div>
  <div class="faq-grid">
    <aside class="faq-sidebar" role="navigation" aria-label="FAQ categories">
      <div class="faq-sl">Browse by Topic</div>
      <div class="faq-cn" role="tablist">
        <button class="faq-cb active" data-cat="general" role="tab" aria-selected="true" aria-controls="fg-general"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>General<span class="faq-cc">4</span></button>
        <button class="faq-cb" data-cat="buyers" role="tab" aria-selected="false" aria-controls="fg-buyers"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>Buyers<span class="faq-cc">4</span></button>
        <button class="faq-cb" data-cat="sellers" role="tab" aria-selected="false" aria-controls="fg-sellers"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/></svg>Sellers<span class="faq-cc">4</span></button>
        <button class="faq-cb" data-cat="deals" role="tab" aria-selected="false" aria-controls="fg-deals"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>Deals<span class="faq-cc">4</span></button>
        <button class="faq-cb" data-cat="payments" role="tab" aria-selected="false" aria-controls="fg-payments"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>Payments<span class="faq-cc">3</span></button>
        <button class="faq-cb" data-cat="security" role="tab" aria-selected="false" aria-controls="fg-security"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>Security<span class="faq-cc">3</span></button>
        <button class="faq-cb" data-cat="account" role="tab" aria-selected="false" aria-controls="fg-account"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.6-7 8-7s8 3 8 7"/></svg>Account<span class="faq-cc">3</span></button>
      </div>
    </aside>
    <div id="faq-panels">
      <div class="faq-nr" id="faq-nr"><div class="faq-nr__icon">&#128269;</div><div class="faq-nr__title">No results found</div><div class="faq-nr__sub">Try a different keyword or browse a category.</div></div><div class="faq-group active" id="fg-general" role="tabpanel">
<div class="faq-sh faq-reveal"><div class="faq-si2" style="background:linear-gradient(135deg,#2563EB 0%,#6366F1 100%)"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg></div><div class="faq-st">General</div></div>
<div class="faq-card faq-reveal" data-cat="general"><button class="faq-question" aria-expanded="false"><span class="faq-qn">01</span><span class="faq-qt">What is MigratePay?</span><span class="faq-qi" aria-hidden="true"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg></span></button><div class="faq-aw"><div class="faq-ai"><div class="faq-answer">MigratePay is a secure escrow and deal-management mediation platform operating in Bangladesh. We act as a trusted third party that holds funds temporarily while a transaction takes place between a buyer and a seller.</div></div></div></div>
<div class="faq-card faq-reveal" data-cat="general" style="transition-delay:.05s"><button class="faq-question" aria-expanded="false"><span class="faq-qn">02</span><span class="faq-qt">How does MigratePay work?</span><span class="faq-qi" aria-hidden="true"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg></span></button><div class="faq-aw"><div class="faq-ai"><div class="faq-answer">The buyer and seller agree on deal terms, then the buyer deposits the agreed amount into MigratePay. We securely hold those funds while the seller completes the agreed work. Once the buyer reviews and approves the delivery, we release the funds to the seller. It is a simple five-step process designed to protect both parties.</div></div></div></div>
<div class="faq-card faq-reveal" data-cat="general" style="transition-delay:.10s"><button class="faq-question" aria-expanded="false"><span class="faq-qn">03</span><span class="faq-qt">Who can use MigratePay?</span><span class="faq-qi" aria-hidden="true"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg></span></button><div class="faq-aw"><div class="faq-ai"><div class="faq-answer">MigratePay is designed for anyone conducting digital transactions in Bangladesh — freelancers, agencies, buyers of digital goods or services, and businesses looking to structure their client payments more securely.</div></div></div></div>
<div class="faq-card faq-reveal" data-cat="general" style="transition-delay:.15s"><button class="faq-question" aria-expanded="false"><span class="faq-qn">04</span><span class="faq-qt">Can we communicate on WhatsApp or Messenger?</span><span class="faq-qi" aria-hidden="true"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg></span></button><div class="faq-aw"><div class="faq-ai"><div class="faq-answer">Yes, you can communicate on any external platform. <strong>However, you must share all critical proofs, work submissions, and revisions via the MigratePay deal chatbox.</strong> If a dispute or conflict arises, the MigratePay team can only mediate and resolve issues based on the evidence provided inside the official deal chatbox.</div></div></div></div>
</div>
<div class="faq-group" id="fg-buyers" role="tabpanel">
<div class="faq-sh faq-reveal"><div class="faq-si2" style="background:linear-gradient(135deg,#2563EB 0%,#06B6D4 100%)"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg></div><div class="faq-st">For Buyers</div></div>
<div class="faq-card faq-reveal" data-cat="buyers"><button class="faq-question" aria-expanded="false"><span class="faq-qn">01</span><span class="faq-qt">How does the escrow process protect me as a buyer?</span><span class="faq-qi" aria-hidden="true"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg></span></button><div class="faq-aw"><div class="faq-ai"><div class="faq-answer">If you are a buyer, your money is safe because the seller does not get paid until they deliver the agreed work. If you are a seller, your time is safe because you know the buyer has already deposited the funds before you start working.</div></div></div></div>
<div class="faq-card faq-reveal" data-cat="buyers" style="transition-delay:.05s"><button class="faq-question" aria-expanded="false"><span class="faq-qn">02</span><span class="faq-qt">How do I start a deal as a buyer?</span><span class="faq-qi" aria-hidden="true"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg></span></button><div class="faq-aw"><div class="faq-ai"><div class="faq-answer">Create a MigratePay account, then initiate a new deal from your dashboard. Enter the deal details — title, description, agreed amount, and delivery timeline — and invite the seller. Once the seller accepts, you can deposit the agreed funds to activate the deal.</div></div></div></div>
<div class="faq-card faq-reveal" data-cat="buyers" style="transition-delay:.10s"><button class="faq-question" aria-expanded="false"><span class="faq-qn">03</span><span class="faq-qt">What happens if the seller does not deliver?</span><span class="faq-qi" aria-hidden="true"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg></span></button><div class="faq-aw"><div class="faq-ai"><div class="faq-answer">If the seller fails to deliver within the agreed timeline, you can report an issue through the deal. Our administrative team will review the deal terms, chatbox communications, and any submitted evidence, then work to resolve the situation based on our platform rules.</div></div></div></div>
<div class="faq-card faq-reveal" data-cat="buyers" style="transition-delay:.15s"><button class="faq-question" aria-expanded="false"><span class="faq-qn">04</span><span class="faq-qt">What happens if the delivery does not match the agreement?</span><span class="faq-qi" aria-hidden="true"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg></span></button><div class="faq-aw"><div class="faq-ai"><div class="faq-answer">If you believe the delivery does not match what was agreed, document the issue with evidence in the deal chatbox and report an issue. Our admin team will review all evidence and make a fair resolution based on the originally agreed deal terms.</div></div></div></div>
</div>
<div class="faq-group" id="fg-sellers" role="tabpanel">
<div class="faq-sh faq-reveal"><div class="faq-si2" style="background:linear-gradient(135deg,#14B8A6 0%,#0891B2 100%)"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/></svg></div><div class="faq-st">For Sellers</div></div>
<div class="faq-card faq-reveal" data-cat="sellers"><button class="faq-question" aria-expanded="false"><span class="faq-qn">01</span><span class="faq-qt">Can I use MigratePay for freelance work?</span><span class="faq-qi" aria-hidden="true"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg></span></button><div class="faq-aw"><div class="faq-ai"><div class="faq-answer">Absolutely. MigratePay is highly suited for freelance projects, digital services, website development, and other custom orders where payment security is needed.</div></div></div></div>
<div class="faq-card faq-reveal" data-cat="sellers" style="transition-delay:.05s"><button class="faq-question" aria-expanded="false"><span class="faq-qn">02</span><span class="faq-qt">When is the payment released to me as a seller?</span><span class="faq-qi" aria-hidden="true"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg></span></button><div class="faq-aw"><div class="faq-ai"><div class="faq-answer">Funds are released to the seller only after the seller submits the work and the buyer reviews and approves the delivery, according to the agreed terms of the deal.</div></div></div></div>
<div class="faq-card faq-reveal" data-cat="sellers" style="transition-delay:.10s"><button class="faq-question" aria-expanded="false"><span class="faq-qn">03</span><span class="faq-qt">What happens if the buyer does not approve the delivery?</span><span class="faq-qi" aria-hidden="true"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg></span></button><div class="faq-aw"><div class="faq-ai"><div class="faq-answer">If the buyer raises a dispute, our administrative team will review the deal terms, all chatbox evidence, and your submitted delivery proof to make a fair resolution. This is why all important communications must go through the official MigratePay deal chatbox.</div></div></div></div>
<div class="faq-card faq-reveal" data-cat="sellers" style="transition-delay:.15s"><button class="faq-question" aria-expanded="false"><span class="faq-qn">04</span><span class="faq-qt">Can sellers use MigratePay for digital products and services?</span><span class="faq-qi" aria-hidden="true"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg></span></button><div class="faq-aw"><div class="faq-ai"><div class="faq-answer">Yes. MigratePay supports a wide range of digital transactions including freelance projects, website development, graphic design, app development, copywriting, SEO services, domain transactions, and B2B service deals. All transactions are subject to MigratePay terms and deal rules.</div></div></div></div>
</div>
<div class="faq-group" id="fg-deals" role="tabpanel">
<div class="faq-sh faq-reveal"><div class="faq-si2" style="background:linear-gradient(135deg,#8B5CF6 0%,#6366F1 100%)"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg></div><div class="faq-st">Deals</div></div>
<div class="faq-card faq-reveal" data-cat="deals"><button class="faq-question" aria-expanded="false"><span class="faq-qn">01</span><span class="faq-qt">How do I create a deal?</span><span class="faq-qi" aria-hidden="true"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg></span></button><div class="faq-aw"><div class="faq-ai"><div class="faq-answer">Log in to your MigratePay account and navigate to your dashboard. From there, create a new deal by providing the deal title, a clear description, the agreed amount, the deadline, and the other party information. Once created, the other party will receive an invitation to review and accept the deal terms.</div></div></div></div>
<div class="faq-card faq-reveal" data-cat="deals" style="transition-delay:.05s"><button class="faq-question" aria-expanded="false"><span class="faq-qn">02</span><span class="faq-qt">Can both buyer and seller communicate about a deal?</span><span class="faq-qi" aria-hidden="true"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg></span></button><div class="faq-aw"><div class="faq-ai"><div class="faq-answer">Yes. Each deal has a built-in chatbox where both parties can exchange messages, share files, submit work, and provide feedback. <strong>All critical communications must go through this official chatbox</strong>, as it is the only evidence our team can use if a dispute arises.</div></div></div></div>
<div class="faq-card faq-reveal" data-cat="deals" style="transition-delay:.10s"><button class="faq-question" aria-expanded="false"><span class="faq-qn">03</span><span class="faq-qt">Can I use MigratePay for website development?</span><span class="faq-qi" aria-hidden="true"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg></span></button><div class="faq-aw"><div class="faq-ai"><div class="faq-answer">Yes. Website development is one of the primary use cases for MigratePay. Both the client (buyer) and the developer (seller) can agree on deliverables, milestone timelines, and payment terms through a structured deal, reducing the risk of disputes or unfinished work.</div></div></div></div>
<div class="faq-card faq-reveal" data-cat="deals" style="transition-delay:.15s"><button class="faq-question" aria-expanded="false"><span class="faq-qn">04</span><span class="faq-qt">What happens if there is a dispute?</span><span class="faq-qi" aria-hidden="true"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg></span></button><div class="faq-aw"><div class="faq-ai"><div class="faq-answer">If the buyer and seller cannot agree, either party can report an issue. Our administrative team will review the deal terms, messages, and delivery evidence to make a fair resolution based on our established platform rules.</div></div></div></div>
</div>
<div class="faq-group" id="fg-payments" role="tabpanel">
<div class="faq-sh faq-reveal"><div class="faq-si2" style="background:linear-gradient(135deg,#0EA5E9 0%,#06B6D4 100%)"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg></div><div class="faq-st">Payments</div></div>
<div class="faq-card faq-reveal" data-cat="payments"><button class="faq-question" aria-expanded="false"><span class="faq-qn">01</span><span class="faq-qt">Does MigratePay hold the buyer payment?</span><span class="faq-qi" aria-hidden="true"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg></span></button><div class="faq-aw"><div class="faq-ai"><div class="faq-answer">Yes. When the buyer deposits funds into MigratePay for a deal, those funds are securely held on the platform until the deal is completed. The seller does not receive the funds until the buyer approves the delivery according to the agreed deal terms.</div></div></div></div>
<div class="faq-card faq-reveal" data-cat="payments" style="transition-delay:.05s"><button class="faq-question" aria-expanded="false"><span class="faq-qn">02</span><span class="faq-qt">When is the payment released?</span><span class="faq-qi" aria-hidden="true"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg></span></button><div class="faq-aw"><div class="faq-ai"><div class="faq-answer">Funds are released to the seller only after the seller submits the work and the buyer reviews and approves the delivery, according to the agreed terms of the deal.</div></div></div></div>
<div class="faq-card faq-reveal" data-cat="payments" style="transition-delay:.10s"><button class="faq-question" aria-expanded="false"><span class="faq-qn">03</span><span class="faq-qt">How can I check my transaction history?</span><span class="faq-qi" aria-hidden="true"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg></span></button><div class="faq-aw"><div class="faq-ai"><div class="faq-answer">All your deal activity, deposits, and transaction records are organized and visible inside your MigratePay dashboard. You can track the current status of each deal and review completed transactions from your account at any time.</div></div></div></div>
</div>
<div class="faq-group" id="fg-security" role="tabpanel">
<div class="faq-sh faq-reveal"><div class="faq-si2" style="background:linear-gradient(135deg,#EF4444 0%,#DC2626 100%)"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg></div><div class="faq-st">Security</div></div>
<div class="faq-card faq-reveal" data-cat="security"><button class="faq-question" aria-expanded="false"><span class="faq-qn">01</span><span class="faq-qt">How does MigratePay help prevent online scams?</span><span class="faq-qi" aria-hidden="true"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg></span></button><div class="faq-aw"><div class="faq-ai"><div class="faq-answer">MigratePay acts as a neutral third party between the buyer and seller. Because neither party has direct access to the funds until the deal terms are fulfilled, common scam tactics — such as a seller taking money and disappearing, or a buyer claiming non-delivery after receiving goods — are significantly harder to execute through our platform.</div></div></div></div>
<div class="faq-card faq-reveal" data-cat="security" style="transition-delay:.05s"><button class="faq-question" aria-expanded="false"><span class="faq-qn">02</span><span class="faq-qt">What happens if a buyer or seller stops responding?</span><span class="faq-qi" aria-hidden="true"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg></span></button><div class="faq-aw"><div class="faq-ai"><div class="faq-answer">If one party becomes unresponsive, the other party can report the issue through the deal. Our administrative team will review the situation based on the deal timeline, chatbox history, and any submitted evidence to make a fair resolution according to our platform rules.</div></div></div></div>
<div class="faq-card faq-reveal" data-cat="security" style="transition-delay:.10s"><button class="faq-question" aria-expanded="false"><span class="faq-qn">03</span><span class="faq-qt">How does MigratePay handle disputes?</span><span class="faq-qi" aria-hidden="true"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg></span></button><div class="faq-aw"><div class="faq-ai"><div class="faq-answer">MigratePay provides a formal reporting system inside every deal. If a deal faces issues, our admins step in to review the evidence and resolve the dispute based on our established platform rules. This structured process ensures neither party has an unfair advantage in a conflict.</div></div></div></div>
</div>
<div class="faq-group" id="fg-account" role="tabpanel">
<div class="faq-sh faq-reveal"><div class="faq-si2" style="background:linear-gradient(135deg,#F59E0B 0%,#D97706 100%)"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.6-7 8-7s8 3 8 7"/></svg></div><div class="faq-st">Account</div></div>
<div class="faq-card faq-reveal" data-cat="account"><button class="faq-question" aria-expanded="false"><span class="faq-qn">01</span><span class="faq-qt">How do I create a MigratePay account?</span><span class="faq-qi" aria-hidden="true"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg></span></button><div class="faq-aw"><div class="faq-ai"><div class="faq-answer">Visit MigratePay and click the Login/Register button. Fill in your name, email address, and a secure password to create your account. Once registered, you can immediately start creating or participating in deals from your dashboard.</div></div></div></div>
<div class="faq-card faq-reveal" data-cat="account" style="transition-delay:.05s"><button class="faq-question" aria-expanded="false"><span class="faq-qn">02</span><span class="faq-qt">Can I update my account information?</span><span class="faq-qi" aria-hidden="true"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg></span></button><div class="faq-aw"><div class="faq-ai"><div class="faq-answer">Yes. You can update your profile information from within your MigratePay dashboard. Navigate to your profile settings to make changes to your display name, contact details, and other account information.</div></div></div></div>
<div class="faq-card faq-reveal" data-cat="account" style="transition-delay:.10s"><button class="faq-question" aria-expanded="false"><span class="faq-qn">03</span><span class="faq-qt">How do I contact MigratePay support?</span><span class="faq-qi" aria-hidden="true"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg></span></button><div class="faq-aw"><div class="faq-ai"><div class="faq-answer">You can reach our support team by emailing <strong>support@migratepay.com</strong> or by calling <strong>+880 1712 345 678</strong>. You can also use the Contact page on our website to send a message directly to our team.</div></div></div></div>
</div>
      <div class="faq-cta faq-reveal">
        <span class="faq-cfi" aria-hidden="true">&#128737;</span>
        <span class="faq-cfi" aria-hidden="true">&#10067;</span>
        <span class="faq-cfi" aria-hidden="true">&#128172;</span>
        <span class="faq-cfi" aria-hidden="true">&#9989;</span>
        <div class="faq-cta__body">
          <div class="faq-cta__title">Still have questions?</div>
          <p class="faq-cta__sub">Our support team is here to help. Reach out any time and we will get back to you as soon as possible.</p>
          <div class="faq-cta__acts">
            <a href="{{ route('contact') }}" class="faq-bw"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>Contact Support</a>
            <a href="{{ route('auth') }}" class="faq-bg2"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>Start a Deal</a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
</div>
</div>
</div>

<script>
document.addEventListener('DOMContentLoaded',function(){
  // ACCORDION
  document.querySelectorAll('.faq-card').forEach(function(card){
    var btn=card.querySelector('.faq-question');
    if(!btn)return;
    btn.addEventListener('click',function(){
      var open=card.classList.contains('is-open');
      var grp=card.closest('.faq-group');
      if(grp)grp.querySelectorAll('.faq-card.is-open').forEach(function(c){
        if(c!==card){c.classList.remove('is-open');var b=c.querySelector('.faq-question');if(b)b.setAttribute('aria-expanded','false');}
      });
      card.classList.toggle('is-open',!open);
      btn.setAttribute('aria-expanded',String(!open));
    });
  });

  // CATEGORY TABS
  var catBtns=document.querySelectorAll('.faq-cb');
  var groups=document.querySelectorAll('.faq-group');
  function switchCat(cat){
    catBtns.forEach(function(b){var a=b.dataset.cat===cat;b.classList.toggle('active',a);b.setAttribute('aria-selected',String(a));});
    groups.forEach(function(g){g.classList.toggle('active',g.id==='fg-'+cat);});
    document.querySelectorAll('.faq-card.is-open').forEach(function(c){c.classList.remove('is-open');var b=c.querySelector('.faq-question');if(b)b.setAttribute('aria-expanded','false');});
    var si=document.getElementById('faq-search');
    if(si&&si.value){si.value='';document.getElementById('faq-sc').classList.remove('visible');runSearch('');}
  }
  catBtns.forEach(function(b){b.addEventListener('click',function(){switchCat(b.dataset.cat);});});

  // SEARCH
  var si=document.getElementById('faq-search');
  var sc=document.getElementById('faq-sc');
  var nr=document.getElementById('faq-nr');
  function runSearch(q){
    q=q.trim().toLowerCase();
    sc.classList.toggle('visible',q.length>0);
    if(!q){document.querySelectorAll('.faq-card').forEach(function(c){c.removeAttribute('data-hidden');});nr.classList.remove('visible');return;}
    groups.forEach(function(g){g.classList.add('active');});
    catBtns.forEach(function(b){b.classList.remove('active');b.setAttribute('aria-selected','false');});
    var found=0;
    document.querySelectorAll('.faq-card').forEach(function(c){
      var qt=c.querySelector('.faq-qt')?c.querySelector('.faq-qt').textContent.toLowerCase():'';
      var at=c.querySelector('.faq-answer')?c.querySelector('.faq-answer').textContent.toLowerCase():'';
      var m=qt.includes(q)||at.includes(q);
      c.setAttribute('data-hidden',m?'false':'true');
      if(m)found++;
    });
    nr.classList.toggle('visible',found===0);
  }
  var st;
  si.addEventListener('input',function(){clearTimeout(st);st=setTimeout(function(){runSearch(si.value);},180);});
  sc.addEventListener('click',function(){si.value='';si.focus();runSearch('');switchCat('general');});

  // SCROLL REVEAL
  var obs=new IntersectionObserver(function(entries){
    entries.forEach(function(e){if(e.isIntersecting){e.target.classList.add('is-vis');obs.unobserve(e.target);}});
  },{threshold:.1,rootMargin:'0px 0px -40px 0px'});
  document.querySelectorAll('.faq-reveal').forEach(function(el){obs.observe(el);});

  // STICKY HEADER
  var hdr=document.getElementById('site-header');
  if(hdr){window.addEventListener('scroll',function(){hdr.classList.toggle('scrolled',window.scrollY>50);},{passive:true});}
});
</script>
@endsection