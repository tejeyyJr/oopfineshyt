<?php
/** Helper functions used by the pages. No database, plain PHP only. */

/** Safe output of any text into HTML */
function e($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function categories()
{
    return ['Hardware', 'Software', 'Network', 'Account & Access'];
}

function requesterTypes()
{
    return ['Student', 'Instructor', 'Staff', 'IT Personnel'];
}

function priorities()
{
    return ['Low', 'Medium', 'High', 'Critical'];
}

function statuses()
{
    return ['Open', 'In Progress', 'Resolved', 'Closed'];
}

function locations()
{
    return [
        'Computer Laboratory 1',
        'Computer Laboratory 2',
        'Computer Laboratory 3',
        'Library',
        'Faculty Room',
        'Registrar',
        'Administration Office',
        'Classroom',
        'IT Office',
        'Other',
    ];
}

/**
 * FACTORY: decides WHICH CHILD CLASS to instantiate.
 * This is where the correct subclass object is created.
 */
function createTicket($category, $id, $name, $type, $location, $description, $priority)
{
    switch ($category) {
        case 'Hardware':
            return new HardwareTicket($id, $name, $type, $location, $description, $priority);
        case 'Software':
            return new SoftwareTicket($id, $name, $type, $location, $description, $priority);
        case 'Network':
            return new NetworkTicket($id, $name, $type, $location, $description, $priority);
        case 'Account & Access':
            return new AccountAccessTicket($id, $name, $type, $location, $description, $priority);
        default:
            return null; // invalid category -> handled as a validation error
    }
}

/**
 * SMART PRIORITY
 * Suggests a priority by looking for keywords in the description.
 */
function suggestPriority($description, $category)
{
    $text = strtolower($description);

    $critical = ['whole laboratory', 'entire laboratory', 'all computers', 'network is down', 'no internet in',
                 'cannot access the portal', 'portal is down', 'system is down', 'server down', 'account locked'];
    $high     = ['class is ongoing', 'ongoing class', 'multiple', 'several computers', 'projector', 'exam',
                 'cannot turn on', 'does not turn on', 'wi-fi', 'wifi', 'no internet'];
    $low      = ['install', 'installation', 'request', 'question', 'how to', 'minor', 'setting'];

    foreach ($critical as $k) { if (strpos($text, $k) !== false) return 'Critical'; }
    foreach ($high as $k)     { if (strpos($text, $k) !== false) return 'High'; }
    foreach ($low as $k)      { if (strpos($text, $k) !== false) return 'Low'; }

    return 'Medium';
}

/** CSS class for a status badge */
function statusClass($status)
{
    $map = [
        'Open'        => 'badge badge-open',
        'In Progress' => 'badge badge-progress',
        'Resolved'    => 'badge badge-resolved',
        'Closed'      => 'badge badge-closed',
    ];
    return isset($map[$status]) ? $map[$status] : 'badge';
}

/** CSS class for a priority badge */
function priorityClass($priority)
{
    $map = [
        'Low'      => 'badge badge-low',
        'Medium'   => 'badge badge-medium',
        'High'     => 'badge badge-high',
        'Critical' => 'badge badge-critical',
    ];
    return isset($map[$priority]) ? $map[$priority] : 'badge';
}

function formatDate($raw)
{
    $time = strtotime($raw);
    return $time ? date('F j, Y g:i A', $time) : $raw;
}

/**
 * VALIDATION of the submitted form.
 * Returns an array of friendly error messages (empty array = valid).
 */
function validateTicketInput($data)
{
    $errors = [];

    $name = isset($data['requesterName']) ? trim($data['requesterName']) : '';
    if ($name === '') {
        $errors['requesterName'] = 'Please enter your name.';
    } elseif (strlen($name) < 3) {
        $errors['requesterName'] = 'Your name must be at least 3 characters long.';
    } elseif (preg_match('/^[0-9\s]+$/', $name)) {
        $errors['requesterName'] = 'Your name cannot contain numbers only.';
    } elseif (strlen($name) > 60) {
        $errors['requesterName'] = 'Your name must be less than 60 characters.';
    }

    $type = isset($data['requesterType']) ? trim($data['requesterType']) : '';
    if ($type === '') {
        $errors['requesterType'] = 'Please select your requester type.';
    } elseif (!in_array($type, requesterTypes(), true)) {
        $errors['requesterType'] = 'Please select a valid requester type.';
    }

    $location = isset($data['location']) ? trim($data['location']) : '';
    if ($location === '') {
        $errors['location'] = 'Please select your location.';
    } elseif (!in_array($location, locations(), true)) {
        $errors['location'] = 'Please select a valid campus location.';
    } elseif ($location === 'Other') {
        $custom = isset($data['customLocation']) ? trim($data['customLocation']) : '';
        if ($custom === '') {
            $errors['customLocation'] = 'Please type the exact location.';
        } elseif (strlen($custom) < 3) {
            $errors['customLocation'] = 'Please give a clearer location (at least 3 characters).';
        }
    }

    $category = isset($data['category']) ? trim($data['category']) : '';
    if ($category === '') {
        $errors['category'] = 'Please select a problem category.';
    } elseif (!in_array($category, categories(), true)) {
        $errors['category'] = 'Invalid ticket category selected.';
    }

    $description = isset($data['description']) ? trim($data['description']) : '';
    if ($description === '') {
        $errors['description'] = 'Please describe the problem.';
    } elseif (strlen($description) < 15) {
        $errors['description'] = 'Please provide a more detailed description of the problem (at least 15 characters).';
    } elseif (strlen($description) > 1000) {
        $errors['description'] = 'Please keep the description under 1000 characters.';
    }

    $priority = isset($data['priority']) ? trim($data['priority']) : '';
    if ($priority === '') {
        $errors['priority'] = 'Please select the urgency of the problem.';
    } elseif ($priority !== 'Smart' && !in_array($priority, priorities(), true)) {
        $errors['priority'] = 'Please select a valid priority.';
    }

    return $errors;
}
