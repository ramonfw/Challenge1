<?php
	namespace GetWith\CoffeeMachine\Models;

	use GetWith\CoffeeMachine\Interfaces\IDataDrink;

	class MdlDrink implements IDataDrink
	{
		private $vDrinkList;

		function __construct($vScope="all")
		{
			if ($vScope="all")
			{
				$jsonDrinkData = file_get_contents('.\src\Models\jsonDrinkData.json');
				$this->vDrinkList= (Array)json_decode($jsonDrinkData);
			}
		}

		public function getDrinkList()
		{
			return json_encode($this->vDrinkList);
		}

		public function getDrinkNames()
		{
			$vLista=[];
			foreach($this->vDrinkList as $key=>$item)
			{
				$vLista[]=$key;
			}
			return json_encode($vLista);
		}


		public function getDrinkByName($vDrinkName)
		{
			$vLista=[];
			foreach($this->vDrinkList as $key=>$item)
			{
				if ($vDrinkName==$key)
				{
					$vLista[]=$item;
				}
			}
			return json_encode($vLista);
		}

	}

?>
