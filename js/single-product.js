document.addEventListener('DOMContentLoaded', () => {

    const sprite360 = window.product360Sprite || '';
    const framesCount = window.product360Count || 0;
    const framesPerRow = window.product360PerRow || 0;

    const modal = document.getElementById('modal-360');
    const modalClose = modal?.querySelector('.modal-360-close');
    const modalViewer = document.getElementById('product-360');
    const modalWrapper = document.getElementById('product-360-wrapper');
    const trigger = document.getElementById('open-360');

    const playPauseBtn = document.getElementById('play-pause-360');
    const zoomInBtn = document.getElementById('zoom-in-360');
    const zoomOutBtn = document.getElementById('zoom-out-360');
    const rotateLeftBtn = document.getElementById('rotate-left-360');
    const rotateRightBtn = document.getElementById('rotate-right-360');

    const controlsToggle = document.getElementById('controls-toggle-360');
    const controlsMenu = document.getElementById('controls-menu-360');
    const fullscreenBtn = document.getElementById('fullscreen-360');
    const header = document.querySelector('.site-header');
    const footer = document.querySelector('.site-footer');

    let modalThreeSixty = null;
    let viewerInitialized = false;

    /* ================= STATE ================= */

    let isPlaying = false;
    let isZoomed = false;

    const MAX_ZOOM_STEPS = 4;
    let zoomStep = 0;
    let zoomScale = 1;

    let imgPosX = 0;
    let imgPosY = 0;

    let dragStartX = 0;
    let dragStartY = 0;
    let isDragging = false;

    /* ================= HELPERS ================= */

    const getViewerSize = () => {
        const fullscreen = modal.classList.contains('fullscreen');
        if (fullscreen) {
            return {
                width: window.innerWidth,
                height: window.innerHeight
            };
        }

        return window.innerWidth < 768
            ? { width: 300, height: 300 }
            : { width: 800, height: 800 };
    };

    const updatePlayButton = () => {
        playPauseBtn.textContent = isPlaying ? '⏸️' : '▶️';
    };

    const disableRotateControls = (disabled) => {
        playPauseBtn.disabled = disabled;
        rotateLeftBtn.disabled = disabled;
        rotateRightBtn.disabled = disabled;
    };

    /* ================= AUTOPLAY ================= */

    const startAutoplay = () => {
        if (!modalThreeSixty || isZoomed) return;
        modalThreeSixty.play();
        isPlaying = true;
        updatePlayButton();
    };

    const stopAutoplay = () => {
        if (!modalThreeSixty) return;
        modalThreeSixty.stop();
        isPlaying = false;
        updatePlayButton();
    };

    /* ================= MODAL ================= */

    const delay = (ms) => new Promise(resolve => setTimeout(resolve, ms));

    const renderLoader = () => {
        modalViewer.innerHTML = `
        <div class="viewer-loader">
            <div class="spinner"></div>
            <div class="loader-text">Загрузка 360°…</div>
        </div>
    `;
    };

    const openModal = async () => {
        if (!modal || !sprite360) return;

        modal.classList.add('active');
        header.style.zIndex = '1';
        footer.style.zIndex = '0';

        if (viewerInitialized) {
            modalViewer.innerHTML = '';

            const { width, height } = getViewerSize();

            modalThreeSixty = new ThreeSixty(modalViewer, {
                image: sprite360,
                width,
                height,
                count: framesCount,
                perRow: framesPerRow,
                speed: 100
            });

            resetZoom();
            disableRotateControls(false);
            startAutoplay();
            return;
        }

        modalViewer.innerHTML = '';

        renderLoader();

        await delay(8000);

        modalViewer.innerHTML = '';

        const { width, height } = getViewerSize();

        modalThreeSixty = new ThreeSixty(modalViewer, {
            image: sprite360,
            width,
            height,
            count: framesCount,
            perRow: framesPerRow,
            speed: 100
        });

        viewerInitialized = true;

        resetZoom();
        disableRotateControls(false);
        startAutoplay();
    };

    const closeModal = () => {
        modal.classList.remove('active');
        modal.classList.remove('fullscreen');
        header.style.zIndex = '10000';
        footer.style.zIndex = '1';
        // modalViewer.innerHTML = '';

        modalThreeSixty?.stop?.();
        modalThreeSixty = null;

        isPlaying = false;
        resetZoom();
        updatePlayButton();
    };

    const lockThreeSixtyHard = () => {
        if (!modalThreeSixty || modalThreeSixty._locked) return;

        modalThreeSixty._locked = true;

        modalThreeSixty._next = modalThreeSixty.next;
        modalThreeSixty._prev = modalThreeSixty.prev;
        modalThreeSixty._play = modalThreeSixty.play;

        modalThreeSixty.next = () => {};
        modalThreeSixty.prev = () => {};
        modalThreeSixty.play = () => {};

        modalThreeSixty.stop();
        isPlaying = false;
        updatePlayButton();
    };


    const unlockThreeSixtyHard = () => {
        if (!modalThreeSixty || !modalThreeSixty._locked) return;

        modalThreeSixty.next = modalThreeSixty._next;
        modalThreeSixty.prev = modalThreeSixty._prev;
        modalThreeSixty.play = modalThreeSixty._play;

        delete modalThreeSixty._next;
        delete modalThreeSixty._prev;
        delete modalThreeSixty._play;
        delete modalThreeSixty._locked;
    };

    /* ================= PLAY / PAUSE ================= */

    const togglePlayPause = () => {
        if (!modalThreeSixty || isZoomed) return;

        isPlaying ? stopAutoplay() : startAutoplay();
    };

    /* ================= ROTATE ================= */

    const rotateLeft = () => {
        if (!modalThreeSixty || isZoomed) return;
        stopAutoplay();
        modalThreeSixty.prev();
    };

    const rotateRight = () => {
        if (!modalThreeSixty || isZoomed) return;
        stopAutoplay();
        modalThreeSixty.next();
    };

    /* ================= ZOOM ================= */

    const applyZoomTransform = () => {
        modalViewer.style.transform =
            `scale(${zoomScale}) translate(${imgPosX / zoomScale}px, ${imgPosY / zoomScale}px)`;
    };

    const enableZoom = () => {
        isZoomed = true;
        lockThreeSixtyHard();
        disableRotateControls(true);

        modalWrapper.classList.add('zoomed');
        modalViewer.style.cursor = 'grab';

        applyZoomTransform();
    };

    const resetZoom = () => {
        isZoomed = false;
        unlockThreeSixtyHard();
        zoomStep = 0;
        zoomScale = 1;
        imgPosX = 0;
        imgPosY = 0;

        modalViewer.style.transform = 'none';
        modalViewer.style.cursor = 'default';
        modalWrapper.classList.remove('zoomed');

        disableRotateControls(false);
    };

    /* ================= ZOOM BUTTONS ================= */

    zoomInBtn?.addEventListener('click', () => {
        if (zoomStep >= MAX_ZOOM_STEPS) return;

        zoomStep++;
        zoomScale = 1 + zoomStep * 0.5;
        enableZoom();
    });

    zoomOutBtn?.addEventListener('click', () => {
        if (zoomStep <= 0) return;

        zoomStep--;
        zoomScale = 1 + zoomStep * 0.5;

        if (zoomStep === 0) {
            resetZoom();
        } else {
            applyZoomTransform();
        }
    });

    /* ================= DRAG ================= */

    modalViewer.addEventListener('mousedown', (e) => {
        if (!isZoomed) return;

        isDragging = true;
        dragStartX = e.clientX - imgPosX;
        dragStartY = e.clientY - imgPosY;

        modalViewer.style.cursor = 'grabbing';
        document.addEventListener('mousemove', onDragMove);
        document.addEventListener('mouseup', stopDrag);
    });

    const onDragMove = (e) => {
        if (!isDragging) return;

        imgPosX = e.clientX - dragStartX;
        imgPosY = e.clientY - dragStartY;

        applyZoomTransform();
    };

    const stopDrag = () => {
        isDragging = false;
        modalViewer.style.cursor = 'grab';
        document.removeEventListener('mousemove', onDragMove);
        document.removeEventListener('mouseup', stopDrag);
    };

    /* ================= CLICK ON VIEWER ================= */

    modalViewer.addEventListener('click', () => {
        if (isZoomed) return;
        togglePlayPause();
    });

    /* ================= FULLSCREEN ================= */

    const toggleFullscreen = () => {
        modal.classList.toggle('fullscreen');

        if (!modalThreeSixty) return;

        const { width, height } = getViewerSize();
        // modalThreeSixty.resize(width, height);
    };

    /* ================= CONTROLS ================= */

    controlsToggle?.addEventListener('click', () => {
        controlsMenu?.classList.toggle('open');
    });

    trigger?.addEventListener('click', openModal);
    modalClose?.addEventListener('click', closeModal);

    playPauseBtn?.addEventListener('click', togglePlayPause);
    rotateLeftBtn?.addEventListener('click', rotateLeft);
    rotateRightBtn?.addEventListener('click', rotateRight);

    fullscreenBtn?.addEventListener('click', toggleFullscreen);

    modal?.addEventListener('click', (e) => {
        if (e.target === modal && !isZoomed) closeModal();
    });

    document.addEventListener('keydown', (e) => {
        if (!modalThreeSixty) return;

        if (e.key === 'Escape') closeModal();
        if (isZoomed) return;

        if (e.key === 'ArrowLeft') rotateLeft();
        if (e.key === 'ArrowRight') rotateRight();
    });

});

document.addEventListener('DOMContentLoaded', function () {
    if (typeof Swiper === 'undefined') {
        console.error('Swiper не найден!');
        return;
    }

    const thumbs = new Swiper('.thumbs-slider', {
        spaceBetween: 10,
        slidesPerView: 4,
        freeMode: true,
        watchSlidesProgress: true,
    });

    const mainSlider = new Swiper('.main-slider', {
        spaceBetween: 10,
        slidesPerView: 1,
        navigation: {
            nextEl: '.swiper-button-next',
            prevEl: '.swiper-button-prev',
        },
        thumbs: {
            swiper: thumbs,
        },
    });

    const galleryContainer = document.querySelector('.main-slider .swiper-wrapper');
    if (galleryContainer && typeof lightGallery === 'function') {
        lightGallery(galleryContainer, {
            selector: '.gallery-item',
            plugins: typeof lgZoom !== 'undefined' ? [lgZoom] : [],
            zoom: true,
            download: false,
        });
    }
});
