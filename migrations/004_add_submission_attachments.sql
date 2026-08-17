-- Optional PDF attachment on a submission. The physical file is stored as
-- var/uploads/{submission_id}.pdf (deterministic path, never derived from
-- user input) - these columns only track the original filename/size for
-- display and download purposes.

ALTER TABLE submissions
    ADD COLUMN attachment_filename VARCHAR(255),
    ADD COLUMN attachment_size INTEGER;
