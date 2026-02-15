<?php

namespace Entity;

class Track {

    private int $id;
    private string $name;
    private string $description;
    private int $lenght_meters;
    private bool $is_active;

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

    public function setLenghtMeters ( string $lenghtMeters ) : void
    {
        $this->lenght_meters = $lenghtMeters;
    }

    public function getLenghtMeters () : int
    {
        return $this->lenght_meters;
    }

     public function setIsActive ( string $isActive ) : void
    {
        $this->is_active = $isActive;
    }

    public function getIsActive () : int
    {
        return $this->is_active;
    }       

}