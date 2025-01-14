<div class="dash_content_sidebar">
    <h3 class="icon-asterisk">dashboard/cadastro</h3>
    <p class="dash_content_sidebar_desc">Gerenciamento de cadastro massivo</p>

    <nav>
        <?php if (!empty($categoriesWithProds)) : ?>
            <select class="icon-pencil-square-o radius select_sidebar_cat" name="" onchange="location = this.value;">
                <option value="" disabled selected><a href="#">Filtre por Categoria</a></option>
                <?php foreach ($categoriesWithProds as $category) : ?>
                    <option value="<?= url("produtos/category/" . $category->id); ?>"><?= $category->name ?></option>
                <?php endforeach; ?>
            </select>
        <?php endif; ?>
        <a class="icon-pencil-square-o radius" href="<?= url('produtos'); ?>">Produtos</a>
        <a class="icon-plus-circle radius" href="<?= url('produtos/adicionar-multiples'); ?>">C. Massivo Manual</a>
        <a class="icon-plus-circle radius" href="<?= url('produtos/multiples-auto'); ?>">C. Massivo Autmático</a>
        <a class="icon-plus-circle radius" href="<?= url('produtos/multiples-excel'); ?>">C. Via Excel</a>
        <a class="icon-plus-circle radius" href="<?= url('produtos/adicionar'); ?>">Novo Produto</a>
    </nav>
</div>