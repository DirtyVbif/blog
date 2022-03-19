docready(() => {
    const cookie_agreement = new CookieAgreement,
        btt = new BackToTop,
        mob_menu = new MobileMenu,
        summary = new SummaryControl;

    cookie_agreement.initialize();
    btt.set();
    mob_menu.initialize();
    summary.initialize();
});