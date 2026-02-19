<?php
/**
 * Service Contract Schema - SDI Business Model
 * Tracks recurring service agreements with Evoqua equipment
 */
return [
    'contract_id',          // Unique identifier
    'contact_id',           // Link to contact
    'customer_id',          // Link to customer  
    'contract_type',        // New / Renewal / Upsell
    'contract_status',      // Draft / Active / Expiring / Expired / Cancelled
    'equipment_type',       // Softener / RO System / Filtration / DI System / Other
    'monthly_fee',          // Monthly recurring revenue
    'annual_value',         // Total annual contract value
    'payment_frequency',    // Monthly / Quarterly / Annual
    'contract_term',        // Term length in months (12/24/36/etc)
    'start_date',          // Contract start date
    'end_date',            // Contract end date
    'renewal_date',        // Auto-renewal date
    'auto_renew',          // Yes / No
    'notice_period',       // Cancellation notice period (days)
    'evoqua_account',      // Evoqua account number
    'evoqua_contract',     // Evoqua contract number
    'equipment_ids',       // Comma-separated equipment IDs
    'service_frequency',   // Weekly / Bi-weekly / Monthly / Quarterly
    'last_service_date',   // Most recent service visit
    'next_service_date',   // Scheduled next service
    'notes',               // Additional contract notes
    'created_date',        // Contract creation date
    'created_by',          // User who created contract
    'modified_date',       // Last modification date
    'modified_by'          // User who last modified
];
