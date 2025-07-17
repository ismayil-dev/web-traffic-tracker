import { config } from './config.js';

export class DataSender {
    async send(data) {
        const payload = this.preparePayload(data);

        return this.sendRequest(payload);
    }

    preparePayload(data) {
        return {
            url: data.url,
            page_title: data.page_title,
            referrer: data.referrer,
            user_agent: data.user_agent
        };
    }

    async sendRequest(payload) {
        try {
            const response = await this.makeRequest(payload);
            
            return response.ok;
        } catch (error) {
            return false;
        }
    }

    makeRequest(payload) {
        const { apiEndpoint, apiKey } = config.getConfig();
        
        if (typeof fetch !== 'undefined') {
            return fetch(apiEndpoint, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${apiKey}`
                },
                body: JSON.stringify(payload)
            });
        } else {
            return new Promise((resolve, reject) => {
                const xhr = new XMLHttpRequest();
                xhr.open('POST', apiEndpoint, true);
                xhr.setRequestHeader('Content-Type', 'application/json');
                xhr.setRequestHeader('Authorization', `Bearer ${apiKey}`);
                
                xhr.onreadystatechange = function() {
                    if (xhr.readyState === 4) {
                        if (xhr.status >= 200 && xhr.status < 300) {
                            resolve({
                                ok: true,
                                status: xhr.status,
                                statusText: xhr.statusText
                            });
                        } else {
                            reject(new Error(`HTTP ${xhr.status}: ${xhr.statusText}`));
                        }
                    }
                };
                
                xhr.onerror = () => reject(new Error('Network error'));
                xhr.send(JSON.stringify(payload));
            });
        }
    }
}

export const sender = new DataSender();