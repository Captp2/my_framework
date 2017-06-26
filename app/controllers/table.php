<?php
namespace Controller;
use Model;
class table extends \my_framework\Controller{
	function index(){
		$data['info'] = 'Blahblah';
		$data['name'] = 'Toto';
		$model = new \Model\Users();
		$dodo = $this->getParam('dodo');
		$data['dodo'] = $dodo;
		$data['tables'] = $model->getTables();
		$this->render('index', $data, get_class());
	}
}