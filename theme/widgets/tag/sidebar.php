<div class="dash_content_sidebar">
    <h3 class="icon-asterisk">dashboard/tag</h3>
    <p class="dash_content_sidebar_desc">Gerencie as tags do site...</p>

    <form action="tags/adicionar" class="app_form mt-5" method="post">

        <label class="label">
            <span class="legend">* Nome Tag:</span>
            <input type="text" name="tag_name" placeholder="O nome da sua tag" value="" required />
        </label>

        <label class="label">
            <span class="legend">Código Tag:</span>
            <input type="text" name="tag_cod" placeholder="O código da sua tag" value="" />
        </label>

        <input type="hidden" name="type" value="new">
        <button class="btn btn-green icon-plus-square-o">Adicionar Tag</button>
    </form>
</div>