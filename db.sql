-- Create the database
CREATE DATABASE IF NOT EXISTS who_screening_tool;

-- Use the created database
USE who_screening_tool;

-- Create the main screenings table (matching IndexedDB structure with sync logic additions)
CREATE TABLE screenings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    screeningId VARCHAR(255) UNIQUE NOT NULL COMMENT 'Unique identifier for each screening (SCR-XXXXXX)',
    travellerInfo JSON COMMENT 'JSON object containing traveller information',
    screeningDetails JSON COMMENT 'JSON object containing screening details',
    symptoms JSON COMMENT 'JSON array of symptoms',
    riskFactors JSON COMMENT 'JSON array of risk factors',
    suspectedDiseases JSON COMMENT 'JSON array of suspected diseases with accuracy',
    timestamp DATETIME NOT NULL COMMENT 'Timestamp of when the screening was conducted',
    metadata JSON COMMENT 'JSON object containing additional metadata',
    createdAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Record creation timestamp',
    updatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Record last update timestamp',
    
    -- Add sync-related columns
    syncStatus ENUM('pending', 'synced', 'failed') DEFAULT 'pending' COMMENT 'Current sync status of the record',
    lastSyncAttempt DATETIME COMMENT 'Timestamp of the last sync attempt',
    syncRetryCount INT DEFAULT 0 COMMENT 'Number of sync retry attempts',
    localCreatedAt DATETIME COMMENT 'Timestamp of when the record was created locally',
    localUpdatedAt DATETIME COMMENT 'Timestamp of when the record was last updated locally',
    deviceId VARCHAR(255) COMMENT 'Identifier of the device that created/updated the record',
    
    -- Add generated columns for searchable fields
    travellerName VARCHAR(255) GENERATED ALWAYS AS (JSON_UNQUOTE(JSON_EXTRACT(travellerInfo, '$.travellerName'))) STORED,
    travellerAgeGroup VARCHAR(50) GENERATED ALWAYS AS (JSON_UNQUOTE(JSON_EXTRACT(travellerInfo, '$.travellerAgeGroup'))) STORED,
    travellerGender VARCHAR(50) GENERATED ALWAYS AS (JSON_UNQUOTE(JSON_EXTRACT(travellerInfo, '$.travellerGender'))) STORED,
    contactInfo VARCHAR(255) GENERATED ALWAYS AS (JSON_UNQUOTE(JSON_EXTRACT(travellerInfo, '$.contactInfo'))) STORED,
    travelDestination VARCHAR(255) GENERATED ALWAYS AS (JSON_UNQUOTE(JSON_EXTRACT(screeningDetails, '$.travelDestination'))) STORED,
    countryOfOrigin VARCHAR(255) GENERATED ALWAYS AS (JSON_UNQUOTE(JSON_EXTRACT(screeningDetails, '$.countryOfOrigin'))) STORED,
    hasSymptoms BOOLEAN GENERATED ALWAYS AS (JSON_EXTRACT(screeningDetails, '$.hasSymptoms')) STORED,
    classification VARCHAR(50) GENERATED ALWAYS AS (JSON_UNQUOTE(JSON_EXTRACT(screeningDetails, '$.classification'))) STORED,
    accuracyProbability DECIMAL(5,2) GENERATED ALWAYS AS (JSON_EXTRACT(screeningDetails, '$.accuracyProbability')) STORED,
    recommendedAction TEXT GENERATED ALWAYS AS (JSON_UNQUOTE(JSON_EXTRACT(screeningDetails, '$.recommendedAction'))) STORED,
    poe VARCHAR(255) GENERATED ALWAYS AS (JSON_UNQUOTE(JSON_EXTRACT(metadata, '$.poe'))) STORED,
    province VARCHAR(255) GENERATED ALWAYS AS (JSON_UNQUOTE(JSON_EXTRACT(metadata, '$.province'))) STORED,
    district VARCHAR(255) GENERATED ALWAYS AS (JSON_UNQUOTE(JSON_EXTRACT(metadata, '$.district'))) STORED,
    screeningOfficer VARCHAR(255) GENERATED ALWAYS AS (JSON_UNQUOTE(JSON_EXTRACT(metadata, '$.screeningOfficer'))) STORED,
    recentTravelHistory TEXT GENERATED ALWAYS AS (JSON_UNQUOTE(JSON_EXTRACT(screeningDetails, '$.recentTravelHistory'))) STORED,
    searchableText TEXT GENERATED ALWAYS AS (
        CONCAT_WS(' ',
            JSON_UNQUOTE(JSON_EXTRACT(travellerInfo, '$.travellerName')),
            JSON_UNQUOTE(JSON_EXTRACT(screeningDetails, '$.travelDestination')),
            JSON_UNQUOTE(JSON_EXTRACT(screeningDetails, '$.countryOfOrigin')),
            JSON_UNQUOTE(JSON_EXTRACT(symptoms, '$[*]')),
            JSON_UNQUOTE(JSON_EXTRACT(riskFactors, '$[*]')),
            JSON_UNQUOTE(JSON_EXTRACT(suspectedDiseases, '$[*].disease')),
            JSON_UNQUOTE(JSON_EXTRACT(metadata, '$.poe')),
            JSON_UNQUOTE(JSON_EXTRACT(metadata, '$.province')),
            JSON_UNQUOTE(JSON_EXTRACT(metadata, '$.district')),
            JSON_UNQUOTE(JSON_EXTRACT(metadata, '$.screeningOfficer'))
        )
    ) STORED,
    
    -- Add indexes for improved query performance
    INDEX idx_syncStatus (syncStatus),
    INDEX idx_lastSyncAttempt (lastSyncAttempt),
    INDEX idx_deviceId (deviceId),
    INDEX idx_travellerName (travellerName),
    INDEX idx_travelDestination (travelDestination),
    INDEX idx_countryOfOrigin (countryOfOrigin),
    INDEX idx_hasSymptoms (hasSymptoms),
    INDEX idx_classification (classification),
    INDEX idx_poe (poe),
    INDEX idx_province (province),
    INDEX idx_district (district),
    INDEX idx_screeningOfficer (screeningOfficer),
    FULLTEXT INDEX idx_searchableText (searchableText)
);

