<?php
return [
  'customer_id',
  'contact_id', // Foreign key to contacts.id
  'address',
  'tank_count',
  // 'tank_size', // Removed: not present in DB schema
  'last_delivery',
  'last_modified'
];
