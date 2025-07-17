export class Config {
    constructor() {
        this.apiKey = null;
        this.initialized = false;
        this.apiEndpoint = process.env.API_ENDPOINT;
    }

    init() {
        const script = document.querySelector('script[data-api-key]');
        if (script) {
            this.apiKey = script.getAttribute('data-api-key');
            this.initialized = true;
        }
    }

    getConfig() {
        return {
            apiEndpoint: this.apiEndpoint,
            apiKey: this.apiKey,
        };
    }
}

export const config = new Config();