<?php
require_once 'BaseController.php';
require_once APPLICATION_PATH . '/models/Item.php';

class IndexController extends BaseController
{

    public function indexAction ()
    {
        $model = new Item();
        $data = $model->fetchAll()->toArray();
        $this->view->items = $data;
    }
}
?>
