<!DOCTYPE html>
<html lang="pt-Br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon"  href="<?=themes("/assets/favicon.ico");?>"/>
    <link rel="stylesheet" href="<?=themes("assets/styles/style.css");?>"/>
    <title>dev.finances$</title>
</head>

<body>
    <div class="ajax_load">
        <div class="c-loader"></div>
    </div>
<!--    --><?php //if(!$error): ?>
        <header>
            <img src="<?=themes("/assets/logo.svg");?>" alt="logo Dev Finances">
        </header>
<!--    --><?php //endif?>
    <main class="container">

        <?= $v->section("content") ;?>
    </main>
    <div class="modal-overlay">
        <div class="modal">
            <div class="form">
                <h2>Nova Transação</h2>
                <form id="form-modal" action="<?=url("/"); ?>"  method="post" enctype="multipart/form-data">
                    <?=csrf_input();?>
                    <div class="input-group">
                        <label for="description"></label>
                        <input type="text" id="description" name="description" placeholder="Descrição">
                    </div>
                    <div class="input-group">
                        <label for="value"></label>
                        <input type="number" step="0.01" id="value" name="value" placeholder="0,00">
                        <small>Use o sinal - (negativo) para as despesas e ,(virgula) para casas decimais </small>
                    </div>
                    <div class="input-group">
                        <label for="date"></label>
                        <input type="date" id="description" name="date">
                    </div>
                    <div class="input-group actions">
                       <a href="#" class="button cancel">Cancelar</a>
                       <button>Salvar</button>
                    </div>                    
                </form>
            </div>
        </div>
    </div>
    <footer>
        <small>dev.finances$</small>
    </footer>
    <script src="<?=url("/shared/script/jquery.min.js");?>"></script>
    <script src="<?=url("/shared/script/jquery.form.js");?>"></script>
    <script src="<?=url("/shared/script/jquery-ui.js");?>"></script>
    <script src="<?=themes("/script.js");?>"></script>
</body>
</html>