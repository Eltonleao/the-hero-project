<?php

class User_Model extends Model{

    public function getLicoes(){
        $consulta = $this->consulta("SELECT *
        FROM licao
        WHERE deletado = 0
        ");

        return $consulta;
    }

    public function getLicao($id){

    }
}