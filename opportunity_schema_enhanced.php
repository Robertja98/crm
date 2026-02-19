<?php
$schema = [
    'id' => 'integer',
    'name' => 'string',
    'value' => 'float',
    'stage' => 'string',
    'contact_id' => 'integer',
];
return $schema;
?>
<?php
/**
 * Enhanced Opportunity Schema - Hybrid Business Model
 * Handles both equipment sales and service contract opportunities
 */
return [
    'id',                   // Unique opportunity ID
    'contact_id',           // Link to contact
    'customer_id',          // Link to customer (optional)
    'opportunity_type',     // Equipment Sale / Service Contract / Renewal / Upsell / Replacement
    'revenue_model',        // One-time / Monthly Recurring / Annual Recurring
    'name',                 // Opportunity name/description
    'value',                // Total opportunity value
    'monthly_value',        // Monthly recurring value (if applicable)
    'annual_value',         // Annual recurring value (if applicable)
    'contract_term',        // Contract term in months (for SDI)
    'stage',                // Prospecting / Qualification / Proposal / Negotiation / Closed Won / Closed Lost
    'probability',          // Win probability (0-100)
    'expected_close',       // Expected close date
    'actual_close',         // Actual close date
    'equipment_type',       // What equipment/service involved
    'product_details',      // Detailed product/service description
    'competitor',           // Competing against (if replacement)
    'payment_terms',        // Net 30 / Net 60 / Monthly / Quarterly
    'source',               // Lead source (Referral / Website / Cold Call / etc)
    'lost_reason',          // Why opportunity was lost (if applicable)
    'contract_id',          // Linked contract ID (after won)
    'equipment_ids',        // Linked equipment IDs (after won)
    'notes',                // Opportunity notes
    'created_date',         // Opportunity creation date
    'created_by',           // User who created
    'modified_date',        // Last modification date
    'modified_by'           // User who last modified
];
