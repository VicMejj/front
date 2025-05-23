<?php
require_once '../models/Wishlist.php';

class WishlistController {
    private $db;
    private $wishlistModel;

    public function __construct($db) {
        $this->db = $db;
        $this->wishlistModel = new Wishlist($db);
    }

    public function create(Wishlist $wishlist) {
        return $wishlist->create();
    }

    public function update(Wishlist $wishlist) {
        return $wishlist->update();
    }

    public function delete($item_id) {
        $wishlist = new Wishlist($this->db);
        $wishlist->setItemId($item_id);
        return $wishlist->delete();
    }

    public function getWishlistsByUser($user_id) {
        return $this->wishlistModel->getByUser($user_id);
    }
}
?>