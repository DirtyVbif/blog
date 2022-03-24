const onload = [],
    docready = action => {
        onload.push(action);
    }

window.onload = () => {
    for (const action of onload) {
        if (action instanceof Function) {
            action();
        }
    }
};

function isCookieAccepted()
{
    let status = localStorage.getItem('cookie-agreement-accepted');
    if (!status) {
        return false;
    } else {
        return true;
    }
}

Math.randomInt = function (
    /** @param {int} int */
    int = 2
) {
    int = parseInt(int);
    if (int == 0 || int == 1) {
        int = 2;
    } else if (int == -1) {
        int = -2;
    }
    if (int < 0) {
        return Math.floor(-1 + Math.random() * int);
    }
    return Math.floor(1 + Math.random() * int);
};

Array.prototype.suffix = function (s)
{
    let result = [];
    for (let i = 0; i < this.length; i++) {
        if (Array.isArray(s)) {
            for (let x = 0; x < s.length; x++) {
                result.push(this[i] + s[x]);
            }
        } else {
            result.push(this[i] + s);
        }
    }
    return result;
};

Array.prototype.prefix = function (p)
{
    let result = [];
    for (let i = 0; i < this.length; i++) {
        if (Array.isArray(p)) {
            for (let x = 0; x < p.length; x++) {
                result.push(this[i] + p[x]);
            }
        } else {
            result.push(this[i] + p);
        }
    }
    return result;
};

function isCookieAccepted()
{
    let status = localStorage.getItem('cookie-agreement-accepted');
    if (!status) {
        return false;
    } else {
        return true;
    }
}


const cookie = new class
{
    get(name)
    {
        let matches = document.cookie.match(new RegExp(
        "(?:^|; )" + name.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, '\\$1') + "=([^;]*)"
        ));
        return matches ? decodeURIComponent(matches[1]) : null;
    }

    /**
     * 
     * @param {string} name 
     * @param {string} value 
     * @param {{
     *  domain?: string,
     *  path?: string,
     *  expires?: Date|string,
     *  'max-age'?: int,
     *  secure?: true,
     *  samesite?: true|string,
     *  httpOnly?: true
     * }} options 
     */
    set(name, value, options = {})
    {
        options = {
            path: '/',
            ...options
        };

        if (options.expires instanceof Date) {
            options.expires = options.expires.toUTCString();
        }

        let updatedCookie = encodeURIComponent(name) + "=" + encodeURIComponent(value);

        for (let optionKey in options) {
            updatedCookie += "; " + optionKey;
            let optionValue = options[optionKey];
            if (optionValue !== true) {
                updatedCookie += "=" + optionValue;
            }
        }

        document.cookie = updatedCookie;
    }

    del(name)
    {
        setCookie(name, "", {
        'max-age': -1
        })
    }
}