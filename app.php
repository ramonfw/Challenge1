#!/usr/bin/env php
<?php

require __DIR__ . '/vendor/autoload.php';

use GetWith\CoffeeMachine\Console\MakeDrinkCommand;
use GetWith\CoffeeMachine\Console\MoneyEarnCommand;
use GetWith\CoffeeMachine\Models\MdlDrink;
use GetWith\CoffeeMachine\Models\MdlSugar;

use Symfony\Component\Console\Application;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

$container = new ContainerBuilder();
$container
    ->register('order-drink', 'GetWith\CoffeeMachine\Console\MakeDrinkCommand')
    ->addMethodCall('setContainer', [new Reference('service_container')]);
$container
    ->register('money-earn', 'GetWith\CoffeeMachine\Console\MakeDrinkCommand')
    ->addMethodCall('setContainer', [new Reference('service_container')]);
$container->compile();

$application = new Application();

$application->add(new MakeDrinkCommand(new MdlDrink(), new MdlSugar()));
$application->add(new MoneyEarnCommand());

$application->run();