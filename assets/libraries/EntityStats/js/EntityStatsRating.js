class EntityStatsRating
{
    /** @returns main class data constants */
    get s() {
        return {
            /** @var {string} trigger html element selector for current stat */
            trigger: '.js-entity__rating',
            vote_class: 'voted',
            /** @var {int} timeout (miliseconds) voting timeout */
            timeout: 1000
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
        this.status = (this.element && this.ctl.statEnabled('rating')) ? true : false;
        /** @type {?int} */
        this.count = this._getCount();
        /** @type {HTMLElement} element with current rating number */
        this.rating;
        /** @type {?HTMLElement} entity rating voting button */
        this.rup = this.rdwn = null;
        /** @type {string[]} entity rating classlist */
        this.classlist;
        this.id = 'entity-' + this.ctl.id + '-vote-result';
        /**
         * @type {int} entity last voting result
         * * -1 - entity was voted down
         * * 0  - entity wasn't voted yet
         * * 1  - entity was voted up
         */
        this.vote_result = parseInt(localStorage.getItem(this.id) ?? 0);
        /** @type {boolean} current voting statement */
        this.voting_in_process = false;
        /** @type {int} unix timestamp (miliseconds) for last voting */
        this.voting_started = 0;
    }

    _getCount()
    {
        if (this.status) {
            return parseInt(this.element.innerText);
        }
        return null;
    }

    async init()
    {
        this._loadGui();
        if (this.vote_result != 0) {
            await this._makeVote(0);;
        }
        if (!this.status) {
            return;
        }
        this._createVotingEvent();
    }

    _loadGui()
    {
        this._setClasslist();
        this._createButtons();
        this.rating = document.createElement('span');
        for (let cls of this.classlist.suffix('-number')) {
            this.rating.classList.add(cls);
        }
        this._setRatingNumber(this.count);
        this.element.innerHTML = '';
        this.element.append(this.rdwn);
        this.element.append(this.rating);
        this.element.append(this.rup);
    }

    _setRatingNumber(number)
    {
        if (number >= 0) {
            number = number > 0 ? "+" + number : number;
            if (this.rating.classList.contains('negative')) {
                this.rating.classList.remove('negative')
            }
        } else if (number < 0) {
            if (!this.rating.classList.contains('negative')) {
                this.rating.classList.add('negative')
            }
        }
        this.rating.innerText = number;
    }

    _setClasslist()
    {
        this.classlist = [];
        let class_to_skip = new RegExp('^js\-entity', 'i');
        for (let i = 0; i < this.element.classList.length; i++) {
            if (class_to_skip.test(this.element.classList[i])) {
                continue;
            }
            this.classlist.push(this.element.classList[i]);
        }
    }

    _createButtons()
    {
        let svg = '<svg><use href="/images/icons/thumbs.svg#svg-thumbs-icon"></use></svg>';
        this.rup = document.createElement('button');
        for (let cls of this.classlist.suffix(['-btn', '-btn_up'])) {
            this.rup.classList.add(cls);
        }
        this.rup.innerHTML = svg;
        this.rup.title = 'Vote up';
        this.rdwn = document.createElement('button');
        for (let cls of this.classlist.suffix(['-btn', '-btn_down'])) {
            this.rdwn.classList.add(cls);
        }
        this.rdwn.innerHTML = svg;
        this.rdwn.title = 'Vote down';
    }

    _setVotingClass(increment)
    {
        // console.log('setting voting class for increment: ' + increment);
        if (increment <= 0) {
            if (this.rup.classList.contains(this.s.vote_class)) {
                this.rup.classList.remove(this.s.vote_class);
            }
            if (increment < 0) {
                this.rdwn.classList.add(this.s.vote_class);
            }
        }
        if (increment >= 0) {
            if (this.rdwn.classList.contains(this.s.vote_class)) {
                this.rdwn.classList.remove(this.s.vote_class);
            }
            if (increment > 0) {
                this.rup.classList.add(this.s.vote_class);
            }
        }
    }

    _createVotingEvent()
    {
        this.rup.addEventListener('click', () => {
            if (this.voting_in_process) {
                return;
            }
            this._makeVote(1);
        });
        this.rdwn.addEventListener('click', () => {
            if (this.voting_in_process) {
                return;
            }
            this._makeVote(-1);
        });
    }

    /**
     * @param {int} increment for rating
     */
    async _makeVote(increment)
    {
        if (
            (increment < 0 && this.vote_result < 0)
            || (increment > 0 && this.vote_result > 0)
        ) {
            // console.log('already voted with same result');
            return;
        }
        let vote_result = this.vote_result;
        this._lockVoting();
        if (increment != 0) {
            this.vote_result += increment;
            // console.log('voting up');
        }
        let result = await this._updateRating(increment);
        if (!result) {
            // console.log('vote declined');
            this.vote_result = vote_result;
        }
        this._completeVoting();
        return;
    }
    
    /**
     * @param {int} increment for rating
     */
    async _updateRating(increment)
    {
        let parameters = {
                argument: 'rating',
                vote_result: this.vote_result
            },
            sessid = cookie.get('PHPSESSID'),
            sessid_vote_result = localStorage.getItem(this.id + '-session');
        if (increment != 0) {
            parameters.increment = increment;
        } else if (sessid == sessid_vote_result) {
            return false;
        }
        let result = await this.ctl.makeRequest(parameters);
        if (result) {
            // console.log('vote accepted');
            this.count += increment;
            this._setRatingNumber(this.count);
            localStorage.setItem(this.id + '-session', sessid);
            localStorage.setItem(this.id, this.vote_result);
        }
        return result;
    }

    _completeVoting(skip_timeout = false)
    {
        let t = Date.now() - this.voting_started;
        if (t < this.s.timeout && !skip_timeout) {
            setTimeout(() => {
                this._completeVoting(true);
            }, this.s.timeout - t);
            return;
        }
        this._setVotingClass(this.vote_result);
        this._clearVotingLockStatus();
    }

    _lockVoting()
    {
        this.voting_in_process = true;
        this.voting_started = Date.now();
        this.rup.setAttribute('disabled', null);
        this.rdwn.setAttribute('disabled', null);
    }

    _clearVotingLockStatus()
    {
        this.voting_started = 0;
        this.voting_in_process = false;
        this.rup.removeAttribute('disabled');
        this.rdwn.removeAttribute('disabled');
    }
}
