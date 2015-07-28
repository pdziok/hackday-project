<?php
namespace HackdayProject\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use HackdayProject\Entity\Entry;
use Doctrine\ORM\EntityManager;

class EntryController
{
    /**
     * An entity manager
     * @var EntityManager
     */
    private $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function getEntriesAction(Request $request)
    {

    }

    public function createEntryAction(Request $request)
    {
        $data = $request->getContent();
        $entryData = json_decode($data, true);

        $entry = new Entry();
        $entry->setName($entryData['name']);
        $entry->setLatitude($entryData['latitude']);
        $entry->setLongitude($entryData['longitude']);

        $this->em->persist($entry);
        $this->em->flush();

        $result = [
            'id' => $entry->getId(),
            'name' => $entry->getName(),
            'latitude' => $entry->getLatitude(),
            'longitude' => $entry->getLongitude(),
        ];

        return new JsonResponse($result, 201);
    }
}
