<?php
class Post
{
    public $id;
    public $titre;
    public $description;
    public $image;
    public $mail; // Ajout de l'attribut mail

    public function __construct($id, $titre, $description, $image, $mail)
    {
        $this->id = $id;
        $this->titre = $titre;
        $this->description = $description;
        $this->image = $image;
        $this->mail = $mail; // Initialiser le mail
    }
}
?>
