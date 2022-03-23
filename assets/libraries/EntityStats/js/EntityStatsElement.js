class EntityStatsElement
{
    /** @returns main class data constants */
    get s() {
        return {
            /** @type {string} */
            trigger_views: '.js-entity__views',
            /** @type {string} */
            trigger_rating: '.js-entity__rating',
            /** @var {object} data - entity data attribute names */
            data: {
                /** @var {string} id attribute name for entity id */
                id: 'data-entity-id',
                /** @var {string} type attribute name for entity type */
                type: 'data-entity-type',
                /** @var {string} disable attribue name for entity disabled stats */
                disable: 'data-entity-disable'
            }
        }
    }

    /**
     * @param {HTMLElement} element 
     */
    constructor(element)
    {
        /** @type {HTMLElement} entity html element */
        this.element = element;
        /** @type {int} entity id */
        this.id;
        /** @type {string} entity type name */
        this.type;
        /** @type {EntityStatsViews} */
        this.views;
        /** @type {EntityStatsRating} */
        this.rating;
        /** @type {string[]} array with name of disabled stats */
        this.disabled = [];
    }

    init()
    {
        this._checkDisabledStats();
        this.id = parseInt(this.element.getAttribute(this.s.data.id));
        this.type = this.element.getAttribute(this.s.data.type);
        this.controller = this.element.getAttribute(this.s.data.ctrl);
        this.views = new EntityStatsViews(this);
        this.rating = new EntityStatsRating(this);
    }

    _checkDisabledStats()
    {
        let disabled = this.element.getAttribute(this.s.data.disable);
        if (!disabled) {
            return;
        }
        this.disabled = disabled.split(new RegExp(',\s*'));
    }

    /**
     * @param {string} name specified stat name
     * @returns {boolean} `true` if specified stat is enabled or `false` otherwise
     */
    statEnabled(name)
    {
        return !this.disabled.includes(name);
    }

    onload()
    {
        this.views.update();
        this.rating.init();
    }

    /**
     * @param {object{{string}: {string}}} parameters 
     */
    async makeRequest(parameters)
    {
        let query_string = [];
        for (const [name, value] of Object.entries(parameters)) {
            if (name && value) {
                query_string.push(name + '=' + value);
            } else if (name) {
                query_string.push(name);
            } else if (value) {
                query_string.push(value);
            }
        }
        if (query_string.length < 1) {
            return;
        }
        query_string.push('entity_id=' + this.id);
        query_string.push('entity_type=' + this.type);
        let url = '/ajax/entity?' + query_string.join('&');
        await this._sendRequest(url);
        return;
    }

    async _sendRequest(url)
    {
        await fetch(url)
            .then(response => response.json())
            .then(response => {
                if (!response.status || response.status < 200 || response.status > 299) {
                    console.warn(response);
                }
            })
            .catch(error => { console.warn(error); });

        return;
    }
}