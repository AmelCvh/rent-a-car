<?php

namespace Core\Session;

// Tableau de session : Garde des informations comme le montant d'un panier

interface SessionInterface {

    public function get(string $key, $default = null);
    
    public function set(string $key, $value) : void;
     
    public function delete(string $key) : void;
    
    public function has(string $key) : bool;
}