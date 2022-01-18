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