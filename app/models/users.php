<?php
namespace Model;
class Users extends \my_framework\Model{
	function getTables(){
		$query = $this::$pdo->prepare('show tables');
		$query->execute();
		while($row = $query->fetch()){
			$result[] = $row;
		}
		return $result[0]->Tables_in_bet;
	}
}