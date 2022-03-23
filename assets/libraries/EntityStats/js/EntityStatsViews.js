class EntityStatsViews
{
    /** @returns main class data constants */
    get s() {
        return {
            /** @var {string} trigger html element selector for current stat */
            trigger: '.js-entity__views',
            /** @var {int} interval (seconds) entities views update interval */
            interval: 15
        }
    }

    /**
     * @param {EntityStatsElement} controller current entity stat parent controller object
     */
    constructor (controller)
    {
        /** @type {EntityStatsElement} current entity stat parent controller object */
        this.ctl = controller;
        /** @type {?HTMLElement} */
        this.element = this.ctl.element.querySelector(this.s.trigger);
        /** @type {boolean} */
        this.status = (this.element && this.ctl.statEnabled('views')) ? true : false;
        /** @type {?int} */
        this.count = this._getCount();
        /** @type {int} entity last view update unix timestamp */
        this.timestamp = parseInt(localStorage.getItem('entity-' + this.ctl.id + '-last-view-uptime') ?? 0);
    }

    _getCount()
    {
        if (this.status) {
            return parseInt(this.element.innerText);
        }
        return null;
    }

    update()
    {
        if (!this.status || !this._updateAvailable()) {
            return;
        }
        let parameters = {
            fn: 'update',
            key: 'views'
        };
        this._updateViewTime();
        this.ctl.makeRequest(parameters);
    }

    _updateAvailable()
    {
        if (!this.timestamp) {
            return true;
        }
        return this.s.interval < Math.floor(Date.now() / 1000) - this.timestamp;
    }

    _updateViewTime()
    {
        let timestamp = Math.floor(Date.now() / 1000);
        localStorage.setItem('entity-' + this.ctl.id + '-last-view-uptime', timestamp);
    }
}