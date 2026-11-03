@extends('layouts.frontend')
@section('title', 'Gallery | Migratepay')
@section('content')
<div class="page-header">
    <div class="container animate-fadeIn">
        <h1>MIGRATEPAY GALLERY</h1>
        <p>A visual journey through our offices, community events, and security architecture. We are committed to transparency and openness.</p>
    </div>
</div>

<div class="section container">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        @for($i = 1; $i <= 9; $i++)
            <div class="group relative aspect-square overflow-hidden rounded-3xl bg-gray-50 border border-gray-100 shadow-sm hover:shadow-2xl transition-all hover:-translate-y-2 cursor-pointer">
                <div class="absolute inset-0 bg-gradient-to-br from-navy-900 to-emerald-600 opacity-20 group-hover:opacity-40 transition-opacity"></div>
                <div class="absolute inset-0 flex flex-col items-center justify-center text-white opacity-0 group-hover:opacity-100 transition-opacity z-10">
                    <span class="text-[10px] font-black uppercase tracking-widest bg-emerald-500/20 px-4 py-1.5 rounded-full mb-2">Corporate Event</span>
                    <h3 class="text-xl font-black uppercase tracking-tighter">Gallery Item {{ $i }}</h3>
                    <p class="text-xs font-medium opacity-60">March 2026</p>
                </div>
                <div class="w-full h-full flex items-center justify-center font-black text-6xl text-gray-200 group-hover:scale-110 transition-transform">
                    {{ $i }}
                </div>
            </div>
        @endfor
    </div>
</div>

<div class="bg-gray-50 py-24">
    <div class="container text-center">
        <h2 class="text-3xl font-black text-navy-900 mb-6 uppercase tracking-tighter">OUR SOCIAL PRESENCE</h2>
        <p class="text-gray-500 mb-10 max-w-2xl mx-auto italic">Follow us on social media for more behind-the-scenes content and real-time updates from our team.</p>
        <div class="flex justify-center gap-6 opacity-40 hover:opacity-100 transition-opacity">
            <span class="text-2xl font-black tracking-[0.2em] italic cursor-pointer hover:text-emerald-500 transition-colors">INSTAGRAM</span>
            <span class="text-2xl font-black tracking-[0.2em] italic cursor-pointer hover:text-emerald-500 transition-colors">FACEBOOK</span>
            <span class="text-2xl font-black tracking-[0.2em] italic cursor-pointer hover:text-emerald-500 transition-colors">LINKEDIN</span>
        </div>
    </div>
</div>
@endsection
