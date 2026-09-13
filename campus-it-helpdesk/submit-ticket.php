<?php
require_once __DIR__ . '/includes/bootstrap.php';

$errors    = [];
$old       = ['requesterName' => '', 'requesterType' => '', 'location' => '', 'customLocation' => '',
              'category' => '', 'description' => '', 'priority' => ''];
$newTicket = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // keep what the user typed so the form can be redisplayed
    foreach ($old as $key => $value) {
        $old[$key] = isset($_POST[$key]) ? trim($_POST[$key]) : '';
    }

    // STEP 1 - VALIDATION
    $errors = validateTicketInput($old);

    if (empty($errors)) {
        // STEP 2 - prepare the values
        $location = $old['location'] === 'Other' ? $old['customLocation'] : $old['location'];
        $priority = $old['priority'] === 'Smart'
            ? suggestPriority($old['description'], $old['category'])   // SMART PRIORITY
            : $old['priority'];

        // STEP 3 - generate the ticket ID automatically
        $ticketId = $manager->generateTicketId();

        // STEP 4 - CREATE THE CORRECT CHILD OBJECT (see includes/functions.php)
        $newTicket = createTicket($old['category'], $ticketId, $old['requesterName'],
            $old['requesterType'], $location, $old['description'], $priority);

        if ($newTicket === null) {
            $errors['category'] = 'Invalid ticket category. Please choose one of the listed categories.';
        } else {
            // STEP 5 - store the object in the array and save
            $manager->addTicket($newTicket);
            saveManager($manager);
        }
    }
}

$pageTitle  = 'Submit Ticket';
$activePage = 'submit';
include __DIR__ . '/includes/header.php';
?>

<section class="page-head">
  <div class="container">
    <h1>Submit a Support Ticket</h1>
    <p>Tell us what is not working. The system will create the matching ticket object and give you a diagnosis and a recommended resolution.</p>
  </div>
</section>

