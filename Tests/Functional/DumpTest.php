<?php
declare(strict_types=1);

namespace Arxy\GdprDump\Tests\Functional;

use Arxy\GdprDump\Tests\Entity\Customer;
use Doctrine\DBAL\Driver\PDOConnection;
use Doctrine\DBAL\Driver\PDOStatement;
use Doctrine\ORM\EntityManagerInterface;
use Ifsnop\Mysqldump\Mysqldump;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\ConsoleOutput;

class TranslatorTest extends WebTestCase
{
    private function buildDb(Application $application)
    {
        $application->run(
            new ArrayInput(
                array(
                    'doctrine:schema:create',
                )
            ),
            new ConsoleOutput()
        );
    }

    private function dropDb(Application $application)
    {
        $application->run(
            new ArrayInput(
                array(
                    'doctrine:schema:drop',
                )
            ),
            new ConsoleOutput()
        );
    }

    public function testSimpleDump()
    {
        $client = static::createClient();
        $kernel = $client->getKernel();

        $application = new Application($kernel);
        $application->setAutoExit(false);

        $this->dropDb($application);
        $this->buildDb($application);

        $container = $kernel->getContainer();
        $testContainer = $container->get('test.service_container');

        /** @var EntityManagerInterface $em */
        $em = $testContainer->get('doctrine')->getManager();

        $customer1 = new Customer();
        $customer1->setId(1);
        $customer1->setFirstName('Angel');
        $customer1->setLastName('Angelov');
        $customer1->setBirthDate(new \DateTimeImmutable('1990-05-20'));
        $em->persist($customer1);
        $em->flush();

        /** @var Mysqldump $mysqldump */
        $mysqldump = $testContainer->get(Mysqldump::class);
        $mysqldump->start('php://memory');

        $sql = file_get_contents('php://memory');

        $this->dropDb($application);
        $this->buildDb($application);

        $connection = $em->getConnection();

        if ($connection instanceof PDOConnection) {
            // PDO Drivers
            $lines = 0;
            $stmt = $connection->prepare($sql);
            assert($stmt instanceof PDOStatement);
            $stmt->execute();
            do {
                // Required due to "MySQL has gone away!" issue
                $stmt->fetch();
                $stmt->closeCursor();
                $lines++;
            } while ($stmt->nextRowset());
        } else {
            // Non-PDO Drivers (ie. OCI8 driver)
            $stmt = $connection->prepare($sql);
            $rs = $stmt->execute();
            if (!$rs) {
                $error = $stmt->errorInfo();
                throw new \RuntimeException($error[2], $error[0]);
            }

            $stmt->closeCursor();
        }

        /** @var Customer $customer1AfterGdpr */
        $customer1AfterGdpr = $em->find(Customer::class, 1);

        $this->assertNotEquals('Angel', $customer1AfterGdpr->getFirstName());
        $this->assertNotEquals('Angelov', $customer1AfterGdpr->getLastName());
        $this->assertNotEquals('1990-05-20', $customer1AfterGdpr->getBirthDate()->format('Y-m-d'));
    }
}