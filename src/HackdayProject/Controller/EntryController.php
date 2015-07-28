<?php
namespace HackdayProject\Controller;

use ApiProblem\NotFound;
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

    public function getEntryAction($id)
    {
        $qb = $this->em->createQueryBuilder();
        $qb->select('e')
            ->from('HackdayProject\Entity\Entry', 'e')
            ->leftJoin('e.image', 'i')
            ->where('e.id = ?1');

        $qb->setParameter('1', $id);
        /** @var Entry $entry */
        $entry = $qb->getQuery()->getSingleResult();

        if (!$entry) {
            throw new NotFound('Entry not found');
        }

        return new JsonResponse($entry->toArray());
    }

    public function getEntriesAction(Request $request)
    {
        $order = $request->get('order', 'id DESC');
        $order = str_replace('+', ' ', $order);
        list($orderBy, $orderDirection) = explode(' ', $order);

        if (!in_array($orderDirection, ['ASC', 'DESC'])) {
            $orderDirection = 'DESC';
        }

        $map = [
            'id' => 'e.id',
            'overallRating' => 'overallRating',
            'votesCount' => 'votesCount'
        ];

        if (isset($map[$orderBy])) {
            $orderBy = $map[$orderBy];
        }

        $limit = (int) $request->get('limit', 10);

        $qb = $this->em->createQueryBuilder();
        $qb->select('e')
            ->addSelect('(SELECT SUM(v.value) FROM HackdayProject\Entity\Vote v WHERE v.entry = e.id) as overallRating')
            ->addSelect('(SELECT COUNT(v2.id) FROM HackdayProject\Entity\Vote v2 WHERE v2.entry = e.id) as votesCount')
           ->from('HackdayProject\Entity\Entry', 'e')
           ->leftJoin('e.image', 'i')
           ->orderBy($orderBy, $orderDirection)
            ->setMaxResults($limit);

        $entries = $qb->getQuery()->getResult();

        $result = [];
        foreach($entries as $entry) {
            $result[] = $entry[0]->toArray();
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
        $entry->setDescription($entryData['description']);

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
