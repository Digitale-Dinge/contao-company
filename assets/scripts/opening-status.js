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
        const openingTimesRanges = this.#openingTimesRangesForToday(config, now);
        const currentOpeningTimesRange = this.#currentOpeningTimesRange(openingTimesRanges, now);

        element.textContent = this.#openingStatusText(config.labels, openingTimesRanges, currentOpeningTimesRange, now);
        element.classList.toggle(this.options.openClass, !!currentOpeningTimesRange);
        element.classList.toggle(this.options.closedClass, !currentOpeningTimesRange);
        element.hidden = false;

        this.#scheduleNextRender(element, now, this.#nextStatusChange(openingTimesRanges, now));
    }

    #openingStatusText(labels, openingTimesRanges, currentOpeningTimesRange, now) {
        if (currentOpeningTimesRange) {
            return this.#formatLabel(labels.open, currentOpeningTimesRange.closes);
        }

        const nextOpeningTimesRange = labels.opens && this.#nextOpeningTimesRange(openingTimesRanges, now);

        return nextOpeningTimesRange ? this.#formatLabel(labels.opens, nextOpeningTimesRange.opens) : labels.closed;
    }

    #formatLabel(label, time) {
        return label.replace(this.options.placeholder, time);
    }

    #scheduleNextRender(element, now, nextStatusChange) {
        const delay = (nextStatusChange * 60 - now.minutes * 60 - now.seconds) * 1000 + 1000;

        this.timers.set(
            element,
            setTimeout(() => this.#render(element), delay),
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

    #openingTimesRangesForToday(config, now) {
        return config.specialOpeningHours?.[now.date] ?? config.days[now.day] ?? [];
    }

    #currentOpeningTimesRange(openingTimesRanges, now) {
        return openingTimesRanges.find(
            (range) => now.minutes >= this.#toMinutes(range.opens) && now.minutes < this.#toMinutes(range.closes),
        );
    }

    #nextOpeningTimesRange(openingTimesRanges, now) {
        return openingTimesRanges.find((range) => this.#toMinutes(range.opens) > now.minutes);
    }

    #nextStatusChange(openingTimesRanges, now) {
        const boundaries = openingTimesRanges.flatMap((range) => [
            this.#toMinutes(range.opens),
            this.#toMinutes(range.closes),
        ]);

        return Math.min(...boundaries.filter((minute) => minute > now.minutes), 1440);
    }
}
