<?php

namespace Bordeux\Bundle\GeoNameBundle\Tests\Command;

use Bordeux\Bundle\GeoNameBundle\Command\ImportCommand;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Console\Tester\CommandTester;


class ImportCommandTest extends WebTestCase
{
    public function testExecute()
    {
        self::bootKernel();
        $application = new Application(self::$kernel);

        $application->add(new ImportCommand());

        $command = $application->find('bordeux:geoname:import');
        $commandTester = new CommandTester($command);
        $commandTester->execute(array(
            'command'  => $command->getName(),
            '--archive' => 'http://download.geonames.org/export/dump/PL.zip',
        ));

        // the output of the command in the console
        $output = $commandTester->getDisplay();
        $this->assertContains('Imported successfully', $output);

        // ...
    }
}
