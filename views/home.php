
<div class="jumbotron">
    <div class="container">
        <h1>Concurso Furgovw <?=$options['year']?></h1>
        <p>Votaciones de los mejores hilos de furgovw.</p>
    </div>
</div>

<?php if ($options['voting-opened'] == 'si'): ?>
    <div class="alert alert-success">La votación está abierta</div>
<?php else: ?>
    <div class="alert alert-danger">La votación está cerrada</div>
<?php endif ?>