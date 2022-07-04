<?php

namespace GetWith\CoffeeMachine\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use GetWith\CoffeeMachine\Interfaces\IDataDrinkSales;
use GetWith\CoffeeMachine\Interfaces\IDataDrink;


class MoneyEarnCommand extends Command
{
    protected static $defaultName = 'app:money-earn';
    private $drinkSales = [];
    private $drinkNames = [];

    public function __construct(IDataDrink $mdlGetDrink=null, IDataDrinkSales  $mdlDrinksServed=null)
    {
        parent::__construct(MoneyEarnCommand::$defaultName);

        if (!is_null($mdlGetDrink))    //  Evitar fallo de test
            $this->drinkNames = json_decode($mdlGetDrink->getDrinkNames());

        if (!is_null($mdlDrinksServed))    //  Evitar fallo de test
            $this->drinkSales = (Array)json_decode($mdlDrinksServed->getDrinkSales());
    }
   
    
    protected function configure(): void
    {
        $drinkTypes =  implode(", ",$this->drinkNames);

        $this->addArgument(
            'drink-type',
            InputArgument::OPTIONAL,
            'The drinks to obtain money earned. ('.$drinkTypes.')',
            'all'
        );

    }

    private function validateDrinkName(&$pDrinkName)
    {
        $found = in_array(strtolower($pDrinkName), $this->drinkNames);
        return $found;
    }


    private function getMoneyEarnByDrink($pDrinkName='all')
    {
        $arrMoneyEarnedByDrink = [];

        $pDrinkName= strtolower($pDrinkName);

        foreach($this->drinkSales as $drink)
        {
            $drink = (Array)$drink;
            if ($pDrinkName=='all' || $pDrinkName==$drink['drink'])
            {
                if (!isset($arrMoneyEarnedByDrink[$drink['drink']]))
                {
                    $arrMoneyEarnedByDrink[$drink['drink']][0] = 0;
                    $arrMoneyEarnedByDrink[$drink['drink']][1] = 0;
                }
                $arrMoneyEarnedByDrink[$drink['drink']][0] += 1;
                $arrMoneyEarnedByDrink[$drink['drink']][1] += $drink['money'];
            }
        }

        return $arrMoneyEarnedByDrink; 
    }


    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $drinkType = $input->getArgument('drink-type');
        if (!$this->validateDrinkName($drinkType)) 
        {
            $output->writeln('');
            if (strtolower($drinkType) != "all")
                $output->writeln('The drink type should be '.implode(" or ",$this->drinkNames).'.');
            else
                $output->writeln('You can select a drink type, and it should be '.implode(" or ",$this->drinkNames).'.');
            $drinkType = "all";
        } 

            $dataEarnedDrinks = $this->getMoneyEarnByDrink($drinkType);

            if (count($dataEarnedDrinks)>0)
            {
                $output->writeln("");
                $output->writeln("Drink \t Total \t Money");
                $output->writeln("=======================");
                foreach($dataEarnedDrinks as $key=>$value)
                {
                    $output->writeln("$key \t ".$value[0]." \t ".$value[1]);
                }
                $output->writeln('');
            }
            else
            {
                $output->writeln("Not drinks sold of type ".$drinkType);
            }

        return 0;
    }
    
}
