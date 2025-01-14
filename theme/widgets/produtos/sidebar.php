<div class="dash_content_sidebar">
    <h3 class="icon-asterisk">dashboard/produtos</h3>
    <p class="dash_content_sidebar_desc">Aqui você gerencia todos os produtos do site...</p>

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
        <!-- <a class="icon-plus-circle radius" href="</?= url('produtos/adicionar-multiples'); ?>">Cadastro Massivo</a> -->
        <a class="icon-plus-circle radius" href="<?= url('produtos/adicionar'); ?>">Novo Produto</a>
    </nav>
</div>