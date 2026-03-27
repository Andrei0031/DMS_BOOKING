<?php
/**
 * ========================================
 * DMS BOOKING - ROUTING CONFIGURATION
 * ========================================
 * 
 * All application routing is handled in:
 * 📍 public/index.php
 * 
 * This file documents the route structure
 * for reference purposes only.
 * 
 * ROUTES SUMMARY:
 * ===============
 * 
 * PUBLIC ROUTES (No auth required)
 * GET  /                          - Home page
 * GET  /login                     - Login page
 * POST /login                     - Process login
 * GET  /register                  - Registration page
 * POST /register                  - Process registration
 * GET  /home                      - Dashboard (after login)
 * POST /account-settings          - Save account settings
 * POST /change-password           - Change password
 * POST /logout                    - Logout
 * 
 * ADMIN PANEL ROUTES (/admin/*)
 * POST /admin/login               - Admin login
 * GET  /admin                     - Admin dashboard
 * GET  /admin/staff               - Staff management
 * POST /admin/staff               - Add staff
 * GET  /admin/staff/{id}/edit     - Edit staff
 * POST /admin/staff/{id}/update   - Update staff
 * POST /admin/staff/{id}/delete   - Delete staff
 * GET  /admin/buses               - Bus management
 * POST /admin/buses               - Add bus
 * GET  /admin/buses/{id}/edit     - Edit bus
 * POST /admin/buses/{id}/update   - Update bus
 * POST /admin/buses/{id}/delete   - Delete bus
 * GET  /admin/routes              - Routes management
 * POST /admin/routes              - Add route
 * POST /admin/routes/{id}/update  - Update route
 * POST /admin/routes/{id}/delete  - Delete route
 * GET  /admin/users               - User management
 * POST /admin/users/{id}/delete   - Delete user
 * GET  /admin/bookings            - Bookings management
 * GET  /admin/advisory            - Travel advisories
 * POST /admin/advisory            - Add advisory
 * GET  /admin/advisory/{id}/edit  - Edit advisory
 * POST /admin/advisory/{id}/update - Update advisory
 * POST /admin/advisory/{id}/delete - Delete advisory
 * POST /admin/advisory/{id}/toggle - Toggle advisory status
 * 
 * OPERATOR PANEL ROUTES (/operator/*)
 * GET  /operator                  - Operator dashboard
 * GET  /operator/advisory         - Manage advisories
 * POST /operator/advisory         - Add advisory
 * GET  /operator/advisory/{id}/edit - Edit advisory
 * POST /operator/advisory/{id}/update - Update advisory
 * POST /operator/advisory/{id}/delete - Delete advisory
 * POST /operator/advisory/{id}/toggle - Toggle advisory status
 * POST /operator/logout           - Logout
 * 
 * IMPLEMENTATION DETAILS:
 * - Session-based authentication
 * - User types: customer, admin, staff, operator
 * - 5-minute inactivity timeout
 * - AJAX page loading with script execution
 * 
 * @see public/index.php - Main routing logic
 */

// This file serves as documentation only.
// All routes are defined in public/index.php
