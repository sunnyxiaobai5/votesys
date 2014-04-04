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
    
    // ��ӹ���IP
    public function addfilterAction ()
    {
        $ip = $this->getRequest()->getParam('ip');
        if (empty($ip)) {
            $this->view->info = 'IP����Ϊ��';
            $this->_forward('err', 'global');
            return;
        } else {
            $filterModel = new Filter();
            $data = array(
                    'ip' => $ip
            );
            $filterModel->insert($data);
            $this->view->info = '��ӳɹ�';
            $this->_forward('ok', 'global');
        }
    }
    
    // �����ѹ��˵�IP
    public function enableipAction ()
    {
        $id = $this->getRequest()->getParam('id');
        $filterModel = new Filter();
        $data = $filterModel->find($id)->toArray();
        if (count($data) > 0) {
            $where = "id=$id";
            $filterModel->delete($where);
            $this->view->info = '���óɹ�';
            $this->render('ok');
        } else {
            $this->view->info = '��������';
            $this->_forward('err', 'global');
        }
    }
}

?>