<?php

namespace Core\Framework\Validator;


class ValidatorError
{
    private string $key;
    private string $rule;
    private array $message = [
        'required' => "Le champ %s est requis.",
        'email' => "Le champ %s doit etre un email valide.",
        'strMin' => "Le champ %s n'atteint pas le minimum de caractères requis.",
        'strMax' => "Le champ %s depasse le nombre de caractères autorisées.",
        'confirm' => "Les mot de passe ne sont pas identiques.",
        'unique' => "La valeur du champ %s est déjà connu du système."
    ];

    public function __construct(string $key, string $rule)
    {
        $this->key = $key;
        $this->rule = $rule;
    }

    public function toString(): string
    {
        if (isset($this->message[$this->rule])) {
            if ($this->key === 'mdp') {
                return sprintf($this->message[$this->rule], 'mot de passe');
            } else {
                return sprintf($this->message[$this->rule], $this->key);
            }
        }

        return $this->rule;
    }
}