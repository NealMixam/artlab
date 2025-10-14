jQuery(function($){
    // Применение фильтров
    $('#product-filters').on('submit', function(e){
        e.preventDefault();
        $('#products-grid').html('<div class="loading">Загрузка...</div>');

        $.post(filters_vars.ajax_url, $(this).serialize()+'&action=filter_products&nonce='+filters_vars.nonce, function(res){
            if(res.success){
                $('#products-grid').html(res.data.html);
            }
        });
    });

    // Сброс фильтров
    $('.reset-filters').on('click', function(){
        $('#product-filters')[0].reset();
        $('#product-filters').submit();
    });
});
