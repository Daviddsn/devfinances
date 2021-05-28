<?php


namespace Source\App;


use Source\Core\Controller;
use Source\Models\Transactions;
use stdClass;


class Web extends Controller
{
    public function __construct()
    {
        parent::__construct(__DIR__."/../../themes/".CONF_VIEW_THEME ."/");
    }

    public function home(?array $data):void
    {


        if (!empty($data['csrf'])) {

            if (!csrf_verify($data)) {
                $json['message'] = $this->message->error("Erro ao enviar, favor use o formulário")->render();
                echo json_encode($json);
                return;
            }

            $transData = filter_var_array($data,FILTER_SANITIZE_STRIPPED);
            if (in_array("",$transData)) {
                $json['message'] = $this->message->warning("Por favor, preencha todos os campos")->render();
                echo json_encode($json);
                return;
            }



            $transaction = new Transactions();
            $transaction->description = $transData["description"];
            $transaction->amount = $transData["value"];
            $transaction->date = $transData["date"];
            $transaction->save();

            $balance =(new Transactions())->balance();

            $balance->income = format_money($balance->income);
            $balance->expense = format_money($balance->expense);
            $balance->total = format_money($balance->total);

            $json['message'] = $this->message->success("Transação cadastrada com Sucesso")->render();
            $json['balance'] = $balance;
            $json["transaction"] = $this->view->render("transaction",[
                "item" => $transaction
            ]);



           echo json_encode($json);
            return;

        }



        $transactions = (new Transactions())
            ->find()
            ->fetch(true);


        $balance = (new Transactions())->balance();

        echo $this->view->render("content",[
            "transactions" => $transactions,
            "balance" => $balance
        ]);
    }

    public function delete(array $data):void
    {
        if(empty($data)){
            return;
        }



        $id =filter_var($data["id"],FILTER_VALIDATE_INT);
        $transaction = (new Transactions())->findById($id);




        if (!$transaction){
            $json['remove'] = false;
            echo json_encode($json);
            return;

        }

        $transaction->delete('id',$transaction->id);


        $balance =(new Transactions())->balance();
        $balance->income = format_money($balance->income);
        $balance->expense = format_money($balance->expense);
        $balance->total = format_money($balance->total);


        $json['balance'] = $balance;

        $json['remove'] = true;
        echo json_encode($json);

    }


    public function error(array $data):void
    {
        $error = new stdClass();


                $error->code = $data['errorcode'];
                $error->title = "Ooops. Conteúdo indispinível :/";
                $error->linkTitle = "Continuar Navegando";
                $error->message = "Sentimos muito, mas o conteúdo que você tentou acessar não existe, está indisponível no momento ou foi removido :/";
                $error->link = url_back();

        echo $this->view->render("error",[
            "error" => $error,
        ]);

    }
}