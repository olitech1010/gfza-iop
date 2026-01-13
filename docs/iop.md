GFZA IOP 
SOFTWARE REQUIREMENTS SPECIFICATION (SRS) 
System Name: GFZA Internal Operations Portal (IOP 
Version: 1.0 
Prepared For: MIS Department – Ghana Free Zones Authority 
Prepared By: Clement Mensah - NSS Personnel (MIS) 
1. INTRODUCTION 
1.1 Purpose 
The GFZA Internal Operations Portal(IOP) is an internal enterprise system 
designed to digitize and streamline internal communication, HR operations, MIS 
workflows, asset management, conference room booking, and employee service 
processes. 
It eliminates paper-based processes and provides a unified internal operations 
portal for staff communication, operations, notifications, and administrative 
workflows. 
1.2 Scope 
The system will support the following core modules: 
1. 
HR Communication & Internal Memos 
2. Electronic Meal Selection System 
3. MIS Support/Troubleshooting Requests 
4. Computer & Asset Lifecycle Management 
5. Conference Room Booking System 
6. Employee Directory & Email Auto-Creation 
7. Email Notifications System 
8. Modular API v1.x Architecture 
9. Admin Dashboards for HR, MIS, and System Admins 
The primary users: 
● HR Department 
● MIS Department 
● General Staff 
● Directors & Senior Management 
● System Administrators 
The platform will be built using: 
● Next Js and superbase or Mongo DB 
● Mobile-first UI 
● Clean minimalist interface 
2. SYSTEM OVERVIEW 
The system acts as a central internal portal for all staff. 
It replaces all paper-based processes such as memos, food selection sheets, MIS 
troubleshooting forms, computer assignment tracking, and room reservation logs. 
The architecture is modular, allowing modules to be plugged in/out without 
breaking the core system. 
API endpoints follow versioning: 
● /api/v1/ . 
● /api/v2/ . (future backward compatibility) 
Notifications are sent through only email 
3. SYSTEM FEATURES 
3.1 Meal Selection System 
Functional Requirements 
● HR uploads the weekly meal plan (Mon–Fri). 
● Employees select their preferred meals for the week 
● HR Ticks after the user is given food. Can tick for a whole department. And 
statuses will show 
● Staff members pay GHS 5 per meal, free for NSS personnel. 
● Staff can pay for the whole week or in advance 
● HR takes records of payments and makes accounts and reports 
● reminders are sent when food is ready, could be sound playing on the 
user’s pc. 
● HR can view Daily food count summary and more info 
● HR can download nad print A4 PDF for caterers to see how many meals to 
prepare under each meal 
● HR can print a pdf that shows who selected what. Department by 
department, for each day. 
● Prevent duplicate  
● Record who selected what and what time
3.2 HR MODULE 
3.1.1 Internal Memos 
Functional Requirements 
● HR can compose a memo and publish it to: 
○ All staff 
○ Selected departments 
○ Selected individuals 
● Staff receive email notification of new memos and sound play for each 
memo 
● Memos appear in each user’s dashboard. 
● Memos can include attachments (PDF, Word, image, xls or other file types). 
● HR can track: 
○ Read/unread status 
○ Recipients reached 
● Searchable memo archive. 
3.3 Employee Directory & Auto Email 
Creation 
Functional Requirements 
● When adding an employee, the system auto-generates an email: 
○ {lastname first letter}.{firstname}@gfzaiop.com 
○ If conflict, append number (e.g.,  mclement2@gfzaiop.com, 
mclement2@gfzaiop.com) for Clement Mensah 
● MIS dashboard shows: 
○ All system-generated emails 
○ Status: Created, Not Created, Activated, Not Activated 
● MIS marks email as: 
○ Created (after creation in email server) 
○ Activated (after user logs in on phone and enables notifications) 
● System provides instructions for user activation. 
3.4 MIS MODULE 
3.4.1 Troubleshooting / IT Support Requests 
Functional Requirements 
● Staff submit IT issues through ticket form: 
○ User name 
○ Department 
○ Floor 
○ Device type 
○ Device serial number 
○ Issue description 
● MIS receives email + ticket in dashboard. 
● MIS assigns technician and updates status: 
○ Open 
○ In Progress 
○ Resolved 
○ Referred 
● Full audit trail maintained. 
3.4.2 Asset Lifecycle Management 
Definition: 
The system automatically tracks the entire life of each GFZA ICT asset (computer, 
laptop, printer, router, etc.) from purchase → assignment → usage → 
reassignment → retirement. 
Functional Requirements 
● MIS registers each asset: 
○ Type [Enum] more can be added 
○ Serial number 
○ Date of purchase 
○ Warranty details 
○ Cost 
○ Vendor  
○ Current condition 
● System tracks: 
○ Which employee currently owns/uses the asset 
○ Assignment date 
○ Transfer history between departments 
○ Maintenance & repair logs 
○ Issues linked to specific assets 
○ End-of-life and replacement timeline 
● When an employee leaves: 
○ MIS marks the asset as “Available for reassignment.” 
○ MIS reassigns the asset to the new user 
○ Historical logs remain intact 
This completely solves the “we don’t know who owns this device" problem. 
3.5 Conference Room Booking 
Functional Requirements 
● Users view available rooms + time slots. 
● Users book rooms with: 
○ Purpose 
○ Meeting type 
○ Start/End time 
○ Required facilities (TV, projector, etc.) 
● Prevent double booking. 
● Email confirmation upon successful reservation. 
● HR/Admin can: 
○ Override or cancel bookings 
○ View daily/weekly schedule 
○ Generate usage reports 
3.6 Community Chat (to be added to 
future features) 
Functional Requirements 
● Single communication feed (like Slack #general). 
● All staff can post and read messages. 
● HR and Management have “priority announcement” posts. 
● Users can: 
○ Comment 
○ Tag departments 
○ Upload simple files/images 
● Email notification summary for important posts only. 
4. NON-FUNCTIONAL REQUIREMENTS 
4.1 Performance 
● Portal must load under 2 seconds on 4G. 
● Optimized ui 
● Cache frequently used pages. 
● Database indexing for large data. 
4.2 Security 
● Role-Based Access Control (RBAC). 
● Encrypt sensitive data. 
● Server-side validation for all forms. 
● Audit trail of all admin actions. 
4.3 Availability 
● 99% uptime during working hours. 
● Daily automated backups. 
4.4 Scalability 
● Modular architecture allows new modules to be added without downtime. 
4.5 Usability 
● Mobile-first design. 
● Simple, clean interface. 
● Accessible for non-technical staff. 
5. SYSTEM ARCHITECTURE 
5.1 Modular Design 
Each module acts independently: 
● HR Module 
● MIS Module 
● Meal Management Module  
● Asset Management Module 
● Chat Module 
● Email Notification Service 
● User Management Module 
● Booking Module 
Modules can be disabled/enabled without affecting the system. 
6. API REQUIREMENTS 
6.1 Versioned API 
All endpoints start with: 
/api/v1/ 
Examples: 
● /api/v1/dashboard/hr 
● /api/v1/dashboard/mis 
● /api/v1/employees/create 
● /api/v1/assets/assign 
● /api/v1/chat/messages 
Future versions will use /api/v2/ . 
7. USER ROLES & PERMISSIONS 
7.1 Admin 
● Full access 
● Manage modules 
● Add or memove roles 
● View system logs 
7.2 HR 
● Manage memos 
● Manage meal selections 
● Manage employee records (excluding MIS functions) 
7.3 MIS 
● Manage troubleshooting tickets 
● Manage asset lifecycle 
● Manage system-generated emails 
● Track email creation/activation 
● Oversee IT infrastructure records 
7.4 General Staff 
● Read memos 
● Select meals 
● Submit IT issues 
● Book rooms 
● Join community chat 
8. EMAIL NOTIFICATION SYSTEM 
Triggers: 
● New memo 
● Meal reminder 
● IT ticket updates 
● Room booking confirmation 
● HR announcements 
● Community chat priority messages 
Emails must be queued to improve performance. 
Notification sounds on dashboards 
9. DATABASE REQUIREMENTS 
All modules require structured relational tables: 
● Employees 
● Departments 
● Memos 
● Meals 
● Tickets 
● Assets 
● Asset history 
● Rooms 
● Bookings 
● Chat messages 
● Email status logs 
10. CONSTRAINTS 
● Must use Nextjs 
● Must be mobile-first. 
● Must be minimalistic in UI. 
● Must use email only for notifications. 
● Must comply with internal IT security practices. 
Can enable and disable email notifications.  
Email config can be done in code and in the admin dashboard as well.  