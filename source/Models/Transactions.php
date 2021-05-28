<?php


namespace Source\Models;


use Source\Core\Model;

class Transactions extends Model
{
    public function __construct()
    {
        parent::__construct("transactions", ["id"], ["description","amount","date"]);
    }


    public function save() :bool
    {
        if(!$this->required()){
            $this->message->warning("Descrição, Valor e Data são obrigatorios");
            return false;
        }


        /** User Update */
        if (!empty($this->id)){
            $userID = $this->id;


            $this->update($this->safe(),"id =:id","id={$userID}");
            if ($this->fail){
                $this->message->error("Erro ao atualizar , verificar dados");
                return false;
            }
        }

        /** User Create */

        $userID = $this->create($this->safe());
        if ($this->fail){
            $this->message->error("Erro ao cadastrar !");
            return false;

        }

        $this->data = ($this->findById($userID))->data();
        return true;

    }

    public function balance() :object
    {
        $balance = new \stdClass();
        $balance->income = 0;
        $balance->expense = 0;
        $balance->total = 0;

        $find = $this->find("","",
                "(SELECT SUM(amount) FROM transactions) AS total,
                (SELECT SUM(amount) FROM transactions WHERE  amount > 0 ) AS income,
                (SELECT SUM(amount) FROM transactions WHERE  amount < 0 ) AS expense",
            )
            ->fetch();
        if($find){
            $balance->income = floatval(($find->income ?? 0));
            $balance->expense = floatval(($find->expense ?? 0));
            $balance->total = ( $balance->income + $balance->expense);
        }

        return $balance;
    }

}