<!doctype html>
<html lang="pt-br">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="../../../../favicon.ico">
    <title>Template de capa, usando Bootstrap.</title>
    <!-- Principal CSS do Bootstrap -->
    <link href="../../bootstrap/css/bootstrap.min.css" rel="stylesheet">

    <!-- Estilos customizados para esse template -->
    <style>
    /*
    * Globals
    */

    /* Links */
        a,
        a:focus,
        a:hover {
        color: #fff;
    }

    /* Botão padrão customizado */
        .btn-secondary,
        .btn-secondary:hover,
        .btn-secondary:focus {
        color: #333;
        text-shadow: none; /* Previne herença do `body` */
        background-color: #fff;
        border: .05rem solid #fff;
    }


    /*
    * Estrutura base
    */

    html,
    body {
        height: 100%;
        background-color: #333;
    }

    body {
        display: -ms-flexbox;
        display: flex;
        color: #fff;
        text-shadow: 0 .05rem .1rem rgba(0, 0, 0, .5);
        box-shadow: inset 0 0 5rem rgba(0, 0, 0, .5);
    }

    .cover-container {
        max-width: 42em;
    }


    /*
    * Cabeçalho
    */
    .masthead {
        margin-bottom: 2rem;
    }

    .masthead-brand {
        margin-bottom: 0;
    }

    .nav-masthead .nav-link {
        padding: .25rem 0;
        font-weight: 700;
        color: rgba(255, 255, 255, .5);
        background-color: transparent;
        border-bottom: .25rem solid transparent;
    }

    .nav-masthead .nav-link:hover,
    .nav-masthead .nav-link:focus {
        border-bottom-color: rgba(255, 255, 255, .25);
    }

    .nav-masthead .nav-link + .nav-link {
        margin-left: 1rem;
    }

    .nav-masthead .active {
        color: #fff;
        border-bottom-color: #fff;
    }

    @media (min-width: 48em) {
    .masthead-brand {
        float: left;
    }
    .nav-masthead {
        float: right;
    }
    }

    /*
    * Capa
    */
    .cover {
        padding: 0 1.5rem;
    }
    .cover .btn-lg {
        padding: .75rem 1.25rem;
        font-weight: 700;
    }

    /*
    * Footer
    */
    .mastfoot {
        color: rgba(255, 255, 255, .5);
    }

    </style>
  </head>

  <body class="text-center">

    <div class="cover-container d-flex w-100 h-100 p-3 mx-auto flex-column">
      <header class="masthead mb-auto">
        <div class="inner">
          <h3 class="masthead-brand">Bosch</h3>
          <nav class="nav nav-masthead justify-content-center">
            <a class="nav-link active" href="../../index.php">Login</a>
            <a class="nav-link" href="#"></a>
            <a class="nav-link" href="#"></a>
          </nav>
        </div>
      </header>

      <main role="main" class="inner cover">
        <h1 class="cover-heading">Parece que algo deu errado.</h1>
        <p class="lead">A senha e/ou usuário informado não forma encontrados em nossa base de dados. Para criação de usuário clique <a class="" href="../usuario/usuario-form.php">aqui</a>.</p>
        <p class="lead">
          <a href="../../index.php" class="btn btn-lg btn-secondary">Voltar</a>
        </p>
      </main>

      <footer class="mastfoot mt-auto">
        <div class="inner">
          </div>
      </footer>
    </div>

    <!-- Principal JavaScript do Bootstrap
    ================================================== -->
    <!-- Foi colocado no final para a página carregar mais rápido -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script>window.jQuery || document.write('<script src="../../assets/js/vendor/jquery-slim.min.js"><\/script>')</script>
    <script src="../../popper/js/vendor/popper.min.js"></script>
    <script src="../../bootstrap/js/bootstrap.min.js"></script>
  </body>
</html>
