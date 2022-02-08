docready(() => {
    const cookie_agreement = new CookieAgreement,
        skill_slider = new SkillSlider,
        btt = new BackToTop;

    cookie_agreement.initialize();
    skill_slider.initialize();
    btt.set();

    const print_summary = document.getElementById('print-summary');
    if (print_summary) {
        print_summary.addEventListener('click', printSummary);
    }
});

function printSummary()
{
    const summary = document.getElementById('summary');
    if (!summary) {
        return;
    }
    const temp = window.open('', '', 'height=600, width=600');    
    temp.document.write('<html>');
    temp.document.write('<head><style type="text/css" media="all">.summary-card{min-height:150px;}.summary-card img{float:left;margin-right:15px;width:150px;height:auto;max-width:40%;object-fit:contain;border-radius:4px;}</style></head>');
    temp.document.write('<body>');
    temp.document.write(summary.innerHTML);
    temp.document.write('</body></html>');
    temp.document.getElementById('print-summary').remove();
    temp.document.close();
    temp.print();
}