docready(() => {
    const cookie_agreement = new CookieAgreement,
        skill_slider = new SkillSlider;

    cookie_agreement.initialize();
    skill_slider.initialize();
});