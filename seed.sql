SET FOREIGN_KEY_CHECKS = 0;

-- =========================
-- REGIONS
-- =========================
INSERT INTO lc_regions (id, iso_alpha_2, icao, iucn, tdwg, is_visible) VALUES
(1, 'AF', 'AFI', 'AFR', 'AFR', 1),
(2, 'EU', 'EUR', 'EUR', 'EUR', 1);

-- =========================
-- COUNTRIES
-- =========================
INSERT INTO lc_countries (id, lc_region_id, uid, official_name, capital, iso_alpha_2, iso_alpha_3)
VALUES
(1, 1, '01HABCDEGYPT1234567890123', 'Egypt', 'Cairo', 'EG', 'EGY'),
(2, 2, '01HABCDFRANCE123456789012', 'France', 'Paris', 'FR', 'FRA');

-- =========================
-- USERS
-- =========================
INSERT INTO users (id, name, email, password, country_id, is_approved, created_at) VALUES
(1, 'Super Admin', 'admin@test.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9kF9h8zWZ9WjQ1X9YyZ8Ka', 1, 1, NOW()),
(2, 'Manager One', 'manager@test.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9kF9h8zWZ9WjQ1X9YyZ8Ka', 1, 1, NOW()),
(3, 'Receptionist', 'rep@test.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9kF9h8zWZ9WjQ1X9YyZ8Ka', 1, 1, NOW()),
(4, 'Client One', 'client@test.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9kF9h8zWZ9WjQ1X9YyZ8Ka', 1, 1, NOW());

-- =========================
-- ROLES
-- =========================
INSERT INTO roles (id, name, guard_name, created_at) VALUES
(1, 'admin', 'web', NOW()),
(2, 'manager', 'web', NOW()),
(3, 'receptionist', 'web', NOW()),
(4, 'client', 'web', NOW());

-- =========================
-- PERMISSIONS
-- =========================
INSERT INTO permissions (id, name, guard_name, created_at) VALUES
(1, 'manage users', 'web', NOW()),
(2, 'manage rooms', 'web', NOW()),
(3, 'manage reservations', 'web', NOW());

-- =========================
-- ROLE HAS PERMISSIONS
-- =========================
INSERT INTO role_has_permissions (permission_id, role_id) VALUES
(1, 1),
(2, 2),
(3, 3);

-- =========================
-- MODEL HAS ROLES
-- =========================
INSERT INTO model_has_roles (role_id, model_type, model_id) VALUES
(1, 'App\\Models\\User', 1),
(2, 'App\\Models\\User', 2),
(3, 'App\\Models\\User', 3),
(4, 'App\\Models\\User', 4);

-- =========================
-- FLOORS
-- =========================
INSERT INTO floors (id, name, number, manager_id, created_at) VALUES
(1, 'First Floor', 1, 2, NOW()),
(2, 'Second Floor', 2, 2, NOW());

-- =========================
-- ROOMS
-- =========================
INSERT INTO rooms (id, number, capacity, price, floor_id, manager_id, created_at) VALUES
(1, '101', 2, 500, 1, 2, NOW()),
(2, '102', 3, 750, 1, 2, NOW()),
(3, '201', 2, 600, 2, 2, NOW());

SET FOREIGN_KEY_CHECKS = 1;
