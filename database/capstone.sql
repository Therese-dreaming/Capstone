-- Create Groups Table
CREATE TABLE `groups` (
    `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` varchar(255) NOT NULL,
    `level` int(11) NOT NULL,
    `status` enum('Active','Inactive') NOT NULL DEFAULT 'Active',
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create Categories Table
CREATE TABLE `categories` (
    `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` varchar(255) NOT NULL,
    `description` text NULL,
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create Users Table
CREATE TABLE `users` (
    `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` varchar(255) NOT NULL,
    `username` varchar(255) NOT NULL UNIQUE,
    `rfid_number` varchar(255) NULL UNIQUE,
    `department` varchar(255) NULL,
    `position` varchar(255) NULL,
    `password` varchar(255) NOT NULL,
    `group_id` bigint(20) UNSIGNED NOT NULL,
    `status` enum('Active','Inactive') NOT NULL DEFAULT 'Active',
    `profile_picture` varchar(255) NULL,
    `last_login` timestamp NULL DEFAULT NULL,
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    FOREIGN KEY (`group_id`) REFERENCES `groups`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create Assets Table
CREATE TABLE `assets` (
    `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` varchar(255) NOT NULL,
    `serial_number` varchar(255) NULL UNIQUE,
    `category_id` bigint(20) UNSIGNED NOT NULL,
    `status` varchar(255) NOT NULL,
    `model` varchar(255) NULL,
    `specification` text NULL,
    `vendor` varchar(255) NULL,
    `purchase_date` date NULL,
    `warranty_period` varchar(255) NULL,
    `lifespan` varchar(255) NULL,
    `purchase_price` decimal(10,2) NULL,
    `location` varchar(255) NULL,
    `photo` varchar(255) NULL,
    `qr_code` varchar(255) NULL,
    `disposal_date` timestamp NULL DEFAULT NULL,
    `disposal_reason` varchar(255) NULL,
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    FOREIGN KEY (`category_id`) REFERENCES `categories`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create Asset Histories Table
CREATE TABLE `asset_histories` (
    `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    `asset_id` bigint(20) UNSIGNED NOT NULL,
    `change_type` varchar(255) NOT NULL,
    `old_value` varchar(255) NULL,
    `new_value` varchar(255) NULL,
    `remarks` text NULL,
    `changed_by` bigint(20) UNSIGNED NOT NULL,
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    FOREIGN KEY (`asset_id`) REFERENCES `assets`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`changed_by`) REFERENCES `users`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create Repair Requests Table
CREATE TABLE `repair_requests` (
    `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    `asset_id` bigint(20) UNSIGNED NULL,
    `department` varchar(255) NULL,
    `office_room` varchar(255) NOT NULL,
    `equipment` varchar(255) NOT NULL,
    `category_id` bigint(20) UNSIGNED NOT NULL,
    `issue` text NOT NULL,
    `status` varchar(255) NOT NULL,
    `ongoing_activity` enum('yes','no') NOT NULL DEFAULT 'no',
    `date_called` date NOT NULL,
    `time_called` time NOT NULL,
    `remarks` text NULL,
    `technician_id` bigint(20) UNSIGNED NULL,
    `completed_at` timestamp NULL DEFAULT NULL,
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    FOREIGN KEY (`asset_id`) REFERENCES `assets`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`technician_id`) REFERENCES `users`(`id`) ON DELETE SET NULL,
    FOREIGN KEY (`category_id`) REFERENCES `categories`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create Maintenance Tasks Table
CREATE TABLE `maintenance_tasks` (
    `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` varchar(255) NOT NULL UNIQUE,
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create Sessions Table
CREATE TABLE `sessions` (
    `id` varchar(255) NOT NULL,
    `user_id` bigint(20) UNSIGNED NULL,
    `ip_address` varchar(45) NULL,
    `user_agent` text NULL,
    `payload` longtext NOT NULL,
    `last_activity` int NOT NULL,
    PRIMARY KEY (`id`),
    KEY `sessions_user_id_index` (`user_id`),
    KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create Maintenances Table
CREATE TABLE `maintenances` (
    `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    `lab_number` varchar(255) NOT NULL,
    `maintenance_task` json NOT NULL,
    `status` varchar(255) NOT NULL DEFAULT 'PENDING',
    `technician_id` bigint(20) UNSIGNED NOT NULL,
    `scheduled_date` date NOT NULL,
    `serial_number` varchar(255) NULL,
    `action_by_id` bigint(20) UNSIGNED NULL,
    `completed_at` timestamp NULL DEFAULT NULL,
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    FOREIGN KEY (`technician_id`) REFERENCES `users`(`id`),
    FOREIGN KEY (`action_by_id`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert Default Maintenance Tasks
INSERT INTO `maintenance_tasks` (`name`, `created_at`, `updated_at`) VALUES
('Format and Software Installation', NOW(), NOW()),
('Physical Checking', NOW(), NOW()),
('Windows Update', NOW(), NOW()),
('General Cleaning', NOW(), NOW()),
('Antivirus Update', NOW(), NOW()),
('Scan for Virus', NOW(), NOW()),
('Disk Cleanup', NOW(), NOW()),
('Cleaning', NOW(), NOW()),
('Disk Maintenance', NOW(), NOW());