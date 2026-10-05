// Yst Qez — client-side helpers (Livewire ships Alpine.js itself)

// ---------- YouTube IFrame API: clickable timestamps ----------
let ytPlayer = null;
let ytApiRequested = false;

function initPlayer() {
    const iframe = document.getElementById('yt-player');
    if (!iframe || ytApiRequested) return;
    ytApiRequested = true;
    if (window.YT && window.YT.Player) {
        try { ytPlayer = new window.YT.Player('yt-player'); } catch (e) { ytPlayer = null; }
        return;
    }
    const prev = window.onYouTubeIframeAPIReady;
    window.onYouTubeIframeAPIReady = () => {
        prev && prev();
        try { ytPlayer = new window.YT.Player('yt-player'); } catch (e) { ytPlayer = null; }
    };
    const tag = document.createElement('script');
    tag.src = 'https://www.youtube.com/iframe_api';
    tag.async = true;
    document.head.appendChild(tag);
}

function seekTo(seconds) {
    const iframe = document.getElementById('yt-player');
    if (!iframe) return;
    iframe.scrollIntoView({ behavior: 'smooth', block: 'center' });
    if (ytPlayer && typeof ytPlayer.seekTo === 'function') {
        ytPlayer.seekTo(seconds, true);
        ytPlayer.playVideo && ytPlayer.playVideo();
        return;
    }
    // Fallback: reload the embed at the given time
    const url = new URL(iframe.src);
    url.searchParams.set('start', String(seconds));
    url.searchParams.set('autoplay', '1');
    iframe.src = url.toString();
}

document.addEventListener('click', (e) => {
    const seek = e.target.closest('[data-seek]');
    if (seek) {
        e.preventDefault();
        seekTo(parseInt(seek.dataset.seek, 10) || 0);
        return;
    }
    const copy = e.target.closest('[data-copy]');
    if (copy) {
        e.preventDefault();
        const text = copy.dataset.copy || window.location.href;
        const done = () => window.dispatchEvent(new CustomEvent('toast', { detail: { message: copy.dataset.copied || 'Link copied' } }));
        if (navigator.clipboard) {
            navigator.clipboard.writeText(text).then(done).catch(() => {});
        } else {
            const ta = document.createElement('textarea');
            ta.value = text; document.body.appendChild(ta); ta.select();
            try { document.execCommand('copy'); done(); } catch (_) {}
            ta.remove();
        }
    }
    const share = e.target.closest('[data-native-share]');
    if (share && navigator.share) {
        e.preventDefault();
        navigator.share({ title: document.title, url: window.location.href }).catch(() => {});
    }
});

// Image fallback for broken thumbnails (e.g. maxres not available)
document.addEventListener('error', (e) => {
    const img = e.target;
    if (img.tagName === 'IMG' && img.dataset.fallback && img.src !== img.dataset.fallback) {
        img.src = img.dataset.fallback;
    } else if (img.tagName === 'IMG' && img.dataset.hideOnError !== undefined) {
        img.style.visibility = 'hidden';
    }
}, true);

document.addEventListener('DOMContentLoaded', initPlayer);
document.addEventListener('livewire:navigated', () => { ytApiRequested = false; ytPlayer = null; initPlayer(); });
if (document.readyState !== 'loading') initPlayer();

// ---------- Shorts-like feed navigation (arrow keys / swipe) ----------
function feedEl() { return document.querySelector('[data-feed]'); }

document.addEventListener('keydown', (e) => {
    const feed = feedEl();
    if (!feed || e.altKey || e.ctrlKey || e.metaKey || e.shiftKey) return;
    const t = e.target;
    if (t && (t.isContentEditable || ['INPUT', 'TEXTAREA', 'SELECT'].includes(t.tagName))) return;
    let url = null;
    if (e.key === 'ArrowDown' || e.key === 'j') url = feed.dataset.nextUrl;
    if (e.key === 'ArrowUp' || e.key === 'k') url = feed.dataset.prevUrl;
    if (url) { e.preventDefault(); window.location.href = url; }
});

let touchStart = null;
document.addEventListener('touchstart', (e) => {
    if (!feedEl() || e.touches.length !== 1) return;
    touchStart = { x: e.touches[0].clientX, y: e.touches[0].clientY, t: Date.now(), atTop: window.scrollY < 40 };
}, { passive: true });
document.addEventListener('touchend', (e) => {
    const feed = feedEl();
    if (!feed || !touchStart) return;
    const dx = e.changedTouches[0].clientX - touchStart.x;
    const dy = e.changedTouches[0].clientY - touchStart.y;
    const fast = Date.now() - touchStart.t < 600;
    // Horizontal swipe: left → next, right → previous (vertical swipes keep normal scrolling)
    if (fast && Math.abs(dx) > 70 && Math.abs(dx) > Math.abs(dy) * 1.5) {
        const url = dx < 0 ? feed.dataset.nextUrl : feed.dataset.prevUrl;
        if (url) window.location.href = url;
    }
    touchStart = null;
}, { passive: true });
