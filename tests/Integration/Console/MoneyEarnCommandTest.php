<?php

namespace GetWith\CoffeeMachine\Tests\Integration\Console;

use GetWith\CoffeeMachine\Console\MoneyEarnCommand;
use GetWith\CoffeeMachine\Tests\Integration\IntegrationTestCase;
use Symfony\Component\Console\Tester\CommandTester;

class MoneyEarnCommandTest extends IntegrationTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->application->add(new MoneyEarnCommand());
    }

    /**
     * @dataProvider ordersProvider
     * @param string $drinkType
     * @param string $expectedOutput
     */
    public function testCoffeeMachineReturnsTheExpectedOutput(
        string $drinkType,
        string $expectedOutput
    ): void {
        $command = $this->application->find('app:money-earn');
        $commandTester = new CommandTester($command);
        $commandTester->execute(array(
            'command'  => $command->getName(),

            // pass arguments to the helper
            'drink-type' => $drinkType
        ));

        // the output of the command in the console
        $output = $commandTester->getDisplay();
        $this->assertSame($expectedOutput, $output);
    }

    public function ordersProvider(): array
    {
        return [
            [
                'chocolate', 'Drink: chocolate    Total money: 2' . PHP_EOL
            ],
            [
                'tea', 'Drink: tea    Total money: 1.2' . PHP_EOL
            ],
            [
                'coffee', 'Drink: coffee    Total money: 1.5' . PHP_EOL
            ],
            [
                'all', 'Drink: chocolate Total money: 2, Drink: tea Total money: 1.2, Drink: coffee Total money: 1.5' . PHP_EOL
            ],
        ];
    }
}
