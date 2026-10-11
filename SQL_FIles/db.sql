CREATE DATABASE ems_db;

USE ems_db;


-- CREATE TABLE role_tbl (
--     role_id INT AUTO_INCREMENT PRIMARY KEY,
--     role_name ENUM('admin', 'user', 'inspector') NOT NULL UNIQUE
-- );

CREATE TABLE fire_extinguishers_tbl (
    extinguisher_id INT AUTO_INCREMENT PRIMARY KEY,
    extinguisher_code VARCHAR(50) NOT NULL UNIQUE,
    type VARCHAR(50) NOT NULL,
    capacity VARCHAR(50) NOT NULL,
    location VARCHAR(255) NOT NULL,
    manufactured_date DATE,
    class ENUM('A','B','C','D','AB','ABC','BC') NOT NULL,
    placement VARCHAR(50) NOT NULL,
    condition_status ENUM('Good', 'Not Good') NOT NULL DEFAULT 'Good',
    remarks VARCHAR(255),
    expiration_date DATE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE activity_logs_tbl (
    log_id INT AUTO_INCREMENT PRIMARY KEY,
    user_name VARCHAR(100) NOT NULL,
    action VARCHAR(100) NOT NULL,
    description VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);


CREATE TABLE inspection_checklist_tbl (
    inspect_id INT AUTO_INCREMENT PRIMARY KEY,
    extinguisher_code VARCHAR(50) NOT NULL UNIQUE,
    location VARCHAR(255) NOT NULL,
    capacity VARCHAR(50) NOT NULL,
    type VARCHAR(50) NOT NULL,
    class VARCHAR(50) NOT NULL,
    date_inspected DATE NOT NULL,
    inspected_by VARCHAR(100) NOT NULL,
    verified_and_approved_by VARCHAR(100) NOT NULL,
    action_taken VARCHAR(50) NOT NULL,
    target_date_of_implementation DATE NOT NULL

    -- all boolean for check list

    is_seal_ok BOOLEAN DEFAULT FALSE,
    is_pin_ok BOOLEAN DEFAULT FALSE,
    is_pressure_ok BOOLEAN DEFAULT FALSE,
    is_hose_ok BOOLEAN DEFAULT FALSE,
    is_nozzle_ok BOOLEAN DEFAULT FALSE,
    is_belt_ok BOOLEAN DEFAULT FALSE,
    is_cylinder_body_ok BOOLEAN DEFAULT FALSE,
    is_demarcation_line_ok BOOLEAN DEFAULT FALSE,
    is_signage_ok BOOLEAN DEFAULT FALSE,
    status BOOLEAN DEFAULT FALSE,
);



-- dummy data for testing purposes only

INSERT INTO fire_extinguishers_tbl
(
    extinguisher_code,
    type,
    capacity,
    location,
    manufactured_date,
    class,
    placement,
    condition_status,
    remarks,
    expiration_date
)
VALUES
(
    'FE-001',
    'Dry Chemical',
    '10 lbs',
    'Building A - 1st Floor',
    '2024-01-15',
    'ABC',
    'Wall Mounted',
    'Good',
    'Ready for use',
    '2027-01-15'
),
(
    'FE-002',
    'CO2',
    '20 lbs',
    'Building A - 2nd Floor',
    '2023-06-20',
    'BC',
    'Wall Mounted',
    'Good',
    'Recently inspected',
    '2026-06-20'
),
(
    'FE-003',
    'Dry Chemical',
    '10 lbs',
    'Building B - Ground Floor',
    '2022-11-10',
    'ABC',
    'Cabinet',
    'Not Good',
    'Needs inspection',
    '2025-11-10'
),
(
    'FE-004',
    'Water',
    '20 lbs',
    'Building B - 1st Floor',
    '2024-03-05',
    'A',
    'Floor Standing',
    'Good',
    'Good condition',
    '2027-03-05'
),
(
    'FE-005',
    'Foam',
    '10 lbs',
    'Building C - 1st Floor',
    '2023-09-12',
    'AB',
    'Wall Mounted',
    'Good',
    'Ready for use',
    '2026-09-12'
),
(
    'FE-006',
    'Wet Chemical',
    '20 lbs',
    'Kitchen Area',
    '2024-02-28',
    'A',
    'Wall Mounted',
    'Good',
    'Kitchen fire protection',
    '2027-02-28'
),
(
    'FE-007',
    'HCFC-123',
    '10 lbs',
    'Server Room',
    '2023-04-18',
    'B',
    'Cabinet',
    'Good',
    'Clean agent extinguisher',
    '2026-04-18'
),
(
    'FE-008',
    'Dry Chemical',
    '50 lbs',
    'Warehouse',
    '2024-05-22',
    'ABC',
    'Floor Standing',
    'Good',
    'Heavy-duty unit',
    '2027-05-22'
),
(
    'FE-009',
    'CO2',
    '20 lbs',
    'Electrical Room',
    '2022-08-30',
    'BC',
    'Wall Mounted',
    'Not Good',
    'Pressure needs checking',
    '2025-08-30'
),
(
    'FE-010',
    'Dry Chemical',
    '10 lbs',
    'Building C - 2nd Floor',
    '2025-01-10',
    'ABC',
    'Wall Mounted',
    'Good',
    'Newly installed',
    '2028-01-10'
);