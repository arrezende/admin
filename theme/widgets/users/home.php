<?php require __DIR__ . "/sidebar.php"; ?>

<section class="dash_content_app">
    <header class="dash_content_app_header">
        <h2 class="icon-user">Usuários</h2>
        <form action="" class="app_search_form">
            <input type="text" name="s" placeholder="Pesquisar Usuário:">
            <button class="icon-search icon-notext"></button>
        </form>
    </header>

    <div class="dash_content_app_box">
        <section class="app_users_home">
            <?php for ($i = 0; $i < 12; $i++): ?>
                <article class="user radius">
                    <div class="cover"></div>
                    <p class="level icon-life-ring">ADMIN</p>
                    <h4>Robson V. Leite</h4>


                    <div class="info">
                        <p>cursos@upinside.com.br</p>
                        <p>Desde 20/10/2019 às 22h30</p>
                    </div>

                    <div class="actions">
                        <a class="icon-cog btn btn-blue" href="" title="">Gerenciar</a>
                    </div>
                </article>
            <?php endfor; ?>

            <nav class="paginator">
                <a class="paginator_item" title="Primeira página" href="">&lt;&lt;</a>
                <span class="paginator_item paginator_active">1</span>
                <a class="paginator_item" title="Página 2" href="">2</a>
                <a class="paginator_item" title="Página 3" href="">3</a>
                <a class="paginator_item" title="Página 4" href="">4</a>
                <a class="paginator_item" title="Última página" href="">&gt;&gt;</a>
            </nav>
        </section>
    </div>
</section>