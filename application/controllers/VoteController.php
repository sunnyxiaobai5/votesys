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
    
    // 添加投票选项
    public 

    function additemAction ()
    {
        $name = $this->getRequest()->getParam('name');
        if (empty($name)) {
            $this->view->info = '选项名不能为空！！！';
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
        $this->view->info = '提交成功';
        $this->forward('ok', 'global');
    }
    
    // 删除投票选项
    public function delitemAction ()
    {
        $id = $this->getRequest()->getParam('id');
        $itemModel = new Item();
        $item = $itemModel->find($id)->toArray();
        if (count($item) > 0) {
            $where = "id=$id";
            $itemModel->delete($where);
            $this->view->info = '删除成功';
            $this->render('ok');
        } else {
            $this->view->info = '参数错误';
            $this->_forward('err', 'global');
        }
    }
    
    // 投票
    public function voteAction ()
    {
        $ip = $_SERVER['SERVER_ADDR'];
        // 查看该IP是否被禁止投票
        $where = "ip='$ip'";
        $filterModel = new Filter();
        $filter_data = $filterModel->fetchAll($where)->toArray();
        if (count($filter_data) > 0) {
            $this->view->info = '你被禁止投票';
            $this->forward('err', 'global');
            return;
        }
        
        $ip_data = $id = $this->getRequest()->getParam('id');
        $today = date('Ymd');
        // 在投票历史表中根据ip和时间查询是否有该ip地址当天的投票记录
        $where = "ip='$ip' and vote_date=$today";
        $voteLogModel = new VoteLog();
        $vote_data = $voteLogModel->fetchAll($where);
        // 若有记录则提醒已投票，并退出
        if (count($vote_data) >= 1) {
            $this->view->info = '你今天已投票，请明天再来';
            $this->render('voted');
            return;
        } else {
            $itemModel = new Item();
            $item = $itemModel->find($id);
            // 若提取的id在item表中有记录才执行后续操作，否则提醒错误
            if (count($item) > 0) {
                $vote_count = $item[0]['vote_count'];
                $vote_count ++;
                $where = "id=$id";
                $data = array(
                        'vote_count' => $vote_count
                );
                // 更新投票数
                $itemModel->update($data, $where);
                
                $data = array(
                        'ip' => $ip,
                        'vote_date' => $today,
                        'item_id' => $id
                );
                // 在投票日志表中记录该投票记录
                $voteLogModel->insert($data);
                $this->view->info = '投票成功!!';
                $this->render('vote');
            } else {
                $this->view->info = '参数错误!';
                $this->_forward('err', 'global');
            }
        }
    }
}
?>
