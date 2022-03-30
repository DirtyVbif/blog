class PasswordFieldSwitcher
{
    constructor()
    {
        this.input;
        this.icon;
    }

    init()
    {
        this.input = document.querySelector('input[type="password"]');
        if (!this.input) {
            return;
        }
        this.icon = this.input.nextElementSibling;
        this.icon.addEventListener('click', () => {
            this._switch();
        });
    }

    _switch()
    {
        if (this.icon.classList.contains('active')) {
            this._deactive();
        } else {
            this._active();
        }
    }

    _active()
    {
        this.icon.classList.add('active');
        this.input.setAttribute('type', 'text');
    }

    _deactive()
    {
        this.icon.classList.remove('active');
        this.input.setAttribute('type', 'password');
    }
}

const pw_switch = new PasswordFieldSwitcher;

docready(() => {
    pw_switch.init();
});