<section class="section">
  <div class="container">

  <?php if ($newTicket !== null): ?>
    <div class="alert alert-success">
      <strong>Ticket <?php echo e($newTicket->getTicketId()); ?> was created successfully.</strong>
      A <code class="inline"><?php echo e($newTicket->getClassName()); ?></code> object was created using its constructor.
    </div>

    <div class="panel">
      <h2>Ticket Result</h2>
      <div class="detail-list">
        <div class="item"><p class="k">Ticket ID</p><p class="v"><?php echo e($newTicket->getTicketId()); ?></p></div>
        <div class="item"><p class="k">Requester</p><p class="v"><?php echo e($newTicket->getRequesterName()); ?></p></div>
        <div class="item"><p class="k">Requester Type</p><p class="v"><?php echo e($newTicket->getRequesterType()); ?></p></div>
        <div class="item"><p class="k">Location</p><p class="v"><?php echo e($newTicket->getLocation()); ?></p></div>
        <div class="item"><p class="k">Category</p><p class="v"><?php echo e($newTicket->getCategory()); ?></p></div>
        <div class="item"><p class="k">Priority</p><p class="v"><span class="<?php echo priorityClass($newTicket->getPriority()); ?>"><?php echo e($newTicket->getPriority()); ?></span></p></div>
        <div class="item"><p class="k">Status</p><p class="v"><span class="<?php echo statusClass($newTicket->getStatus()); ?>"><?php echo e($newTicket->getStatus()); ?></span></p></div>
        <div class="item"><p class="k">Created</p><p class="v"><?php echo e(formatDate($newTicket->getCreatedAt())); ?></p></div>
      </div>

      <p style="margin-top:1rem"><strong>Problem described:</strong><br><?php echo nl2br(e($newTicket->getDescription())); ?></p>

      <div class="result-box">
        <h3>Diagnosis &mdash; <?php echo e($newTicket->getClassName()); ?></h3>
        <p style="margin:0"><?php echo e($newTicket->diagnose()); ?></p>
      </div>
      <div class="result-box resolution">
        <h3>Recommended Resolution &mdash; <?php echo e($newTicket->getClassName()); ?></h3>
        <p style="margin:0"><?php echo e($newTicket->getResolution()); ?></p>
      </div>
      <p class="muted">Assigned team: <?php echo e($newTicket->getTeam()); ?></p>

      <div class="form-actions">
        <a class="btn btn-primary" href="ticket-details.php?id=<?php echo urlencode($newTicket->getTicketId()); ?>">Open Ticket Details</a>
        <a class="btn btn-light" href="tickets.php">All Tickets</a>
        <a class="btn btn-light" href="submit-ticket.php">Submit Another Ticket</a>
      </div>
    </div>
  <?php endif; ?>

  <?php if (!empty($errors)): ?>
    <div class="alert alert-error" role="alert">
      <strong>Please complete all required fields.</strong>
      <ul>
        <?php foreach ($errors as $message): ?><li><?php echo e($message); ?></li><?php endforeach; ?>
      </ul>
    </div>
  <?php endif; ?>

  <?php if ($newTicket === null): ?>
    <form class="panel" method="post" action="submit-ticket.php" novalidate>
      <h2>Ticket Information</h2>
      <div class="form-grid">

        <div class="field <?php echo isset($errors['requesterName']) ? 'field-error' : ''; ?>">
          <label for="requesterName">Requester Name <span aria-hidden="true">*</span></label>
          <input type="text" id="requesterName" name="requesterName" maxlength="60"
                 value="<?php echo e($old['requesterName']); ?>" placeholder="e.g. Tommy Candia Jr." required>
          <?php if (isset($errors['requesterName'])): ?><p class="error-text"><?php echo e($errors['requesterName']); ?></p><?php endif; ?>
        </div>

        <div class="field <?php echo isset($errors['requesterType']) ? 'field-error' : ''; ?>">
          <label for="requesterType">Requester Type <span aria-hidden="true">*</span></label>
          <select id="requesterType" name="requesterType" required>
            <option value="">-- Select --</option>
            <?php foreach (requesterTypes() as $type): ?>
              <option value="<?php echo e($type); ?>" <?php echo $old['requesterType'] === $type ? 'selected' : ''; ?>><?php echo e($type); ?></option>
            <?php endforeach; ?>
          </select>
          <?php if (isset($errors['requesterType'])): ?><p class="error-text"><?php echo e($errors['requesterType']); ?></p><?php endif; ?>
        </div>

        <div class="field <?php echo isset($errors['location']) ? 'field-error' : ''; ?>">
          <label for="location">Location <span aria-hidden="true">*</span></label>
          <select id="location" name="location" required>
            <option value="">-- Select --</option>
            <?php foreach (locations() as $loc): ?>
              <option value="<?php echo e($loc); ?>" <?php echo $old['location'] === $loc ? 'selected' : ''; ?>><?php echo e($loc); ?></option>
            <?php endforeach; ?>
          </select>
          <?php if (isset($errors['location'])): ?><p class="error-text"><?php echo e($errors['location']); ?></p><?php endif; ?>
        </div>

        <div class="field <?php echo isset($errors['customLocation']) ? 'field-error' : ''; ?>" id="customLocationWrap" style="display:none">
          <label for="customLocation">Exact Location (if "Other")</label>
          <input type="text" id="customLocation" name="customLocation" maxlength="60"
                 value="<?php echo e($old['customLocation']); ?>" placeholder="e.g. Guidance Office">
          <?php if (isset($errors['customLocation'])): ?><p class="error-text"><?php echo e($errors['customLocation']); ?></p><?php endif; ?>
        </div>

        <div class="field <?php echo isset($errors['category']) ? 'field-error' : ''; ?>">
          <label for="category">Problem Category <span aria-hidden="true">*</span></label>
          <select id="category" name="category" required>
            <option value="">-- Select --</option>
            <?php foreach (categories() as $cat): ?>
              <option value="<?php echo e($cat); ?>" <?php echo $old['category'] === $cat ? 'selected' : ''; ?>><?php echo e($cat); ?></option>
            <?php endforeach; ?>
          </select>
          <p class="hint">The category decides which child class is created (HardwareTicket, SoftwareTicket, NetworkTicket or AccountAccessTicket).</p>
          <?php if (isset($errors['category'])): ?><p class="error-text"><?php echo e($errors['category']); ?></p><?php endif; ?>
        </div>

        <div class="field <?php echo isset($errors['priority']) ? 'field-error' : ''; ?>">
          <label for="priority">Urgency / Priority <span aria-hidden="true">*</span></label>
          <select id="priority" name="priority" required>
            <option value="">-- Select --</option>
            <option value="Smart" <?php echo $old['priority'] === 'Smart' ? 'selected' : ''; ?>>Smart Priority (let the system decide)</option>
            <?php foreach (priorities() as $p): ?>
              <option value="<?php echo e($p); ?>" <?php echo $old['priority'] === $p ? 'selected' : ''; ?>><?php echo e($p); ?></option>
            <?php endforeach; ?>
          </select>
          <p class="hint">Smart Priority reads your description and suggests Low, Medium, High or Critical.</p>
          <?php if (isset($errors['priority'])): ?><p class="error-text"><?php echo e($errors['priority']); ?></p><?php endif; ?>
        </div>

        <div class="field full <?php echo isset($errors['description']) ? 'field-error' : ''; ?>">
          <label for="description">Problem Description <span aria-hidden="true">*</span></label>
          <textarea id="description" name="description" maxlength="1000"
                    placeholder="Describe the problem clearly..." required><?php echo e($old['description']); ?></textarea>
          <p class="hint">At least 15 characters. <span id="descCount">0 characters</span></p>
          <?php if (isset($errors['description'])): ?><p class="error-text"><?php echo e($errors['description']); ?></p><?php endif; ?>
        </div>
      </div>

      <div class="form-actions">
        <button type="submit" class="btn btn-primary">Submit Ticket</button>
        <button type="reset" class="btn btn-light">Clear Form</button>
        <a class="btn btn-light" href="tickets.php">Cancel</a>
      </div>
    </form>
  <?php endif; ?>

  </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
