<?php
namespace Controller;
use Model;
class Index extends \my_framework\Controller{
	function index(){
		$data['info'] = 'Blahblah';
		$data['name'] = 'Toto';
		$model = new \Model\Users();
		$data['dodo'] = $this->getParam('dodo');
		$data['tables'] = $model->getTables();
		$this->render('index', $data, get_class());
	}
}