/**
 * Portofolio interaktif — semua animasi & interaksi ada di sini.
 * Dijalankan setelah DOM siap, dan aman kalau elemennya tidak ada di halaman.
 */

const prefersReduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
const animationsOn = document.documentElement.dataset.anim !== 'off';

const $ = (sel, root = document) => root.querySelector(sel);
const $$ = (sel, root = document) => Array.from(root.querySelectorAll(sel));

/* ------------------------------------------------------------------
 * 1. Muncul saat discroll
 * ------------------------------------------------------------------ */
function initReveal() {
    const items = $$('[data-reveal]');
    if (!items.length) return;

    if (prefersReduced || !animationsOn) {
        items.forEach((el) => el.classList.add('is-visible'));
        return;
    }

    const observer = new IntersectionObserver(
        (entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-visible');
                    observer.unobserve(entry.target);
                }
            });
        },
        { threshold: 0.12, rootMargin: '0px 0px -8% 0px' }
    );

    items.forEach((el) => observer.observe(el));
}

/* ------------------------------------------------------------------
 * 2. Bilah progres scroll + tombol kembali ke atas
 * ------------------------------------------------------------------ */
function initScrollProgress() {
    const bar = $('#scroll-progress');
    const topBtn = $('#back-to-top');
    const nav = $('#main-nav');

    const update = () => {
        const max = document.documentElement.scrollHeight - window.innerHeight;
        const pct = max > 0 ? (window.scrollY / max) * 100 : 0;
        if (bar) bar.style.width = pct + '%';
        if (topBtn) topBtn.classList.toggle('is-visible', window.scrollY > 420);
        if (nav) nav.classList.toggle('is-scrolled', window.scrollY > 24);
    };

    window.addEventListener('scroll', update, { passive: true });
    window.addEventListener('resize', update);
    update();

    topBtn?.addEventListener('click', () => {
        window.scrollTo({ top: 0, behavior: prefersReduced ? 'auto' : 'smooth' });
    });
}

/* ------------------------------------------------------------------
 * 3. Menu mobile
 * ------------------------------------------------------------------ */
function initMobileMenu() {
    const btn = $('#menu-toggle');
    const panel = $('#mobile-menu');
    if (!btn || !panel) return;

    btn.addEventListener('click', () => {
        const open = panel.classList.toggle('is-open');
        btn.setAttribute('aria-expanded', String(open));
        panel.style.maxHeight = open ? panel.scrollHeight + 'px' : '0px';
    });

    $$('a', panel).forEach((link) =>
        link.addEventListener('click', () => {
            panel.classList.remove('is-open');
            panel.style.maxHeight = '0px';
            btn.setAttribute('aria-expanded', 'false');
        })
    );
}

/* ------------------------------------------------------------------
 * 4. Tema terang / gelap
 * ------------------------------------------------------------------ */
function initTheme() {
    const btn = $('#theme-toggle');
    if (!btn) return;

    btn.addEventListener('click', () => {
        const html = document.documentElement;
        const next = html.dataset.theme === 'dark' ? 'light' : 'dark';
        html.dataset.theme = next;
        try {
            localStorage.setItem('portfolio-theme', next);
        } catch (e) {
            /* localStorage bisa diblokir; abaikan saja */
        }
        $$('[data-theme-icon]').forEach((icon) => {
            icon.textContent = next === 'dark' ? '🌙' : '☀️';
        });
    });
}

/* ------------------------------------------------------------------
 * 5. Cahaya mengikuti kursor
 * ------------------------------------------------------------------ */
function initCursorGlow() {
    const glow = $('#cursor-glow');
    if (!glow || prefersReduced || !animationsOn) return;
    if (window.matchMedia('(hover: none)').matches) return; // perangkat sentuh

    let x = window.innerWidth / 2;
    let y = window.innerHeight / 2;
    let cx = x;
    let cy = y;

    window.addEventListener('mousemove', (e) => {
        x = e.clientX;
        y = e.clientY;
        glow.style.opacity = '1';
    });

    window.addEventListener('mouseleave', () => (glow.style.opacity = '0'));

    const loop = () => {
        cx += (x - cx) * 0.12;
        cy += (y - cy) * 0.12;
        glow.style.transform = `translate(${cx}px, ${cy}px) translate(-50%, -50%)`;
        requestAnimationFrame(loop);
    };
    loop();
}

/* ------------------------------------------------------------------
 * 6. Efek mengetik di hero
 * ------------------------------------------------------------------ */
