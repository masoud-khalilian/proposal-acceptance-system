-- Reference data for the default "university thesis proposal" configuration
-- of the generic workflow engine. Other configurations can add their own
-- roles/workflow_types rows without touching the schema.

INSERT INTO roles (key, label_fa, label_en) VALUES
    ('submitter', 'دانشجو', 'Submitter'),
    ('reviewer', 'استاد', 'Reviewer'),
    ('admin', 'ادمین', 'Admin');

INSERT INTO workflow_types (key, label_fa, label_en) VALUES
    ('thesis_proposal', 'پروپوزال پایان نامه', 'Thesis Proposal');
