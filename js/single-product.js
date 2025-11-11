document.addEventListener('DOMContentLoaded', () => {
    const images360 = window.product360Images || [];
    const modal = document.getElementById('modal-360');
    const modalClose = modal?.querySelector('.modal-360-close');
    const modalViewer = document.getElementById('product-360');
    const trigger = document.getElementById('open-360');
    const playPauseBtn = document.getElementById('play-pause-360');

    let modalThreeSixty = null;
    let isPlaying = false;

    const getViewerSize = () => window.innerWidth < 768 ? { width: 300, height: 300 } : { width: 600, height: 600 };

    const preloadImages = (images) => {
        return Promise.all(images.map(src => new Promise((resolve, reject) => {
            const img = new Image();
            img.onload = resolve;
            img.onerror = reject;
            img.src = src;
        })));
    };

    const openModal = async () => {
        if (!modal) return;
        modal.classList.add('active');

        if (!images360.length) return;

        await preloadImages(images360);

        if (modalThreeSixty && typeof modalThreeSixty.destroy === 'function') {
            modalThreeSixty.destroy();
            modalThreeSixty = null;
        }

        modalViewer.innerHTML = '';
        const { width, height } = getViewerSize();

        modalThreeSixty = new ThreeSixty(modalViewer, {
            image: images360,
            width,
            height,
            speed: 100,
        });
    };

    const closeModal = () => {
        if (!modal) return;
        if (modalThreeSixty && typeof modalThreeSixty.stop === 'function') modalThreeSixty.stop();
        modal.classList.remove('active');
        modalViewer.innerHTML = '';
        modalThreeSixty = null;
        isPlaying = false;
        if (playPauseBtn) playPauseBtn.textContent = '▶️ Play';
    };

    const togglePlayPause = () => {
        if (!modalThreeSixty) return;
        if (isPlaying) {
            modalThreeSixty.stop();
            playPauseBtn.textContent = '▶️ Play';
            isPlaying = false;
        } else {
            modalThreeSixty.play();
            playPauseBtn.textContent = '⏸ Pause';
            isPlaying = true;
        }
    };

    trigger?.addEventListener('click', openModal);
    modalClose?.addEventListener('click', closeModal);
    playPauseBtn?.addEventListener('click', togglePlayPause);
    modal?.addEventListener('click', (e) => { if (e.target === modal) closeModal(); });
    document.addEventListener('keydown', (e) => { if (e.key === 'Escape' && modal?.classList.contains('active')) closeModal(); });
});


