
    <nav class="navbar navbar-default" role="navigation">
      <!-- Brand and toggle get grouped for better mobile display -->
      <div class="navbar-header">
        <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-ex1-collapse">
          <span class="sr-only">Toggle navigation</span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
        </button>
        <a class="navbar-brand" href="<?=Competition::BASE_URL?>">Concurso furgovw <?=$competition->year()?></a>
      </div>

      <!-- Collect the nav links, forms, and other content for toggling -->
      <div class="collapse navbar-collapse navbar-ex1-collapse">
        <ul class="nav navbar-nav">
            <li>
                <a href="<?=Competition::BASE_URL?>">Inicio</a>
            </li>
            <li>
                <a href="<?=Competition::BASE_URL?>?votar">Votar</a>
            </li>
            <li>
                <a href="<?=Competition::BASE_URL?>?resultado">Ver Resultado</a>
            </li>
            <?php if ($competition->is_admin()): ?>
                <li>
                    <a href="<?=Competition::BASE_URL?>?options">Opciones [Sólo moderadores]</a>
                </li>
            <?php endif ?>
        </ul>
      </div><!-- /.navbar-collapse -->
    </nav>