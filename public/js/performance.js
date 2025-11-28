// Lazy Loading Images Script
document.addEventListener('DOMContentLoaded', function() {
    // Lazy load images
    if ('loading' in HTMLImageElement.prototype) {
        // Native lazy loading supported
        const images = document.querySelectorAll('img[data-src]');
        images.forEach(img => {
            img.src = img.dataset.src;
            img.removeAttribute('data-src');
        });
    } else {
        // Fallback for browsers that don't support lazy loading
        const script = document.createElement('script');
        script.src = 'https://cdnjs.cloudflare.com/ajax/libs/lozad.js/1.16.0/lozad.min.js';
        script.onload = function() {
            const observer = lozad('.lazy', {
                loaded: function(el) {
                    el.classList.add('loaded');
                }
            });
            observer.observe();
        };
        document.body.appendChild(script);
    }

    // Preload critical resources
    const preloadLink = document.createElement('link');
    preloadLink.rel = 'preload';
    preloadLink.as = 'font';
    preloadLink.type = 'font/woff2';
    preloadLink.crossOrigin = 'anonymous';

    // Defer non-critical CSS
    const loadCSS = function(href) {
        const link = document.createElement('link');
        link.rel = 'stylesheet';
        link.href = href;
        link.media = 'print';
        link.onload = function() { this.media = 'all'; };
        document.head.appendChild(link);
    };

    // Remove unused CSS classes (example)
    const removeUnusedClasses = function() {
        // This would require a more sophisticated approach in production
        // Using PurgeCSS or similar tools during build process
    };
});

// Service Worker for caching (if supported)
if ('serviceWorker' in navigator) {
    window.addEventListener('load', function() {
        // Register service worker for offline support and caching
        // This improves performance on repeat visits
    });
}

// Resource hints
const addResourceHints = function() {
    const hints = [
        { rel: 'dns-prefetch', href: '//fonts.googleapis.com' },
        { rel: 'dns-prefetch', href: '//cdn.jsdelivr.net' },
        { rel: 'preconnect', href: 'https://fonts.googleapis.com' },
        { rel: 'preconnect', href: 'https://fonts.gstatic.com', crossorigin: true }
    ];

    hints.forEach(hint => {
        const link = document.createElement('link');
        link.rel = hint.rel;
        link.href = hint.href;
        if (hint.crossorigin) link.crossOrigin = 'anonymous';
        document.head.appendChild(link);
    });
};

// Initialize performance optimizations
addResourceHints();
