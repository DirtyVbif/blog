class ItemProjectorList
{
    get s() {
        return {
            /** @type {int} time for random switching projecting items (ms) */
            interval: 12500,
            /** @type {int} time for projecting animation (ms) */
            animtime: 400
        };
    }

    /**
     * @param {HTMLElement} list 
     * @param {HTMLElement[]} items 
     * @param {HTMLElement} placeholder 
     */
    constructor(list, items, placeholder)
    {
        /** @type {HTMLElement} list element with items for projecting */
        this.list = list;
        /** @type {HTMLElement[]} list of projected elements */
        this.items = items;
        /** @type {HTMLElement} container which contains projecting item */
        this.placeholder = placeholder;
        /** @type {int} currently projected item index */
        this.projected_item = -1;
        /** @type {int[]} stack of last projected item indexes. If All items was projected then stack would be refreshed */
        this.last_projected_items = [];
        this.timer = null;
        /** @type {Function} */
        this.callback = null;
    }

    /**
     * @returns {int} new random item to project index
     */
    newRandomItemIndex()
    {
        let index = Math.randomInt(this.items.length) - 1;
        if (this.last_projected_items.length < this.items.length) {
            while (this.last_projected_items.includes(index)) {
                index = Math.randomInt(this.items.length) - 1;
            }
        } else {
            this.last_projected_items = [];
            while (index == this.projected_item) {
                index = Math.randomInt(this.items.length) - 1;
            }
        }
        return index;
    }

    /**
     * @param {HTMLElement} clone_item 
     * @param {int} index 
     */
    projectNewItem(clone_item, index)
    {
        this.projected_item = index;
        this._animateProjecting(clone_item);
        if (this.last_projected_items.length >= this.items.length) {
            this.last_projected_items = [];
        }
        if (!this.last_projected_items.includes(index)) {
            this.last_projected_items.push(index);
        }
        this._setNewRandomizerInterval();
    }

    /**
     * @param {Function} callback 
     */
    setRandomProjectionTimer(callback)
    {
        this.callback = callback;
        this._setNewRandomizerInterval();
    }

    _setNewRandomizerInterval()
    {
        if (this.timer != null) {
            clearInterval(this.timer);
        }
        if (this.callback != null) {
            this.timer = setInterval(this.callback, this.s.interval);
        }
    }

    /**
     * @param {HTMLElement} item 
     */
    _animateProjecting(item)
    {
            /** @type {float} */
        let height_start = this._getPlaceholderCurrentHeight(),
            /** @type {float} */
            height_end = this._getPlaceholderNewHeight(item);
        
        this._animatePlaceholderChildrenFadeout();
        this._animatePlaceholderSizeChanging(height_start, height_end);
        setTimeout(() => {
            this.placeholder.innerHTML = '';
            item.style.opacity = 0;
            this.placeholder.appendChild(item);
            this._animatePlaceholderChildFadein(item);
        }, this.s.animtime / 2);
    }

    _animatePlaceholderChildrenFadeout()
    {
        for (const child of this.placeholder.children) {
            child.animate([
                { opacity: 1 },
                { opacity: 0 }
            ], {
                duration: this.s.animtime / 2,
            }).onfinish = () => {
                child.remove();
            };
        }
    }

    /**
     * @param {HTMLElement} item 
     */
    _animatePlaceholderChildFadein(item)
    {
        item.animate([
            { opacity: 0 },
            { opacity: 1 }
        ], {
            duration: this.s.animtime / 2,
        }).onfinish = () => {
            item.removeAttribute('style');
        };
    }

    /**
     * @param {float} height_start 
     * @param {float} height_end 
     */
    _animatePlaceholderSizeChanging(height_start, height_end)
    {
        this.placeholder.animate([
            { height: height_start + 'px' },
            { height: height_end + 'px' }
        ], {
            duration: this.s.animtime,
        }).onfinish = () => {
            this.placeholder.removeAttribute('style');
        };
    }

    /**
     * @returns {float}
     */
    _getPlaceholderCurrentHeight()
    {
        return parseFloat(this.placeholder.offsetHeight);
    }

    /**
     * @param {HTMLElement} item 
     * 
     * @returns {float}
     */
    _getPlaceholderNewHeight(item)
    {
        let style = getComputedStyle(this.placeholder),
            bord_w_top = parseFloat(style.borderTopWidth) || 0,
            bord_w_bot = parseFloat(style.borderBottomWidth) || 0,
            pad_h_top = parseFloat(style.paddingTop) || 0,
            pad_h_bot = parseFloat(style.paddingBottom) || 0,
            add_height = bord_w_top + bord_w_bot + pad_h_top + pad_h_bot,
            /** @type {HTMLElement} */
            clone = item.cloneNode(true);

        clone.style.visibility = 'hidden';
        clone.style.position = 'absolute';
        clone.style.zIndex = -999;
        clone.style.opacity = 0;

        this.placeholder.appendChild(clone);
        let c_style = getComputedStyle(clone),
            marg_top = parseFloat(c_style.marginTop),
            marg_bot = parseFloat(c_style.marginBottom),
            height = parseFloat(clone.offsetHeight) + marg_top + marg_bot + add_height;
        clone.remove();
        return height;
    }
}