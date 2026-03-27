DELETE FROM admins;
INSERT INTO admins (name, email, password, phone, created_at, updated_at) 
VALUES ('Admin', 'admin@dms.local', '0192023a7bbd73250516f069df18b500', '1234567890', NOW(), NOW());
