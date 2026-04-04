-- Update script to add favorites column to existing database
-- Run this after updating setup.sql

USE plotoryx;

-- Add favorite column to entries table if it doesn't exist
ALTER TABLE entries ADD COLUMN favorite BOOLEAN DEFAULT FALSE;