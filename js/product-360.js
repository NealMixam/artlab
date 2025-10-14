// document.addEventListener('DOMContentLoaded', function() {
//     console.log('images360:', images360);
//
//     if (!images360 || images360.length < 2) {
//         console.warn('Недостаточно изображений для 360° просмотра');
//         return;
//     }
//
//     const elem = document.getElementById('product-360');
//     const wrapper = document.getElementById('product-360-wrapper');
//
//     if (!elem || !wrapper) {
//         console.error('Контейнеры для 360° просмотра не найдены');
//         return;
//     }
//
//     wrapper.style.display = 'block';
//
//     try {
//         const product360 = new ThreeSixty(elem, {
//             images: images360,
//             width: 600,
//             height: 600,
//             drag: true
//         });
//
//         console.log('ThreeSixty инициализирован:', product360);
//
//     } catch (err) {
//         console.error('Ошибка инициализации ThreeSixty:', err);
//     }
// });
