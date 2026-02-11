jQuery(function($){
    // Функция для отправки данных
    function fetch_filtered_products() {
        const $form = $('#product-filters');
        const $grid = $('#products-grid');

        $grid.html('<div class="loading">Загрузка...</div>');

        const data = $form.serialize() + '&action=filter_products&nonce=' + filters_vars.nonce;

        $.post(filters_vars.ajax_url, data, function(res){
            if(res.success){
                $grid.html(res.data.html);
                // Если у вас есть инициализация анимаций или галереи для карточек, 
                // вызывайте её здесь после обновления HTML
            }
        });
    }

    // Отправка по кнопке (сабмит)
    $('#product-filters').on('submit', function(e){
        e.preventDefault();
        fetch_filtered_products();
    });

    // ОПЦИОНАЛЬНО: Авто-отправка при изменении любого поля (select, checkbox)
    // Но для текстовых полей (цены) лучше оставить кнопку или добавить задержку (debounce)
    $('#product-filters select, #product-filters input[type="checkbox"]').on('change', function(){
        fetch_filtered_products();
    });

    // Сброс фильтров
    $('.reset-filters').on('click', function(e){
        e.preventDefault();
        $('#product-filters')[0].reset();
        fetch_filtered_products();
    });
});
// jQuery(function($){
//     $('#product-filters').on('submit', function(e){
//         e.preventDefault();
//         $('#products-grid').html('<div class="loading">Загрузка...</div>');

//         $.post(filters_vars.ajax_url, $(this).serialize()+'&action=filter_products&nonce='+filters_vars.nonce, function(res){
//             if(res.success){
//                 $('#products-grid').html(res.data.html);
//             }
//         });
//     });

//     $('.reset-filters').on('click', function(){
//         $('#product-filters')[0].reset();
//         $('#product-filters').submit();
//     });
// });
