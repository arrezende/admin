<?php require __DIR__ . "/sidebar.php"; ?>

<section class="dash_content_app">
    <header class="dash_content_app_header">
        <h2 class="icon-coffee">Control</h2>
    </header>

    <div class="dash_content_app_box">
        <div class="app_control_home">
            <section class="app_control_home_stats">
                <article class="radius">
                    <h4 class="icon-user">Assinantes</h4>
                    <p>244</p>
                </article>

                <article class="radius">
                    <h4 class="icon-user-plus">Por 30 dias</h4>
                    <p>33</p>
                </article>

                <article class="radius">
                    <h4 class="icon-calendar-check-o">Este mês:</h4>
                    <p>R$ 3.200,00</p>
                </article>

                <article class="radius">
                    <h4 class="icon-retweet">Recorrência:</h4>
                    <p>R$ 20.500,00</p>
                </article>
            </section>


            <section class="app_control_subs radius">
                <h3 class="icon-heartbeat">Assinaturas:</h3>
                <?php for ($i = 0; $i < 10; $i++): ?>
                    <article class="subscriber">
                        <h5>22.10.18 22h - Robson V. Leite</h5>
                        <p>PRO - R$ 5,00/mês</p>
                        <p>Concluída</p>
                    </article>
                <?php endfor; ?>
            </section>
        </div>
    </div>
</section>