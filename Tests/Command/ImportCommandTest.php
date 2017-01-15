<?php

namespace Bordeux\Bundle\GeoNameBundle\Tests\Command;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;


class ImportCommandTest extends WebTestCase
{
    public function testExecute()
    {
        self::bootKernel();
        $application = new Application(self::$kernel);

    }
}
