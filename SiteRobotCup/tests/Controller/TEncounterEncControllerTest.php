<?php

namespace App\Tests\Controller;

use App\Entity\TEncounterEnc;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class TEncounterEncControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private EntityManagerInterface $manager;
    private EntityRepository $repository;
    private string $path = '/t/encounter/enc/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->manager = static::getContainer()->get('doctrine')->getManager();
        $this->repository = $this->manager->getRepository(TEncounterEnc::class);

        foreach ($this->repository->findAll() as $object) {
            $this->manager->remove($object);
        }

        $this->manager->flush();
    }

    public function testIndex(): void
    {
        $this->client->followRedirects();
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('TEncounterEnc index');

        // Use the $crawler to perform additional assertions e.g.
        // self::assertSame('Some text on the page', $crawler->filter('.p')->first());
    }

    public function testNew(): void
    {
        $this->markTestIncomplete();
        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            't_encounter_enc[scoreBlue]' => 'Testing',
            't_encounter_enc[scoreGreen]' => 'Testing',
            't_encounter_enc[state]' => 'Testing',
            't_encounter_enc[teamBlue]' => 'Testing',
            't_encounter_enc[teamGreen]' => 'Testing',
            't_encounter_enc[championship]' => 'Testing',
        ]);

        self::assertResponseRedirects($this->path);

        self::assertSame(1, $this->repository->count([]));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new TEncounterEnc();
        $fixture->setScoreBlue('My Title');
        $fixture->setScoreGreen('My Title');
        $fixture->setState('My Title');
        $fixture->setTeamBlue('My Title');
        $fixture->setTeamGreen('My Title');
        $fixture->setChampionship('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('TEncounterEnc');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new TEncounterEnc();
        $fixture->setScoreBlue('Value');
        $fixture->setScoreGreen('Value');
        $fixture->setState('Value');
        $fixture->setTeamBlue('Value');
        $fixture->setTeamGreen('Value');
        $fixture->setChampionship('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            't_encounter_enc[scoreBlue]' => 'Something New',
            't_encounter_enc[scoreGreen]' => 'Something New',
            't_encounter_enc[state]' => 'Something New',
            't_encounter_enc[teamBlue]' => 'Something New',
            't_encounter_enc[teamGreen]' => 'Something New',
            't_encounter_enc[championship]' => 'Something New',
        ]);

        self::assertResponseRedirects('/t/encounter/enc/');

        $fixture = $this->repository->findAll();

        self::assertSame('Something New', $fixture[0]->getScoreBlue());
        self::assertSame('Something New', $fixture[0]->getScoreGreen());
        self::assertSame('Something New', $fixture[0]->getState());
        self::assertSame('Something New', $fixture[0]->getTeamBlue());
        self::assertSame('Something New', $fixture[0]->getTeamGreen());
        self::assertSame('Something New', $fixture[0]->getChampionship());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();
        $fixture = new TEncounterEnc();
        $fixture->setScoreBlue('Value');
        $fixture->setScoreGreen('Value');
        $fixture->setState('Value');
        $fixture->setTeamBlue('Value');
        $fixture->setTeamGreen('Value');
        $fixture->setChampionship('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertResponseRedirects('/t/encounter/enc/');
        self::assertSame(0, $this->repository->count([]));
    }
}
