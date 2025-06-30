export const EVENT = {
    MODAL_OPEN: 'MODAL_OPEN',
    MODAL_CLOSE: 'MODAL_CLOSE',
    START_LOADING: 'START_LOADING',
    STOP_LOADING: 'STOP_LOADING',
    IS_MOBILE_CHANGED: 'IS_MOBILE_CHANGED',
    LOCALE_CHANGED: 'LOCALE_CHANGED',
};
class EventBus {
    constructor() {
        this.events = {};
    }

    on(eventName, fn) {
        this.events[eventName] = this.events[eventName] || [];
        this.events[eventName].push(fn);
    }

    off(eventName, fn) {
        if (this.events[eventName]) {
            for (let i = 0; i < this.events[eventName].length; i++) {
                if (this.events[eventName][i] === fn) {
                    this.events[eventName].splice(i, 1);
                    break;
                }
            }
        }
    }

    emit(eventName, data) {
        if (this.events[eventName]) {
            this.events[eventName].forEach(function (fn) {
                fn(data);
            });
        }
    }
}

export default new EventBus();
