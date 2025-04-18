<?php
require_once __DIR__ . '/../models/ContactModel.php'; // Corrected path

class ContactController {
    private $model;

    public function __construct() {
        $this->model = new ContactModel();
    }

    public function createMessage($data) {
        return $this->model->create($data);
    }

    public function getAllMessages() {
        return $this->model->readAll();
    }

    public function deleteMessage($id) {
        return $this->model->delete($id);
    }

    public function updateMessage($id, $data) {
        return $this->model->update($id, $data);
    }
}
?>
