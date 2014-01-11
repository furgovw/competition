
<div class="row">
    <div class="col-xs-12 col-sm-8 col-sm-offset-1">

        <h2>Votaciones concurso furgovw <?=$options['year']?></h2>
        <h4><?=$stats->totalMembers?> foreros están participando en la votación</h4>

        <div class="alert alert-info">Marca un hilo por cada categoría, debe ser el que quieres que sea el ganador.</div>
        <div class="alert alert-info">No tienes que decidir todos ahora, puedes guardar tus votos y volver más tarde.</div>
        <div class="alert alert-info">Haz click en los títulos de los hilos para verlos.</div>

        <form method="post" role="form">
            <input type="hidden" name="vote" value="yes">
            <?php $cont = 1; ?>
            <?php foreach($categories as $category): ?>
                <h3><?=$cont?>. <?=$category->name?></h3>
                    <?php foreach($topics as $topic): ?>
                        <?php if ($topic->category_id == $category->id): ?>
                            <div class="checkbox">
                                <label>
                                    <input <?php if (isset($votes[$topic->topic_id])): ?>checked<?php endif ?> type="radio" name="category<?=$category->id?>" value="<?=$topic->topic_id?>"> <a target="_blank" href="<?=$topic->url?>"><?=$topic->title?></a>
                                </label>
                            </div>
                        <?php endif ?>
                    <?php endforeach ?>
                    <?php $cont++;?>
            <?php endforeach ?>

            <button type="submit" class="btn btn-default">Guardar votos</button>
        </form>
    </div>
</div>
