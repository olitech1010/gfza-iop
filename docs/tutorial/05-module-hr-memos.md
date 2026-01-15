# Module 3: HR Memos & Notifications

This module enables the HR department to broadcast information to staff.

## 1. Memos (The Content)
We created the system to author and manage memos.

### Step 1.1: Create Models
`php artisan make:model Memo`
`php artisan make:model MemoRecipient`

We configured the `Memo` model to have a `recipients()` relationship (BelongsToMany Users).

### Step 1.2: Create Memo Resource
`php artisan make:filament-resource Memo --generate`

We customized `MemoResource.php`:
- **Rich Text Editor**: Used `RichEditor` for the body content.
- **File Upload**: Allowed uploading PDFs to the `memos` directory.
- **Workflow**: Added a `status` field (Draft vs Published).
- **Recipients**: Added a multi-select to choose which users receive the memo.

## 2. Tracking (Memo Recipients)
We created `MemoRecipient` to track *who* read *what*.
`php artisan make:filament-resource MemoRecipient --generate`

We decided to **hide** this resource from the main sidebar (`shouldRegisterNavigation = false`) because for now, we only need the database structure. In the future, we can add a "Read Receipts" report.

## 3. Next Steps
We will move to **Module 4: MIS Support**, which handles Asset Management and Ticketing.
