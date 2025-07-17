export class DataCollector {
    constructor() {
        this.sessionId = null;
        this.lastUrl = null;
        this.pageLoadTime = Date.now();
    }

    init() {
        this.sessionId = this.getSessionId();
        this.lastUrl = window.location.href;
        this.pageLoadTime = Date.now();
    }

    collectPageData() {
        return {
            url: window.location.href,
            page_title: document.title || 'Untitled',
            referrer: document.referrer || null,
            user_agent: navigator.userAgent,
            session_id: this.sessionId,
            timestamp: new Date().toISOString(),
            page_load_time: this.pageLoadTime
        };
    }

    hasUrlChanged() {
        const currentUrl = window.location.href;
        const currentPath = this.getPathOnly(currentUrl);
        const lastPath = this.getPathOnly(this.lastUrl || '');
        const changed = currentPath !== lastPath;
        if (changed) {
            this.lastUrl = currentUrl;
            this.pageLoadTime = Date.now();
        }
        return changed;
    }

    getPathOnly(url) {
        try {
            const urlObj = new URL(url);
            return urlObj.origin + urlObj.pathname;
        } catch {
            return url;
        }
    }

    getSessionId() {
        let sessionId = sessionStorage.getItem('tracker_session');
        if (!sessionId) {
            sessionId = Math.random().toString(36).substring(2, 15);
            sessionStorage.setItem('tracker_session', sessionId);
        }
        return sessionId;
    }
}

export const collector = new DataCollector();