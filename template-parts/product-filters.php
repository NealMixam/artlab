<form id="product-filters">
    <div class="filter-group">
        <h3>Характеристика покрытия</h3>
        <?php
        $finishes = get_terms(['taxonomy'=>'product_finish','hide_empty'=>false]);
        foreach($finishes as $f){
            echo "<label><input type='checkbox' name='product_finish[]' value='{$f->slug}'> {$f->name}</label>";
        }
        ?>
    </div>

    <div class="filter-group">
        <h3>Стиль интерьера</h3>
        <?php
        $styles = get_terms(['taxonomy'=>'product_style','hide_empty'=>false]);
        foreach($styles as $s){
            echo "<label><input type='checkbox' name='product_style[]' value='{$s->slug}'> {$s->name}</label>";
        }
        ?>
    </div>

    <div class="filter-group">
        <h3>Бренд</h3>
        <?php
        $brands = get_terms(['taxonomy'=>'product_brand','hide_empty'=>false]);
        foreach($brands as $b){
            echo "<label><input type='checkbox' name='product_brand[]' value='{$b->slug}'> {$b->name}</label>";
        }
        ?>
    </div>

    <div class="filter-group">
        <h3>Сложность нанесения</h3>
        <?php
        $apps = get_terms(['taxonomy'=>'product_application','hide_empty'=>false]);
        foreach($apps as $a){
            echo "<label><input type='checkbox' name='product_application[]' value='{$a->slug}'> {$a->name}</label>";
        }
        ?>
    </div>

    <div class="filter-group">
        <h3>Цена</h3>
        <input type="number" name="min_price" placeholder="Мин">
        <input type="number" name="max_price" placeholder="Макс">
    </div>

    <button type="submit" class="apply-filters">Применить</button>
    <button type="button" class="reset-filters">Сбросить</button>
</form>
