document.addEventListener("DOMContentLoaded", () => {
  const sprite360 = window.product360Sprite || "";
  const framesCount = window.product360Count || 0;
  const framesPerRow = window.product360PerRow || 0;

  const modal = document.getElementById("modal-360");
  const modalClose = modal?.querySelector(".modal-360-close");
  const modalViewer = document.getElementById("product-360");
  const modalWrapper = document.getElementById("product-360-wrapper");
  const trigger = document.getElementById("open-360");

  const playPauseBtn = document.getElementById("play-pause-360");
  const zoomInBtn = document.getElementById("zoom-in-360");
  const zoomOutBtn = document.getElementById("zoom-out-360");
  const rotateLeftBtn = document.getElementById("rotate-left-360");
  const rotateRightBtn = document.getElementById("rotate-right-360");

  const controlsToggle = document.getElementById("controls-toggle-360");
  const controlsMenu = document.getElementById("controls-menu-360");
  const fullscreenBtn = document.getElementById("fullscreen-360");
  const header = document.querySelector(".site-header");
  const footer = document.querySelector(".site-footer");

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

  const getCoords = (e) => {
    if (e.touches && e.touches.length > 0) {
      return { x: e.touches[0].clientX, y: e.touches[0].clientY };
    }
    return { x: e.clientX, y: e.clientY };
  };

  const getViewerSize = () => {
    const fullscreen = modal.classList.contains("fullscreen");
    if (fullscreen) {
      return {
        width: window.innerWidth,
        height: window.innerHeight,
      };
    }

    return window.innerWidth < 768
      ? { width: 300, height: 300 }
      : { width: 800, height: 800 };
  };

  const updatePlayButton = () => {
    playPauseBtn.textContent = isPlaying ? "⏸️" : "▶️";
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

  const delay = (ms) => new Promise((resolve) => setTimeout(resolve, ms));

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

    modal.classList.add("active");
    header.style.zIndex = "1";
    footer.style.zIndex = "0";

    if (viewerInitialized) {
      modalViewer.innerHTML = "";

      const { width, height } = getViewerSize();

      modalThreeSixty = new ThreeSixty(modalViewer, {
        image: sprite360,
        width,
        height,
        count: framesCount,
        perRow: framesPerRow,
        speed: 100,
      });

      // resetZoom();
      disableRotateControls(false);
      startAutoplay();
      return;
    }

    modalViewer.innerHTML = "";

    renderLoader();

    await delay(2000);

    modalViewer.innerHTML = "";

    const { width, height } = getViewerSize();

    modalThreeSixty = new ThreeSixty(modalViewer, {
      image: sprite360,
      width,
      height,
      count: framesCount,
      perRow: framesPerRow,
      speed: 100,
    });

    viewerInitialized = true;

    // resetZoom();
    disableRotateControls(false);
    startAutoplay();
  };

  const closeModal = () => {
    modal.classList.remove("active");
    modal.classList.remove("fullscreen");
    header.style.zIndex = "10000";
    footer.style.zIndex = "1";
    // modalViewer.innerHTML = '';

    modalThreeSixty?.stop?.();
    modalThreeSixty = null;

    isPlaying = false;
    // resetZoom();
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

  /* ================= ZOOM STATE ================= */

  const MAX_ZOOM = 3;
  const MIN_ZOOM = 1;
  const ZOOM_STEP = 0.5;

  // Ссылки на элементы (убедитесь, что они определены в начале скрипта)
  // const modalViewer = document.getElementById('product-360');
  // const modalWrapper = document.getElementById('product-360-wrapper');

  /* ================= ZOOM FUNCTIONS ================= */

  const applyZoomTransform = () => {
    // Трансформируем саму картинку
    modalViewer.style.transform = `translate(${imgPosX}px, ${imgPosY}px) scale(${zoomScale})`;
  };

  // const enableZoomMode = () => {
  //     isZoomed = true;

  //     // Добавляем класс картинке (чтобы включить pointer-events: none)
  //     modalViewer.classList.add('is-zoomed');

  //     // Меняем курсор на обертке, так как картинка теперь "неклиакбельна"
  //     modalWrapper.style.cursor = 'grab';

  //     // Старые блокировки можно оставить на всякий случай,
  //     // но pointer-events: none делает основную работу.
  //     disableRotateControls(true);
  // };

  const enableZoomMode = () => {
    isZoomed = true;

    // ОСТАНАВЛИВАЕМ вращение при входе в зум
    stopAutoplay();

    // Добавляем класс картинке
    modalViewer.classList.add("is-zoomed");

    // Меняем курсор на обертке
    modalWrapper.style.cursor = "grab";

    // Блокируем кнопки поворота (уже было в вашем коде)
    disableRotateControls(true);
  };

  const disableZoomMode = () => {
    isZoomed = false;
    zoomScale = 1;
    imgPosX = 0;
    imgPosY = 0;

    modalViewer.classList.remove("is-zoomed");
    modalViewer.style.transform = "none";

    modalWrapper.style.cursor = "default";

    disableRotateControls(false);
  };

  /* ================= ZOOM BUTTONS HANDLERS ================= */

  zoomInBtn?.addEventListener("click", (e) => {
    e.stopPropagation();
    if (zoomScale >= MAX_ZOOM) return;

    zoomScale += ZOOM_STEP;
    if (zoomScale > MAX_ZOOM) zoomScale = MAX_ZOOM;

    enableZoomMode();
    applyZoomTransform();
  });

  zoomOutBtn?.addEventListener("click", (e) => {
    e.stopPropagation();
    if (zoomScale <= MIN_ZOOM) return;

    zoomScale -= ZOOM_STEP;

    if (zoomScale <= MIN_ZOOM) {
      disableZoomMode();
    } else {
      applyZoomTransform();
    }
  });

  /* ================= DRAG LOGIC (PANS THE IMAGE) ================= */

  // СЛУШАЕМ СОБЫТИЯ НА ОБЕРТКЕ (WRAPPER), А НЕ НА КАРТИНКЕ
  //   modalWrapper.addEventListener("mousedown", (e) => {
  //     if (!isZoomed) return;

  //     // Если кликнули, например, на контрол внутри wrapper-а, игнорируем
  //     if (e.target.closest(".controls-360")) return;

  //     e.preventDefault();
  //     isDragging = true;

  //     modalWrapper.classList.add("is-dragging"); // Для курсора grabbing
  //     modalViewer.style.transition = "none"; // Убираем плавность при драге

  //     dragStartX = e.clientX - imgPosX;
  //     dragStartY = e.clientY - imgPosY;

  //     document.addEventListener("mousemove", onMouseMove);
  //     document.addEventListener("mouseup", onMouseUp);
  //   });

  const handleDragStart = (e) => {
    if (!isZoomed) return;
    if (e.target.closest(".controls-360")) return;

    // Для touch-событий не используем preventDefault(),
    // так как touch-action: none в CSS уже всё делает.
    if (e.type === "mousedown") e.preventDefault();

    isDragging = true;
    modalWrapper.classList.add("is-dragging");
    modalViewer.style.transition = "none";

    const coords = getCoords(e);
    dragStartX = coords.x - imgPosX;
    dragStartY = coords.y - imgPosY;

    // Добавляем слушатели на перемещение и отпускание
    if (e.type === "mousedown") {
      document.addEventListener("mousemove", onMouseMove);
      document.addEventListener("mouseup", onMouseUp);
    } else {
      document.addEventListener("touchmove", onMouseMove, { passive: false });
      document.addEventListener("touchend", onMouseUp);
    }
  };

  // Привязываем события к wrapper
  modalWrapper.addEventListener("mousedown", handleDragStart);
  modalWrapper.addEventListener("touchstart", handleDragStart, {
    passive: false,
  });

  //   const onMouseMove = (e) => {
  //     if (!isDragging) return;
  //     e.preventDefault();

  //     imgPosX = e.clientX - dragStartX;
  //     imgPosY = e.clientY - dragStartY;

  //     applyZoomTransform();
  //   };
  const onMouseMove = (e) => {
    if (!isDragging) return;

    // Блокируем скролл страницы на мобильных во время драга
    if (e.cancelable) e.preventDefault();

    const coords = getCoords(e);
    imgPosX = coords.x - dragStartX;
    imgPosY = coords.y - dragStartY;

    applyZoomTransform();
  };

  const onMouseUp = () => {
    isDragging = false;
    modalWrapper.classList.remove("is-dragging");
    modalViewer.style.transition =
      "transform 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94)";

    // Удаляем все слушатели
    document.removeEventListener("mousemove", onMouseMove);
    document.removeEventListener("mouseup", onMouseUp);
    document.removeEventListener("touchmove", onMouseMove);
    document.removeEventListener("touchend", onMouseUp);
  };
  //   const onMouseUp = () => {
  //     isDragging = false;

  //     modalWrapper.classList.remove("is-dragging");
  //     modalViewer.style.transition =
  //       "transform 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94)"; // Возвращаем плавность

  //     document.removeEventListener("mousemove", onMouseMove);
  //     document.removeEventListener("mouseup", onMouseUp);
  //   };

  /* ================= CLICK ON VIEWER ================= */

  modalViewer.addEventListener("click", () => {
    if (isZoomed) return;
    togglePlayPause();
  });

  const toggleFullscreen = async () => {
    try {
      if (!document.fullscreenElement) {
        // Если мы еще не в полном экране — входим в него
        // Запрашиваем полный экран для элемента modal
        await modal.requestFullscreen();
      } else {
        // Если мы уже в полном экране — выходим
        await document.exitFullscreen();
      }
    } catch (err) {
      console.error(
        `Ошибка при переходе в полноэкранный режим: ${err.message}`,
      );
    }
  };

  /* ================= CONTROLS ================= */

  controlsToggle?.addEventListener("click", () => {
    controlsMenu?.classList.toggle("open");
  });

  trigger?.addEventListener("click", openModal);
  modalClose?.addEventListener("click", closeModal);

  playPauseBtn?.addEventListener("click", togglePlayPause);
  rotateLeftBtn?.addEventListener("click", rotateLeft);
  rotateRightBtn?.addEventListener("click", rotateRight);

  fullscreenBtn?.addEventListener("click", toggleFullscreen);

  modal?.addEventListener("click", (e) => {
    if (e.target === modal && !isZoomed) closeModal();
  });

  document.addEventListener("keydown", (e) => {
    if (!modalThreeSixty) return;

    if (e.key === "Escape") closeModal();
    if (isZoomed) return;

    if (e.key === "ArrowLeft") rotateLeft();
    if (e.key === "ArrowRight") rotateRight();
  });

  document.addEventListener("fullscreenchange", () => {
    // Проверяем, есть ли сейчас активный полноэкранный элемент
    const isFullscreen = !!document.fullscreenElement;

    // Переключаем класс для CSS стилизации (скрыть хедер, футер и т.д.)
    if (isFullscreen) {
      modal.classList.add("fullscreen");
      modalWrapper.classList.add("fullscreen-mode"); // Доп. класс для стилей обертки
    } else {
      modal.classList.remove("fullscreen");
      modalWrapper.classList.remove("fullscreen-mode");
    }

    if (modalThreeSixty) {
      // Останавливаем текущий
      modalThreeSixty.stop();
      modalViewer.innerHTML = ""; // Очищаем контейнер

      // Получаем новые размеры (ваша функция getViewerSize уже учитывает класс 'fullscreen' или window.innerWidth)
      //   const { width, height } = getViewerSize();

      // Создаем заново
      modalThreeSixty = new ThreeSixty(modalViewer, {
        image: sprite360,
        width: 800,
        height: 800,
        count: framesCount,
        perRow: framesPerRow,
        speed: 100,
      });

      // Если нужно, возвращаем автоплей или сбрасываем зум
      // resetZoom();
      if (isPlaying) modalThreeSixty.play();
    }
  });
});

document.addEventListener("DOMContentLoaded", function () {
  if (typeof Swiper === "undefined") {
    console.error("Swiper не найден!");
    return;
  }

  const thumbs = new Swiper(".thumbs-slider", {
    spaceBetween: 10,
    slidesPerView: 4,
    freeMode: true,
    watchSlidesProgress: true,
  });

  const mainSlider = new Swiper(".main-slider", {
    spaceBetween: 10,
    slidesPerView: 1,
    navigation: {
      nextEl: ".swiper-button-next",
      prevEl: ".swiper-button-prev",
    },
    thumbs: {
      swiper: thumbs,
    },
  });

  const galleryContainer = document.querySelector(
    ".main-slider .swiper-wrapper",
  );
  if (galleryContainer && typeof lightGallery === "function") {
    lightGallery(galleryContainer, {
      selector: ".gallery-item",
      plugins: typeof lgZoom !== "undefined" ? [lgZoom] : [],
      zoom: true,
      download: false,
    });
  }
});
