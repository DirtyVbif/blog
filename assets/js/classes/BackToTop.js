class BackToTop
{
    constructor()
    {
        // class default parameters
        this.s = {
            class: 'js-back-to-top',
            offsetY: 200
        };

        // object fields
        this.btt_btn;
        this.activated = false;
    }

    set()
    {
        this.btt_btn = document.createElement('button');
        this.btt_btn.setAttribute('id', 'back-to-top');
        this.btt_btn.setAttribute('aria-label', 'наверх');
        this.btt_btn.classList.add(this.s.class);
        document.body.appendChild(this.btt_btn);
        this._activate();
        document.addEventListener('scroll', event => {
            this._activate();
        });
        this.btt_btn.addEventListener('click', event => {
            this._scrollToTop();
        });
    }

    _activate()
    {
        if (window.scrollY < this.s.offsetY) {
            this._deactivate();
            return;
        }
        this.activated = true;
        if (this.btt_btn.classList.contains('hidden')) {
            this.btt_btn.classList.remove('hidden');
        }
    }

    _deactivate()
    {
        this.activated = false;
        if (!this.btt_btn.classList.contains('hidden')) {
            this.btt_btn.classList.add('hidden');
        }
        return;
    }

    _reactivate()
    {
        if (window.scrollY < this.s.offsetY && this.activated) {
            this._deactivate();
        } else if (window.scrollY >= this.s.offsetY && !this.activated) {
            this._activate();
        }
        return;
    }

    _scrollToTop()
    {
        window.scrollTo({
            top: 0,
            behavior: "smooth"
        });
    }
}