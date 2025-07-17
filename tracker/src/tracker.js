import { config } from './config.js';
import { collector } from './collector.js';
import { sender } from './sender.js';

export class Tracker {
    constructor() {
        this.initialized = false;
        this.debouncedTrack = this.debounce(this.trackPageView.bind(this), 100);
    }

    init() {
        if (this.initialized) {
            return;
        }

        config.init();
        collector.init();

        this.initialized = true;
        this.trackInitialPageView();
        this.setupSpaTracking();
    }

    trackInitialPageView() {
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => {
                this.trackPageView();
            });
        } else {
            this.trackPageView();
        }
    }

    trackPageView() {
        try {
            const data = collector.collectPageData();
            sender.send(data);
        } catch (error) {
            console.error('Failed to track page view', error);
        }
    }

    setupSpaTracking() {
        window.addEventListener('popstate', () => {
            setTimeout(() => {
                if (collector.hasUrlChanged()) {
                    this.debouncedTrack();
                }
            }, 50);
        });

        this.interceptHistoryMethods();
    }

    interceptHistoryMethods() {
        const originalPushState = history.pushState;
        const originalReplaceState = history.replaceState;

        history.pushState = function(...args) {
            originalPushState.apply(history, args);
            setTimeout(() => {
                if (collector.hasUrlChanged()) {
                    this.debouncedTrack();
                }
            }, 50);
        }.bind(this);

        history.replaceState = function(...args) {
            originalReplaceState.apply(history, args);
            setTimeout(() => {
                if (collector.hasUrlChanged()) {
                    this.debouncedTrack();
                }
            }, 50);
        }.bind(this);
    }

    track() {
        this.trackPageView();
    }

    debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        }
    }
}

export const tracker = new Tracker();