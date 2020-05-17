<?php

declare(strict_types=1);

namespace Library;

/**
 * Class Model
 *
 * @method int getId()
 *
 * @package Library
 */
class Model
{
    private $data = [];
    private $changes = [];

    public function __construct(array $data = [])
    {
        $this->data = $data;
    }

    public function getData(string $property = '')
    {
        if(!$property) {
            return $this->data;
        }

        return $this->data[$property] ?? false;
    }

    public function setData($property, $value = ''): Model
    {
        if(is_array($property)) {
            foreach($property as $prop => $value) {
                $this->setData($prop, $value);
            }
        } else if(is_string($property) && !empty($value)) {
            // Make the model aware that something is changed.
            $this->changes[$property] = 1;
            $this->data[$property] = $value;
        }

        return $this;
    }

    public function getChanges()
    {
        return $this->changes;
    }

    public function isChanged()
    {
        return !!count(array_keys($this->getChanges()));
    }

    public function __call($name, $arguments)
    {
        $value = $arguments[0];
        $method = substr($name, 0, 3);
        $property = strtolower(substr($name, 3));

        if($method === 'set') {
            return $this->setData($property, $value);
        }

        return $this->getData($property);
    }
}