docready(() => {
    const cache_btn = document.getElementById('cache-clear-button');
    if (cache_btn) {
        cache_btn.addEventListener('click', () => {
            clearCache();
        });
    }
});

function clearCache()
{
    fetch('/ajax/cache?fn=clear')
        .then(response => response.json())
        .then(response => { console.info(response); })
        .catch(error => { console.warn(error); });
    return;
    
}