-- Create indexes for improved query performance
CREATE INDEX idx_screeningId ON screenings(screeningId);
CREATE INDEX idx_timestamp ON screenings(timestamp);
CREATE INDEX idx_traveller_name ON screenings(travellerName);
CREATE INDEX idx_travel_destination ON screenings(travelDestination);
CREATE INDEX idx_country_of_origin ON screenings(countryOfOrigin);
CREATE INDEX idx_has_symptoms ON screenings(hasSymptoms);
CREATE INDEX idx_classification ON screenings(classification);
CREATE INDEX idx_poe ON screenings(poe);
CREATE INDEX idx_province ON screenings(province);
CREATE INDEX idx_district ON screenings(district);

-- Create full-text indexes for text searching
CREATE FULLTEXT INDEX idx_fulltext_screening ON screenings(travellerName, travelDestination, countryOfOrigin, poe, province, district);
CREATE FULLTEXT INDEX idx_fulltext_searchable ON screenings(searchableText);

-- Create a table for storing sync status (useful for handling offline scenarios)
CREATE TABLE sync_status (
    id INT AUTO_INCREMENT PRIMARY KEY,
    screeningId VARCHAR(255) UNIQUE NOT NULL COMMENT 'Reference to screenings table',
    status ENUM('pending', 'synced', 'failed') DEFAULT 'pending' COMMENT 'Sync status',
    lastSyncAttempt DATETIME COMMENT 'Timestamp of last sync attempt',
    errorMessage TEXT COMMENT 'Error message if sync failed',
    createdAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Record creation timestamp',
    updatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Record last update timestamp',
    FOREIGN KEY (screeningId) REFERENCES screenings(screeningId) ON DELETE CASCADE
);

