 // JavaScript для интерактивности хедера
 
document.addEventListener('DOMContentLoaded', function() {
    document.body.classList.add('js-enabled');
     // Плавная прокрутка для навигационных ссылок
     const navLinks = document.querySelectorAll('.nav-link');
 
     navLinks.forEach(link => {
         link.addEventListener('click', function(e) {
             const href = this.getAttribute('href') || '';
             if (href.startsWith('#')) {
                 e.preventDefault();
                 const targetId = href.substring(1);
                 const targetElement = document.getElementById(targetId);
                 if (targetElement) {
                     targetElement.scrollIntoView({
                         behavior: 'smooth',
                         block: 'start'
                     });
                 }
             }
         });
     });
 
    // Лёгкий эффект на логотипе при наведении
     const logoSection = document.querySelector('.logo-section');
     if (logoSection) {
         logoSection.addEventListener('mouseenter', function() {
             this.style.transform = 'scale(1.04)';
             this.style.transition = 'transform 0.25s ease';
         });
         logoSection.addEventListener('mouseleave', function() {
             this.style.transform = 'scale(1)';
         });
     }

    // Открытие/закрытие мобильного меню
    const menuToggle = document.querySelector('.menu-toggle');
    const navigation = document.querySelector('.navigation');
    if (menuToggle && navigation) {
        const toggleMenu = () => {
            menuToggle.classList.toggle('active');
            navigation.classList.toggle('open');
        };

        menuToggle.addEventListener('click', toggleMenu);

        // Закрываем меню при клике по ссылке
        const closeMenu = () => {
            menuToggle.classList.remove('active');
            navigation.classList.remove('open');
        };

        navigation.querySelectorAll('.nav-link').forEach(link => {
            link.addEventListener('click', () => {
                // Если меню открыто, свернём его
                if (navigation.classList.contains('open')) {
                    closeMenu();
                }
            });
        });

        // Сбрасываем состояние меню при изменении ширины окна
        window.addEventListener('resize', () => {
            if (window.innerWidth > 768) {
                closeMenu();
            }
        });
    }

    // Анимация блока услуг при первом скролле
    const servicesSection = document.querySelector('.services');
    if (servicesSection && 'IntersectionObserver' in window) {
        const servicesObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    servicesSection.classList.add('services-revealed');
                    observer.disconnect();
                }
            });
        }, { threshold: 0.35 });

        servicesObserver.observe(servicesSection);
    } else if (servicesSection) {
        servicesSection.classList.add('services-revealed');
    }

    // Карусель в футере
    const carousel = document.querySelector('.gallery-carousel');
    if (carousel) {
        const slides = Array.from(carousel.querySelectorAll('.carousel-slide'));
        const prevBtn = carousel.querySelector('.prev');
        const nextBtn = carousel.querySelector('.next');
        const dots = Array.from(carousel.querySelectorAll('.carousel-dot'));
        let currentIndex = 0;

        const setActiveSlide = () => {
            slides.forEach((slide, slideIdx) => {
                slide.classList.toggle('is-active', slideIdx === currentIndex);
            });
        };

        const updateCarousel = (index) => {
            dots.forEach((dot, dotIndex) => {
                dot.classList.toggle('active', dotIndex === index);
            });
        };

        const triggerFlash = (slide) => {
            if (!slide) return;
            slide.classList.remove('flash');
            // перезапускаем анимацию
            void slide.offsetWidth;
            slide.classList.add('flash');
        };

        const goToSlide = (index, shouldFlash = true) => {
            if (index < 0) {
                index = slides.length - 1;
            } else if (index >= slides.length) {
                index = 0;
            }
            currentIndex = index;
            setActiveSlide();
            updateCarousel(currentIndex);
            if (shouldFlash) {
                triggerFlash(slides[currentIndex]);
            }
        };

        let autoPlayId = null;
        const startAutoPlay = () => {
            clearInterval(autoPlayId);
            autoPlayId = setInterval(() => goToSlide(currentIndex + 1), 5000);
        };

        window.addEventListener('resize', () => {
            updateCarousel(currentIndex);
        });

        setActiveSlide();
        updateCarousel(currentIndex);
        triggerFlash(slides[currentIndex]);
        startAutoPlay();
    }

    // FAQ анимация появления лесенкой
    const faqSection = document.querySelector('.faq');
    if (faqSection) {
        const faqItems = faqSection.querySelectorAll('.faq-item');
        if ('IntersectionObserver' in window) {
            const faqObserver = new IntersectionObserver((entries, observer) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        faqItems.forEach((item, index) => {
                            setTimeout(() => item.classList.add('visible'), index * 130);
                        });
                        observer.disconnect();
                    }
                });
            }, { threshold: 0.2 });

            faqObserver.observe(faqSection);
        } else {
            faqItems.forEach(item => item.classList.add('visible'));
        }
    }

    // Анимация появления формы связи снизу
    const contactWrapper = document.querySelector('.contact-wrapper');
    if (contactWrapper) {
        if ('IntersectionObserver' in window) {
            const contactObserver = new IntersectionObserver((entries, observer) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        contactWrapper.classList.add('visible');
                        observer.disconnect();
                    }
                });
            }, { threshold: 0.15 });

            contactObserver.observe(contactWrapper);
        } else {
            contactWrapper.classList.add('visible');
        }
    }

    // Cookie consent banner
    const cookieBanner = document.querySelector('.cookie-banner');
    const cookieAcceptBtn = cookieBanner ? cookieBanner.querySelector('.cookie-banner__btn--accept') : null;
    const COOKIE_CONSENT_KEY = 'krom_cookie_consent';
    const COOKIE_CONSENT_VALUE = 'accepted';

    const storageIsAvailable = (type) => {
        try {
            const storage = window[type];
            const testKey = '__krom_test__';
            storage.setItem(testKey, testKey);
            storage.removeItem(testKey);
            return true;
        } catch (err) {
            return false;
        }
    };

    const storageAvailable = storageIsAvailable('localStorage');

    const hasConsent = () => {
        if (storageAvailable) {
            return window.localStorage.getItem(COOKIE_CONSENT_KEY) === COOKIE_CONSENT_VALUE;
        }
        return document.cookie.split('; ').some(row => row.startsWith(`${COOKIE_CONSENT_KEY}=${COOKIE_CONSENT_VALUE}`));
    };

    const saveConsent = () => {
        if (storageAvailable) {
            window.localStorage.setItem(COOKIE_CONSENT_KEY, COOKIE_CONSENT_VALUE);
            return;
        }
        const expires = new Date();
        expires.setFullYear(expires.getFullYear() + 1);
        document.cookie = `${COOKIE_CONSENT_KEY}=${COOKIE_CONSENT_VALUE};expires=${expires.toUTCString()};path=/`;
    };

    const showBanner = () => cookieBanner && cookieBanner.classList.add('cookie-banner--visible');
    const hideBanner = () => cookieBanner && cookieBanner.classList.remove('cookie-banner--visible');

    if (cookieBanner && cookieAcceptBtn && !hasConsent()) {
        setTimeout(showBanner, 800);
        cookieAcceptBtn.addEventListener('click', () => {
            saveConsent();
            hideBanner();
        });
    }
 });

