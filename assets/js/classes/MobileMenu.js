class MobileMenu
{
    constructor()
    {
        // default class parameters
        this.s = {
            menu_id: 'menu-main',
            btn_id: 'mobile-menu-button',
            mobile_width: 600
        };

        // object fields
        this.menu;
        this.mob_btn;
        this.mobile_status = false;
        this.expanded = false;
        this.mobile_menu_height;
    }

    initialize()
    {
        this.menu = document.getElementById(this.s.menu_id);
        this.mob_btn = document.getElementById(this.s.btn_id);
        if (!this.menu || !this.mob_btn) {
            return;
        }
        this._updateStatus();
        this._updateMobileMenuHeight();
        this._setWidnowResizeEvent();
        this._setClickEvent();
    }

    _setWidnowResizeEvent()
    {
        window.addEventListener('resize', () => {
            this._updateStatus();
            this._updateMobileMenuHeight();
        });
    }

    _setClickEvent()
    {
        this.mob_btn.addEventListener('click', event => {
            event.stopPropagation();
            if (this.mobile_status) {
                this._toggleMenu();
            }
        });

        window.addEventListener('click', event => {
            if (this.mobile_status && this.expanded) {
                this.expanded = false;
                this._animateSlideUp();
            }
        })
    }

    _updateStatus()
    {
        if (window.innerWidth > this.s.mobile_width && this.mobile_status) {
            this.mobile_status = false;
            this._collapseMenu();
        } else if (window.innerWidth <= this.s.mobile_width && !this.mobile_status) {
            this.mobile_status = true;
            this._collapseMenu();
        }
        return;
    }

    _collapseMenu()
    {
        this.menu.removeAttribute('style');
        if (this.menu.classList.contains('expanded')) {
            this.menu.classList.remove('expanded');
        }
        this.expanded = false;
    }

    _updateMobileMenuHeight()
    {
        if (!this.mobile_status) {
            return;
        }
        const temp_menu = this.menu.cloneNode(true);
        temp_menu.style.zIndex = -999;
        temp_menu.style.visibility = 'hidden';
        temp_menu.style.position = 'fixed';
        temp_menu.style.left = '100wv'
        temp_menu.style.height = 'auto';
        document.body.appendChild(temp_menu);
        this.mobile_menu_height = parseFloat(temp_menu.offsetHeight);
        temp_menu.remove();
        return;
    }

    _toggleMenu()
    {
        if (this.expanded) {
            this.expanded = false;
            this._animateSlideUp();
        } else {
            this.expanded = true;
            this._animateSlideDown();
        }
    }

    _animateSlideDown()
    {
        this.menu.animate([
            {height: 0},
            {height: this.mobile_menu_height + 'px'}
        ], 150).onfinish = () => {
            if (!this.menu.classList.contains('expanded')) {
                this.menu.classList.add('expanded');
            }
        };
    }

    _animateSlideUp()
    {
        this.menu.animate([
            {height: this.mobile_menu_height + 'px'},
            {height: 0}
        ], 150).onfinish = () => {
            if (this.menu.classList.contains('expanded')) {
                this.menu.classList.remove('expanded');
            }
        };
    }
}