<?php
declare(strict_types=1);

namespace Arxy\GdprDumpBundle\Tests\Functional;

use Arxy\GdprDumpBundle\Tests\Entity\Customer;
use Doctrine\ORM\EntityManagerInterface;
use Ifsnop\Mysqldump\Mysqldump;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\Output;

class TranslatorTest extends WebTestCase
{
    private function buildDb(Application $application, Output $output)
    {
        $application->run(
            new ArrayInput(
                array(
                    'doctrine:schema:create',
                )
            ),
            $output
        );
    }

    private function dropDb(Application $application, Output $output)
    {
        $application->run(
            new ArrayInput(
                array(
                    'doctrine:schema:drop',
                    '--force' => null,
                    '--full-database' => null,
                )
            ),
            $output
        );
    }

    public function testSimpleDump()
    {
        $client = static::createClient();
        $kernel = $client->getKernel();

        $application = new Application($kernel);
        $application->setAutoExit(false);

        $output = new ConsoleOutput();
        $this->dropDb($application, $output);
        $this->buildDb($application, $output);

        $container = $kernel->getContainer();
        $testContainer = $container->get('test.service_container');

        /** @var EntityManagerInterface $em */
        $em = $testContainer->get('doctrine')->getManager();

        $customer1 = new Customer();
        $customer1->setId(1);
        $customer1->setFirstName('Angel');
        $customer1->setLastName('Angelov');
        $customer1->setBirthDate(new \DateTime('1990-05-20'));
        $em->persist($customer1);
        $em->flush();
        $em->clear();

        $file = tempnam(sys_get_temp_dir(), 'mysqldump');
        /** @var Mysqldump $mysqldump */
        $mysqldump = $testContainer->get(Mysqldump::class);
        $mysqldump->start($file);

        $sql = file_get_contents($file);

        $this->dropDb($application, $output);
        $this->buildDb($application, $output);

        $conn = $em->getConnection();
        $conn->exec($sql);

        /** @var Customer $customer1AfterGdpr */
        $customer1AfterGdpr = $em->find(Customer::class, 1);

        $this->assertNotNull($customer1AfterGdpr, 'Customer 1 not found');
        $this->assertNotEquals('Angel', $customer1AfterGdpr->getFirstName());
        $this->assertNotEquals('Angelov', $customer1AfterGdpr->getLastName());
        $this->assertNotEquals('1990-05-20', $customer1AfterGdpr->getBirthDate()->format('Y-m-d'));
    }
}