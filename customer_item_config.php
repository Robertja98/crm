<?php
// Maps to columns in the `equipment` table
return [
  'serial_number',    // tank number
  'tank_size',
  'resin_type',       // type of resin
  'resin_qty_cuft',   // resin quantity in cubic feet
  'last_service_date',// delivery date
  'regeneration_id',  // regeneration number
  'purchase_order',
  'ownership',        // rental | customer-owned | purchased
];
