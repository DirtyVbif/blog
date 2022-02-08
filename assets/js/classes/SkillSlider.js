class SkillSlider
{
    constructor()
    {
        // class default settings
        this.s = {
            trigger: '.js-slider',
            item_prev: 'left',
            item_next: 'right',
            item_current: 'middle',
            item_hidden: 'hidden',
            control_prev: 'prev',
            control_next: 'next'
        };

        // class parameters
        this.items = [];
        this.slider;
        this.btn_next;
        this.btn_prev;
        this.i;
        this.i_next;
        this.i_prev;
        this.event_prev;
        this.event_next;
    }

    initialize()
    {
        this.slider = document.querySelector(this.s.trigger);
        if (!this.slider) {
            return;
        }
        this.btn_next = this.slider.querySelector(this.s.trigger + '__control.' + this.s.control_next);
        this.btn_prev = this.slider.querySelector(this.s.trigger + '__control.' + this.s.control_prev);
        this.items = this.slider.querySelectorAll(this.s.trigger + '__item');
        this._setCurrentIndex();
        this._createEvents();
    }

    _setCurrentIndex()
    {
        for (let i = 0; i < this.items.length; i++) {
            if (this.items[i].classList.contains(this.s.item_current)) {
                this.i = i;
                break;
            }
        }
        this._setCurrentSiblingsIndex();
    }

    _setCurrentSiblingsIndex()
    {
        this.i_prev = this._getPreviousIndex();
        this.i_next = this._getNextIndex();
    }

    _setIndexToNext()
    {
        this.i = this._getNextIndex();
        this._setCurrentSiblingsIndex();
    }

    _setIndexToPrevious()
    {
        this.i = this._getPreviousIndex();
        this._setCurrentSiblingsIndex();
    }

    _getPreviousIndex()
    {
        if (this.i <= 0) {
            return this.items.length - 1;
        }
        return this.i - 1;
    }

    _getNextIndex()
    {
        if (this.i >= this.items.length - 1) {
            return 0;
        }
        return this.i + 1;
    }

    _createEvents()
    {
        this.btn_next.addEventListener('click', event => {
            this._slideSkillToNext();
        });
        this.btn_prev.addEventListener('click', event => {
            this._slideSkillToPrevious();
        });
        this._setSkillSlideClickEvent();
    }

    _slideSkillToNext()
    {
        this._removeSkillSlideClickEvents();
        this._removeClassesFromCurrentItems();
        this._setIndexToNext();
        this._addClassesToCurrentItems();
        this._setSkillSlideClickEvent();
    }

    _slideSkillToPrevious()
    {
        this._removeSkillSlideClickEvents();
        this._removeClassesFromCurrentItems();
        this._setIndexToPrevious();
        this._addClassesToCurrentItems();
        this._setSkillSlideClickEvent();
    }

    _removeSkillSlideClickEvents()
    {
        if (this.event_prev) {
            this.items[this.i_prev].removeEventListener('click', this.event_prev);
        }
        if (this.event_next) {
            this.items[this.i_next].removeEventListener('click', this.event_next);
        }
    }

    _setSkillSlideClickEvent()
    {
        this.items[this.i_prev].addEventListener('click', this.event_prev = event => {
            this._slideSkillToPrevious();
        });
        this.items[this.i_next].addEventListener('click', this.event_next = event => {
            this._slideSkillToNext();
        });
    }

    _removeClassesFromCurrentItems()
    {
        this.items[this.i_prev].classList.remove(this.s.item_prev);
        this.items[this.i_prev].classList.add(this.s.item_hidden);
        this.items[this.i].classList.remove(this.s.item_current);
        this.items[this.i].classList.add(this.s.item_hidden);
        this.items[this.i_next].classList.remove(this.s.item_next);
        this.items[this.i_next].classList.add(this.s.item_hidden);
    }

    _addClassesToCurrentItems()
    {
        this.items[this.i_prev].classList.remove(this.s.item_hidden);
        this.items[this.i].classList.remove(this.s.item_hidden);
        this.items[this.i_next].classList.remove(this.s.item_hidden);
        if (!this.items[this.i].classList.contains(this.s.item_current)) {
            this.items[this.i].classList.add(this.s.item_current);
        }
        if (!this.items[this.i_prev].classList.contains(this.s.item_prev)) {
            this.items[this.i_prev].classList.add(this.s.item_prev);
        }
        if (!this.items[this.i_next].classList.contains(this.s.item_next)) {
            this.items[this.i_next].classList.add(this.s.item_next);
        }
    }
}