-- Create index for sync status queries
CREATE INDEX idx_sync_status ON sync_status(status, lastSyncAttempt);

-- Create a table for audit logs
CREATE TABLE audit_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    screeningId VARCHAR(255) COMMENT 'Reference to screenings table',
    action ENUM('create', 'update', 'delete') NOT NULL COMMENT 'Type of action performed',
    performedBy VARCHAR(255) COMMENT 'User or system that performed the action',
    actionTimestamp DATETIME NOT NULL COMMENT 'Timestamp of when the action was performed',
    oldValue JSON COMMENT 'Previous state of the record (for updates and deletes)',
    newValue JSON COMMENT 'New state of the record (for creates and updates)',
    FOREIGN KEY (screeningId) REFERENCES screenings(screeningId) ON DELETE SET NULL
);

-- Create indexes for audit log queries
CREATE INDEX idx_audit_screeningId ON audit_logs(screeningId);
CREATE INDEX idx_audit_action ON audit_logs(action);
CREATE INDEX idx_audit_timestamp ON audit_logs(actionTimestamp);

-- Create a table for storing application settings
CREATE TABLE app_settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    settingKey VARCHAR(255) UNIQUE NOT NULL COMMENT 'Unique identifier for the setting',
    settingValue JSON COMMENT 'Value of the setting in JSON format',
    description TEXT COMMENT 'Description of the setting',
    createdAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Record creation timestamp',
    updatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Record last update timestamp'
);

-- Create index for quick setting lookups
CREATE INDEX idx_setting_key ON app_settings(settingKey);

-- Create a table for storing user information
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(255) UNIQUE NOT NULL COMMENT 'Unique username for the user',
    passwordHash VARCHAR(255) NOT NULL COMMENT 'Hashed password',
    email VARCHAR(255) UNIQUE NOT NULL COMMENT 'User email address',
    role ENUM('admin', 'screener', 'supervisor', 'province', 'district', 'national') NOT NULL COMMENT 'User role for access control',
    poeId INT COMMENT 'Associated Point of Entry ID', -- Match with INT type of points_of_entry.id
    lastLogin DATETIME COMMENT 'Timestamp of last login',
    createdAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Record creation timestamp',
    updatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Record last update timestamp',
    FOREIGN KEY (poeId) REFERENCES points_of_entry(id) ON DELETE SET NULL
);

CREATE INDEX idx_username ON users(username);


-- Create index for user lookups
CREATE INDEX idx_username ON users(username);

-- Create a table for Points of Entry (POE)
CREATE TABLE points_of_entry (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL COMMENT 'Name of the Point of Entry',
    type ENUM('airport', 'land_border', 'seaport') NOT NULL COMMENT 'Type of POE',
    location JSON COMMENT 'JSON object containing location details (country, province, district, etc.)',
    status ENUM('active', 'inactive', 'maintenance') DEFAULT 'active' COMMENT 'Current status of the POE',
    capacity INT COMMENT 'Daily capacity of the POE',
    createdAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Record creation timestamp',
    updatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Record last update timestamp'
);

-- Create index for POE lookups
CREATE INDEX idx_poe_name ON points_of_entry(name);
CREATE INDEX idx_poe_type ON points_of_entry(type);
CREATE INDEX idx_poe_status ON points_of_entry(status);

-- Create a table for diseases
CREATE TABLE diseases (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) UNIQUE NOT NULL COMMENT 'Name of the disease',
    description TEXT COMMENT 'Description of the disease',
    icdCode VARCHAR(50) COMMENT 'ICD-10 or ICD-11 code for the disease',
    createdAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Record creation timestamp',
    updatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Record last update timestamp'
);

-- Create index for disease lookups
CREATE INDEX idx_disease_name ON diseases(name);
CREATE INDEX idx_disease_icd ON diseases(icdCode);

-- Create a table for symptoms
CREATE TABLE symptoms (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) UNIQUE NOT NULL COMMENT 'Name of the symptom',
    description TEXT COMMENT 'Description of the symptom',
    createdAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Record creation timestamp',
    updatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Record last update timestamp'
);

