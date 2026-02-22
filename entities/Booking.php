<?php


class Booking {

    private int $id;
    private int $user_id;
    private int $session_id;
    private int $nb_of_participants;
    private string $booking_status;
    private float $total_price;
    private string $booked_at;
    private string $cancelled_at;

        public function setId( int $id ) : void
    {
        $this->id = $id;
    }

        public function getId() : int
    {
        return $this->id;
    }

        public function setUserId( int $userId) : void
    {
        $this->user_id = $userId;
    }

        public function getUserId() : int
    {
        return $this->user_id;
    }

        public function setSessionId( string $sessionId ) : void
    {
        $this->session_id = $sessionId;
    }

        public function getSessionId() : int
    {
        return $this->session_id;
    }

       public function setNbOfParticipants( int $participants ) : void
    {
        $this->nb_of_participants = $participants;
    }

        public function getNbOfParticipants() : int 
    {
        return $this->nb_of_participants;
    }

        public function setBookingStatus( string $bookingStatus ) : void
    {
        $this->booking_status = $bookingStatus;
    }

        public function getBookingStatus() : string
    {
        return $this->booking_status;
    }

        public function setTotalPrice( string $totalPrice ) : void
    {
        $this->total_price = $totalPrice;
    }
    
        public function getTotalPrice() : float
    {
        return $this->total_price;
    }

        public function setBookedAt( string $bookedAt ) : void
    {
        $this->booked_at = $bookedAt;
    }
    
        public function getBookedAt() : string
    {
        return $this->booked_at;
    }

        public function setCancelledAt( string $cancelledAt ) : void
    {
        $this->cancelled_at = $cancelledAt;
    }
    
        public function getCancelledAt() : string
    {
        return $this->cancelled_at;
    }

}