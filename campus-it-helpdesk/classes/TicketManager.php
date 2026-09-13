<?php
/**
 * TicketManager
 * -------------
 * Holds the ARRAY OF TICKET OBJECTS for the running application.
 * This is NOT a database - it is only a PHP array kept in the session.
 */
class TicketManager
{
    private $tickets = [];   // array of SupportTicket objects
    private $counter = 0;    // used for automatic ticket IDs

    /** Generates IT-0001, IT-0002, ... */
    public function generateTicketId()
    {
        $this->counter++;
        return 'IT-' . str_pad($this->counter, 4, '0', STR_PAD_LEFT);
    }

    /** Adds any object that IS-A SupportTicket (polymorphic parameter) */
    public function addTicket(SupportTicket $ticket)
    {
        $this->tickets[] = $ticket;
        return $ticket;
    }

    public function getTickets()
    {
        return $this->tickets;
    }

    public function countTickets()
    {
        return count($this->tickets);
    }

    /** Finds one ticket by its ID, or null when it does not exist */
    public function findTicket($ticketId)
    {
        foreach ($this->tickets as $ticket) {
            if (strcasecmp($ticket->getTicketId(), (string) $ticketId) === 0) {
                return $ticket;
            }
        }
        return null;
    }

        /** Removes one ticket by its ID. Returns true if it was found and removed. */
    public function removeTicket($ticketId)
    {
        foreach ($this->tickets as $index => $ticket) {
            if (strcasecmp($ticket->getTicketId(), (string) $ticketId) === 0) {
                unset($this->tickets[$index]);
                $this->tickets = array_values($this->tickets); // re-index the array
                return true;
            }
        }
        return false;
    }

    /** Status is changed through the object's own method (encapsulation) */
    public function updateTicketStatus($ticketId, $status)
    {
        $ticket = $this->findTicket($ticketId);
        if ($ticket === null) {
            return false;
        }
        if ($status === 'Closed') {
            return $ticket->closeTicket();
        }
        return $ticket->setStatus($status);
    }

    /** Dashboard numbers, computed from the object array */
    public function getStatistics()
    {
        $stats = [
            'total'       => 0,
            'Open'        => 0,
            'In Progress' => 0,
            'Resolved'    => 0,
            'Closed'      => 0,
            'urgent'      => 0, // High + Critical
            'categories'  => ['Hardware' => 0, 'Software' => 0, 'Network' => 0, 'Account & Access' => 0],
        ];

        foreach ($this->tickets as $ticket) {
            $stats['total']++;
            $status = $ticket->getStatus();
            if (isset($stats[$status])) {
                $stats[$status]++;
            }
            if (in_array($ticket->getPriority(), ['High', 'Critical'], true)) {
                $stats['urgent']++;
            }
            $cat = $ticket->getCategory();
            if (isset($stats['categories'][$cat])) {
                $stats['categories'][$cat]++;
            }
        }

        return $stats;
    }

    /**
     * Search + filter, done on the PHP array (no SQL at all).
     */
    public function filterTickets($search = '', $category = 'All', $priority = 'All', $status = 'All', $requesterType = 'All')
    {
        $search = trim(strtolower($search));
        $result = [];

        foreach ($this->tickets as $ticket) {
            if ($category !== 'All' && $ticket->getCategory() !== $category)            continue;
            if ($priority !== 'All' && $ticket->getPriority() !== $priority)            continue;
            if ($status !== 'All' && $ticket->getStatus() !== $status)                  continue;
            if ($requesterType !== 'All' && $ticket->getRequesterType() !== $requesterType) continue;

            if ($search !== '') {
                $haystack = strtolower(
                    $ticket->getTicketId() . ' ' . $ticket->getRequesterName() . ' ' . $ticket->getDescription()
                );
                if (strpos($haystack, $search) === false) {
                    continue;
                }
            }
            $result[] = $ticket;
        }

        return $result;
    }
}
