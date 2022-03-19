const onload = [],
    docready = action => {
        onload.push(action);
    }

window.onload = () => {
    for (const action of onload) {
        action();
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

Math.randomInt = function(
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
