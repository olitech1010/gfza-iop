# Module 5: Leave Management

This module allows staff to apply for leave and managers to approve it, with specific constraints.

## 1. Leave Policies
*   **Annual Allowance**: 36 Days.
*   **Max per Request**: 18 Days.
*   **Restriction**: Leaves cannot be taken in consecutive months.

## 2. The Model (`LeaveRequest`)
We store requests with:
*   Start/End Dates.
*   Reason.
*   Workflow Timestamps: `dept_head_approved_at`, `hr_approved_at`.

## 3. The Resource (`LeaveRequestResource`)

### Form Features
*   **Date Calculations**: Validates that date range <= 18 days.
*   **Consecutive Check**: Checks database for leaves in adjacent months.
*   **Auto-Calculations**: `days_requested` is saved automatically.

### Approval Workflow
Defined in the Table Actions:
1.  **Pending Dept Head**: Visible to Managers. Action moves it to -> **Pending HR**.
2.  **Pending HR**: Visible to HR Dept. Action moves it to -> **Approved**.
3.  **Approved/Rejected**: Final states.

### Reports / Roaster
The Table view serves as the Roaster.
*   Filter by Month/Year (Standard Filament Filters can be added).
*   Search by Staff Name.
*   Print using browser Print function (Filament is print-friendly).
