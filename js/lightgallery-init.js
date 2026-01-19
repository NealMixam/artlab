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

    const galleryBlocks = document.querySelectorAll('.gallery-block');
    galleryBlocks.forEach(function(galleryBlock) {
        lightGallery(galleryBlock, {
            selector: '.gallery-img a', 
            plugins: [lgZoom],
            zoom: true,
            download: false
        });
    });

    const aboutGallery = document.querySelectorAll('.about-gallery');
    aboutGallery.forEach(function(galleryBlock) {
        lightGallery(galleryBlock, {
            selector: '.about-image img',
            plugins: [lgZoom],
            zoom: true,
            download: false
        });
    });

    // const projectGallery = document.querySelectorAll('.gallery-grid');
    // projectGallery.forEach(function(galleryBlock) {
    //     lightGallery(galleryBlock, {
    //         selector: '.gallery-card img',
    //         plugins: [lgZoom],
    //         zoom: true,
    //         download: false
    //     });
    // });
});