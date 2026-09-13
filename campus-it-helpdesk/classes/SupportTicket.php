<?php
/**
 * PARENT CLASS
 * ------------
 * SupportTicket holds everything that every IT support ticket has in common.
 * Child classes (HardwareTicket, SoftwareTicket, NetworkTicket,
 * AccountAccessTicket) extend this class and override diagnose()
 * and getResolution() -> this is our inheritance + polymorphism.
 */
class SupportTicket
{
    // PROTECTED: child classes are allowed to read/use these
    protected $ticketId;
    protected $requesterName;
    protected $requesterType;
    protected $location;
    protected $category;
    protected $description;
    protected $priority;

    // PRIVATE: only this parent class may change these directly
    private $status;
    private $createdAt;

    /** Allowed status values (simple workflow) */
    private static $allowedStatus = ['Open', 'In Progress', 'Resolved', 'Closed'];

    /**
     * CONSTRUCTOR
     * Initializes the ticket when the object is created with "new".
     */
    public function __construct(
        $ticketId,
        $requesterName,
        $requesterType,
        $location,
        $description,
        $priority,
        $category = 'General'
    ) {
        $this->ticketId      = $ticketId;
        $this->requesterName = $requesterName;
        $this->requesterType = $requesterType;
        $this->location      = $location;
        $this->description   = $description;
        $this->priority      = $priority;
        $this->category      = $category;

        // private properties are initialized here, inside the parent class
        $this->status    = 'Open';
        $this->createdAt = date('Y-m-d H:i:s');
    }

    /* ---------------- GETTERS (encapsulation) ---------------- */

    public function getTicketId()      { return $this->ticketId; }
    public function getRequesterName() { return $this->requesterName; }
    public function getRequesterType() { return $this->requesterType; }
    public function getLocation()      { return $this->location; }
    public function getCategory()      { return $this->category; }
    public function getDescription()   { return $this->description; }
    public function getPriority()      { return $this->priority; }
    public function getStatus()        { return $this->status; }
    public function getCreatedAt()     { return $this->createdAt; }

    /** The real PHP class name, useful for the OOP demonstration page */
    public function getClassName()     { return get_class($this); }

    /* ---------------- CONTROLLED SETTERS ---------------- */

    /**
     * The private $status can only be changed through this method,
     * and only to a valid value. Outside code cannot touch $status directly.
     */
    public function setStatus($newStatus)
    {
        if (!in_array($newStatus, self::$allowedStatus, true)) {
            return false;
        }
        $this->status = $newStatus;
        return true;
    }

    /** Convenience method required by the project specification */
    public function closeTicket()
    {
        $this->status = 'Closed';
        return true;
    }

    public function setPriority($priority)
    {
        $valid = ['Low', 'Medium', 'High', 'Critical'];
        if (in_array($priority, $valid, true)) {
            $this->priority = $priority;
            return true;
        }
        return false;
    }

    /* ---------------- METHODS THAT CHILDREN OVERRIDE ---------------- */

    /** Generic diagnosis. Every child class overrides this. */
    public function diagnose()
    {
        return 'Review the reported problem and gather more information from the requester.';
    }

    /** Generic resolution. Every child class overrides this. */
    public function getResolution()
    {
        return 'Forward the request to the campus IT office for manual assessment.';
    }

    /** Short label describing the handling team. Children may override. */
    public function getTeam()
    {
        return 'General IT Support';
    }

    /** Inherited (NOT overridden) method - same for every ticket type. */
    public function getSummary()
    {
        return $this->ticketId . ' - ' . $this->category . ' issue reported by '
            . $this->requesterName . ' (' . $this->requesterType . ') at ' . $this->location . '.';
    }
}
