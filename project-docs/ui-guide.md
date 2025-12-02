GFZA Internal  Operations Portal (IOP) - UI Design Guide
Version 1.0

1. DESIGN SYSTEM OVERVIEW
Vision
Modern, minimal, clean interface designed for enterprise staff portal usage. Mobile-first responsive design with consistency across all modules.
Core Principles
Minimalism - Remove unnecessary elements, focus on content
Consistency - Unified design language across all dashboards
Accessibility - Clear hierarchy, readable typography, sufficient contrast
Efficiency - Intuitive navigation, quick task completion
Professional - Enterprise-ready, polished aesthetic

2. COLOR PALETTE
Primary Colors
Primary Green: #00c73f
  - CTA buttons, active states, highlights
  - Hover: #00b033
  - Active: #009929
  - Disabled: #cccccc

Neutral White: #ffffff
  - Cards, backgrounds, surfaces

Neutral Background: #f5f5f5
  - Page background, secondary surfaces
  - Subtle dividers: #e0e0e0

Dark Text: #1a1a1a
  - Primary text color
  - High contrast for readability

Secondary Gray: #666666
  - Secondary text, descriptions
  - Helper text, hints


Semantic Colors
Success: #4caf50 (for positive actions, confirmations)
Warning: #ff9800 (for cautions, alerts)
Error: #f44336 (for errors, deletions)
Info: #2196f3 (for informational messages)
Disabled: #bdbdbd (for inactive states)