-- Create index for symptom lookups
CREATE INDEX idx_symptom_name ON symptoms(name);

-- Create a table for risk factors
CREATE TABLE risk_factors (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) UNIQUE NOT NULL COMMENT 'Name of the risk factor',
    description TEXT COMMENT 'Description of the risk factor',
    createdAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Record creation timestamp',
    updatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Record last update timestamp'
);

-- Create index for risk factor lookups
CREATE INDEX idx_risk_factor_name ON risk_factors(name);

-- Create a table for alerts
CREATE TABLE alerts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    poeId INT COMMENT 'Associated Point of Entry ID',
    type ENUM('health_risk', 'capacity_warning', 'system_issue') NOT NULL COMMENT 'Type of alert',
    severity ENUM('low', 'medium', 'high', 'critical') NOT NULL COMMENT 'Severity of the alert',
    message TEXT NOT NULL COMMENT 'Alert message',
    isResolved BOOLEAN DEFAULT FALSE COMMENT 'Whether the alert has been resolved',
    createdAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Record creation timestamp',
    updatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Record last update timestamp',
    resolvedAt DATETIME COMMENT 'Timestamp when the alert was resolved',
    FOREIGN KEY (poeId) REFERENCES points_of_entry(id) ON DELETE SET NULL
);

-- Create indexes for alert queries
CREATE INDEX idx_alert_poe ON alerts(poeId);
CREATE INDEX idx_alert_type ON alerts(type);
CREATE INDEX idx_alert_severity ON alerts(severity);
CREATE INDEX idx_alert_status ON alerts(isResolved);

-- Create a table for reports
CREATE TABLE reports (
    id INT AUTO_INCREMENT PRIMARY KEY,
    poeId INT COMMENT 'Associated Point of Entry ID',
    userId INT NOT NULL COMMENT 'User who generated the report',
    type ENUM('daily', 'weekly', 'monthly', 'custom') NOT NULL COMMENT 'Type of report',
    startDate DATE NOT NULL COMMENT 'Start date of the report period',
    endDate DATE NOT NULL COMMENT 'End date of the report period',
    reportData JSON COMMENT 'JSON object containing the report data',
    createdAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Record creation timestamp',
    FOREIGN KEY (poeId) REFERENCES points_of_entry(id) ON DELETE SET NULL,
    FOREIGN KEY (userId) REFERENCES users(id)
);

-- Create indexes for report queries
CREATE INDEX idx_report_poe ON reports(poeId);
CREATE INDEX idx_report_user ON reports(userId);
CREATE INDEX idx_report_type ON reports(type);
CREATE INDEX idx_report_date_range ON reports(startDate, endDate);

-- Create a table for traveler information (for more detailed traveler tracking)
CREATE TABLE travelers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    passportNumber VARCHAR(50) UNIQUE NOT NULL COMMENT 'Passport number of the traveler',
    fullName VARCHAR(255) NOT NULL COMMENT 'Full name of the traveler',
    dateOfBirth DATE NOT NULL COMMENT 'Date of birth of the traveler',
    nationality VARCHAR(100) NOT NULL COMMENT 'Nationality of the traveler',
    gender ENUM('male', 'female', 'other') NOT NULL COMMENT 'Gender of the traveler',
    createdAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Record creation timestamp',
    updatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Record last update timestamp'
);

-- Create indexes for traveler queries
CREATE INDEX idx_traveler_passport ON travelers(passportNumber);
CREATE INDEX idx_traveler_name ON travelers(fullName);

-- Create a table for quarantine facilities
CREATE TABLE quarantine_facilities (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL COMMENT 'Name of the quarantine facility',
    location JSON COMMENT 'JSON object containing location details',
    capacity INT NOT NULL COMMENT 'Total capacity of the facility',
    currentOccupancy INT DEFAULT 0 COMMENT 'Current number of occupants',
    status ENUM('active', 'inactive', 'maintenance') DEFAULT 'active' COMMENT 'Current status of the facility',
    createdAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Record creation timestamp',
    updatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Record last update timestamp'
);

