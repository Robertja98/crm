<nav class="navbar">
  <ul>
    <li><a href="dashboard.php" class="<?= $currentPage === 'dashboard.php' ? 'active' : '' ?>">Dashboard</a></li>
    <li><a href="contacts_list.php" class="<?= $currentPage === 'contacts_list.php' ? 'active' : '' ?>">Contact List</a></li>
    <li><a href="add_customer.php" class="<?= $currentPage === 'add_customer.php' ? 'active' : '' ?>">➕ Add Customer</a></li>
    <li><a href="customers_list.php" class="<?= $currentPage === 'customers_list.php' ? 'active' : '' ?>">📋 Customer List</a></li>
    <li><a href="customer_view.php?id=demo" class="<?= $currentPage === 'customer_view.php' ? 'active' : '' ?>">👁 View Customer</a></li>
    <li><a href="calendar.php" class="<?= $currentPage === 'calendar.php' ? 'active' : '' ?>">📅 Calendar</a></li>
    <li><a href="contact_form.php" class="<?= $currentPage === 'contact_form.php' ? 'active' : '' ?>">Add Contact</a></li>
    <li><a href="opportunities_list.php" class="<?= $currentPage === 'opportunities_list.php' ? 'active' : '' ?>">Opportunities</a></li>
    <li><a href="opportunity_form.php" class="<?= $currentPage === 'opportunity_form.php' ? 'active' : '' ?>">Add Opportunity</a></li>
    <li><a href="import_contacts.php" class="<?= $currentPage === 'import_contacts.php' ? 'active' : '' ?>">Import</a></li>
    <li><a href="export_contacts.php" class="<?= $currentPage === 'export_contacts.php' ? 'active' : '' ?>">Export</a></li>
    <li><a href="inventory_list.php" class="<?= $currentPage === 'inventory_list.php' ? 'active' : '' ?>">📦 Inventory</a></li>
    <li><a href="purchase_orders_list.php" class="<?= $currentPage === 'purchase_orders_list.php' ? 'active' : '' ?>">🧾 Purchase Orders</a></li>
  </ul>
</nav>
