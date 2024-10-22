CREATE TABLE `aaa_points_of_entry` (
  `id` int NOT NULL,
  `name` varchar(255) NOT NULL COMMENT 'Name of the Point of Entry',
  `type` enum('airport','land_border','seaport') NOT NULL COMMENT 'Type of POE',
  `location` json DEFAULT NULL COMMENT 'JSON object containing location details (country, province, district, etc.)',
  `status` enum('active','inactive','maintenance') DEFAULT 'active' COMMENT 'Current status of the POE',
  `capacity` int DEFAULT NULL COMMENT 'Daily capacity of the POE',
  `createdAt` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Record creation timestamp',
  `updatedAt` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Record last update timestamp'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;



INSERT INTO `aaa_points_of_entry` (`id`, `name`, `type`, `location`, `status`, `capacity`, `createdAt`, `updatedAt`) VALUES
(1, 'Kigali International Airport', 'airport', '{\"country\": \"Rwanda\", \"district\": \"Kicukiro\", \"province\": \"Kigali City\"}', 'active', 10000, '2024-10-14 14:51:47', '2024-10-14 14:51:47'),
(2, 'Kamembe International Airport', 'airport', '{\"country\": \"Rwanda\", \"district\": \"Rusizi\", \"province\": \"Western Province\"}', 'active', 5000, '2024-10-14 14:51:47', '2024-10-14 14:51:47'),
(3, 'Rusumo Border Post', 'land_border', '{\"country\": \"Rwanda\", \"district\": \"Kirehe\", \"province\": \"Eastern Province\"}', 'active', 8000, '2024-10-14 14:51:47', '2024-10-14 14:51:47'),
(4, 'Gatuna Border Post', 'land_border', '{\"country\": \"Rwanda\", \"district\": \"Gicumbi\", \"province\": \"Northern Province\"}', 'active', 7000, '2024-10-14 14:51:47', '2024-10-14 14:51:47'),
(5, 'Kagitumba Border Post', 'land_border', '{\"country\": \"Rwanda\", \"district\": \"Nyagatare\", \"province\": \"Eastern Province\"}', 'active', 5000, '2024-10-14 14:51:47', '2024-10-14 14:51:47'),
(6, 'La Corniche Border Post', 'land_border', '{\"country\": \"Rwanda\", \"district\": \"Rubavu\", \"province\": \"Western Province\"}', 'active', 6000, '2024-10-14 14:51:47', '2024-10-14 14:51:47'),
(7, 'Poids Lourds Border Post', 'land_border', '{\"country\": \"Rwanda\", \"district\": \"Rubavu\", \"province\": \"Western Province\"}', 'active', 6000, '2024-10-14 14:51:47', '2024-10-14 14:51:47'),
(8, 'Cyangugu Border Post', 'land_border', '{\"country\": \"Rwanda\", \"district\": \"Rusizi\", \"province\": \"Western Province\"}', 'active', 5000, '2024-10-14 14:51:47', '2024-10-14 14:51:47'),
(9, 'Rusizi II Border Post', 'land_border', '{\"country\": \"Rwanda\", \"district\": \"Rusizi\", \"province\": \"Western Province\"}', 'active', 4500, '2024-10-14 14:51:47', '2024-10-14 14:51:47'),
(10, 'Bugarama Border Post', 'land_border', '{\"country\": \"Rwanda\", \"district\": \"Rusizi\", \"province\": \"Western Province\"}', 'active', 4000, '2024-10-14 14:51:47', '2024-10-14 14:51:47'),
(11, 'Buhita Border Post', 'land_border', '{\"country\": \"Rwanda\", \"district\": \"Nyamasheke\", \"province\": \"Western Province\"}', 'active', 3500, '2024-10-14 14:51:47', '2024-10-14 14:51:47'),
(12, 'Gisenyi Border Post', 'land_border', '{\"country\": \"Rwanda\", \"district\": \"Rubavu\", \"province\": \"Western Province\"}', 'active', 6000, '2024-10-14 14:51:47', '2024-10-14 14:51:47'),
(13, 'Nemba Border Post', 'land_border', '{\"country\": \"Rwanda\", \"district\": \"Bugesera\", \"province\": \"Eastern Province\"}', 'active', 5000, '2024-10-14 14:51:47', '2024-10-14 14:51:47'),
(14, 'Bugarama Border Post', 'land_border', '{\"country\": \"Rwanda\", \"district\": \"Rusizi\", \"province\": \"Western Province\"}', 'active', 4500, '2024-10-14 14:51:47', '2024-10-14 14:51:47'),
(15, 'Nyagatare Border Post', 'land_border', '{\"country\": \"Rwanda\", \"district\": \"Nyagatare\", \"province\": \"Eastern Province\"}', 'active', 5000, '2024-10-14 14:51:47', '2024-10-14 14:51:47'),
(16, 'Nyungwe Forest Checkpoint', 'land_border', '{\"country\": \"Rwanda\", \"district\": \"Nyamagabe\", \"province\": \"Western Province\"}', 'active', 4000, '2024-10-14 14:51:47', '2024-10-14 14:51:47');



CREATE TABLE `users` (
  `id` int NOT NULL,
  `username` varchar(255) NOT NULL COMMENT 'Unique username for the user',
--   `password` varchar(255) NOT NULL COMMENT 'Unique username for the user',
  `passwordHash` varchar(255) NOT NULL COMMENT 'Hashed password',
  `email` varchar(255) NOT NULL COMMENT 'User email address',
  `role` enum('admin','screener','supervisor','province','district','national') NOT NULL,
  `poeId` int DEFAULT NULL COMMENT 'Associated Point of Entry ID',
  `lastLogin` datetime DEFAULT NULL COMMENT 'Timestamp of last login',
  `createdAt` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Record creation timestamp',
  `updatedAt` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Record last update timestamp'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `passwordHash`, `email`, `role`, `poeId`, `lastLogin`, `createdAt`, `updatedAt`) VALUES
(4, 'atimothy', '$2y$12$8o0DJNssznRLWj34hbhTMO/2yD7e1Ju7O3/yZ649/LNwvgrabgBTa', 'atimothy@ecsahc.org', 'national', NULL, '2024-10-14 17:41:10', '2024-10-14 13:47:46', '2024-10-14 17:41:10'),
(5, 'Ayebare', '$2y$12$DcdeJ9KpftXOkgoquJHA8OAzE/oylBDNa7ny/OrLOvKgfDpuKrKgW', 'vexa256@gmail.com', 'admin', 1, '2024-10-19 16:26:30', '2024-10-14 14:43:01', '2024-10-19 16:26:30'),
(6, 'Chris Minja', '$2y$12$wh0BhNATtFlEPHzHN8lwSufRDNiv.yYkMTpXeJCOnZWVV944zydse', 'minja@gmail.com', 'screener', 6, NULL, '2024-10-15 07:46:55', '2024-10-15 07:46:55'),
(7, 'Kamatari Olivier', '$2y$12$ovMlez8eBGqsfZKucqcdN.w8.mvpGaRxrVU4Ee2G8LsQ0NIM1/.ua', 'kamatari@email.com', 'screener', 1, NULL, '2024-10-17 14:15:27', '2024-10-17 14:15:27');



--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `poeId` (`poeId`),
  ADD KEY `idx_username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;



CREATE TABLE ScreeningData (
    screening_id VARCHAR(20) PRIMARY KEY,
    screening_timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    -- Point of Entry (POE) Information
    poe_id INT NOT NULL,
    poe_name VARCHAR(100) NOT NULL,
    poe_type ENUM('Airport', 'Seaport', 'Land Crossing') NOT NULL,
    poe_country VARCHAR(100) NOT NULL,
    poe_province VARCHAR(100),
    poe_district VARCHAR(100),
    poe_latitude DECIMAL(10, 8),
    poe_longitude DECIMAL(11, 8),
    poe_status ENUM('Active', 'Inactive') NOT NULL,
    poe_capacity INT,
    
    -- Screener Information
    screener_id INT NOT NULL,
    screener_username VARCHAR(50) NOT NULL,
    screener_email VARCHAR(100) NOT NULL,
    screener_role ENUM('Admin', 'Screener', 'Analyst', 'Manager') NOT NULL,
    screener_last_login DATETIME,
    
    -- Traveler Information
    traveller_name VARCHAR(100) NOT NULL,
    traveller_age_group ENUM('0-4', '5-14', '15-24', '25-64', '65+') NOT NULL,
    traveller_gender ENUM('Male', 'Female', 'Other') NOT NULL,
    traveller_contact_info VARCHAR(255),
    traveller_nationality VARCHAR(100),
    
    -- Travel Information
    origin_country VARCHAR(100) NOT NULL,
    destination_country VARCHAR(100) NOT NULL,
    recent_travel_history TEXT,
    
    -- Screening Results
    has_symptoms BOOLEAN NOT NULL,
    symptoms JSON,
    risk_factors JSON,
    
    -- Disease Analysis
    suspected_diseases JSON,
    classification ENUM('Non-Case', 'Contact', 'Suspected Case', 'Suspected VHF Case') NOT NULL,
    accuracy_probability DECIMAL(5, 2),
    
    -- Actions and Alerts
    recommended_action TEXT NOT NULL,
    endemic_warning TEXT,
    high_risk_alert BOOLEAN DEFAULT FALSE,
    
    -- Additional Data
    body_temperature DECIMAL(4, 1),
    additional_notes TEXT,
    
    -- Metadata
    data_version VARCHAR(10) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- Indexes for improved query performance
    INDEX idx_screening_timestamp (screening_timestamp),
    INDEX idx_poe_id (poe_id),
    INDEX idx_poe_name (poe_name),
    INDEX idx_poe_country (poe_country),
    INDEX idx_origin_country (origin_country),
    INDEX idx_destination_country (destination_country),
    INDEX idx_classification (classification),
    INDEX idx_has_symptoms (has_symptoms),
    INDEX idx_high_risk_alert (high_risk_alert),
    INDEX idx_screener_id (screener_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;




