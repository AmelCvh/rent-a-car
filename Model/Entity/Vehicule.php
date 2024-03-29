<?php

namespace Model\Entity;


use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="vehicule")
 * @ORM\Entity
 */
class Vehicule
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer", name="id")
     * @ORM\GeneratedValue(strategy="IDENTITY")
     *
     * @var string
     */
    private int $id;

    /**
     * @ORM\JoinColumn(referencedColumnName="id", name="marque_id", onDelete="CASCADE")
     * @ORM\ManyToOne(targetEntity="Marque", inversedBy="vehicules") 
     * @var Marque
     */

     private Marque $marque;

    /**
     * @ORM\Column(type="string", name="couleur",length="55")
     *
     * @var string
     */
    private string $couleur;




    /**
     * @ORM\Column(type="string", name="model",length="55")
     *
     * @var string
     */
    private string $model;

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }


    /**
     * Set couleur.
     *
     * @param string $couleur
     *
     * @return Vehicule
     */
    public function setCouleur($couleur)
    {
        $this->couleur = $couleur;

        return $this;
    }

    /**
     * Get couleur.
     *
     * @return string
     */
    public function getCouleur()
    {
        return $this->couleur;
    }

    /**
     * Set model.
     *
     * @param string $model
     *
     * @return Vehicule
     */
    public function setModel($model)
    {
        $this->model = $model;

        return $this;
    }

    /**
     * Get model.
     *
     * @return string
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * Set marque.
     *
     * @param \Model\Entity\Marque|null $marque
     *
     * @return Vehicule
     */
    public function setMarque(\Model\Entity\Marque $marque = null)
    {
        $this->marque = $marque;

        return $this;
    }

    /**
     * Get marque.
     *
     * @return \Model\Entity\Marque|null
     */
    public function getMarque()
    {
        return $this->marque;
    }
}