Color Usage Guidelines
Primary green (#00c73f) for:
Main CTA buttons
Active navigation items
Success states
Highlights and accents
Neutral colors for:
Card backgrounds
Table structures
Form layouts
Semantic colors for:
Status indicators
Alert messages
Form validation

3. TYPOGRAPHY
Font Family
Primary Font: Poppins
- All headings, body text, and UI elements
- Import: https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap

Font Sizes & Weights
Heading 1 (H1)
- Size: 32px
- Weight: 700 (Bold)
- Line Height: 40px
- Usage: Page titles, main dashboard headings

Heading 2 (H2)
- Size: 24px
- Weight: 600 (Semibold)
- Line Height: 32px
- Usage: Section headings, card titles

Heading 3 (H3)
- Size: 20px
- Weight: 600 (Semibold)
- Line Height: 28px
- Usage: Subsections, module titles

Heading 4 (H4)
- Size: 16px
- Weight: 600 (Semibold)
- Line Height: 24px
- Usage: Component titles, labels

Body Large (Body L)
- Size: 16px
- Weight: 400 (Regular)
- Line Height: 24px
- Usage: Primary body text, descriptions

Body Regular (Body M)
- Size: 14px
- Weight: 400 (Regular)
- Line Height: 20px
- Usage: Standard text, table content

Body Small (Body S)
- Size: 12px
- Weight: 400 (Regular)
- Line Height: 18px
- Usage: Captions, helper text, timestamps

Button Text
- Size: 14px
- Weight: 600 (Semibold)
- Line Height: 20px
- Usage: All button labels

Caption
- Size: 12px
- Weight: 500 (Medium)
- Line Height: 16px
- Usage: Field labels, small headings


4. LAYOUT & SPACING
Spacing Scale
Base Unit: 8px

xs: 4px
sm: 8px
md: 16px
lg: 24px
xl: 32px
2xl: 48px
3xl: 64px

Layout Structure
Page Layout:
Sidebar: 280px (fixed)
Main Content: Remaining width
Top Padding: 24px
Left/Right Padding: 24px
Bottom Padding: 24px
Desktop (1920px+)
Max content width: 1400px
Column grid: 12 columns
Gap: 16px
Tablet (768px - 1024px)
Sidebar: Collapsible to 80px icon-only
Content padding: 16px
Column grid: 8 columns
Gap: 12px
Mobile (< 768px)
Sidebar: Full-screen overlay or bottom navigation
Content padding: 12px
Single column layout
Gap: 8px
Card Spacing
Padding: 16px (inside cards)
Margin Between Cards: 16px
Border Radius: 8px
Box Shadow: 0 1px 3px rgba(0,0,0,0.08)

Dashboard Sections
Page Title + Breadcrumbs: 16px margin-bottom
Filter Bar: 16px margin-bottom
Main Content Grid: Gap 16px between cards
Stats Row (KPI Cards): Gap 16px horizontally
Tables: 16px margin-top/bottom

5. SIDEBAR NAVIGATION
Sidebar Structure
Width: 280px (fixed)
Background: #ffffff
Border Right: 1px solid #e0e0e0
Overflow: auto
Position: fixed, left: 0, top: 0, height: 100vh
Z-index: 1000

Collapsible on tablet/mobile to:
Width: 80px (icon-only mode)

Sidebar Components
Logo Section (Top)
Height: 64px
Padding: 12px 16px
Display: flex, align-items: center, justify-content: center
Border Bottom: 1px solid #e0e0e0
Logo: 40x40px (centered when collapsed)

Navigation Items
Height: 44px per item
Padding: 0 16px
Margin: 4px 8px
Display: flex, align-items: center, gap: 12px
Border Radius: 8px
Transition: all 0.2s ease

States:
- Default: Text #666666, Icon #666666
- Hover: Background #f5f5f5, Text #1a1a1a
- Active: Background #00c73f, Text #ffffff, Icon #ffffff (MUI icon)

Icon Size: 24px (MUI)
Text Size: 14px (Poppins, Regular)

Navigation Groups
Group Label:
- Size: 12px (Poppins, 600)
- Color: #999999
- Padding: 12px 16px
- Text Transform: uppercase
- Letter Spacing: 0.5px
- Margin Top: 16px (first), 12px (others)

Items in group indented: 0px (keep consistent)

User Profile Section (Bottom)
Height: 56px
Padding: 12px 16px
Border Top: 1px solid #e0e0e0
Position: sticky, bottom: 0
Display: flex, align-items: center, gap: 12px
Background: #f5f5f5

Avatar: 36x36px, border-radius: 50%
Name: 12px, #1a1a1a (Poppins 600)
Email: 12px, #999999 (Poppins 400)

Hover: Background #eeeeee

Sidebar Navigation Map
Home
├── Dashboard (icon: Dashboard)
├── My Tasks (icon: CheckCircle)
└── Notifications (icon: Bell)

HR Operations
├── Memos (icon: Mail)
├── Meal Planning (icon: Restaurant)
└── Employee Directory (icon: People)

MIS Support
├── IT Tickets (icon: BuildCircle)
├── Asset Management (icon: Inventory2)
└── System Emails (icon: Email)

Facilities
├── Conference Rooms (icon: MeetingRoom)
└── Room Bookings (icon: EventNote)

Community
└── Discussion Feed (icon: ChatBubble) [Future]

Settings & Admin
├── Settings (icon: Settings) [if admin/staff]
├── User Management (icon: AdminPanelSettings) [if admin]
├── Reports (icon: BarChart) [if admin/hr]
└── System Logs (icon: History) [if admin]


6. AUTHENTICATION PAGES
Layout Structure
Two-Column Layout:
┌─────────────────────────────────────┬─────────────────────────────────────┐
│                                     │                                     │
│     Left Column (50%)               │     Right Column (50%)              │
│     Form & Content                  │     Hero Image / Gradient           │
│                                     │                                     │
└─────────────────────────────────────┴─────────────────────────────────────┘

Left Column (Form)
Width: 50%
Padding: 48px
Display: flex, flex-direction: column, justify-content: center
Min Height: 100vh
Background: #ffffff

Logo & Title
Logo: 32x32px, margin-bottom: 32px
Title: H1, "Welcome Back" or "Create Account"
Subtitle: Body M, #666666, margin-bottom: 32px

Form Elements
Max Width: 400px
Spacing Between Fields: 16px

Input Fields:
- Height: 44px
- Padding: 12px 16px
- Border: 1px solid #e0e0e0
- Border Radius: 8px
- Font: Body M, Poppins 400
- Placeholder: #999999
- Focus: Border #00c73f, Box Shadow: 0 0 0 3px rgba(0,199,63,0.1)
- Error: Border #f44336

Labels:
- Size: 14px (Caption)
- Weight: 600
- Margin Bottom: 8px
- Color: #1a1a1a

Buttons
Primary CTA Button (Login/Register):
- Width: 100%
- Height: 44px
- Background: #00c73f
- Color: #ffffff
- Font: Button Text (14px, 600)
- Border Radius: 8px
- Border: none
- Hover: Background #00b033
- Active: Background #009929
- Disabled: Background #cccccc, Cursor not-allowed
- Transition: all 0.2s ease

Secondary Links:
- Color: #00c73f
- Text Decoration: none
- Hover: Text Decoration: underline

Form Footer
Spacing: 16px margin-top
Text: Body S, #666666
Link: Body S, Color #00c73f, Font-weight 600
Divider: 1px solid #e0e0e0, margin: 24px 0
Social Login Buttons: Height 44px, outline style, gap 12px

Right Column (Hero)
Width: 50%
Background: Linear gradient from #00c73f (top-left) to #009929 (bottom-right)
Position: relative
Display: flex, align-items: center, justify-content: center
Overflow: hidden

Hero Content
Image or Illustration:
- Size: 400x400px (or responsive)
- Opacity: 0.95
- Filter: drop-shadow(0 20px 40px rgba(0,0,0,0.15))

OR Gradient Overlay + Text:
- Overlay Color: rgba(0,0,0,0.2)
- Heading: H1, #ffffff, centered
- Subtext: Body M, rgba(255,255,255,0.9), centered
- Icons: MUI icons in #ffffff, scattered decoratively

Responsive Behavior
Desktop (> 1024px): Two-column layout as described

Tablet (768px - 1024px):
- Left Column: 60%, Right Column: 40%
- Left Padding: 32px
- Right Image: Smaller or partially visible

Mobile (< 768px):
- Single Column Layout
- Right Column: Hero image as background (opacity 0.3)
- Form on top with white background
- Full width, padding: 20px
- Image positioned absolute behind form

Auth Page Variations
Login Page
Title: "Welcome Back"
Subtitle: "Sign in to your GFZA Staff Portal"
Fields:
  - Email input
  - Password input
  - "Forgot Password?" link
Button: "Sign In"
Footer: "Don't have an account? Sign up"

Registration Page
Title: "Create Account"
Subtitle: "Join GFZA Staff Portal"
Fields:
  - First Name input
  - Last Name input
  - Email input
  - Department selector (dropdown)
  - Password input
  - Confirm Password input
  - Terms checkbox
Button: "Create Account"
Footer: "Already have an account? Sign in"

Forgot Password Page
Title: "Reset Password"
Subtitle: "Enter your email to receive reset instructions"
Fields:
  - Email input
Button: "Send Reset Link"
Footer: "Back to Sign In" (link)


7. DASHBOARD LAYOUTS
Dashboard Header
Breadcrumbs: 12px text, path with "/" separator
  - Color: #666666
  - Active: #1a1a1a

Page Title: H1, margin: 12px 0
Quick Actions: Right-aligned buttons
  - Refresh Icon: MUI Refresh
  - Filter Icon: MUI FilterList
  - More Options: MUI MoreVert

KPI Cards (Stats Row)
Card Design:
- Background: #ffffff
- Padding: 20px
- Border Radius: 8px
- Border: 1px solid #e0e0e0
- Shadow: 0 1px 3px rgba(0,0,0,0.08)
- Transition: all 0.3s ease

Hover State:
- Shadow: 0 4px 12px rgba(0,0,0,0.12)
- Transform: translateY(-2px)

Content Layout:
- Icon (top-left): 32x32px, #00c73f
- Label (top-right): 12px, #999999, uppercase
- Value (bottom-left): H3, #1a1a1a
- Change (bottom-right): 12px, green/red, with icon

KPI Card Example
┌────────────────────────────────────────┐
│ 🔧                        Open Tickets │
│                                        │
│ 24                  ↑ 12% (from prev)  │
└────────────────────────────────────────┘

Data Tables
Table Header
Background: #f5f5f5
Height: 44px
Padding: 12px 16px
Font: Caption (12px, 600)
Color: #666666
Text Transform: uppercase
Letter Spacing: 0.5px
Border Bottom: 1px solid #e0e0e0

Checkbox Column (if selectable): 40px wide
Action Column (if present): 60px wide

Table Rows
Height: 48px
Padding: 12px 16px
Border Bottom: 1px solid #e0e0e0
Font: Body M (14px, 400)
Color: #1a1a1a

Hover State:
- Background: #f5f5f5
- Cursor: pointer

Selected State:
- Background: rgba(0,199,63,0.05)
- Border Left: 3px solid #00c73f

Striped Rows (optional):
- Even rows: #ffffff
- Odd rows: #fafafa

Table Pagination
Position: Bottom right
Height: 44px
Padding: 12px 16px
Display: flex, align-items: center, gap: 12px

Elements:
- "Rows per page:" text + dropdown (12px)
- Page info: "1–10 of 150" (12px)
- Previous/Next buttons with arrows
- Jump to page input (if needed)

Button Style:
- Width: 36px, Height: 36px
- Border Radius: 6px
- Border: 1px solid #e0e0e0
- Background: transparent
- Hover: Background #f5f5f5
- Active: Background #00c73f, Color #ffffff, Border transparent

Table with Actions
Right Column: Action Icons (MUI)
- Edit Icon: MUI Edit (24px, #00c73f)
- View Icon: MUI Visibility (24px, #2196f3)
- Delete Icon: MUI Delete (24px, #f44336)
- More Options: MUI MoreVert (24px, #666666)

Icons in Row:
- Display on hover (mobile: always show)
- Gap between icons: 8px
- Hover icon color: darker shade
- Click: Show tooltip or open menu

Cards Grid Layout
Card Design
Background: #ffffff
Border: 1px solid #e0e0e0
Border Radius: 8px
Padding: 16px
Shadow: 0 1px 3px rgba(0,0,0,0.08)
Transition: all 0.3s ease

Hover:
- Shadow: 0 8px 24px rgba(0,0,0,0.12)
- Transform: translateY(-4px)

Click/Active:
- Border: 2px solid #00c73f
- Shadow: 0 0 0 4px rgba(0,199,63,0.1)

Card Header
Title: H4 (16px, 600), #1a1a1a
Subtitle/Date: 12px, #999999
Icon/Badge: Top-right corner, 32x32px
Divider: 1px solid #e0e0e0, margin: 12px 0

Card Body
Padding: 12px 0 (inside card padding area)
Content: Body M or S, #1a1a1a
Links: Color #00c73f, hover underline

Card Footer
Divider: 1px solid #e0e0e0, margin: 12px -16px 0 -16px
Padding: 12px 16px 0 16px
Buttons/Actions: Flex, justify-content: space-between
- Primary button: #00c73f
- Secondary button: outline style

Grid Responsive Behavior
Desktop (1920px+): 4 columns, gap 16px
Desktop (1440px): 3 columns, gap 16px
Laptop (1024px): 3 columns, gap 16px
Tablet (768px): 2 columns, gap 12px
Mobile (< 768px): 1 column, gap 12px


8. FORM COMPONENTS
Text Inputs
Height: 44px
Padding: 12px 16px
Border: 1px solid #e0e0e0
Border Radius: 8px
Font: Body M, Poppins 400
Background: #ffffff

States:
- Default: Border #e0e0e0
- Focus: Border #00c73f, Box-shadow: 0 0 0 3px rgba(0,199,63,0.1)
- Error: Border #f44336, Box-shadow: 0 0 0 3px rgba(244,67,54,0.1)
- Disabled: Background #f5f5f5, Border #e0e0e0, Color #999999, Cursor not-allowed

Placeholder: #999999, opacity 0.6
Label (above): 14px (Caption), color #1a1a1a, margin-bottom 8px

Error Message (below): 12px, color #f44336, margin-top 4px
Helper Text (below): 12px, color #999999, margin-top 4px

Selects / Dropdowns
Height: 44px
Padding: 12px 16px
Border: 1px solid #e0e0e0
Border Radius: 8px
Font: Body M, Poppins 400
Background: #ffffff
Icon: MUI ExpandMore (24px, right-aligned, #999999)

Dropdown Menu:
- Position: absolute, below input
- Min Width: 100% (input width)
- Background: #ffffff
- Border: 1px solid #e0e0e0
- Border Radius: 8px
- Shadow: 0 8px 24px rgba(0,0,0,0.12)
- Max Height: 280px
- Overflow: auto
- Z-index: 1100

Menu Items:
- Height: 40px
- Padding: 12px 16px
- Font: Body M
- Color: #1a1a1a
- Hover: Background #f5f5f5
- Selected: Background rgba(0,199,63,0.1), Color #00c73f, Font-weight 600

Checkboxes
Size: 20x20px
Border: 2px solid #e0e0e0
Border Radius: 4px
Background: transparent

States:
- Unchecked: Border #e0e0e0, Background transparent
- Checked: Border #00c73f, Background #00c73f, Icon #ffffff (checkmark)
- Hover: Border #00c73f
- Disabled: Border #bdbdbd, Background #f5f5f5, Cursor not-allowed
- Indeterminate: Border #00c73f, Background #00c73f, Icon: minus line

Label (next to checkbox):
- Font: Body M
- Color: #1a1a1a
- Margin-left: 12px
- Cursor: pointer

Radio Buttons
Size: 20x20px
Border: 2px solid #e0e0e0
Border Radius: 50%
Background: transparent

States:
- Unchecked: Border #e0e0e0, Background transparent
- Checked: Border 6px solid #00c73f (or filled circle)
- Hover: Border #00c73f
- Disabled: Border #bdbdbd, Cursor not-allowed

Label (next to radio):
- Font: Body M
- Color: #1a1a1a
- Margin-left: 12px
- Cursor: pointer

Buttons
Primary Button
Padding: 12px 24px
Min Height: 44px
Background: #00c73f
Color: #ffffff
Font: Button Text (14px, 600)
Border Radius: 8px
Border: none
Cursor: pointer
Transition: all 0.2s ease

States:
- Default: Background #00c73f
- Hover: Background #00b033
- Active: Background #009929
- Disabled: Background #cccccc, Cursor not-allowed, Opacity 0.6
- Loading: Show spinner icon, text hidden or "Loading..."

Sizes:
- Small: 36px height, 16px padding
- Medium: 44px height, 24px padding (default)
- Large: 52px height, 32px padding

Secondary Button
Padding: 12px 24px
Min Height: 44px
Background: transparent
Color: #00c73f
Font: Button Text (14px, 600)
Border: 2px solid #00c73f
Border Radius: 8px
Cursor: pointer
Transition: all 0.2s ease

States:
- Default: Background transparent, Border #00c73f
- Hover: Background rgba(0,199,63,0.08), Border #00c73f
- Active: Background rgba(0,199,63,0.12), Border #00b033
- Disabled: Border #cccccc, Color #cccccc, Cursor not-allowed

Tertiary/Ghost Button
Padding: 12px 24px
Min Height: 44px
Background: transparent
Color: #00c73f
Font: Button Text (14px, 600)
Border: none
Border Radius: 8px
Cursor: pointer
Transition: all 0.2s ease

States:
- Default: Background transparent
- Hover: Background rgba(0,199,63,0.08)
- Active: Background rgba(0,199,63,0.12)
- Disabled: Color #cccccc, Cursor not-allowed

Icon Button
Size: 40x40px (standard) or 36x36px (compact)
Display: flex, align-items: center, justify-content: center
Background: transparent
Border: none
Border Radius: 50% or 8px
Cursor: pointer
Transition: all 0.2s ease

Icon: 24px (MUI)

States:
- Default: Color #666666
- Hover: Background #f5f5f5, Color #1a1a1a
- Active: Background #00c73f, Color #ffffff
- Disabled: Color #bdbdbd, Cursor not-allowed

Variants:
- Primary: Color #00c73f, Hover background rgba(0,199,63,0.1)
- Danger: Color #f44336, Hover background rgba(244,67,54,0.1)
- Info: Color #2196f3, Hover background rgba(33,150,243,0.1)

Chip / Tag Component
Height: 32px
Padding: 4px 12px
Background: #f5f5f5
Border: 1px solid #e0e0e0
Border Radius: 16px (pill-shaped)
Font: 12px (Body S)
Color: #1a1a1a
Display: inline-flex, align-items: center, gap: 8px

Icon (optional): 16px MUI
Close Icon (if removable): 16px MUI, cursor pointer

Variants:
- Default: Background #f5f5f5, Border #e0e0e0
- Success: Background rgba(76,175,80,0.1), Border #4caf50, Color #4caf50
- Warning: Background rgba(255,152,0,0.1), Border #ff9800, Color #ff9800
- Error: Background rgba(244,67,54,0.1), Border #f44336, Color #f44336
- Primary: Background rgba(0,199,63,0.1), Border #00c73f, Color #00c73f

States:
- Hover: Opacity 0.8
- Click: Background slightly darker


9. ALERTS & MODALS
Alert / Toast Notification
Position: Fixed, top-right corner (or bottom-right)
Margin: 16px from edges
Min Width: 320px
Max Width: 480px
Padding: 16px
Border Radius: 8px
Shadow: 0 4px 16px rgba(0,0,0,0.15)
Display: flex, gap: 12px, align-items: flex-start
Animation: slide-in from right (0.3s ease)
Auto-dismiss: 5s (configurable)

Icon (left): 24px MUI
Title: 14px (600), color varies
Message: 14px (400), color varies
Close Button: MUI Close (24px), right-aligned

Variants:
- Success: Background #4caf50, Icon #ffffff, Text #ffffff
- Error: Background #f44336, Icon #ffffff, Text #ffffff
- Warning: Background #ff9800, Icon #ffffff, Text #ffffff
- Info: Background #2196f3, Icon #ffffff, Text #ffffff

Modal / Dialog
Position: Fixed, center of screen
Overlay: Background rgba(0,0,0,0.5), z-index 1200
Modal Box:
  - Background: #ffffff
  - Border Radius: 12px
  - Shadow: 0 20px 60px rgba(0,0,0,0.3)
  - Min Width: 400px
  - Max Width: 600px
  - Max Height: 80vh
  - Overflow: auto
  - Z-index: 1201
  - Animation: scale-up + fade-in (0.3s ease)

Modal Header:
  - Padding: 20px
  - Border Bottom: 1px solid #e0e0e0
  - Display: flex, justify-content: space-between, align-items: center
  - Title: H3 (20px, 600)
  - Close Button: MUI Close, 28x28px, cursor pointer

Modal Body:
  - Padding: 20px
  - Font: Body M
  - Color: #1a1a1a

Modal Footer:
  - Padding: 20px
  - Border Top: 1px solid #e0e0e0
  - Display: flex, justify-content: flex-end, gap: 12px
  - Buttons: Primary (confirm), Secondary (cancel)

Sizes:
- Small: 400px
- Medium: 600px (default)
- Large: 800px
- Full: 90vw (with max-width)

Confirmation Dialog
Title: "Confirm Action" (H3)
Message: Body M, explain consequence
Icon: MUI Warning or AlertCircle (32x32px, #ff9800)

Buttons:
- Cancel (Secondary): on left
- Confirm (Primary/Danger): on right
  - If destructive: use #f44336 instead of #00c73f


10. STATUS INDICATORS & BADGES
Badge
Display: inline-block
Padding: 4px 8px
Border Radius: 4px
Font: 11px (600)
Text Transform: uppercase
Letter Spacing: 0.5px

Variants:
- Success (green): Background #4caf50, Color #ffffff
- Warning (orange): Background #ff9800, Color #ffffff
- Error (red): Background #f44336, Color #ffffff
- Info (blue): Background #2196f3, Color #ffffff
- Default (gray): Background #e0e0e0, Color #1a1a1a
- Primary: Background #00c73f, Color #ffffff

Status Indicator (Dot)
Size: 12x12px
Border Radius: 50%
Display: inline-block
Margin-right: 8px (when with text)

Variants:
- Active / Online: #4caf50
- Inactive / Offline: #bdbdbd
- Pending: #ff9800
- Error: #f44336
- Processing: #2196f3 (animated pulse)

Progress Bar
Height: 8px
Background: #e0e0e0
Border Radius: 4px
Overflow: hidden

Filled Portion:
- Background: #00c73f
- Height: 100%
- Transition: width 0.3s ease
- Border Radius: 4px

Label (above bar): Caption (12px), "#value%"

Variants:
- Success: #4caf50
- Warning: #ff9800
- Error: #f44336


11. MODULE-SPECIFIC LAYOUTS
HR Dashboard
Layout: 3-column cards + table

Top Row (KPIs):
- Total Staff
- New Memos (This Week)
- Pending Approvals (if any)

Main Content:
- Recent Memos Card (left column)
- Upcoming Events (right column)
- Meal Planning & Selections (bottom table)

Color Accent: #00c73f for HR-related items

MIS Dashboard
Layout: 4-column stats + tabs

KPI Cards:
- Open Tickets
- Resolved This Month
- Assigned Assets
- Maintenance Due

Tabs:
- Tickets (table view)
- Assets (grid or table)
- System Emails Status

Color Accent


