<?php $this->layout("_theme"); ?>
<?php require __DIR__ . "/widgets/category/sidebar.php"; ?>
<section class="dash_content_app">
    <header class="dash_content_app_header">
        <h2 class="icon-pencil-square-o">Categorias</h2>
        <form method="POST" action='<?= url('busca/categorias'); ?>' class="app_search_form ajax_off">
            <input type="text" name="s" list="browsers" placeholder="Pesquisar Categorias:">
            <button class="icon-search icon-notext"></button>
        </form>
        <!-- <a class="icon-plus-circle btn btn-green" href="categorias/adicionar">Nova Categoria</a> -->
    </header>

    <div class="dash_content_app_box">
        <section class="app_blog_categories">
            <?php if (!is_null($categories)) : ?>
                <?php foreach ($categories as $category) : ?>
                    <article class="radius <?= $category->status; ?>">
                        <div class="thumb">
                            <div class="cover embed radius">
                                <?php if ($category->cover) : ?>
                                    <img src="<?= url("$category->cover"); ?>" alt="<?= $category->name; ?>">
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="info">
                            <div class="title_box">
                                <h3 class="title"><?= $category->name; ?> [ <b><?= $category->total_products; ?> produto(s) aqui</b> ]</h3>
                                <span><b>Ordem:</b> <?= $category->priority; ?></span>
                            </div>
                            <p class="desc"><?= $category->description; ?></p>

                            <div class="actions">
                                <a class="icon-pencil btn btn-blue" href="<?= url("categorias/editar/$category->id"); ?>" title="Editar">Editar</a>
                                <?php if ($category->status != 'trash') : ?>
                                    <button class="icon-trash-o btn btn-red" onclick="deletar('<?= url("categorias/remover/$category->id"); ?>')">Deletar</button>
                                <?php else : ?>
                                    <button class="icon-check btn btn-green" onclick="ativar('<?= url("categorias/ativar/$category->id"); ?>')">Ativar</button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            <?php else : ?>
                <h3>Nenhuma categoria cadastrada</h3>
            <?php endif; ?>
            <?= $pagination; ?>
        </section>
    </div>
</section>
<?php $this->start("scripts"); ?>
<?php $this->stop() ?>