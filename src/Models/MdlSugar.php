<?php
	namespace GetWith\CoffeeMachine\Models;	
	use GetWith\CoffeeMachine\Interfaces\IDataSugar;

	class MdlSugar implements IDataSugar
	{
		public function getSugarList()
		{
			$jsonSugarData = file_get_contents('.\src\Models\jsonSugarData.json');
			return $jsonSugarData;
		}
	}

?>
