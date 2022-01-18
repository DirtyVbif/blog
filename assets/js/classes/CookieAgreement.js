class CookieAgreement
{
    constructor()
    {
        // class default settings
        this.s = {
            trigger: '#cookie-agreement',
            hidden_class: 'hidden',
            get_status_path: '/ajax/cookie?is-accepted',
            show_times: 3
        };

        // class parameters
        this.modal;
        this.window_resize_event;
        this.submit;
    }

    initialize()
    {
        localStorage.setItem('cookie-agreement-show-times', 0);
        this.modal = document.querySelector(this.s.trigger);
        if (this.modal.length < 1) {
            return;
        }
        if (!isCookieAccepted()) {
            this._createEvent();
        } else {
            this._setAsAccepted();
        }
    }

    _createEvent()
    {
        let counter = localStorage.getItem('cookie-agreement-show-times') ?? 0;
        counter = parseInt(counter) == 'NaN' ? this.s.show_times : parseInt(counter);
        if (counter > this.s.show_times) {
            return this._setAsAccepted();
        } else {
            localStorage.setItem('cookie-agreement-show-times', ++counter);
        }
        this.modal.classList.remove(this.s.hidden_class);
        this._setBottomOffset();
        this.submit = this.modal.querySelector('input[type="submit"]');
        this.submit.addEventListener('click', event => {
            event.preventDefault();
            this._acceptCookies();
        });
    }

    _setBottomOffset()
    {
        this._setDocumentBottomOffset();
        window.addEventListener('resize', event => {
            this.window_resize_event = event;
            this._setDocumentBottomOffset();
        });
    }

    _setDocumentBottomOffset()
    {
        document.body.style.marginBottom = parseFloat(this.modal.offsetHeight) + 'px';
    }

    _removeBottomOffset()
    {
        this._setDocumentBottomOffset();
        if (this.window_resize_event) {
            window.removeEventListener('resize', this.window_resize_event);
        }
        document.body.style.marginBottom = null;
    }

    _acceptCookies()
    {
        this._removeBottomOffset();
    }

    _setAsAccepted()
    {
        this.modal.remove();
        // TODO: send cookie accept status on server;
    }
}