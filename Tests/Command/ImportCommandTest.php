<?php

namespace Bordeux\Bundle\GeoNameBundle\Tests\Command;

use Bordeux\Bundle\GeoNameBundle\Command\ImportCommand;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\StreamOutput;

class ImportCommandTest extends WebTestCase
{
    /**
     * @inheritDoc
     */
    protected function setUp()
    {
        static::$kernel = static::createKernel();
        static::$kernel->boot();
    }

    public function testDownload()
    {
        $application = new Application(static::$kernel);
        $application->add(new ImportCommand());

        $command = $application->find('bordeux:geoname:import');
        $command->setApplication($application);


        $input = new ArrayInput([
            'command' => $command->getName(),
            '--archive' => 'http://download.geonames.org/export/dump/PL.zip'
        ]);

        $output = new StreamOutput(fopen('php://stdout', 'w', false));;

        $command->run($input, $output);


        

        //$this->assertContains('Imported successfully', $commandTester->getDisplay());
    }
}
