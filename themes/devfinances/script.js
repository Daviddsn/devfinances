


$(function () {
function updateDisplay(balance){
    $('#incomeDisplay').text(balance.income);
    $('#expenseDisplay').text(balance.expense);
    $('#totalDisplay').text(balance.total);
}


const transaction =  $("tbody");
$("body").on("click",".button:not('.back')" ,function (e){
    e.preventDefault();
    const modal = $(".modal-overlay");
    modal.toggleClass('active');
});

$("body").on("click","[data-action]" ,function (e){
    e.preventDefault();
    const data = $(this).data();
    const div = $(this).parent().parent();

    $.post(data.action,data,function (e){
        div.fadeOut(200);
        updateDisplay(e.balance)
    },"json");

});

// AJAX FORM JQUERY
$("form:not('.ajax_off')").submit(function (e) {
    e.preventDefault();
    const form = $(this);
    const load = $(".ajax_load");
    const flashClass = "ajax_response";
    let flash = $("." + flashClass);


    form.ajaxSubmit({
        url: form.attr("action"),
        type: "POST",
        dataType: "json",
        beforeSend: function () {
            load.fadeIn(200).css("display", "flex");
        },
        success: function (response) {
            //redirect
            if (response.redirect) {
                window.location.href = response.redirect;
            }

            //message
            if (response.message) {
                if (flash.length) {
                    flash.html(response.message).effect("bounce",{times:3},300)
                } else {
                    form.prepend("<div class='" + flashClass + "'>" + response.message + "</div>");
                }
            } else {
                flash.fadeOut(100);
            }


            if(response.transaction){
                transaction.prepend(response.transaction)
            }

            if(response.balance){
                console.log(response);
                updateDisplay(response.balance);
            }
        },
        complete: function () {
            load.fadeOut(200);
            form.each (function(){
                this.reset();
            });

        }
    });

})
});

//PAGE OF ERROR
