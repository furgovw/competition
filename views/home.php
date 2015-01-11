
<?php $stats = $this->competition->proposedTopicsStats(date('Y')); ?>
<div class="jumbotron">
	<div class="container">
		<h1>Concurso Furgovw <?=$this->competition->year()?></h1>
		<p>Votaciones de los mejores hilos de furgovw.</p>
	</div>
</div>

<div class="alert alert-info">
	<h2>Hilos propuestos para <?=date('Y')?></h2>
	Se han propuesto <?=$stats['totalProposedTopics']?> temas para las votaciones del mejor hilo de Furgovw de <?=date('Y')?>
	<ul>
	<?php foreach($stats['proposedTopics'] as $topic): ?>
	    <li><strong><a href="/index.php?topic=<?=$topic["topic_id"]?>"><?=$topic['subject']?></a></strong> (<?=$topic['category_name']?>)</li>
	<?php endforeach; ?>
	</ul>
</div>

<?php if (!empty($stats['yourProposedTopics'])): ?>
    <div class="alert alert-info">
        Tú has propuesto los siguientes hilos:
        <ul>
        <?php foreach($stats['yourProposedTopics'] as $topic): ?>
            <li><strong><a href="/index.php?topic=<?=$topic["topic_id"]?>"><?=$topic['subject']?></a></strong> (<?=$topic['category_name']?>)</li>
        <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<?php if ($this->competition->votingOpened()): ?>
	<div class="alert alert-success">La votación está abierta</div>
<?php else: ?>
	<div class="alert alert-danger">La votación está cerrada</div>
<?php endif ?>
