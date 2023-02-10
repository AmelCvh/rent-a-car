<?php

namespace Core\Framework\Router;

    class Route
    {
        private string $name;
        private $callable;
        private array $params;

        public function __construct(string $name, $callable, array $params)
        {
            $this->name = $name;
            $this->callable = $callable;
            $this->params = $params;
        }

        /**
         * Get the value of name
         */ 
        public function getName()
        {
            return $this->name;
        }

        /**
         * Get the value of callable
         */ 
        public function getCallBack()
        {
            return $this->callable;
        }

        /**
         * Get the value of params
         */ 
        public function getParams()
        {
            return $this->params;
        }
    }
?>
