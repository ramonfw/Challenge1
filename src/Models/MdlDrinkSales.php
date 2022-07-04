<?php
	namespace GetWith\CoffeeMachine\Models;

	use GetWith\CoffeeMachine\Interfaces\IDataDrinkSales;

	class MdlDrinkSales implements IDataDrinkSales
	{
		private $drinksSales = [];

		function __construct($vScope="all")
		{
			if ($vScope="all")
			{
				$file_path = '.\src\Models\jsonDrinkSales.json';
				if (!file_exists($file_path))
				{
					file_put_contents($file_path,"[]");
				}
				$jsonDrinksSalesData = file_get_contents('.\src\Models\jsonDrinkSales.json');
				$this->drinksSales = json_decode($jsonDrinksSalesData,false);
			}

		}

		public function getDrinkSales()  
		{
			return json_encode($this->drinksSales);
		}

		public function getDrinkMoneyAll()  
		{
			$moneyEarned = 0;
			foreach($this->drinksSales as $key=>$item)
			{
				$moneyEarned += $item['money'];
			}
			return $moneyEarned;
		}

	}

?>
