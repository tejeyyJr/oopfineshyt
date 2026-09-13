<?php
/** Shared page header + navigation. $pageTitle and $activePage are set by each page. */
$pageTitle  = isset($pageTitle) ? $pageTitle : 'Campus IT Help Desk';
$activePage = isset($activePage) ? $activePage : '';

$navItems = [
    'index'         => ['label' => 'Dashboard',     'href' => 'index.php'],
    'submit'        => ['label' => 'Submit Ticket', 'href' => 'submit-ticket.php'],
    'tickets'       => ['label' => 'Tickets',       'href' => 'tickets.php'],
    
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?php echo e($pageTitle); ?> | Campus IT Help Desk</title>
<meta name="description" content="Campus IT Help Desk - an object-oriented PHP IT support ticket management system for schools.">
<link rel="stylesheet" href="css/style.css">
</head>
<body>
<a class="skip-link" href="#main">Skip to main content</a>

<header class="site-header">
  <div class="container header-inner">
    <a class="brand" href="index.php" aria-label="Campus IT Help Desk home">
      <img class="brand-mark" src="classes/img/ITicon.jpg" alt="Campus IT Help Desk logo">
      <span class="brand-text">
        <strong>CAMPUS IT HELP DESK</strong>
        <small>Report. Diagnose. Resolve.</small>
      </span>
    </a>

    <button class="nav-toggle" id="navToggle" aria-expanded="false" aria-controls="mainNav" aria-label="Toggle navigation menu">
      <span></span><span></span><span></span>
    </button>

    <nav class="main-nav" id="mainNav" aria-label="Main navigation">
      <ul>
        <?php foreach ($navItems as $key => $item): ?>
          <li>
            <a href="<?php echo e($item['href']); ?>"
               class="<?php echo $activePage === $key ? 'active' : ''; ?>"
               <?php echo $activePage === $key ? 'aria-current="page"' : ''; ?>>
              <?php echo e($item['label']); ?>
            </a>
          </li>
        <?php endforeach; ?>
      </ul>
    </nav>
  </div>
</header>

<main id="main">