// document.addEventListener('DOMContentLoaded', () => {
//     const images360 = window.product360Images || [];
//     const modal = document.getElementById('modal-360');
//     const modalClose = modal?.querySelector('.modal-360-close');
//     const modalViewer = document.getElementById('product-360');
//     const trigger = document.getElementById('open-360');
//     const playPauseBtn = document.getElementById('play-pause-360');
//
//     let modalThreeSixty = null;
//     let isPlaying = false;
//
//     // Определяем размеры под устройство
//     const getViewerSize = () => {
//         return window.innerWidth < 768
//             ? { width: 300, height: 300 } // мобильные
//             : { width: 600, height: 600 }; // десктоп
//     };
//     const openModal = () => {
//         if (!modal) return;
//         modal.classList.add('active');
//
//         // ждём завершения transition (если есть)
//         modal.addEventListener('transitionend', function handler() {
//             modal.removeEventListener('transitionend', handler);
//
//             if (modalViewer && typeof ThreeSixty === 'function' && Array.isArray(images360) && images360.length > 1) {
//                 // уничтожаем старый экземпляр, если есть
//                 if (modalThreeSixty && typeof modalThreeSixty.destroy === 'function') {
//                     modalThreeSixty.destroy();
//                     modalThreeSixty = null;
//                 }
//
//                 modalViewer.innerHTML = '';
//                 const { width, height } = getViewerSize();
//
//                 try {
//                     modalThreeSixty = new ThreeSixty(modalViewer, {
//                         image: images360,
//                         width,
//                         height,
//                         speed: 100,
//                     });
//
//                     // ждем загрузки кадров
//                     if (modalThreeSixty.onReady) {
//                         modalThreeSixty.onReady(() => console.log('✅ Все кадры загружены'));
//                     }
//
//                     console.log('✅ ThreeSixty инициализирован в модалке');
//                 } catch (e) {
//                     console.error('Ошибка инициализации ThreeSixty в модалке:', e);
//                 }
//             } else {
//                 console.warn('⚠️ Нет изображений для 360° или ThreeSixty не подключён');
//             }
//         });
//     };
//
//
//     // const openModal = () => {
//     //     if (!modal) return;
//     //     modal.classList.add('active');
//     //
//     //     setTimeout(() => {
//     //         if (modalViewer && typeof ThreeSixty === 'function' && Array.isArray(images360) && images360.length > 1) {
//     //             modalViewer.innerHTML = '';
//     //             const { width, height } = getViewerSize();
//     //             try {
//     //                 modalThreeSixty = new ThreeSixty(modalViewer, {
//     //                     image: images360,
//     //                     width,
//     //                     height,
//     //                     speed: 100,
//     //                     // drag: true,
//     //                     // count: 30,
//     //                 });
//     //                 console.log('✅ ThreeSixty инициализирован в модалке');
//     //             } catch (e) {
//     //                 console.error('Ошибка инициализации ThreeSixty в модалке:', e);
//     //             }
//     //         } else {
//     //             console.warn('⚠️ Нет изображений для 360° или ThreeSixty не подключён');
//     //         }
//     //     }, 350);
//     // };
//
//     const closeModal = () => {
//         if (!modal) return;
//
//         // ⏹️ Если проигрывание идёт — останавливаем
//         if (modalThreeSixty && typeof modalThreeSixty.stop === 'function') {
//             modalThreeSixty.stop();
//         }
//
//         modal.classList.remove('active');
//         if (modalViewer) modalViewer.innerHTML = '';
//         modalThreeSixty = null;
//
//         // Сбрасываем состояние кнопки
//         isPlaying = false;
//         if (playPauseBtn) playPauseBtn.textContent = '▶️ Play';
//     };
//
//     const togglePlayPause = () => {
//         if (!modalThreeSixty || typeof modalThreeSixty.play !== 'function') return;
//
//         if (isPlaying) {
//             modalThreeSixty.stop();
//             playPauseBtn.textContent = '▶️ Play';
//             isPlaying = false;
//         } else {
//             modalThreeSixty.play();
//             playPauseBtn.textContent = '⏸ Pause';
//             isPlaying = true;
//         }
//     };
//
//     // === Обработчики событий ===
//     trigger?.addEventListener('click', openModal);
//     modalClose?.addEventListener('click', closeModal);
//     playPauseBtn?.addEventListener('click', togglePlayPause);
//
//     modal?.addEventListener('click', (e) => {
//         if (e.target === modal) closeModal();
//     });
//
//     document.addEventListener('keydown', (e) => {
//         if (e.key === 'Escape' && modal?.classList.contains('active')) {
//             closeModal();
//         }
//     });
// });