function initTyping() {
    const el = $('#typing-target');
    if (!el) return;

    const phrases = JSON.parse(el.dataset.phrases || '[]').filter(Boolean);
    if (!phrases.length) return;

    if (prefersReduced || !animationsOn) {
        el.textContent = phrases[0];
        return;
    }

    const typeSpeed = Number(el.dataset.speed || 55);
    const holdTime = Number(el.dataset.hold || 2100);
    let phraseIndex = 0;
    let charIndex = 0;
    let deleting = false;

    const tick = () => {
        const current = phrases[phraseIndex];
        charIndex += deleting ? -1 : 1;
        el.textContent = current.slice(0, charIndex);

        let delay = deleting ? typeSpeed / 2 : typeSpeed;

        if (!deleting && charIndex === current.length) {
            deleting = true;
            delay = holdTime;
        } else if (deleting && charIndex === 0) {
            deleting = false;
            phraseIndex = (phraseIndex + 1) % phrases.length;
            delay = 380;
        }

        setTimeout(tick, delay);
    };

    tick();
}

/* ------------------------------------------------------------------
 * 7. Efek 3D pada kartu
 * ------------------------------------------------------------------ */
function initTilt() {
    if (prefersReduced || !animationsOn) return;
    if (window.matchMedia('(hover: none)').matches) return;

    $$('[data-tilt]').forEach((card) => {
        const strength = Number(card.dataset.tilt) || 9;

        card.addEventListener('mousemove', (e) => {
            const rect = card.getBoundingClientRect();
            const px = (e.clientX - rect.left) / rect.width;
            const py = (e.clientY - rect.top) / rect.height;
            const rx = (0.5 - py) * strength;
            const ry = (px - 0.5) * strength;

            card.style.transform = `perspective(900px) rotateX(${rx}deg) rotateY(${ry}deg) translateY(-6px)`;
            card.style.setProperty('--mx', px * 100 + '%');
            card.style.setProperty('--my', py * 100 + '%');
        });

        card.addEventListener('mouseleave', () => {
            card.style.transform = '';
        });
    });
}

/* ------------------------------------------------------------------
 * 8. Bilah keahlian + angka berjalan
 * ------------------------------------------------------------------ */
function initSkillBars() {
    const bars = $$('[data-skill-level]');
    if (!bars.length) return;

    const fill = (el) => {
        const level = Math.max(0, Math.min(100, Number(el.dataset.skillLevel) || 0));
        el.style.width = level + '%';
    };

    if (prefersReduced || !animationsOn) {
        bars.forEach(fill);
        return;
    }

    const observer = new IntersectionObserver(
        (entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    setTimeout(fill.bind(null, entry.target), 120);
                    observer.unobserve(entry.target);
                }
            });
        },
        { threshold: 0.35 }
    );
    bars.forEach((el) => observer.observe(el));
}

function initCounters() {
    const counters = $$('[data-count-to]');
    if (!counters.length) return;

    const run = (el) => {
        const target = Number(el.dataset.countTo) || 0;
        const suffix = el.dataset.countSuffix || '';
        if (prefersReduced || !animationsOn) {
            el.textContent = target + suffix;
            return;
        }
        const duration = 1300;
        const start = performance.now();

        const step = (now) => {
            const p = Math.min(1, (now - start) / duration);
            const eased = 1 - Math.pow(1 - p, 3);
            el.textContent = Math.round(target * eased) + suffix;
            if (p < 1) requestAnimationFrame(step);
        };
        requestAnimationFrame(step);
    };

    const observer = new IntersectionObserver(
        (entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    run(entry.target);
                    observer.unobserve(entry.target);
                }
            });
        },
        { threshold: 0.5 }
    );
    counters.forEach((el) => observer.observe(el));
}

/* ------------------------------------------------------------------
 * 9. Filter kategori (dipakai section proyek & keahlian)
 * ------------------------------------------------------------------ */
function initProjectFilter() {
    const buttons = $$('[data-filter]');
    if (!buttons.length) return;

    const items = $$('[data-filter-item]');
    const empty = $('#filter-empty');

    const valueOf = (item) => item.dataset.filterValue || item.dataset.projectCategory || '';

    const apply = (value) => {
        let shown = 0;
        items.forEach((item) => {
            const match = value === 'all' || valueOf(item) === value;
            item.style.display = match ? '' : 'none';
            if (match) {
                shown++;
                item.classList.remove('is-visible');
                requestAnimationFrame(() => item.classList.add('is-visible'));
            }
        });
        if (empty) empty.style.display = shown === 0 ? '' : 'none';
    };

    buttons.forEach((btn) => {
        btn.addEventListener('click', () => {
            buttons.forEach((b) => b.classList.toggle('is-active', b === btn));
            const value = btn.dataset.filter;
            if (document.startViewTransition && !prefersReduced) {
                document.startViewTransition(() => apply(value));
            } else {
                apply(value);
            }
        });
    });
}

