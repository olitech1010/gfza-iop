# GFZA IOP - Software Requirements Specification (SRS)

## Document Information

| Field | Value |
|-------|-------|
| **System Name** | GFZA Internal Operations Portal (IOP) |
| **Version** | 1.0 |
| **Prepared For** | MIS Department – Ghana Free Zones Authority |
| **Prepared By** | Clement Mensah - NSS Personnel (MIS) |

---

## 1. Introduction

### 1.1 Purpose

The GFZA Internal Operations Portal (IOP) is an internal enterprise system designed to digitize and streamline:
- Internal communication
- HR operations
- MIS workflows
- Asset management
- Conference room booking
- Employee service processes

It eliminates paper-based processes and provides a unified internal operations portal for staff communication, operations, notifications, and administrative workflows.

### 1.2 Scope

The system will support the following core modules:

- HR Communication & Internal Memos
- Electronic Meal Selection System
- MIS Support/Troubleshooting Requests
- Computer & Asset Lifecycle Management
- Conference Room Booking System
- Employee Directory & Email Auto-Creation
- Email Notifications System
- Modular API v1.x Architecture
- Admin Dashboards for HR, MIS, and System Admins

#### Primary Users

- HR Department
- MIS Department
- General Staff
- Directors & Senior Management
- System Administrators

#### Technology Stack

The platform will be built using:
- **Framework**: Laravel + Blade (Fast, secure, efficient)
- **UI Design**: Mobile-first UI
- **Interface Style**: Clean minimalist interface

---

## 2. System Overview

The system acts as a central internal portal for all staff. It replaces all paper-based processes such as:
- Memos
- Food selection sheets
- MIS troubleshooting forms
- Computer assignment tracking
- Room reservation logs

The architecture is modular, allowing modules to be plugged in/out without breaking the core system.

### API Versioning

API endpoints follow versioning:
- `/api/v1/...` (current)
- `/api/v2/...` (future backward compatibility)

### Notification System

Notifications are sent only through email.

---

## 3. System Features

### 3.1 HR Module

#### 3.1.1 Internal Memos

**Functional Requirements:**

HR can compose a memo and publish it to:
- All staff
- Selected departments
- Selected individuals

**Features:**
- Staff receive email notification of new memos
- Memos appear in each user's dashboard
- Memos can include attachments (PDF, Word)

**HR Tracking Capabilities:**
- Read/unread status
- Recipients reached
- Searchable memo archive

#### 3.2 Meal Selection System

**Functional Requirements:**

- HR uploads the daily or weekly meal plan (Mon–Fri)
- Employees select their preferred meals daily
- Email reminders are sent each morning based on HR time

**HR View Capabilities:**
- Daily food count summary
- Downloadable CSV for catering

**System Features:**
- Prevent duplicate or late submissions
- Logs who selected what

#### 3.3 Employee Directory & Auto Email Creation

**Functional Requirements:**

When adding an employee, the system auto-generates an email:
- **Format**: `{lastname first letter}.{firstname}@gfzaiop.com`
- **Conflict Resolution**: Append number (e.g., `mclement2@gfzaiop.com`)

**MIS Dashboard Shows:**
- All system-generated emails
- Status: Created, Not Created, Activated, Not Activated

**MIS Email Marking:**
- **Created** (after creation in email server)
- **Activated** (after user logs in on phone and enables notifications)

System provides instructions for user activation.

### 3.4 MIS Module

#### 3.4.1 Troubleshooting / IT Support Requests

**Functional Requirements:**

Staff submit IT issues through ticket form with:
- User name
- Department
- Floor
- Device type
- Device serial number
- Issue description

**MIS Workflow:**
- MIS receives email + ticket in dashboard
- MIS assigns technician and updates status:
  - Open
  - In Progress
  - Resolved
  - Referred

Full audit trail maintained.

#### 3.4.2 Asset Lifecycle Management

**Definition:**

The system automatically tracks the entire life of each GFZA ICT asset (computer, laptop, printer, router, etc.) from purchase → assignment → usage → reassignment → retirement.

