-- 002 seeded role labels in university terms (دانشجو/استاد = Student/
-- Professor) even though the engine itself is generic - that made the fa
-- locale (the default) feel hardcoded to the university use case. Switch to
-- role-neutral terms, and add a second workflow type so the UI actually
-- demonstrates this is a general submission -> reviewer approval engine,
-- not something that only knows about thesis proposals.

UPDATE roles SET label_fa = 'ارسال‌کننده' WHERE key = 'submitter';
UPDATE roles SET label_fa = 'داور' WHERE key = 'reviewer';

INSERT INTO workflow_types (key, label_fa, label_en) VALUES
    ('general_request', 'درخواست عمومی', 'General Request')
ON CONFLICT (key) DO NOTHING;