-- Create indexes for quarantine facility queries
CREATE INDEX idx_facility_name ON quarantine_facilities(name);
CREATE INDEX idx_facility_status ON quarantine_facilities(status);

-- Create a table for quarantine assignments
CREATE TABLE quarantine_assignments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    travelerId INT NOT NULL COMMENT 'ID of the traveler assigned to quarantine',
    facilityId INT NOT NULL COMMENT 'ID of the quarantine facility',
    screeningId VARCHAR(255) NOT NULL COMMENT 'ID of the screening that led to quarantine',
    startDate DATE NOT NULL COMMENT 'Start date of quarantine',
    endDate DATE COMMENT 'End date of quarantine',
    status ENUM('active', 'completed', 'terminated') DEFAULT 'active' COMMENT 'Status of the quarantine assignment',
    createdAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Record creation timestamp',
    updatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Record last update timestamp',
    FOREIGN KEY (travelerId) REFERENCES travelers(id),
    FOREIGN KEY (facilityId) REFERENCES quarantine_facilities(id),
    FOREIGN KEY (screeningId) REFERENCES screenings(screeningId)
);

-- Create indexes for quarantine assignment queries
CREATE INDEX idx_assignment_traveler ON quarantine_assignments(travelerId);
CREATE INDEX idx_assignment_facility ON  quarantine_assignments(facilityId);
CREATE INDEX idx_assignment_screening ON quarantine_assignments(screeningId);
CREATE INDEX idx_assignment_status ON quarantine_assignments(status);

-- Create a table for system logs
CREATE TABLE system_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    logLevel ENUM('info', 'warning', 'error', 'critical') NOT NULL COMMENT 'Severity level of the log',
    message TEXT NOT NULL COMMENT 'Log message',
    source VARCHAR(255) COMMENT 'Source of the log (e.g., module name, function)',
    timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Timestamp of the log entry'
);

-- Create indexes for system log queries
CREATE INDEX idx_log_level ON system_logs(logLevel);
CREATE INDEX idx_log_timestamp ON system_logs(timestamp);

-- Create a table for notification templates
CREATE TABLE notification_templates (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) UNIQUE NOT NULL COMMENT 'Name of the notification template',
    type ENUM('email', 'sms', 'push') NOT NULL COMMENT 'Type of notification',
    subject VARCHAR(255) COMMENT 'Subject line for email notifications',
    content TEXT NOT NULL COMMENT 'Content of the notification',
    variables JSON COMMENT 'JSON array of variables used in the template',
    createdAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Record creation timestamp',
    updatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Record last update timestamp'
);

-- Create index for notification template lookups
CREATE INDEX idx_template_name ON notification_templates(name);
CREATE INDEX idx_template_type ON notification_templates(type);

-- Create a table for sent notifications
CREATE TABLE sent_notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    templateId INT NOT NULL COMMENT 'ID of the notification template used',
    recipientType ENUM('user', 'traveler') NOT NULL COMMENT 'Type of recipient',
    recipientId INT NOT NULL COMMENT 'ID of the recipient (user or traveler)',
    content TEXT NOT NULL COMMENT 'Actual content of the sent notification',
    status ENUM('sent', 'delivered', 'failed') NOT NULL COMMENT 'Status of the notification',
    sentAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Timestamp when the notification was sent',
    deliveredAt TIMESTAMP COMMENT 'Timestamp when the notification was delivered',
    FOREIGN KEY (templateId) REFERENCES notification_templates(id)
);

-- Create indexes for sent notification queries
CREATE INDEX idx_notification_template ON sent_notifications(templateId);
CREATE INDEX idx_notification_recipient ON sent_notifications(recipientType, recipientId);
CREATE INDEX idx_notification_status ON sent_notifications(status);



ALTER TABLE users
MODIFY COLUMN role ENUM('admin', 'screener', 'supervisor', 'province', 'district', 'national') NOT NULL;



