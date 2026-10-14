<?php

return [
    'title' => 'Quality Control (QC) & COA Validation',
    'subtitle' => 'Laboratory assay potency, microbial limits, heavy metals testing, and quarantine release.',
    'create_inspection' => 'Log New QC Inspection',
    'inspection_code' => 'Inspection Code',
    'batch' => 'Inspected Batch',
    'sample_size' => 'Sample Size Drawn',
    'inspector' => 'Testing Chemist / Analyst',
    'verifier' => 'QA Director (Verifier)',
    'verdict' => 'Laboratory Verdict',
    'tested_at' => 'Testing Completed At',
    'verified_at' => 'Authorization Timestamp',
    'parameters' => 'Analytical Test Parameters',
    'param_name' => 'Parameter Name (e.g. Assay, pH, Moisture)',
    'specification' => 'Clinical Specification Standard',
    'result_value' => 'Observed Laboratory Result',
    'param_status' => 'Parameter Verdict (Pass / Fail)',
    'coa_file' => 'Certificate of Analysis (COA PDF)',
    'waiver_reason' => 'Conditional Pass Waiver Justification',
    'waiver_expiry' => 'Waiver Expiry Date',
    'actions' => [
        'verify_release' => 'Authorize & Verify Release',
        'download_coa' => 'Download Lab COA PDF',
    ],
    'messages' => [
        'created' => 'QC Inspection logged successfully. Awaiting second-tier verification.',
        'approved' => 'Inspection verified and batch successfully released from quarantine into approved stock.',
        'rejected' => 'Inspection verified and batch permanently locked as REJECTED.',
    ],
    'errors' => [
        'self_verification_prohibited' => 'Separation of duties violation: An inspector cannot verify their own inspection.',
    ],
];
