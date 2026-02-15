<?php

namespace Entity;

class Session {
    
    private int $id;
    private int $vehicle_id;
    private int $track_id;
    private string $start_time;
    private string $end_time;
    private int $capacity;
    private float $price;
    private string $session_status;
    private string $created_at;

    public function setId( int $id ) : void
    {
        $this->id = $id;
    }

    public function getId() : int
    {
        return $this->id;
    }

    public function setVehicleId( int $vehicleId ) : void
    {
        $this->vehicle_id = $vehicleId;
    }

    public function getVehicleId() : int 
    {
        return $this->vehicle_id;
    }

    public function setTrackId( int $vehicleId ) : void
    {
        $this->vehicle_id = $vehicleId;
    }

    public function getTrackId() : int 
    {
        return $this->track_id;
    }

    public function setStartTime( string $startTime ) : void
    {
        $this->start_time = $startTime;
    }

    public function getStartTime( string $startTime ) : string
    {
        return $this->start_time;
    }

    public function setEndTime( string $endTime ) : void
    {
        $this->end_time = $endTime;
    }

    public function getEndTime( string $endTime ) : string
    {
        return $this->end_time;
    }

    public function setCapacity( int $capacity ) : void
    {
        $this->capacity = $capacity;
    }

    public function getCapacity() : int
    {
        return $this->capacity;
    }

    public function setPrice( int $price ) : void
    {
        $this->price = $price;
    }

    public function getPrice() : float
    {
        return $this->price;
    }

    public function setSessionStatus( string $sessionStatus ) : void
    {
        $this->session_status = $sessionStatus;
    }

    public function getSessionStatus() : string 
    {
        return $this->session_status;
    }

    public function setCreatedAt( string $createdAt ) : void
    {
        $this->created_at = $createdAt;
    }

    public function getCreatedAt() : string
    {
        return $this->created_at;
    }
}

