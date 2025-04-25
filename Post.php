<?php
class Post
{
    public $id;
    public $titre;
    public $description;
    public $image;

    public function __construct($id, $titre, $description, $image)
    {
        $this->id = $id;
        $this->titre = $titre;
        $this->description = $description;
        $this->image = $image;
    }
}
?>
