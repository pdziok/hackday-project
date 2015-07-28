<?php
/**
 * @author pdziok
 */
namespace Utils;

class DataMapper
{
    private $map = [];

    /**
     * DataMapper constructor.
     *
     * @param array $map
     */
    public function __construct(array $map)
    {
        $this->map = $map;
    }

    public function map($input)
    {
        $output = [];
        foreach ($input as $paramName => $paramValue) {
            if (isset($this->map[$paramName])) {
                $mappedParam = $this->map[$paramName];
                $output[$mappedParam] = $paramValue;
            }
        }

        return $output;
    }
}
