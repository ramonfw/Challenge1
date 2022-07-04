<?php

namespace GetWith\CoffeeMachine\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use GetWith\CoffeeMachine\Interfaces\IDataDrink;
use GetWith\CoffeeMachine\Interfaces\IDataSugar;


class MakeDrinkCommand extends Command
{
    protected static $defaultName = 'app:order-drink';
    private $drinkNames = [];
    private $drinksData = [];
    private $drinkSugars = [];

    public function __construct(IDataDrink $mdlGetDrink=null, IDataSugar  $mdlGetSugar=null)
    {
        parent::__construct(MakeDrinkCommand::$defaultName);

        if (!is_null($mdlGetSugar))    //  Evitar fallo de test
            $this->drinkSugars = (Array)json_decode($mdlGetSugar->getSugarList());

        if (!is_null($mdlGetDrink))    //  Evitar fallo de test
        {
            $this->drinksData = (Array)json_decode($mdlGetDrink->getDrinkList());
            $this->drinkNames = json_decode($mdlGetDrink->getDrinkNames());
        }
    }
   
    
    protected function configure(): void
    {
        $drinkTypes =  implode(", ",$this->drinkNames);

        $this->addArgument(
            'drink-type',
            InputArgument::REQUIRED,
            'The type of the drink. ('.$drinkTypes.')'
        );

        $this->addArgument(
            'money',
            InputArgument::REQUIRED,
            'The amount of money given by the user'
        );

        $sugarRange =  implode(",",$this->drinkSugars);

        $this->addArgument(
            'sugars',
            InputArgument::OPTIONAL,
            'The number of sugars you want. ('.$sugarRange.')',
            0
        );

        $this->addOption(
            'extra-hot',
            'e',
            InputOption::VALUE_NONE,
            $description = 'If the user wants to make the drink extra hot'
        );
    }

    private function validateDrinkName(&$pDrinkName)
    {
        $found = in_array(strtolower($pDrinkName), $this->drinkNames);
        return $found;
    }

    private function validateDrinkPrice($pDrinkName, $pDrinkPrice, &$pReturnMsg)
    {
        $pReturnMsg = "";
        $pDrinkName=strtolower($pDrinkName);
        $correctPrice = $this->drinksData[$pDrinkName] == $pDrinkPrice;
        if (!$correctPrice)
            $pReturnMsg = 'The '.$pDrinkName.' costs '.$this->drinksData[$pDrinkName].'.';

        return $correctPrice; 
    }

    private function validateDrinkSugar($pDrinkSugars, &$pReturnMsg)
    {
        $pReturnMsg = '';
        $correctSugar = in_array($pDrinkSugars, $this->drinkSugars);

        if ($correctSugar)
        {
            if ($pDrinkSugars>0)
                $pReturnMsg = ' with ' .$pDrinkSugars . ' sugars (stick included)';
        }
        else
        {
            $pReturnMsg = 'The number of sugars should be between '.$this->drinkSugars[0].' and '.($this->drinkSugars[count($this->drinkSugars)-1]).'.';
        }

        return $correctSugar;
    }

    private function saveDrinkServed($pDrinkName, $pDrinkPrice, $pDrinkSugar, $pExtraHot, &$pReturnMsg = "")
    {
        $pReturnMsg = "";

        $arrReturnMsg = [[
                            "drink"=>strtolower($pDrinkName),
                            "money"=>$pDrinkPrice,
                            "sugar"=>$pDrinkSugar,
                            "extraHot"=>$pExtraHot
                        ]];
        $vJsonReturnMsg = json_encode( $arrReturnMsg); 

        try {

            $file_path = '.\src\Models\jsonDrinkSales.json';
            if (!file_exists($file_path))
                $dataSaved="[]";
            else
                $dataSaved = file_get_contents($file_path);

            if (strlen($dataSaved)<5)
                $dataSaved = $vJsonReturnMsg;
            else
            {
                $dataSaved .= $vJsonReturnMsg;
                $dataSaved = str_replace('][',',',$dataSaved);
            }

            $pReturnMsg = "Drink sale registered sussesfully";           
            file_put_contents($file_path,$dataSaved);
            return true; 
        }
        catch (Exception $e)
        {
            $pReturnMsg = 'Error saving data dink server. ('.$e->getMessage().')';
            return false;
        }

    }


    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $drinkType = $input->getArgument('drink-type');
        if (!$this->validateDrinkName($drinkType)) 
        {
//            $output->writeln('');
            $output->writeln('The drink type should be '.implode(" or ",$this->drinkNames).'.');
        } 
        else 
        {
            $priceValidationMsg = "";
            $money = $input->getArgument('money');
            if (!$this->validateDrinkPrice($drinkType, $money, $priceValidationMsg))
            {
                $output->writeln($priceValidationMsg);
                return 0;
            }

            $sugarValidationMsg = "";
            $sugars = $input->getArgument('sugars');
            $stick = false;

            $extraHot = $input->getOption('extra-hot');

            if ($this->validateDrinkSugar($sugars, $sugarValidationMsg))
            {
//                $output->writeln('');
                $output->write('You have ordered a ' . $drinkType);
                if ($extraHot) {
                    $output->write(' extra hot');
                }
                $output->write($sugarValidationMsg);

                $returnMsg = "";
                $this->saveDrinkServed($drinkType, $money, $sugars, $extraHot, $returnMsg);
//                $output->writeln($returnMsg);
//                $output->writeln('');
            }
            else
            {
                $output->writeln($sugarValidationMsg);
            }
        }

        return 0;
    }
    
}
