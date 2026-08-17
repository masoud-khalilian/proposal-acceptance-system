-- Demo submitter/reviewer/admin accounts for easy local login/testing.
-- Password for all three is "123456" (see README.md). ON CONFLICT makes
-- this safe to re-run against a database that already has them.
--
-- The hash below is password_hash('123456', PASSWORD_DEFAULT) — bcrypt,
-- not a secret derived from anything else. Never seed real credentials
-- this way outside of local/dev use.

INSERT INTO actors (username, password_hash, first_name, last_name, role_id, capacity, profile)
SELECT 'student', '$2y$10$wS5MnFfjKEUvJ/fy2YlL5uJSfWvyc0qGbAYDAbZ4hs8xBEDVm0ylu', 'Demo', 'Student', id, NULL, '{"field_level": "s_bachelor"}'
FROM roles WHERE key = 'submitter'
ON CONFLICT (username) DO NOTHING;

INSERT INTO actors (username, password_hash, first_name, last_name, role_id, capacity)
SELECT 'professor', '$2y$10$wS5MnFfjKEUvJ/fy2YlL5uJSfWvyc0qGbAYDAbZ4hs8xBEDVm0ylu', 'Demo', 'Professor', id, 3
FROM roles WHERE key = 'reviewer'
ON CONFLICT (username) DO NOTHING;

INSERT INTO actors (username, password_hash, first_name, last_name, role_id, capacity)
SELECT 'admin', '$2y$10$wS5MnFfjKEUvJ/fy2YlL5uJSfWvyc0qGbAYDAbZ4hs8xBEDVm0ylu', 'Admin', 'Account', id, NULL
FROM roles WHERE key = 'admin'
ON CONFLICT (username) DO NOTHING;
