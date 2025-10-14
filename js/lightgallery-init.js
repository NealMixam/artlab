jQuery(document).ready(function($){
    const gallery = document.querySelector('.gallery-grid');
    if(gallery){
        lightGallery(gallery, {
            selector: '.gallery-item',
            plugins: [lgZoom],
            zoom: true,
            download: false
        });
    }
});
