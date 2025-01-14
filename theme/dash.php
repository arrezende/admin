<?php $this->layout("_theme");?>
<div class="dash_content_sidebar">
    <h3 class="icon-asterisk">dashboard</h3>
    <p class="dash_content_sidebar_desc">Tenha insights poderosos para escalar seus resultaods...</p>

    
</div>

<section class="dash_content_app">
    <header class="dash_content_app_header">
        <h2 class="icon-home">Dash</h2>
    </header>

    <div class="dash_content_app_box">
        <section class="app_dash_home_stats">
            <article class="control radius">
                <h4 class="icon-coffee">Produtos</h4>
                <p><b>Produtos Cadastrados:</b> <?=$products;?></p>
            </article>

            <article class="blog radius">
                <h4 class="icon-pencil-square-o">Categorias</h4>
                
                <p><b>Categorias Cadastradas:</b> <?=$categories;?></p>
            </article>

            <article class="blog radius">
                <h4 class="icon-pencil-square-o">Tags</h4>
                
                <p><b>Tags Cadastradas: <?=$tags;?></b> </p>
            </article>

            <article class="users radius">
                <h4 class="icon-user">Galerias</h4>
                <p><b>Total:</b> <?=$gallery?></p>
            </article>
        </section>        
    </div>
</section>
