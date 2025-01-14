<div class="dash_content_sidebar">
    <h3 class="icon-asterisk">dashboard/Produtos</h3>
    <p class="dash_content_sidebar_desc">Aqui você gerencia todos os produtos e categorias...</p>

    <nav>
        <?php
        $nav = function ($icon, $href, $title) use ($getApp) {
            $active = ($getApp == $href ? "active" : null);
            return "<a class=\"icon-{$icon} radius {$active}\" href=\"dash.php?app={$href}\">{$title}</a>";
        };

        echo $nav("pencil-square-o", "produtos/home", "Produtos");
        echo $nav("bookmark", "produtos/categories", "Categorias");
        echo $nav("plus-circle", "produtos/post-create", "Novo Produto");
        ?>
    </nav>
</div>