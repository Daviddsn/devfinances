<?php $v->layout("_theme");?>
<section id="balance">
    <h2 class="sr-only">Balanço</h2>
    <div class="card">
        <h3>
            <span>Entradas</span>
            <img src="<?=themes("/assets/income.svg");?>" alt="Image Entrada">
        </h3>
        <p id="incomeDisplay"><?=format_money(($balance->income ?? 0));?></p>
    </div>
    <div class="card">
        <h3>
            <span>Saídas</span>
            <img src="<?=themes("/assets/expense.svg");?>" alt="Imagem de Saídas">
        </h3>
        <p id="expenseDisplay"><?=format_money(($balance->expense ?? 0));?></p>
    </div>
    <div class="card total">
        <h3>
            <span>Total</span>
            <img src="<?=themes("/assets/total.svg");?>" alt="Image de Total">
        </h3>
        <p id="totalDisplay"><?=format_money(($balance->total ?? 0));?></p>
    </div>
</section>

<section id="transaction">
    <h2 class="sr-only">Transações</h2>
    <a href="#" class="button new"> + Nova Transação</a>
    <table id="table-data">
        <thead>
        <tr>
            <th>Descrição</th>
            <th>Valor</th>
            <th>Data</th>
            <th></th>
        </tr>
        </thead>
        <tbody>

            <?php
            if(!empty($transactions)):
                foreach ($transactions as $item):
                    $v->insert("transaction",["item"=> $item ]);
                endforeach;
            endif;
            ?>
        </tbody>
    </table>
</section>