// document.addEventListener('DOMContentLoaded', () => {
//     const images360 = window.product360Images || [];
//     const modal = document.getElementById('modal-360');
//     const modalClose = modal?.querySelector('.modal-360-close');
//     const modalViewer = document.getElementById('product-360');
//     const trigger = document.getElementById('open-360'); // кнопка "360°"
//     let modalThreeSixty = null;
//
//     const openModal = () => {
//         if (!modal) return;
//         modal.classList.add('active');
//
//         // Инициализируем 360 только после отображения модалки
//         setTimeout(() => {
//             if (modalViewer && typeof ThreeSixty === 'function' && Array.isArray(images360) && images360.length > 1) {
//                 modalViewer.innerHTML = ''; // очищаем контейнер
//                 try {
//                     modalThreeSixty = new ThreeSixty(modalViewer, {
//                         image: images360,
//                         width: 600,
//                         height: 600,
//                         speed: 100,
//                         drag: true,
//                         count: 30,
//                     });
//                     console.log('✅ ThreeSixty инициализирован в модалке');
//                 } catch (e) {
//                     console.error('Ошибка инициализации ThreeSixty в модалке:', e);
//                 }
//             } else {
//                 console.warn('⚠️ Нет изображений для 360° или ThreeSixty не подключён');
//             }
//         }, 150);
//     };
//
//     const closeModal = () => {
//         if (!modal) return;
//         modal.classList.remove('active');
//
//         // Полностью очищаем viewer при закрытии
//         if (modalViewer) {
//             modalViewer.innerHTML = '';
//         }
//         modalThreeSixty = null;
//     };
//
//     // === Обработчики событий ===
//     trigger?.addEventListener('click', openModal);
//     modalClose?.addEventListener('click', closeModal);
//
//     modal?.addEventListener('click', (e) => {
//         if (e.target === modal) closeModal();
//     });
//
//     document.addEventListener('keydown', (e) => {
//         if (e.key === 'Escape' && modal?.classList.contains('active')) {
//             closeModal();
//         }
//     });

    // // 360 VIEW INIT ===
    // const images360 = window.product360Images || [];
    // console.log('images360:', images360);
    //
    // if (images360.length > 1 && typeof ThreeSixty === 'function') {
    //     const elem = document.getElementById('product-360');
    //     if (elem) {
    //         try {
    //             const threesixty = new ThreeSixty(elem, {
    //                 image: images360,
    //                 width: 600,
    //                 height: 600,
    //                 speed: 80,
    //                 drag: true,
    //                 count: 30,
    //             });
    //             // threesixty.play();
    //             console.log('ThreeSixty инициализирован на странице');
    //         } catch (e) {
    //             console.error('Ошибка инициализации ThreeSixty:', e);
    //         }
    //     }
    // }
    //
    // // MODAL WINDOW ===
    // const modal = document.getElementById('modal-360');
    // const modalClose = modal?.querySelector('.modal-360-close');
    // const modalViewer = document.getElementById('modal-360-viewer');
    // const trigger = document.getElementById('open-360');
    // let modalThreeSixty = null;
    //
    // const openModal = () => {
    //     if (!modal) return;
    //     modal.classList.add('active');
    //
    //     setTimeout(() => {
    //         if (modalViewer && typeof ThreeSixty === 'function' && Array.isArray(images360) && images360.length > 1) {
    //             modalViewer.innerHTML = '';
    //             try {
    //                 modalThreeSixty = new ThreeSixty(modalViewer, {
    //                     image: images360,
    //                     width: 600,
    //                     height: 600,
    //                     speed: 100,
    //                     drag: true,
    //                 });
    //                 console.log('✅ ThreeSixty открыт в модалке');
    //             } catch (e) {
    //                 console.error('Ошибка инициализации ThreeSixty в модалке:', e);
    //             }
    //         } else {
    //             console.warn('⚠️ Не найдены изображения для 360 или ThreeSixty не подключён.');
    //         }
    //     }, 100);
    // };
    //
    // const closeModal = () => {
    //     if (!modal) return;
    //     modal.classList.remove('active');
    //     if (modalViewer) modalViewer.innerHTML = '';
    //     modalThreeSixty = null;
    // };
    //
    // trigger?.addEventListener('click', openModal);
    // modalClose?.addEventListener('click', closeModal);
    //
    // modal?.addEventListener('click', (e) => {
    //     if (e.target === modal) closeModal();
    // });
    //
    // document.addEventListener('keydown', (e) => {
    //     if (e.key === 'Escape' && modal?.classList.contains('active')) {
    //         closeModal();
    //     }
    // });

    // === GALLERY ===
    // const galleryContainer = document.querySelector('.gallery-grid');
    // if (galleryContainer && typeof lightGallery === 'function') {
    //     const plugins = typeof lgZoom !== 'undefined' ? [lgZoom] : [];
    //     try {
    //         lightGallery(galleryContainer, {
    //             selector: '.gallery-item',
    //             plugins,
    //             zoom: true,
    //             download: false,
    //         });
    //         console.log('lightGallery инициализирован');
    //     } catch (e) {
    //         console.error('Ошибка инициализации lightGallery:', e);
    //     }
    // }

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
