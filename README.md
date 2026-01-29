# CP-Project-Audit-logs-

<h2> Phase 1 Progress — Activity Log Viewer (M16)</h2>

<h3>✅ Completed</h3>

<h4>Database Setup</h4>
<ul>
  <li>Created project database <strong>audit_db</strong></li>
  <li>Designed and created the <strong>audit_log</strong> table to store user activity details (actor, module, action, IP, timestamps, old/new data)</li>
</ul>

<h4>Dummy Data Simulation</h4>
<ul>
  <li>Inserted sample log entries to simulate real system actions (insert, update, delete, login, etc.)</li>
  <li>Used JSON fields to store <strong>before</strong> and <strong>after</strong> snapshots for update operations</li>
</ul>

<h4>Admin Log Viewer (logs.php)</h4>
<ul>
  <li>Built the initial <strong>Activity Log Viewer UI</strong> for admins</li>
  <li>Connected to database and fetched logs ordered by latest activity</li>
  <li>Displayed data in a structured tabular format</li>
</ul>

<h4>Basic Search &amp; Filters</h4>
<ul>
  <li>Implemented initial filtering functionality based on the action (Insert, Update, Delete) </li>
  <li>Designed the system to easily extend with advanced filters</li>
</ul>

