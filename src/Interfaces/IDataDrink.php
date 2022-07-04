<?php
	namespace GetWith\CoffeeMachine\Interfaces;
	interface IDataDrink 
	{
		public function getDrinkList();

		public function getDrinkNames();

		public function getDrinkByName($vDrinkName);
	}

?>
