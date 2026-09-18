export class OpeningStatus {
    constructor(options) {
        this.options = {
            selector: '[data-company-opening-times]',
            root: document.documentElement,
            openClass: 'is-open',
            closedClass: 'is-closed',
            placeholder: '%time%',
            ...options,
        };

        this.timers = new WeakMap();

        this.#renderAll();

        new MutationObserver((records) => {
            if (records.some((record) => !record.target.matches(this.options.selector))) {
                this.#renderAll();
            }
        }).observe(this.options.root, { childList: true, subtree: true });
    }

    #renderAll() {
        for (const element of this.options.root.querySelectorAll(this.options.selector)) {
            this.#render(element);
        }
    }

    #render(element) {
        clearTimeout(this.timers.get(element));

        if (!element.isConnected) {
            return;
        }

        const config = JSON.parse(element.dataset.companyOpeningTimes);
        const now = this.#now(config.timezone);
        const interval = this.#currentInterval(config, now);

        element.textContent = interval
            ? config.labels.open.replace(this.options.placeholder, interval.closes)
            : config.labels.closed;

        element.classList.toggle(this.options.openClass, !!interval);
        element.classList.toggle(this.options.closedClass, !interval);

        element.hidden = false;

        this.timers.set(
            element,
            setTimeout(() => this.#render(element), this.#calculateNextChange(config, now)),
        );
    }

    #now(timeZone) {
        const date = new Date().toLocaleDateString('en-CA', { timeZone });
        const [hours, minutes, seconds] = new Date().toLocaleTimeString('en-GB', { timeZone }).split(':').map(Number);

        return { date, day: new Date(date).getUTCDay() || 7, minutes: hours * 60 + minutes, seconds };
    }

    #toMinutes(time) {
        const [hours, minutes] = time.split(':');

        return Number(hours) * 60 + Number(minutes);
    }

    #currentInterval(config, now) {
        if (config.closingTimes.some((period) => now.date >= period.from && now.date <= period.to)) {
            return null;
        }

        return (
            (config.days[now.day] || []).find(
                (interval) =>
                    now.minutes >= this.#toMinutes(interval.opens) && now.minutes < this.#toMinutes(interval.closes),
            ) || null
        );
    }

    #calculateNextChange(config, now) {
        const boundaries = (config.days[now.day] || []).flatMap((interval) => [
            this.#toMinutes(interval.opens),
            this.#toMinutes(interval.closes),
        ]);

        const next = Math.min(...boundaries.filter((minute) => minute > now.minutes), 1440);

        return (next * 60 - now.minutes * 60 - now.seconds) * 1000 + 1000;
    }
}
