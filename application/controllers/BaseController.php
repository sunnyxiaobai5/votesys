<?php

// �������Ϊ����Ҫ�������ݿ��Controller��ĸ���
class BaseController extends Zend_Controller_Action
{

    public function init ()
    {
        $url = constant("APPLICATION_PATH") . DIRECTORY_SEPARATOR . 'configs' .
                 DIRECTORY_SEPARATOR . 'application.ini';
        $config = new Zend_Config_Ini($url, 'mysql');
        $db = Zend_Db::factory($config->db);
        $db->query('SET NAMES gbk');
        Zend_Db_Table::setDefaultAdapter($db);
    }
}
?>