/* ------------------------------------------------------------------
 * 10. Lightbox galeri
 * ------------------------------------------------------------------ */
function initLightbox() {
    const items = $$('[data-lightbox]');
    if (!items.length) return;

    const overlay = document.createElement('div');
    overlay.className = 'lightbox';
    overlay.style.display = 'none';
    overlay.innerHTML = '<img alt="Pratinjau gambar">';
    document.body.appendChild(overlay);

    const img = overlay.querySelector('img');

    const close = () => {
        overlay.style.display = 'none';
        document.body.style.overflow = '';
    };

    items.forEach((item) =>
        item.addEventListener('click', (e) => {
            e.preventDefault();
            img.src = item.dataset.lightbox || item.src;
            overlay.style.display = 'flex';
            document.body.style.overflow = 'hidden';
        })
    );

    overlay.addEventListener('click', close);
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') close();
    });
}

/* ------------------------------------------------------------------
 * 11. Salin ke clipboard
 * ------------------------------------------------------------------ */
function initCopy() {
    $$('[data-copy]').forEach((btn) => {
        btn.addEventListener('click', async (e) => {
            e.preventDefault();
            const text = btn.dataset.copy;
            try {
                await navigator.clipboard.writeText(text);
                showToast('Disalin: ' + text);
            } catch (err) {
                showToast('Gagal menyalin, salin manual: ' + text);
            }
        });
    });
}

/* ------------------------------------------------------------------
 * 12. Toast notifikasi
 * ------------------------------------------------------------------ */
function showToast(message, tone = 'success') {
    const toast = document.createElement('div');
    toast.className = 'toast';
    if (tone === 'error') {
        toast.style.borderColor = 'color-mix(in srgb, #ef4444 40%, transparent)';
    }
    toast.innerHTML = `<span>${tone === 'error' ? '⚠️' : '✅'}</span><span>${message}</span>`;
    document.body.appendChild(toast);

    setTimeout(() => {
        toast.classList.add('is-hiding');
        setTimeout(() => toast.remove(), 400);
    }, 3600);
}

window.showToast = showToast;

/* ------------------------------------------------------------------
 * 13. Konfirmasi hapus (modal buatan sendiri)
 * ------------------------------------------------------------------ */
function initConfirmDelete() {
    const forms = $$('form[data-confirm]');
    if (!forms.length) return;

    const overlay = document.createElement('div');
    overlay.className = 'modal-backdrop';
    overlay.style.display = 'none';
    overlay.innerHTML = `
        <div class="modal-box">
            <div style="font-size:2rem;line-height:1">🗑️</div>
            <h3 style="margin:.6rem 0 .3rem;font-size:1.05rem;font-weight:600" data-confirm-title>Hapus data ini?</h3>
            <p style="color:var(--muted);font-size:.875rem;margin-bottom:1.25rem" data-confirm-text>
                Data yang dihapus tidak bisa dikembalikan.
            </p>
            <div style="display:flex;gap:.6rem;justify-content:flex-end">
                <button type="button" class="btn btn-ghost btn-sm" data-confirm-cancel>Batal</button>
                <button type="button" class="btn btn-primary btn-sm" data-confirm-ok>Ya, hapus</button>
            </div>
        </div>`;
    document.body.appendChild(overlay);

    let pending = null;

    const close = () => {
        overlay.style.display = 'none';
        pending = null;
    };

    forms.forEach((form) =>
        form.addEventListener('submit', (e) => {
            if (form.dataset.confirmed === '1') return;
            e.preventDefault();
            pending = form;
            overlay.querySelector('[data-confirm-text]').textContent =
                form.dataset.confirm || 'Data yang dihapus tidak bisa dikembalikan.';
            const title = form.dataset.confirmTitle;
            if (title) overlay.querySelector('[data-confirm-title]').textContent = title;
            overlay.style.display = 'flex';
        })
    );

    overlay.querySelector('[data-confirm-cancel]').addEventListener('click', close);
    overlay.addEventListener('click', (e) => {
        if (e.target === overlay) close();
    });
    overlay.querySelector('[data-confirm-ok]').addEventListener('click', () => {
        if (pending) {
            pending.dataset.confirmed = '1';
            pending.submit();
        }
        close();
    });
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && overlay.style.display === 'flex') close();
    });
}

/* ------------------------------------------------------------------
 * 14. Baris dinamis (sosial media) — admin
 * ------------------------------------------------------------------ */
