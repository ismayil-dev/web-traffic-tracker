import { tracker } from './tracker.js';
import { config } from './config.js';

tracker.init();

window.TrafficTracker = {
    track: () => tracker.track(),
    config: config.getConfig(),
};
