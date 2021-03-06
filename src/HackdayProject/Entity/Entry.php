<?php

namespace HackdayProject\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Table(name="entries")
 * @ORM\Entity
 */
class Entry
{
    /**
     * @var integer
     *
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var float
     *
     * @ORM\Column(name="latitude", type="decimal", precision=10, scale=8)
     */
    private $latitude;

    /**
     * @var float
     *
     * @ORM\Column(name="longitude", type="decimal", precision=11, scale=8)
     */
    private $longitude;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=2047)
     */
    private $description;

    /**
     * @ORM\OneToOne(targetEntity="Image")
     * @ORM\JoinColumn(name="image_id", referencedColumnName="id")
     **/
    private $image;

    /**
     * @ORM\OneToMany(targetEntity="Vote", mappedBy="entry")
     **/
    private $votes;

    public function __construct()
    {
        $this->votes = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return Image
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * @param Image $image
     */
    public function setImage($image)
    {
        $this->image = $image;
    }

    /**
     * @return float
     */
    public function getLatitude()
    {
        return $this->latitude;
    }

    /**
     * @param float $latitude
     */
    public function setLatitude($latitude)
    {
        $this->latitude = $latitude;
    }

    /**
     * @return float
     */
    public function getLongitude()
    {
        return $this->longitude;
    }

    /**
     * @param float $longitude
     */
    public function setLongitude($longitude)
    {
        $this->longitude = $longitude;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getVotes()
    {
        return $this->votes;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     * @return $this
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return int
     */
    public function getOverallRating()
    {
        $overallRating = 0;
        /** @var Vote $vote */
        foreach ($this->votes as $vote) {
            $overallRating += $vote->getValue();
        }

        return $overallRating;
    }

    /**
     * @return array
     */
    public function getRating()
    {
        $pluses = 0;
        $minuses = 0;
        /** @var Vote $vote */
        foreach ($this->votes as $vote) {
            if ($vote->getValue() > 0) {
                $pluses++;
            } else {
                $minuses++;
            }
        }

        return [
            'pluses' => $pluses,
            'minuses' => $minuses
        ];
    }

    public function getVotesCount()
    {
        return $this->votes->count();
    }

    /**
     * Convert to array
     *
     * @return array
     */
    public function toArray()
    {
        $result = [
            'id' => $this->getId(),
            'name' => $this->getName(),
            'position' => [
                'lat' => (float) $this->getLatitude(),
                'lng' => (float) $this->getLongitude(),
            ],
            'image' => '',
            'description' => $this->getDescription() ?: '',
            'rating' => $this->getRating(),
            'overallRating' => $this->getOverallRating(),
            'votesCount' => $this->getVotesCount()
        ];

        if ($this->getImage()) {
            $result['image'] = $this->getImage()->toArray();
        }

        return $result;
    }
}
