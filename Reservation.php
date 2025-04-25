<?php
class Reservation
{
    public $id;
    public $nom;
    public $prenom;
    public $age;
    public $depuis;
    public $jusqua;

    public function __construct($id, $nom, $prenom, $age, $depuis, $jusqua) {
        $this->id     = $id;
        $this->nom    = $nom;
        $this->prenom = $prenom;
        $this->age    = $age;
        $this->depuis = $depuis;
        $this->jusqua = $jusqua;
    }

    // Getters
    public function getId() { return $this->id; }
    public function getNom() { return $this->nom; }
    public function getPrenom() { return $this->prenom; }
    public function getAge() { return $this->age; }
    public function getDepuis() { return $this->depuis; }
    public function getJusqua() { return $this->jusqua; }

    // Setters
    public function setId($id) { $this->id = $id; }
    public function setNom($nom) { $this->nom = $nom; }
    public function setPrenom($prenom) { $this->prenom = $prenom; }
    public function setAge($age) { $this->age = $age; }
    public function setDepuis($depuis) { $this->depuis = $depuis; }
    public function setJusqua($jusqua) { $this->jusqua = $jusqua; }

    // Méthode pour transformer l'objet en tableau associatif
    public function toArray() {
        return [
            'id'     => $this->id,
            'nom'    => $this->nom,
            'prenom' => $this->prenom,
            'age'    => $this->age,
            'depuis' => $this->depuis,
            'jusqua' => $this->jusqua
        ];
    }

    // Méthode statique pour créer un objet à partir d'un tableau
    public static function fromArray($data) {
        return new self(
            $data['id'],
            $data['nom'],
            $data['prenom'],
            $data['age'],
            $data['depuis'],
            $data['jusqua']
        );
    }
}