function initRepeater() {
    $$('[data-repeater]').forEach((wrap) => {
        const target = wrap.querySelector('[data-repeater-rows]');
        const template = wrap.querySelector('template[data-repeater-template]');
        const addBtn = wrap.querySelector('[data-repeater-add]');
        if (!target || !template || !addBtn) return;

        const reindex = () => {
            $$('[data-repeater-row]', target).forEach((row, rowIndex) => {
                $$('[name]', row).forEach((input) => {
                    input.name = input.name.replace(/\[\d+\]/, `[${rowIndex}]`);
                });
            });
        };

        addBtn.addEventListener('click', () => {
            const html = template.innerHTML.replace(/__INDEX__/g, target.children.length);
            target.insertAdjacentHTML('beforeend', html);
            reindex();
        });

        target.addEventListener('click', (e) => {
            const remove = e.target.closest('[data-repeater-remove]');
            if (!remove) return;
            e.preventDefault();
            const rows = $$('[data-repeater-row]', target);
            if (rows.length > 1) {
                remove.closest('[data-repeater-row]').remove();
                reindex();
            } else {
                $$('[name]', remove.closest('[data-repeater-row]')).forEach((i) => (i.value = ''));
            }
        });
    });
}

/* ------------------------------------------------------------------
 * 15. Pratinjau gambar sebelum diunggah
 * ------------------------------------------------------------------ */
function initImagePreview() {
    $$('input[type="file"][data-preview]').forEach((input) => {
        input.addEventListener('change', () => {
            const holder = document.querySelector(input.dataset.preview);
            if (!holder || !input.files || !input.files[0]) return;
            const url = URL.createObjectURL(input.files[0]);
            holder.innerHTML = `<img src="${url}" alt="Pratinjau" style="width:100%;height:100%;object-fit:cover">`;
        });
    });
}

/* ------------------------------------------------------------------
 * 16. Marquee: duplikasi isi supaya mulus tanpa jeda
 * ------------------------------------------------------------------ */
function initMarquee() {
    $$('.marquee-track').forEach((track) => {
        if (track.dataset.duplicated === '1') return;
        track.innerHTML += track.innerHTML;
        track.dataset.duplicated = '1';
    });
}

/* ------------------------------------------------------------------
 * 17. Tandai menu yang sedang aktif sesuai section
 * ------------------------------------------------------------------ */
function initScrollSpy() {
    const links = $$('[data-spy-link]');
    if (!links.length) return;

    const sections = links
        .map((link) => document.querySelector(link.getAttribute('href')))
        .filter(Boolean);
    if (!sections.length) return;

    const observer = new IntersectionObserver(
        (entries) => {
            entries.forEach((entry) => {
                if (!entry.isIntersecting) return;
                links.forEach((link) =>
                    link.classList.toggle('is-active', link.getAttribute('href') === '#' + entry.target.id)
                );
            });
        },
        { rootMargin: '-45% 0px -50% 0px' }
    );

    sections.forEach((section) => observer.observe(section));
}

/* ------------------------------------------------------------------
 * 18. Parallax ringan pada elemen ber-data-parallax
 * ------------------------------------------------------------------ */
function initParallax() {
    const items = $$('[data-parallax]');
    if (!items.length || prefersReduced || !animationsOn) return;

    const update = () => {
        const y = window.scrollY;
        items.forEach((el) => {
            const speed = Number(el.dataset.parallax) || 0.08;
            el.style.transform = `translate3d(0, ${y * speed}px, 0)`;
        });
    };

    window.addEventListener('scroll', update, { passive: true });
    update();
}

/* ------------------------------------------------------------------
 * 19. Form kontak: status tombol saat dikirim
 * ------------------------------------------------------------------ */
function initContactForm() {
    const form = $('#contact-form');
    if (!form) return;

    form.addEventListener('submit', () => {
        const btn = form.querySelector('button[type="submit"]');
        if (btn) {
            btn.disabled = true;
            btn.innerHTML = '⏳ Mengirim...';
        }
    });
}

/* ------------------------------------------------------------------
 * 20. Tutup toast bawaan server
 * ------------------------------------------------------------------ */
function initServerToast() {
    $$('[data-toast]').forEach((el) => {
        setTimeout(() => {
            el.classList.add('is-hiding');
            setTimeout(() => el.remove(), 400);
        }, 4000);
    });
}

/* ------------------------------------------------------------------
 * Bootstrap
 * ------------------------------------------------------------------ */
function boot() {
    initReveal();
    initScrollProgress();
    initMobileMenu();
    initTheme();
    initCursorGlow();
    initTyping();
    initTilt();
    initSkillBars();
    initCounters();
    initProjectFilter();
    initLightbox();
    initCopy();
    initConfirmDelete();
    initRepeater();
    initImagePreview();
    initMarquee();
    initScrollSpy();
    initParallax();
    initContactForm();
    initServerToast();
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', boot);
} else {
    boot();
}
