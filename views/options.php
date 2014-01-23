<h1>Opciones concurso [sólo moderadores]</h1>

<h3>Opciones concurso <a class="options-toggle" href="#">Mostrar/Ocultar</a></h3>
<div class="row options">
	<div class="col-xs-12 col-sm-8">
		<form method="post" role="form">
			<?php foreach($this->competition->getOptions() as $index => $option): ?>
				<label for="<?=$index?>"><?=$index?></label> <input type="text" name="<?=$index?>" value="<?=$option?>"><br>
			<?php endforeach; ?>
			<label for="new-option">Nueva opción</label> <input type="text" name="new-option">
			<label for="new-option-value">Valor nueva opción</label> <input type="text" name="new-option-value"><br>

			<button type="submit" class="btn btn-default">Enviar</button>
		</form>
	</div>
</div>

<h3>Hilos más vistos <a class="most-viewed-toggle" href="#">Mostrar/Ocultar</a></h3>
<table class="table most-viewed">
	<thead>
		<tr>
			<th>Categoría</th>
			<th>Vistas</th>
			<th>Título</th>
			<th>Autor</th>
			<th>Fecha</th>
		</tr>
	</thead>

	<tbody>
	<?php foreach($most_viewed_topics as $category => $most_viewed_topic_group): ?>
		<?php foreach($most_viewed_topic_group as $most_viewed_topic): ?>
			<tr>
				<td><?=$category?></td>
				<td><?=$most_viewed_topic->num_views?></td>
				<td><a href="<?=$most_viewed_topic->url?>"><?=$most_viewed_topic->subject?></a></td>
				<td><?=$most_viewed_topic->poster_name?></td>
				<td><?=$most_viewed_topic->date?></td>
			</tr>
		<?php endforeach ?>
	<?php endforeach ?>
	</tbody>
</table>

<h3>Hilos ya añadidos a la votación <a class="added-topics-toggle" href="#">Mostrar/Ocultar</a></h3>
<div class="row added-topics">
<table class="hilos table table-bordered col-xs-12">

	<thead>
		<tr>
			<th>Categoría</th>
			<th>Título hilo</th>
			<th>Autor</th>
			<th>Fecha</th>
			<th>Borrar</th>
		</tr>
	</thead>

	<tbody>
	<?php foreach ($topics as $topic): ?>
		<tr>
			<td>
				<?= $topic->category ?>
			</td>
			<td>
				<a href="<?=$topic->url?>"><?= $topic->title ?></a>
			</td>
			<td>
				<?= $topic->author ?>
			</td>
			<td>
				<?= $topic->date ?>
			</td>
			<td>
				<a href="<?=$this->competition->getBaseUrl()?>?delete=<?=$topic->id?>">Borrar</a>
			</td>
		</tr>
	<?php endforeach ?>
	</tbody>

</table>
</div>
<br>

<h3>Añadir más hilos</h3>
<div class="row">
	<div class="col-xs-12 col-sm-8">
		<form method="post" role="form">
			<div class="form-group">
				<label for="categoria">Categoria</label>
				<select class="form-control" name="categoria">
					<?php foreach($categories as $category): ?>
						<option value="<?=$category->id?>"><?=$category->name?></option>
					<?php endforeach; ?>
				</select>
			</div>
			<?php for ($cont=1; $cont<=10; $cont++): ?>
				<div class="form-group">
					<label for="new-topic">Hilo <?=$cont?> (pega aquí el enlace al hilo)</label>
					<input type="text" class="form-control" name="new-topic[]">
				</div>
			<?php endfor; ?>

			<button type="submit" class="btn btn-default">Enviar</button>
		</form>
	</div>
</div>
