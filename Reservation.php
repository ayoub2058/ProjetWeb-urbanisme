<?php
class Reservation
{
    public $id;
    public $nom;
    public $prenom;
    public $age;
<<<<<<< Updated upstream
    public $depuis;
    public $jusqua;

    public function __construct($id, $nom, $prenom, $age, $depuis, $jusqua) {
=======
    public $duree;

    public function __construct($id, $nom, $prenom, $age, $duree) {
>>>>>>> Stashed changes
        $this->id     = $id;
        $this->nom    = $nom;
        $this->prenom = $prenom;
        $this->age    = $age;
<<<<<<< Updated upstream
        $this->depuis = $depuis;
        $this->jusqua = $jusqua;
=======
        $this->duree  = $duree;
>>>>>>> Stashed changes
    }

    // Getters
    public function getId() { return $this->id; }
    public function getNom() { return $this->nom; }
    public function getPrenom() { return $this->prenom; }
    public function getAge() { return $this->age; }
<<<<<<< Updated upstream
    public function getDepuis() { return $this->depuis; }
    public function getJusqua() { return $this->jusqua; }
=======
    public function getDuree() { return $this->duree; }
>>>>>>> Stashed changes

    // Setters
    public function setId($id) { $this->id = $id; }
    public function setNom($nom) { $this->nom = $nom; }
    public function setPrenom($prenom) { $this->prenom = $prenom; }
    public function setAge($age) { $this->age = $age; }
<<<<<<< Updated upstream
    public function setDepuis($depuis) { $this->depuis = $depuis; }
    public function setJusqua($jusqua) { $this->jusqua = $jusqua; }
=======
    public function setDuree($duree) { $this->duree = $duree; }
>>>>>>> Stashed changes

    // Méthode pour transformer l'objet en tableau associatif
    public function toArray() {
        return [
            'id'     => $this->id,
            'nom'    => $this->nom,
            'prenom' => $this->prenom,
            'age'    => $this->age,
<<<<<<< Updated upstream
            'depuis' => $this->depuis,
            'jusqua' => $this->jusqua
=======
            'duree'  => $this->duree
>>>>>>> Stashed changes
        ];
    }

    // Méthode statique pour créer un objet à partir d'un tableau
    public static function fromArray($data) {
        return new self(
            $data['id'],
            $data['nom'],
            $data['prenom'],
            $data['age'],
<<<<<<< Updated upstream
            $data['depuis'],
            $data['jusqua']
        );
    }
}
=======
            $data['duree']
        );
    }
}
?>
>>>>>>> Stashed changes
