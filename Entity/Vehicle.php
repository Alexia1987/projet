<?php

namespace Entity;

class Vehicle {

    private int $id;
    private string $name;
    private string $description;
    private int $max_speed_kmh;
    private bool $is_available;

    public function setId( int $id ) : void
    {
        $this->id = $id;
    }

    public function getId() : int
    {
        return $this->id;
    }

    public function setName ( string $name ) : void
    {
        $this->name = $name;
    }

    public function getName () : string
    {
        return $this->name;
    }

    public function setDescription ( string $description ) : void
    {
        $this->description = $description;
    }

    public function getDescription () : string
    {
        return $this->description;
    }

    public function setMaxSpeedKmh ( string $maxSpeed ) : void
    {
        $this->max_speed_kmh = $maxSpeed;
    }

    public function getMaxSpeedKmh () : int
    {
        return $this->max_speed_kmh;
    }

    public function setIsAvailable ( string $isAvailable ) : void
    {
        $this->is_available = $isAvailable;
    }

    public function getIsAvailable () : int
    {
        return $this->is_available;
    }       

}