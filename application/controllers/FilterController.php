<?php
require_once 'BaseController.php';
require_once APPLICATION_PATH . '/models/Filter.php';

class FilterController extends BaseController
{

    public function addfilteruiAction ()
    {}

    public function enableipuiAction ()
    {
        $filterModel = new Filter();
        $data = $filterModel->fetchAll()->toArray();
        $this->view->items = $data;
    }
    
    // 添加过滤IP
    public function addfilterAction ()
    {
        $ip = $this->getRequest()->getParam('ip');
        if (empty($ip)) {
            $this->view->info = 'IP不能为空';
            $this->_forward('err', 'global');
            return;
        } else {
            $filterModel = new Filter();
            $data = array(
                    'ip' => $ip
            );
            $filterModel->insert($data);
            $this->view->info = '添加成功';
            $this->_forward('ok', 'global');
        }
    }
    
    // 启用已过滤的IP
    public function enableipAction ()
    {
        $id = $this->getRequest()->getParam('id');
        $filterModel = new Filter();
        $data = $filterModel->find($id)->toArray();
        if (count($data) > 0) {
            $where = "id=$id";
            $filterModel->delete($where);
            $this->view->info = '启用成功';
            $this->render('ok');
        } else {
            $this->view->info = '参数错误';
            $this->_forward('err', 'global');
        }
    }
}

?>