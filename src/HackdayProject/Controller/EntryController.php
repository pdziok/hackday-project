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
        $qb = $this->em->createQueryBuilder();
        $qb->select('e')
           ->from('HackdayProject\Entity\Entry', 'e')
           ->leftJoin('e.image', 'i')
           ->orderBy('e.id', 'DESC');


        $entries = $qb->getQuery()->getResult();

        $result = [];
        foreach($entries as $entry) {
            $result[] = $entry->toArray();
        }

        return new JsonResponse($result);
    }

    public function createEntryAction(Request $request)
    {
        $data = $request->getContent();
        $entryData = json_decode($data, true);

        $entry = new Entry();
        $entry->setName($entryData['name']);
        $entry->setLatitude($entryData['latitude']);
        $entry->setLongitude($entryData['longitude']);

        $image = $this->em->getRepository('HackdayProject\\Entity\\Image')
            ->findOneById($entryData['image_id']);

        if (!$image) {
            return new JsonResponse(
                ['error' => 'Image not found by id: ' . $entryData['image_id']],
                422
            );
        }

        $entry->setImage($image);

        $this->em->persist($entry);
        $this->em->flush();

        return new JsonResponse($entry->toArray(), 201);
    }
}
