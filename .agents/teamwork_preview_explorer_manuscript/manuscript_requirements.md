# SSFO eLog System: Manuscript Requirements Analysis Report

This report presents a detailed analysis of the requirements extracted from the project manuscript (`Manuscript.docx`) for the **Support Services Facilitators Office Electronic Logging (SSFO eLog) System**.

---

## 1. System Overview & Context

### 1.1 Introduction
The **SSFO eLog** is a web-based electronic logging system designed to replace the manual paper logbook currently used by the **Support Services Facilitators Office (SSFO)**. The manual system has critical pain points:
* Records are prone to getting lost or damaged.
* Retrieving past records is highly time-consuming.
* Request status tracking is difficult.
* Preparation of reports is a slow, manual process.
* Delays cause clients to wait long periods for assistance.

The SSFO eLog system introduces digitization and automation to centralize records, assign unique request numbers, and provide tracking and analytical reporting tools to streamline operations, secure data, and improve office efficiency.

### 1.2 Purpose and Objectives
* **Objective 1**: Design a web-based electronic logging system to record and monitor client requests at the SSFO.
* **Objective 2**: Develop a platform to manage client records, track request statuses, and generate analytical reports.
* **Objective 3**: Provide a reliable, secure, and easy-to-use application with a centralized database to eliminate manual workflows.

### 1.3 Scope & Limitations
* **In-Scope**:
  * Logging and tracking client requests for assistance.
  * Separate portals/pages for Clients and Administrators.
  * Uploading verification attachments.
  * Status tracking and update notifications.
  * Reviewing, editing, approving, or rejecting requests.
  * Generating analytical reports on approved and rejected requests.
* **Out-of-Scope (Limitations)**:
  * Financial/payment transactions.
  * Offline capability (the system strictly requires an internet connection).
  * Comprehensive automation of all underlying assistance workflows (limited to request logging, verification, approval/rejection, and reporting).

---

## 2. User Roles & Access Control

The system defines two primary users of the web application, with references to operational roles from the manual workflow:

| Role | Description | Key System Interactions |
| :--- | :--- | :--- |
| **Client** | Resident/citizen seeking assistance | Scan QR code, log in, submit request, upload attachments, track status, receive notifications, log out. |
| **SSFO Staff / Admin** | Office administrators/facilitators | Log in, review/verify requests and attachments, approve/reject requests, edit/update details, generate reports, send notifications, log out. |
| **CBF In-charge** *(Operational)* | Staff member forwarding requests | Handled via admin request routing/management (from current flow). |
| **Municipal Facilitator** *(Operational)* | Decision-maker reviewing/approving requests | Handled via admin approval mechanism (from current flow). |

---

## 3. Functional Requirements

### 3.1 Client Portal (Front-End)
1. **Access Initiation**: The client initiates the transaction by scanning a QR code at the office or accessing the web address.
2. **Authentication**:
   * **Login**: Authenticate to access the dashboard.
   * **Logout**: Terminate session securely.
3. **Submit Request**: Create and submit a request for assistance.
4. **Upload Attachments**: Upload supporting documents/papers to prove need/eligibility.
5. **Track Status**: Monitor the real-time status of submitted requests.
6. **Receive Notifications**: Receive automated updates regarding status changes, approvals, or required next steps.

### 3.2 Admin/Staff Portal (Back-End)
1. **Authentication**:
   * **Login**: Secure login for authorized administrative staff.
   * **Logout**: Terminate admin session.
2. **Review & Verify Requests**: View client submissions, metadata, and uploaded attachments.
3. **Approve or Reject (Decline) Requests**:
   * Approve valid requests.
   * Reject invalid requests (requires entering a reason/status update).
4. **Update/Edit Client Records**: Edit details of existing request records or client files.
5. **Generate Reports**: Export or view analytical reports of approved and rejected requests for record management and decision support.
6. **Send Notifications**: Send alerts or status notifications directly to clients.

---

## 4. Non-Functional Requirements

* **Architecture**: Standard web application utilizing a database-driven architecture (specifically PHP, MySQL, HTML, CSS, JavaScript).
* **Database Centralization**: All records must reside in a single centralized MySQL database to guarantee data integrity, searchability, and ease of backup.
* **Usability & Maintainability**: Simple user interface (UI) to minimize user error, ensure quick staff training, and support easy system updates.
* **Reliability & Security**: Prevent data loss/damage (a major drawback of the paper logbook) and ensure secure authentication for admins and clients.