**Functional Requirements:**

MIS registers each asset with:
- Type
- Serial number
- Date of purchase
- Warranty details
- Cost
- Vendor
- Current condition

**System Tracking:**
- Which employee currently owns/uses the asset
- Assignment date
- Transfer history between departments
- Maintenance & repair logs
- Issues linked to specific assets
- End-of-life and replacement timeline

**Employee Departure Workflow:**
- MIS marks asset as "Available for reassignment"
- MIS reassigns asset to new user
- Historical logs remain intact

This completely solves the "we don't know who owns this device" problem.

### 3.5 Conference Room Booking

**Functional Requirements:**

Users can:
- View available rooms + time slots
- Book rooms with:
  - Purpose
  - Meeting type
  - Start/End time
  - Required facilities (TV, projector, etc.)

**System Features:**
- Prevent double booking
- Email confirmation upon successful reservation

**HR/Admin Capabilities:**
- Override or cancel bookings
- View daily/weekly schedule
- Generate usage reports

### 3.6 Community Chat (Future Feature)

**Functional Requirements:**

- Single communication feed (like Slack #general)
- All staff can post and read messages
- HR and Management have "priority announcement" posts

**User Capabilities:**
- Comment
- Tag departments
- Upload simple files/images

Email notification summary for important posts only.

---

## 4. Non-Functional Requirements

### 4.1 Performance

- Portal must load under 2 seconds on 4G
- Optimized blade templates
- Cache frequently used pages
- Database indexing for large HR data

### 4.2 Security

- Enforce GFZA password rules
- Role-Based Access Control (RBAC)
- Encrypt sensitive data
- Server-side validation for all forms
- Audit trail of all admin actions

### 4.3 Availability

- 99% uptime during working hours
- Daily automated backups

### 4.4 Scalability

- Modular architecture allows new modules to be added without downtime

### 4.5 Usability

- Mobile-first design
- Simple, clean interface
- Accessible for non-technical staff

---

## 5. System Architecture

### 5.1 Modular Design

Each module acts independently:
- HR Module
- MIS Module
- Asset Management Module
- Chat Module
- Email Notification Service
- User Management Module
- Booking Module

Modules can be disabled/enabled without affecting the system.

---

## 6. API Requirements

### 6.1 Versioned API

All endpoints start with: `/api/v1/`

**Examples:**
```
/api/v1/dashboard/hr
/api/v1/dashboard/mis
/api/v1/employees/create
/api/v1/assets/assign
/api/v1/chat/messages
```

Future versions will use `/api/v2/...`

---

## 7. User Roles & Permissions

### 7.1 Admin

- Full access
- Manage modules
- View system logs

### 7.2 HR

- Manage memos
- Manage meal selections
- Manage employee records (excluding MIS functions)

### 7.3 MIS

- Manage troubleshooting tickets
- Manage asset lifecycle
- Manage system-generated emails
- Track email creation/activation
- Oversee IT infrastructure records

### 7.4 General Staff

- Read memos
- Select meals
- Submit IT issues
- Book rooms
- Join community chat

---

## 8. Email Notification System

**Triggers:**
- New memo
- Meal reminder
- IT ticket updates
- Room booking confirmation
- HR announcements
- Community chat priority messages

Emails must be queued to improve performance.

---

## 9. Database Requirements

All modules require structured relational tables:

| Table | Purpose |
|-------|---------|
| Employees | Staff information |
| Departments | Organizational structure |
| Memos | Internal communications |
| Meals | Meal planning and selections |
| Tickets | IT support requests |
| Assets | ICT asset inventory |
| Asset history | Asset lifecycle tracking |
| Rooms | Conference room information |
| Bookings | Room reservations |
| Chat messages | Community discussions |
| Email status logs | Email delivery tracking |

---

## 10. Constraints

- Must use Laravel + Blade
- Must be mobile-first
- Must be minimalistic in UI
- Must use email only for notifications
- Must comply with internal IT security practices

---

## 11. Future Extensions (Not Implemented Now)

- Whatsapp integration
- Community Chat (All-staff communication)
