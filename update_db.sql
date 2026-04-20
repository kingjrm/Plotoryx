-- Add new columns to existing entries table
ALTER TABLE entries
ADD COLUMN date_started DATE,
ADD COLUMN date_ended DATE,
ADD COLUMN link VARCHAR(500);