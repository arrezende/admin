<?php $this->layout("_theme"); ?>
<?php require __DIR__ . "/widgets/tag/sidebar.php"; ?>
<section class="dash_content_app">
    <header class="dash_content_app_header">
        <h2 class="icon-pencil-square-o">Tags</h2>
        <form method="POST" action='<?= url('busca/tags'); ?>' class="app_search_form ajax_off">
            <input type="text" name="s" list="browsers" placeholder="Pesquisar Tags:">
            <button class="icon-search icon-notext"></button>
        </form>
        <!-- <a class="icon-plus-circle btn btn-green" href="categorias/adicionar">Nova Categoria</a> -->
    </header>

    <div class="dash_content_app_box">
        <section class="app_blog_categories">
            <?php if (!is_null($tags)) : ?>
                <?php foreach ($tags as $tag) : ?>
                    <article class="radius">
                        <div>
                            <span><?= $tag->id; ?></span>
                        </div>
                        <div class="info taginfo">
                            <form action="tags/editar/<?= $tag->id; ?>" method="post" data-confirm="true">
                                <div class="title_box">
                                    <h3 class="title"><b>Tag:</b> <input type="text" name="tag_name" value="<?= $tag->tag_name; ?>"></h3>
                                    <span class="title"><b>Código:</b> <input type="text" name="tag_cod" value="<?= $tag->tag_cod; ?>"></span>
                                    <input type="hidden" name="type" value="edit">
                                    <button class="icon-pencil btn btn-blue" type="submit">Editar</button>
                                </div>
                            </form>
                            
                            <div class="actions">
                                <!-- <button class="icon-pencil btn btn-blue" onclick="editar(</?= url("tags/editar/$tag->id"); ?>)">Editar</button> -->
                                <?php if ($tag->status != 'trash') : ?>
                                    <button class="icon-trash-o btn btn-red" onclick="deletar('<?= url("tags/remover/$tag->id"); ?>')">Deletar</button>
                                <?php else : ?>
                                    <button class="icon-check btn btn-green" onclick="ativar('<?= url("tags/ativar/$tag->id"); ?>')">Ativar</button>
                                <?php endif; ?>
                            </div>
                        </div>

                    </article>
                <?php endforeach; ?>
            <?php else : ?>
                <h3>Nenhuma tag cadastrada</h3>
            <?php endif; ?>
            <?= $pagination; ?>
        </section>
    </div>
</section>
<?php $this->start("scripts"); ?>
<?php $this->stop() ?>