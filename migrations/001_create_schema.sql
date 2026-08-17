-- Generic submission -> reviewer approval workflow engine schema.
-- Roles/workflow types are data, not code, so the same schema can model
-- the original university proposal flow (submitter=student, reviewer=professor)
-- or any other approval workflow without further migrations.

CREATE TABLE roles (
    id SERIAL PRIMARY KEY,
    key VARCHAR(50) UNIQUE NOT NULL,
    label_fa VARCHAR(100) NOT NULL,
    label_en VARCHAR(100) NOT NULL
);

CREATE TABLE actors (
    id SERIAL PRIMARY KEY,
    username VARCHAR(100) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    role_id INTEGER NOT NULL REFERENCES roles(id),
    capacity INTEGER,
    profile JSONB NOT NULL DEFAULT '{}',
    created_at TIMESTAMPTZ NOT NULL DEFAULT now()
);

CREATE INDEX idx_actors_role_id ON actors(role_id);

CREATE TABLE workflow_types (
    id SERIAL PRIMARY KEY,
    key VARCHAR(50) UNIQUE NOT NULL,
    label_fa VARCHAR(100) NOT NULL,
    label_en VARCHAR(100) NOT NULL
);

CREATE TABLE submissions (
    id SERIAL PRIMARY KEY,
    workflow_type_id INTEGER NOT NULL REFERENCES workflow_types(id),
    submitter_id INTEGER NOT NULL REFERENCES actors(id),
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    status VARCHAR(30) NOT NULL DEFAULT 'pending',
    approved_by INTEGER REFERENCES actors(id),
    created_at TIMESTAMPTZ NOT NULL DEFAULT now(),
    approved_at TIMESTAMPTZ,
    CONSTRAINT chk_submission_status CHECK (status IN ('pending', 'approved'))
);

CREATE INDEX idx_submissions_submitter_id ON submissions(submitter_id);

CREATE TABLE submission_reviewers (
    id SERIAL PRIMARY KEY,
    submission_id INTEGER NOT NULL REFERENCES submissions(id) ON DELETE CASCADE,
    reviewer_id INTEGER NOT NULL REFERENCES actors(id),
    decision VARCHAR(30) NOT NULL DEFAULT 'pending',
    comment TEXT,
    decided_at TIMESTAMPTZ,
    UNIQUE (submission_id, reviewer_id),
    CONSTRAINT chk_reviewer_decision CHECK (decision IN ('pending', 'changes_requested', 'approved', 'withdrawn'))
);

CREATE INDEX idx_submission_reviewers_reviewer_id ON submission_reviewers(reviewer_id);
CREATE INDEX idx_submission_reviewers_submission_id ON submission_reviewers(submission_id);
