<form id="coating-filters">
    <?php
    $types = get_terms(['taxonomy'=>'coating_type','hide_empty'=>false]);
    foreach($types as $t){ echo "<label><input type='checkbox' name='coating_type[]' value='{$t->slug}'> {$t->name}</label>"; }
    ?>
    <button type="submit">Применить</button>
</form>
<div id="coatings-grid"></div>
