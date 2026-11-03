document.addEventListener('DOMContentLoaded', function() {
    // 1. Add class to body for continuous background
    document.body.classList.add('legal-page-body');

    // 2. Inject premium animated background — matches FAQ page design
    const bgContainer = document.createElement('div');
    bgContainer.className = 'legal-bg-decorations';
    bgContainer.setAttribute('aria-hidden', 'true');
    bgContainer.innerHTML = `
        <!-- Animated glow blobs -->
        <div class="legal-blob blob-blue"></div>
        <div class="legal-blob blob-cyan"></div>
        <div class="legal-blob blob-ash"></div>
        <div class="legal-blob blob-indigo"></div>

        <!-- Floating decorative SVG icons -->
        <svg class="legal-icon icon-shield" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
        </svg>

        <svg class="legal-icon icon-lock" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
            <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
        </svg>

        <svg class="legal-icon icon-document" width="42" height="42" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
            <polyline points="14 2 14 8 20 8"></polyline>
            <line x1="16" y1="13" x2="8" y2="13"></line>
            <line x1="16" y1="17" x2="8" y2="17"></line>
        </svg>

        <svg class="legal-icon icon-check" width="46" height="46" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="20 6 9 17 4 12"></polyline>
        </svg>

        <svg class="legal-icon icon-circle" width="38" height="38" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="12" cy="12" r="10"></circle>
            <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path>
            <line x1="12" y1="17" x2="12.01" y2="17"></line>
        </svg>

        <svg class="legal-icon icon-extra" width="42" height="42" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
        </svg>
    `;

    // Insert at the very start of body (fixed position, sits behind all content)
    document.body.prepend(bgContainer);

    // 3. TOC active link tracking on scroll
    const tocLinks = document.querySelectorAll('.toc-link');
    const sections = document.querySelectorAll('.terms-section[id]');

    if (sections.length && tocLinks.length) {
        const observer = new IntersectionObserver(function(entries) {
            entries.forEach(function(entry) {
                if (entry.isIntersecting) {
                    tocLinks.forEach(function(l) { l.classList.remove('active'); });
                    const active = document.querySelector('.toc-link[href="#' + entry.target.id + '"]');
                    if (active) active.classList.add('active');
                }
            });
        }, { rootMargin: '-20% 0px -70% 0px' });

        sections.forEach(function(s) { observer.observe(s); });
    }

    // 4. Mobile TOC toggle
    const toggle = document.getElementById('mobileTocToggle');
    const sidebar = document.getElementById('termsSidebar');
    const chevron = document.getElementById('tocChevron');

    if (toggle && sidebar) {
        toggle.addEventListener('click', function() {
            sidebar.classList.toggle('expanded');
            if (chevron) chevron.style.transform = sidebar.classList.contains('expanded') ? 'rotate(180deg)' : '';
        });
    }

    // 5. Back-to-top button
    const btt = document.querySelector('.back-to-top');
    if (btt) {
        window.addEventListener('scroll', function() {
            btt.classList.toggle('visible', window.scrollY > 400);
        }, { passive: true });
        btt.addEventListener('click', function() {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    }

    // 6. Sticky header scroll class
    const hdr = document.getElementById('site-header');
    if (hdr) {
        window.addEventListener('scroll', function() {
            hdr.classList.toggle('scrolled', window.scrollY > 50);
        }, { passive: true });
    }
});

