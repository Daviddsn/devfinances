<tr>
    <td class="description"><?=$item->description;?></td>
    <td class="<?= ($item->amount < 0 ? "expense":"income" )?>"><?=format_money($item->amount)?></td>
    <td class="date"><?=date_fmt($item->date,"d/m/Y")?></td>
    <td class="remove" >
        <img src="<?=themes("/assets/minus.svg");?>" alt="Remover Transação"
             data-action="<?=url("/delete")?>"
             data-id ="<?=$item->id;?>"
        >
    </td>
</tr>
