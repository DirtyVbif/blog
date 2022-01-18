class CookieAgreement
{
    constructor()
    {
        // class default settings
        this.s = {
            trigger: '#cookie-agreement',
            hidden_class: 'hidden',
            ajax_path: '/ajax/cookie',
            show_times: 3
        };

        // class parameters
        this.modal;
        this.window_resize_event;
        this.submit;
        this.form;
    }

    initialize()
    {
        this.modal = document.querySelector(this.s.trigger);
        this.form = document.querySelector(this.s.trigger + ' form');
        this.submit = document.querySelector(this.s.trigger + ' form input[type="submit"]');
        if (!this.modal || !this.form || !this.submit) {
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
        this.submit.addEventListener('click', event => {
            this._acceptCookies(event);
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

    _setDocumentBottomOffset(unset = false)
    {
        let margin = null;
        if (!unset) {
            margin = parseFloat(this.modal.offsetHeight) + 'px';
        }
        document.body.style.marginBottom = margin;
    }

    _removeBottomOffset()
    {
        if (this.window_resize_event) {
            window.removeEventListener('resize', this.window_resize_event);
        }
        this._setDocumentBottomOffset(true);
    }

    _acceptCookies(event)
    {
        event.preventDefault();
        this._removeBottomOffset();
        this._setAsAccepted();
    }

    _setAsAccepted()
    {
        localStorage.setItem('cookie-agreement-accepted', 1);
        this.modal.remove();
        // TODO: send cookie accept status on server;
        let formData = new FormData(this.form);
        fetch(this.s.ajax_path, {
            method: 'post',
            body: formData
        }).then(response => response.json())
            .then(response => console.log(response))
            .catch(error => console.warn(error));
    }
}