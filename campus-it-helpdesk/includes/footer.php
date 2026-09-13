</main>

<footer class="site-footer">
  <div class="container footer-inner">
    <div>
      <strong>CAMPUS IT HELP DESK</strong>
      <p> IT Support Ticket Management System.</p>
      
   
  </div>
</footer>

<script>
  (function () {
    var toggle = document.getElementById('navToggle');
    var nav = document.getElementById('mainNav');
    if (!toggle || !nav) return;
    toggle.addEventListener('click', function () {
      var open = nav.classList.toggle('open');
      toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
    });
  })();

  // Show the "Other location" text box only when needed
  (function () {
    var select = document.getElementById('location');
    var wrap = document.getElementById('customLocationWrap');
    if (!select || !wrap) return;
    function sync() { wrap.style.display = select.value === 'Other' ? 'block' : 'none'; }
    select.addEventListener('change', sync);
    sync();
  })();

  // Live character counter for the description
  (function () {
    var area = document.getElementById('description');
    var out = document.getElementById('descCount');
    if (!area || !out) return;
    function sync() { out.textContent = area.value.trim().length + ' characters'; }
    area.addEventListener('input', sync);
    sync();
  })();
</script>
</body>
</html>
