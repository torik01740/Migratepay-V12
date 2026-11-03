@extends('layouts.frontend')
@section('title', 'Documents & Verification | Migratepay')
@section('content')
<div class="page-header">
    <div class="container animate-fadeIn">
        <h1>VERIFICATION PAPERS</h1>
        <p>Migratepay is a fully compliant digital commerce facilitator in Bangladesh. We provide our regulatory documentation to build transparency and trust.</p>
    </div>
</div>

<div class="section container">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-12">
        @foreach(['Trade License', 'BIN / VAT Certificate', 'TIN Certificate', 'DCC License', 'BRP Information', 'Insurance Papers'] as $doc)
            <div class="group relative bg-white border border-gray-100 rounded-3xl p-10 hover:shadow-2xl transition-all hover:-translate-y-2 text-center flex flex-col items-center">
                <div class="w-20 h-20 bg-emerald-50 text-emerald-500 rounded-full flex items-center justify-center font-black text-2xl mb-8 group-hover:bg-emerald-500 group-hover:text-white transition-all shadow-inner">📄</div>
                <h3 class="text-xl font-black text-navy-900 mb-2 uppercase tracking-tighter">{{ $doc }}</h3>
                <p class="text-xs font-black text-slate-400 uppercase tracking-widest mb-6">Verified: March 2026</p>
                <button class="w-full py-3 bg-white border border-emerald-500 text-emerald-500 font-black uppercase text-[10px] tracking-widest rounded-xl hover:bg-emerald-500 hover:text-white transition-all">VIEW DOCUMENT</button>
            </div>
        @endforeach
    </div>
</div>

<div class="bg-navy-900 py-24 text-white">
    <div class="container text-center">
        <h2 class="text-3xl font-black mb-6 uppercase tracking-tighter">REGULATORY COMPLIANCE</h2>
        <p class="text-gray-400 mb-10 max-w-2xl mx-auto italic font-medium">We strictly adhere to the guidelines set by the Bangladesh Bank and the ICT Division. Every transaction is legally documentable and can be provided as evidence in legal proceedings.</p>
        <div class="flex flex-wrap justify-center gap-12 opacity-50 grayscale hover:grayscale-0 transition-all">
            <span class="font-black text-xl tracking-[0.2em] italic">BD COMMERCE AUTHORITY</span>
            <span class="font-black text-xl tracking-[0.2em] italic">CENTRAL BANK BD</span>
            <span class="font-black text-xl tracking-[0.2em] italic">ICT DIVISION BD</span>
        </div>
    </div>
</div>
@endsection
