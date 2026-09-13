const motionQuery = window.matchMedia('(prefers-reduced-motion: reduce)');
const prefersReducedMotion = motionQuery.matches;

const hideLoader = () => {
    const loader = document.querySelector('[data-landing-loader]');

    if (!loader) {
        return;
    }

    window.setTimeout(() => {
        loader.classList.add('is-hidden');
        window.setTimeout(() => loader.remove(), 700);
    }, prefersReducedMotion ? 0 : 650);
};

const revealAll = () => {
    document.querySelectorAll('.landing-reveal').forEach((element) => {
        element.classList.add('is-visible');
    });
};

const initReveal = () => {
    const elements = document.querySelectorAll('.landing-reveal');

    if (prefersReducedMotion || !('IntersectionObserver' in window)) {
        revealAll();

        return;
    }

    const observer = new IntersectionObserver(
        (entries, obs) => {
            entries.forEach((entry) => {
                if (!entry.isIntersecting) {
                    return;
                }

                entry.target.classList.add('is-visible');
                obs.unobserve(entry.target);
            });
        },
        { threshold: 0.15, rootMargin: '0px 0px -40px 0px' },
    );

    elements.forEach((element) => observer.observe(element));
};

const animateCounter = (element) => {
    const target = Number(element.dataset.counterTarget ?? 0);
    const suffix = element.dataset.counterSuffix ?? '';
    const duration = 1400;
    const start = performance.now();

    const tick = (now) => {
        const progress = Math.min((now - start) / duration, 1);
        const eased = 1 - (1 - progress) ** 3;

        element.textContent = `${Math.round(target * eased)}${suffix}`;

        if (progress < 1) {
            requestAnimationFrame(tick);
        }
    };

    requestAnimationFrame(tick);
};

const initCounters = () => {
    const counters = document.querySelectorAll('[data-counter-target]');

    const settle = () => {
        counters.forEach((counter) => {
            counter.textContent = `${counter.dataset.counterTarget}${counter.dataset.counterSuffix ?? ''}`;
        });
    };

    if (prefersReducedMotion || !('IntersectionObserver' in window)) {
        settle();

        return;
    }

    const observer = new IntersectionObserver(
        (entries, obs) => {
            entries.forEach((entry) => {
                if (!entry.isIntersecting) {
                    return;
                }

                animateCounter(entry.target);
                obs.unobserve(entry.target);
            });
        },
        { threshold: 0.4 },
    );

    counters.forEach((counter) => observer.observe(counter));
};

const initHeader = () => {
    const header = document.querySelector('[data-landing-header]');

    if (!header) {
        return;
    }

    header.classList.add('landing-header');

    const toggleScrolled = () => {
        header.classList.toggle('is-scrolled', window.scrollY > 12);
    };

    toggleScrolled();
    window.addEventListener('scroll', toggleScrolled, { passive: true });
};

const initAnchorScroll = () => {
    document.querySelectorAll('a[href^="#"]').forEach((link) => {
        link.addEventListener('click', (event) => {
            const id = link.getAttribute('href')?.slice(1);
            const target = id ? document.getElementById(id) : null;

            if (!target) {
                return;
            }

            event.preventDefault();
            target.scrollIntoView({ behavior: prefersReducedMotion ? 'auto' : 'smooth', block: 'start' });
        });
    });
};

/**
 * Autoplay is best-effort: browsers may refuse it, and the poster frame is the
 * designed fallback, so rejected play() promises are intentionally swallowed.
 */
const playSafely = (video) => {
    video.muted = true;
    video.play().catch(() => {});
};

const initHeroVideo = () => {
    const video = document.querySelector('[data-landing-hero-video]');

    if (!video) {
        return;
    }

    if (prefersReducedMotion) {
        video.removeAttribute('autoplay');
        video.pause();

        return;
    }

    playSafely(video);

    document.addEventListener('visibilitychange', () => {
        if (document.hidden) {
            video.pause();

            return;
        }

        playSafely(video);
    });
};

/**
 * Bento tile videos ship with preload="none" and only stream while on screen,
 * so the grid costs nothing until a visitor actually scrolls to it.
 */
const initTileVideos = () => {
    const videos = document.querySelectorAll('[data-landing-tile-video]');

    if (!videos.length || prefersReducedMotion || !('IntersectionObserver' in window)) {
        return;
    }

    const observer = new IntersectionObserver(
        (entries) => {
            entries.forEach((entry) => {
                const video = entry.target;

                if (entry.isIntersecting) {
                    video.preload = 'auto';
                    playSafely(video);

                    return;
                }

                video.pause();
            });
        },
        { threshold: 0.25 },
    );

    videos.forEach((video) => observer.observe(video));
};

const init = () => {
    hideLoader();
    initReveal();
    initCounters();
    initHeader();
    initAnchorScroll();
    initHeroVideo();
    initTileVideos();
};

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
} else {
    init();
}

window.addEventListener('load', hideLoader);

/** Last-resort guard: never leave the page hidden behind the loader. */
window.setTimeout(() => {
    hideLoader();
    revealAll();
}, 4000);
