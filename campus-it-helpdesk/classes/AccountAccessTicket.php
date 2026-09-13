<?php
/**
 * CHILD CLASS - Account and access problems
 */
class AccountAccessTicket extends SupportTicket
{
    public function __construct($ticketId, $requesterName, $requesterType, $location, $description, $priority)
    {
        parent::__construct($ticketId, $requesterName, $requesterType, $location, $description, $priority, 'Account & Access');
    }

    // METHOD OVERRIDING
    public function diagnose()
    {
        return 'Account check: confirm the account status (active, locked or expired), the assigned access '
            . 'permissions for a ' . strtolower($this->requesterType) . ' account, the login details used, '
            . 'and whether the school system itself is available.';
    }

    // METHOD OVERRIDING
    public function getResolution()
    {
        return 'Verify the identity of the requester, unlock the account or reset the password, '
            . 'restore the correct access permissions, and advise the user to log in again.';
    }

    public function getTeam()
    {
        return 'Accounts & Access Team';
    }
}
