// User Activity Tracking System
(function() {
    'use strict';

    const Tracker = {
        endpoint: '/api/v1/track',
        sessionData: {
            startTime: Date.now(),
            lastActivity: Date.now(),
            pageLoadTime: null,
            scrollDepth: 0,
            interactions: []
        },

        init() {
            this.trackPageLoad();
            this.setupScrollTracking();
            this.setupClickTracking();
            this.setupFormTracking();
            this.setupErrorTracking();
            this.setupTimeTracking();
            this.setupSearchTracking();
        },

        // Track page load time
        trackPageLoad() {
            window.addEventListener('load', () => {
                const loadTime = performance.timing.loadEventEnd - performance.timing.navigationStart;
                this.sessionData.pageLoadTime = loadTime;
                
                this.send('page_performance', {
                    event_category: 'performance',
                    event_action: 'page_load',
                    event_data: {
                        load_time: loadTime,
                        dom_ready: performance.timing.domContentLoadedEventEnd - performance.timing.navigationStart
                    }
                });
            });
        },

        // Track scroll depth
        setupScrollTracking() {
            let maxScroll = 0;
            let scrollTimer = null;

            window.addEventListener('scroll', () => {
                clearTimeout(scrollTimer);
                
                const scrollPercentage = Math.round(
                    ((window.scrollY + window.innerHeight) / document.documentElement.scrollHeight) * 100
                );
                
                if (scrollPercentage > maxScroll) {
                    maxScroll = scrollPercentage;
                    this.sessionData.scrollDepth = scrollPercentage;
                }

                // Track significant scroll milestones
                scrollTimer = setTimeout(() => {
                    if (scrollPercentage === 25 || scrollPercentage === 50 || 
                        scrollPercentage === 75 || scrollPercentage === 100) {
                        this.send('scroll', {
                            event_category: 'engagement',
                            event_action: 'scroll_depth',
                            event_label: `${scrollPercentage}%`,
                            scroll_depth: scrollPercentage
                        });
                    }
                }, 500);
            });
        },

        // Track clicks on important elements
        setupClickTracking() {
            document.addEventListener('click', (e) => {
                const target = e.target.closest('a, button, .trackable');
                
                if (target) {
                    const elementType = target.tagName.toLowerCase();
                    const elementText = target.textContent?.trim().substring(0, 100) || '';
                    const elementClass = target.className || '';
                    const href = target.href || '';

                    // Track shop card clicks
                    if (target.closest('.shop-card, .similar-shop')) {
                        const shopName = target.querySelector('h5, .shop-name')?.textContent || '';
                        this.send('interaction', {
                            event_category: 'interaction',
                            event_action: 'clicked_shop',
                            event_label: shopName,
                            event_data: {
                                element_type: elementType,
                                element_text: elementText,
                                shop_name: shopName
                            }
                        });
                    }
                    
                    // Track navigation clicks
                    else if (target.closest('.navbar, .breadcrumb')) {
                        this.send('interaction', {
                            event_category: 'navigation',
                            event_action: 'clicked_nav',
                            event_label: elementText,
                            event_data: {
                                element_type: elementType,
                                element_text: elementText,
                                href: href
                            }
                        });
                    }
                    
                    // Track CTA buttons (call, directions, share)
                    else if (target.closest('.action-btn, .btn-primary')) {
                        const actionType = target.className.includes('call') ? 'phone_call' :
                                         target.className.includes('directions') ? 'get_directions' :
                                         target.className.includes('share') ? 'share' :
                                         target.className.includes('favorite') ? 'favorite' : 'button_click';
                        
                        this.send('conversion', {
                            event_category: 'conversion',
                            event_action: actionType,
                            event_label: elementText,
                            event_data: {
                                button_type: actionType,
                                button_text: elementText
                            }
                        });
                    }
                    
                    // Track tab switches
                    else if (target.closest('.tab-btn')) {
                        const tabName = target.dataset.tab || elementText;
                        this.send('interaction', {
                            event_category: 'interaction',
                            event_action: 'switched_tab',
                            event_label: tabName,
                            event_data: {
                                tab_name: tabName
                            }
                        });
                    }
                }
            });
        },

        // Track form interactions
        setupFormTracking() {
            // Track search form submissions
            const searchForms = document.querySelectorAll('form[action*="search"], .search-form');
            searchForms.forEach(form => {
                form.addEventListener('submit', (e) => {
                    const searchInput = form.querySelector('input[type="search"], input[name="query"], input[name="keywords"]');
                    if (searchInput) {
                        this.send('search', {
                            event_category: 'search',
                            event_action: 'performed_search',
                            event_label: searchInput.value,
                            event_data: {
                                query: searchInput.value,
                                form_id: form.id,
                                source: form.dataset.source || 'unknown'
                            }
                        });
                    }
                });
            });

            // Track filter changes
            const filters = document.querySelectorAll('.filter-select, #product-category-filter, #service-category-filter');
            filters.forEach(filter => {
                filter.addEventListener('change', (e) => {
                    this.send('interaction', {
                        event_category: 'filter',
                        event_action: 'changed_filter',
                        event_label: `${e.target.id}: ${e.target.value}`,
                        event_data: {
                            filter_id: e.target.id,
                            filter_value: e.target.value
                        }
                    });
                });
            });
        },

        // Track search within page
        setupSearchTracking() {
            const searchInputs = document.querySelectorAll('#product-search, #service-search');
            searchInputs.forEach(input => {
                let searchTimer = null;
                input.addEventListener('input', (e) => {
                    clearTimeout(searchTimer);
                    if (e.target.value.length >= 3) {
                        searchTimer = setTimeout(() => {
                            this.send('search', {
                                event_category: 'search',
                                event_action: 'inline_search',
                                event_label: e.target.value,
                                event_data: {
                                    query: e.target.value,
                                    search_type: e.target.id
                                }
                            });
                        }, 1000);
                    }
                });
            });
        },

        // Track JavaScript errors
        setupErrorTracking() {
            window.addEventListener('error', (e) => {
                this.send('error', {
                    event_category: 'error',
                    event_action: 'javascript_error',
                    event_label: e.message,
                    event_data: {
                        error_message: e.message,
                        error_file: e.filename,
                        error_line: e.lineno,
                        error_column: e.colno,
                        stack: e.error?.stack
                    }
                });
            });

            // Track unhandled promise rejections
            window.addEventListener('unhandledrejection', (e) => {
                this.send('error', {
                    event_category: 'error',
                    event_action: 'promise_rejection',
                    event_label: e.reason?.message || 'Promise rejected',
                    event_data: {
                        reason: e.reason?.toString()
                    }
                });
            });
        },

        // Track time on page
        setupTimeTracking() {
            // Send time on page before unload
            window.addEventListener('beforeunload', () => {
                const timeOnPage = Math.round((Date.now() - this.sessionData.startTime) / 1000);
                
                // Use sendBeacon for reliable tracking during page unload
                this.sendBeacon('page_exit', {
                    event_category: 'engagement',
                    event_action: 'time_on_page',
                    time_on_page: timeOnPage,
                    scroll_depth: this.sessionData.scrollDepth
                });
            });

            // Track idle time
            let idleTimer = null;
            const resetIdleTimer = () => {
                clearTimeout(idleTimer);
                this.sessionData.lastActivity = Date.now();
                
                idleTimer = setTimeout(() => {
                    this.send('engagement', {
                        event_category: 'engagement',
                        event_action: 'user_idle',
                        event_label: '5_minutes',
                        time_on_page: Math.round((Date.now() - this.sessionData.startTime) / 1000)
                    });
                }, 300000); // 5 minutes
            };

            ['mousedown', 'mousemove', 'keypress', 'scroll', 'touchstart'].forEach(event => {
                document.addEventListener(event, resetIdleTimer, true);
            });

            resetIdleTimer();
        },

        // Send tracking data via fetch
        send(eventType, data) {
            if (!this.endpoint) return;

            const payload = {
                event_type: eventType,
                ...data,
                timestamp: Date.now()
            };

            // Use fetch with keepalive for better reliability
            fetch(this.endpoint, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(payload),
                keepalive: true
            }).catch(err => {
                // Silently fail - don't disrupt user experience
                console.debug('Tracking failed:', err);
            });
        },

        // Send tracking data via navigator.sendBeacon (for page unload)
        sendBeacon(eventType, data) {
            if (!this.endpoint || !navigator.sendBeacon) {
                return this.send(eventType, data);
            }

            const payload = {
                event_type: eventType,
                ...data,
                timestamp: Date.now()
            };

            const blob = new Blob([JSON.stringify(payload)], { type: 'application/json' });
            navigator.sendBeacon(this.endpoint, blob);
        }
    };

    // Initialize tracking when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => Tracker.init());
    } else {
        Tracker.init();
    }

    // Expose tracker globally for manual tracking
    window.CityTracker = Tracker;
})();