-- CREATE TABLE final_screenings_table (
--     id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY COMMENT 'Unique identifier for each screening record',
--     screening_id VARCHAR(20) UNIQUE NOT NULL COMMENT 'Human-readable unique identifier for each screening session',
--     timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Date and time when the screening was conducted',

--     -- Traveler Information
--     traveler_name VARCHAR(100) COMMENT 'Full name of the traveler being screened',
--     traveler_gender VARCHAR(10) COMMENT 'Gender of the traveler (e.g., male, female, other)',
--     contact_info VARCHAR(200) COMMENT 'Contact information of the traveler for follow-up if needed',

--     -- Travel Information
--     travel_destination VARCHAR(100) COMMENT 'Intended destination of the traveler',
--     country_of_origin VARCHAR(100) COMMENT 'Country from which the traveler is arriving',

--     -- Screening Details
--     has_symptoms BOOLEAN COMMENT 'Boolean flag indicating if the traveler has any symptoms',
--     symptoms JSON COMMENT 'JSON array of symptoms reported by the traveler',
--     risk_factors JSON COMMENT 'JSON array of risk factors identified for the traveler',

--     -- Rash/Skin Photo
--     skin_photo_url VARCHAR(255) COMMENT 'URL or path to the photo of skin condition, if applicable',

--     -- Classification Results
--     classification VARCHAR(50) COMMENT 'Final classification of the traveler (e.g., Suspected Case, Contact, Non-Case)',
--     suspected_diseases JSON COMMENT 'JSON array of suspected diseases with their probability scores',
--     confidence_level FLOAT COMMENT 'Confidence level of the classification (0-100)',
--     recommended_action TEXT COMMENT 'Recommended action based on the screening results',

--     -- Scoring and Analysis
--     symptom_score FLOAT COMMENT 'Calculated score based on reported symptoms',
--     travel_risk_score FLOAT COMMENT 'Calculated score based on travel history and destination',
--     endemic_risk_score FLOAT COMMENT 'Calculated score based on endemic diseases in origin/destination',
--     overall_risk_score FLOAT COMMENT 'Overall risk score combining all factors',

--     -- Metadata
--     poe_id INT UNSIGNED COMMENT 'Unique identifier for the Point of Entry where screening was conducted',
--     poe_name VARCHAR(100) COMMENT 'Name of the Point of Entry',
--     poe_type VARCHAR(50) COMMENT 'Type of Point of Entry (e.g., airport, seaport, land crossing)',
--     poe_location JSON COMMENT 'JSON object containing detailed location information of the POE',
--     poe_status VARCHAR(20) COMMENT 'Current status of the Point of Entry',
--     poe_capacity INT UNSIGNED COMMENT 'Capacity of the Point of Entry for handling travelers',

--     -- User Information
--     screening_officer_id INT UNSIGNED COMMENT 'Unique identifier of the officer conducting the screening',
--     screening_officer_username VARCHAR(50) COMMENT 'Username of the screening officer',
--     screening_officer_role VARCHAR(20) COMMENT 'Role of the screening officer in the system',

--     -- Additional Data
--     additional_notes TEXT COMMENT 'Any additional notes or comments about the screening',
--     follow_up_required BOOLEAN DEFAULT FALSE COMMENT 'Boolean flag indicating if follow-up is required',
--     follow_up_date DATE COMMENT 'Date scheduled for follow-up, if required',

--     -- Audit Trail
--     created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Timestamp when the record was created in the database',
--     updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Timestamp when the record was last updated',

--     -- Raw Data Storage
--     raw_form_data JSON COMMENT 'JSON object containing the raw form data submitted during screening'
-- );

-- Create indexes for faster querying
CREATE INDEX idx_screening_id ON final_screenings_table(screening_id);
CREATE INDEX idx_timestamp ON final_screenings_table(timestamp);
CREATE INDEX idx_classification ON final_screenings_table(classification);
CREATE INDEX idx_poe_id ON final_screenings_table(poe_id);
CREATE INDEX idx_overall_risk_score ON final_screenings_table(overall_risk_score);
