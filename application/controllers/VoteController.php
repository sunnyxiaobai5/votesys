<?php
require_once 'BaseController.php';
require_once APPLICATION_PATH . '/models/Item.php';
require_once APPLICATION_PATH . '/models/VoteLog.php';
require_once APPLICATION_PATH . '/models/Filter.php';

class VoteController extends BaseController
{

    public function indexAction ()
    {}

    public function additemuiAction ()
    {}

    public function delitemuiAction ()
    {
        $itemModel = new Item();
        $items = $itemModel->fetchAll()->toArray();
        $this->view->items = $items;
    }
    
    // ���ͶƱѡ��
    public 

    function additemAction ()
    {
        $name = $this->getRequest()->getParam('name');
        if (empty($name)) {
            $this->view->info = 'ѡ��������Ϊ�գ�����';
            $this->_forward('err', 'global');
            return;
        }
        $des = $this->getRequest()->getParam('description');
        $vote_count = $this->getRequest()->getParam('vote_count');
        $vote_count = empty($vote_count) ? 0 : $vote_count;
        $data = array(
                'name' => $name,
                'description' => $des,
                'vote_count' => $vote_count
        );
        
        $model = new Item();
        $model->insert($data);
        $this->view->info = '�ύ�ɹ�';
        $this->forward('ok', 'global');
    }
    
    // ɾ��ͶƱѡ��
    public function delitemAction ()
    {
        $id = $this->getRequest()->getParam('id');
        $itemModel = new Item();
        $item = $itemModel->find($id)->toArray();
        if (count($item) > 0) {
            $where = "id=$id";
            $itemModel->delete($where);
            $this->view->info = 'ɾ���ɹ�';
            $this->render('ok');
        } else {
            $this->view->info = '��������';
            $this->_forward('err', 'global');
        }
    }
    
    // ͶƱ
    public function voteAction ()
    {
        $ip = $_SERVER['SERVER_ADDR'];
        // �鿴��IP�Ƿ񱻽�ֹͶƱ
        $where = "ip='$ip'";
        $filterModel = new Filter();
        $filter_data = $filterModel->fetchAll($where)->toArray();
        if (count($filter_data) > 0) {
            $this->view->info = '�㱻��ֹͶƱ';
            $this->forward('err', 'global');
            return;
        }
        
        $ip_data = $id = $this->getRequest()->getParam('id');
        $today = date('Ymd');
        // ��ͶƱ��ʷ���и���ip��ʱ���ѯ�Ƿ��и�ip��ַ�����ͶƱ��¼
        $where = "ip='$ip' and vote_date=$today";
        $voteLogModel = new VoteLog();
        $vote_data = $voteLogModel->fetchAll($where);
        // ���м�¼��������ͶƱ�����˳�
        if (count($vote_data) >= 1) {
            $this->view->info = '�������ͶƱ������������';
            $this->render('voted');
            return;
        } else {
            $itemModel = new Item();
            $item = $itemModel->find($id);
            // ����ȡ��id��item�����м�¼��ִ�к����������������Ѵ���
            if (count($item) > 0) {
                $vote_count = $item[0]['vote_count'];
                $vote_count ++;
                $where = "id=$id";
                $data = array(
                        'vote_count' => $vote_count
                );
                // ����ͶƱ��
                $itemModel->update($data, $where);
                
                $data = array(
                        'ip' => $ip,
                        'vote_date' => $today,
                        'item_id' => $id
                );
                // ��ͶƱ��־���м�¼��ͶƱ��¼
                $voteLogModel->insert($data);
                $this->view->info = 'ͶƱ�ɹ�!!';
                $this->render('vote');
            } else {
                $this->view->info = '��������!';
                $this->_forward('err', 'global');
            }
        }
    }
}
?>
