(() => {
    const revealItems = document.querySelectorAll(".reveal");
    const form = document.getElementById("leadForm");
    const formStatus = document.getElementById("formStatus");

    // نمایش تدریجی بخش‌ها هنگام ورود به دید کاربر
    if ("IntersectionObserver" in window) {
        const observer = new IntersectionObserver((entries, currentObserver) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    entry.target.classList.add("is-visible");
                    currentObserver.unobserve(entry.target);
                }
            });
        }, {
            threshold: 0.12,
            rootMargin: "0px 0px -35px 0px"
        });

        revealItems.forEach((item) => observer.observe(item));
    } else {
        revealItems.forEach((item) => item.classList.add("is-visible"));
    }

    // فرم فعلاً نمایشی است؛ بعداً باید به پردازش امن PHP و CRM متصل شود.
    if (form) {
        form.addEventListener("submit", (event) => {
            event.preventDefault();

            if (!form.reportValidity()) {
                return;
            }

            if (formStatus) {
                formStatus.textContent =
                    "ظاهر فرم آماده است؛ اتصال ثبت درخواست در مرحله بک‌اند انجام می‌شود.";
            }
        });
    }
})();