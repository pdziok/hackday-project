<?php
/**
 * @author pdziok
 */
namespace HackdayProject\Controller;

use ApiProblem\NotFound;
use Doctrine\ORM\EntityManager;
use HackdayProject\Entity\Image;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class ImageController
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

    public function postImageAction(Request $request)
    {
        /** @var UploadedFile $file */
        $file = $request->files->get('file');
        /* Make sure that Upload Directory is properly configured and writable */
        $path = ROOT_PATH . '/web/upload/';
        $filename = $file->getClientOriginalName();
        $newFilename = md5($filename . time()) . '.' . $file->getClientOriginalExtension();
        $savedFile = $file->move($path,$newFilename);

        $image = new Image();
        $image->setFilename($savedFile->getBasename());

        $this->em->persist($image);
        $this->em->flush($image);

        return new JsonResponse($image->toArray(), 201);
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
