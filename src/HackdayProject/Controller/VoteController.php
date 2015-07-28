<?php
/**
 * @author pdziok
 */
namespace HackdayProject\Controller;

use ApiProblem\NotFound;
use Doctrine\ORM\EntityManager;
use HackdayProject\Entity\Image;
use HackdayProject\Entity\Vote;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class VoteController
{
    /** @var  EntityManager */
    private $em;

    /**
     * ImageController constructor.
     *
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function postVoteAction(Request $request)
    {
        $data = $request->getContent();
        $voteData = json_decode($data, true);

        $vote = new Vote();

        if (isset($voteData['user_id']) && $userId = $voteData['user_id']) {
            $user = $this->em->find('\HackdayProject\Entity\User', $userId);

            if (!$user) {
                throw new NotFound('User cannot be found');
            }

            $vote->setUser($user);
        }

        if (isset($voteData['entry_id']) && $entryId = $voteData['entry_id']) {
            $entry = $this->em->find('\HackdayProject\Entity\Entry', $entryId);

            if (!$entry) {
                throw new NotFound('User cannot be found');
            }

            $vote->setEntry($entry);
        } else {
            throw new BadRequestHttpException();
        }

        $vote->setValue($voteData['value']);

        $this->em->persist($vote);
        $this->em->flush($vote);

        return new JsonResponse($vote->toArray(), 201);
    }

    public function getImageAction($id)
    {
        $image = $this->em->find('HackdayProject\Entity\Image', $id);

        if (!$image) {
            throw new NotFound('Image cannot be found');
        }

        return new JsonResponse($image->toArray());
    }
}
