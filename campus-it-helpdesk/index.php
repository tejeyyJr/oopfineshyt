<?php
require_once __DIR__ . '/includes/bootstrap.php';

// Reset the demo data back to the four sample tickets
if (isset($_GET['reset'])) {
    $manager = resetHelpDesk();
    header('Location: index.php?msg=reset');
    exit;
}

$stats   = $manager->getStatistics();
$tickets = $manager->getTickets();
$recent  = array_slice(array_reverse($tickets), 0, 5);
$max     = max(1, $stats['Open'], $stats['In Progress'], $stats['Resolved'], $stats['Closed']);

$pageTitle  = 'Dashboard';
$activePage = 'index';
include __DIR__ . '/includes/header.php';
?>

<section class="hero">
  <div class="container">
    <p class="tagline">Report. Diagnose. Resolve.</p>
    <h1>Campus IT Help Desk</h1>
    <p class="lead">
      Report your technology problems and get guided troubleshooting recommendations.
    IT Support Ticket Management System for students, instructors and staff.
    </p>
    <div class="hero-actions">
      <a class="btn btn-primary" href="submit-ticket.php">Submit a Ticket</a>
      <a class="btn btn-outline" href="tickets.php">View Tickets</a>
    </div>
  </div>
</section>

<section class="section">
  <div class="container">
    <?php if (isset($_GET['msg']) && $_GET['msg'] === 'reset'): ?>
      <div class="alert alert-info">The help desk was reset back to the four sample tickets.</div>
    <?php endif; ?>

    <div class="grid grid-3">
      <div class="feature">
        <p class="step">Step 1 &middot; Report</p>
        <h3>Submit your IT problem</h3>
        <p class="muted">Fill in the ticket form with your name, location, problem category and a clear description.</p>
      </div>
      <div class="feature">
        <p class="step">Step 2 &middot; Diagnose</p>
        <h3>Category-specific guidance</h3>
        <p class="muted">The matching ticket object runs its own <code class="inline"></code> method and returns advice for that problem type.</p>
      </div>
      <div class="feature">
        <p class="step">Step 3 &middot; Resolve</p>
        <h3>Track until it is fixed</h3>
        <p class="muted">Follow the ticket from Open to In Progress, Resolved and finally Closed.</p>
      </div>
    </div>
  </div>
</section>

<section class="section" style="padding-top:0">
  <div class="container">
    <h2>Ticket Statistics</h2>
    <p class="muted">Computed directly from the array of ticket objects.</p>

    <div class="stats">
      <div class="stat-card"><p class="label">Total Tickets</p><p class="value"><?php echo $stats['total']; ?></p></div>
      <div class="stat-card stat-open"><p class="label">Open</p><p class="value"><?php echo $stats['Open']; ?></p></div>
      <div class="stat-card stat-progress"><p class="label">In Progress</p><p class="value"><?php echo $stats['In Progress']; ?></p></div>
      <div class="stat-card stat-resolved"><p class="label">Resolved</p><p class="value"><?php echo $stats['Resolved']; ?></p></div>
      <div class="stat-card stat-closed"><p class="label">Closed</p><p class="value"><?php echo $stats['Closed']; ?></p></div>
      <div class="stat-card stat-urgent"><p class="label">High / Critical</p><p class="value"><?php echo $stats['urgent']; ?></p></div>
    </div>
  </div>
</section>

<section class="section" style="padding-top:0">
  <div class="container grid grid-2">
    <div class="panel">
      <h2>Ticket Status</h2>
      <?php
      $bars = [
          'Open'        => '',
          'In Progress' => 'progress',
          'Resolved'    => 'resolved',
          'Closed'      => 'closed',
      ];
      foreach ($bars as $label => $cls):
          $count = $stats[$label];
          $pct   = round(($count / $max) * 100);
      ?>
        <div class="bar-row">
          <span><?php echo e($label); ?></span>
          <span class="bar <?php echo $cls; ?>"><span style="width:<?php echo $pct; ?>%"></span></span>
          <strong><?php echo $count; ?></strong>
        </div>
      <?php endforeach; ?>
    </div>

    <div class="panel">
      <h2>Tickets by Category</h2>
      <?php
      $catMax = max(1, max($stats['categories']));
      foreach ($stats['categories'] as $cat => $count):
          $pct = round(($count / $catMax) * 100);
      ?>
        <div class="bar-row">
          <span><?php echo e($cat); ?></span>
          <span class="bar"><span style="width:<?php echo $pct; ?>%"></span></span>
          <strong><?php echo $count; ?></strong>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section class="section" style="padding-top:0">
  <div class="container">
    <div class="panel">
      <h2>Recent Tickets</h2>
      <?php if (empty($recent)): ?>
        <p class="muted">No tickets have been submitted yet.</p>
      <?php else: ?>
        <div class="table-wrap">
          <table>
            <caption class="sr-only"></caption>
            <thead>
              <tr><th>Ticket ID</th><th>Requester</th><th>Category</th><th>Location</th><th>Priority</th><th>Status</th><th>Action</th></tr>
            </thead>
            <tbody>
            <?php foreach ($recent as $t): ?>
              <tr>
                <td><strong><?php echo e($t->getTicketId()); ?></strong></td>
                <td><?php echo e($t->getRequesterName()); ?><br><span class="muted"><?php echo e($t->getRequesterType()); ?></span></td>
                <td><?php echo e($t->getCategory()); ?></td>
                <td><?php echo e($t->getLocation()); ?></td>
                <td><span class="<?php echo priorityClass($t->getPriority()); ?>"><?php echo e($t->getPriority()); ?></span></td>
                <td><span class="<?php echo statusClass($t->getStatus()); ?>"><?php echo e($t->getStatus()); ?></span></td>
                <td><a class="btn btn-sm btn-light" href="ticket-details.php?id=<?php echo urlencode($t->getTicketId()); ?>">View</a></td>
              </tr>
            <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php endif; ?>
      <p style="margin-top:1rem">
        <a class="btn btn-primary" href="tickets.php">All Support Tickets</a>
        <a class="btn btn-light" href="index.php?reset=1" onclick="return confirm('Reset the help desk back to the sample tickets?');">Reset Demo Data</a>
      </p>
    </div>
  </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