---

## 5. System Workflow & Business Rules

### 5.1 Manual Workflow vs. Proposed eLog Workflow

```
[ MANUAL WORKFLOW ]
Client Visits Office ➔ Goes to Logbook Area ➔ Staff/CBF Logs Request Manually ➔ Request Forwarded to Municipal Facilitator ➔ Review & Approval ➔ Client Notified (Manual/Delay)

[ PROPOSED DIGITAL WORKFLOW ]
Client Scans QR/Accesses Web ➔ Logs In & Submits Request with Attachments ➔ System Logs Request & Notifies Client ➔ SSFO Staff/Admin Reviews Online ➔ Approval/Rejection Decided ➔ System Automatically Updates Client & Sends Notifications ➔ Admin Generates Analytical Reports
```

### 5.2 Business Rules
1. **Unique Request Tracking**: Every submitted request must be assigned a unique sequential or system-generated tracking number immediately.
2. **Attachment Validation**: A client request must allow uploading supporting documents before review can occur.
3. **State Transitions**: A request must transition through predefined statuses (e.g., `Pending` ➔ `Under Review` ➔ `Approved` OR `Rejected`).
4. **Notification Trigger**: Changing a request status (approval/rejection) or updating details must trigger a notification to the associated Client.
5. **Read-Only Client Limits**: Clients can only create requests and view their own request history and status; they cannot edit a request once submitted or view other clients' data.
6. **Admin Authorization**: Only authenticated administrators/staff can view all requests, update statuses, edit records, and generate reports.

---

## 6. Inferred Data Model (Entity-Relationship Suggestion)

Based on the functional processes (CRUD operations) and diagrams, the following MySQL database schema is suggested:

### 6.1 Entity: `users`
Stores client and administrative accounts.
* `id` (INT, Primary Key, Auto Increment)
* `username` (VARCHAR, Unique)
* `password` (VARCHAR, Hashed)
* `role` (ENUM: 'client', 'admin')
* `first_name` (VARCHAR)
* `last_name` (VARCHAR)
* `contact_number` (VARCHAR)
* `created_at` (TIMESTAMP)

### 6.2 Entity: `requests`
Stores logged requests.
* `id` (INT, Primary Key, Auto Increment)
* `request_number` (VARCHAR, Unique tracking identifier)
* `client_id` (INT, Foreign Key referencing `users.id`)
* `description` (TEXT, details of assistance needed)
* `status` (ENUM: 'pending', 'approved', 'rejected', 'under_review')
* `rejection_reason` (TEXT, optional comment if rejected)
* `reviewed_by` (INT, Foreign Key referencing `users.id` representing admin)
* `created_at` (TIMESTAMP)
* `updated_at` (TIMESTAMP)

### 6.3 Entity: `attachments`
Stores paths to supporting documents.
* `id` (INT, Primary Key, Auto Increment)
* `request_id` (INT, Foreign Key referencing `requests.id`)
* `file_path` (VARCHAR, path to stored file)
* `uploaded_at` (TIMESTAMP)

### 6.4 Entity: `notifications`
Stores system messages for clients.
* `id` (INT, Primary Key, Auto Increment)
* `client_id` (INT, Foreign Key referencing `users.id`)
* `request_id` (INT, Foreign Key referencing `requests.id`)
* `message` (TEXT)
* `is_read` (BOOLEAN, default false)
* `created_at` (TIMESTAMP)

---

## 7. Academic References (from Manuscript)

* **Libadia et al. (2025)**: Development of a Web-based records management system (ERMS) for the Office of Senior Citizen Affairs in the Philippines. (Supports web centralization).
* **Williams and Lane (2020)**: Database-Driven Web Applications for Data Management using PHP & MySQL CRUD operations. (Supports tech stack selection).
* **Silberschatz, Korth, and Sudarshan (2020)**: Database systems importance for data organization and avoiding redundancy. (Supports database schema design).
* **Aliling et al. (2025)**: Digitalizing Governance processes in the Philippines. (Supports digitization of local offices).
* **Franco et al. (2023)**: Barangay Health Workers’ Recording and Monitoring System. (Supports replacing hand-written logbooks).
* **Gallera (2023)**: Centralized Information Management Systems for local government units. (Supports centralized tracking and usability).
