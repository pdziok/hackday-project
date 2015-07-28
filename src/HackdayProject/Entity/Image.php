<?php

namespace HackdayProject\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="images")
 * @ORM\Entity
 */
class Image
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
     * @ORM\Column(name="filename", type="string", length=255)
     */
    private $filename;

    /**
     * @var Entry
     *
     * @ORM\ManyToOne(targetEntity="Entry", inversedBy="images")
     * @ORM\JoinColumn(name="entry_id", referencedColumnName="id")
     **/
    private $entry;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return Entry
     */
    public function getEntry()
    {
        return $this->entry;
    }

    /**
     * @param Entry $entry
     */
    public function setEntry(Entry $entry)
    {
        $this->entry = $entry;
    }

    /**
     * @return string
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * @param string $filename
     */
    public function setFilename($filename)
    {
        $this->filename = $filename;
    }

    /**
     * Convert to array
     *
     * @return array
     */
    public function toArray()
    {
        return [
            'id' => $this->getId(),
            'filename' => $this->getFilename()
        ];
    }
}
