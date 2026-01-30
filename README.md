# CP-Project-Audit-logs (Activity Log Viewer - M16)

This project is an administrative security module designed to visualize system usage. It serves as a frontend interface for an Audit Log system, allowing administrators to inspect data changes (Insert, Update, Delete) across the application.

## 🚀 Phase 1 Progress

### ✅ Completed Tasks

#### 1. Database Setup
- Created project database **`audit_db`**.
- Designed and created the **`audit_log`** table to store user activity details:
  - Actor (User)
  - Module
  - Action (Insert/Update/Delete)
  - IP Address
  - Timestamps
  - Old/New Data (JSON format)

#### 2. Dummy Data Simulation
- Inserted sample log entries to simulate real system actions (e.g., login, updates, deletions).
- Utilized JSON fields to store **before** and **after** snapshots for update operations to track specific data changes.

#### 3. Admin Log Viewer (`logs.php`)
- Built the initial **Activity Log Viewer UI** for administrators.
- Established database connection to fetch logs, ordered by the latest activity.
- Displayed data in a structured, responsive tabular format.

#### 4. Search & Filters
- Implemented filtering functionality based on:
  - **Action** (Insert, Update, Delete)
  - **User Search** (Text search)
  - **Date Range** (Start and End dates)
- Designed the system architecture to easily extend with more advanced filters in the future.

---

## 🛠️ Tech Stack
- **Frontend:** HTML5, CSS3 (Responsive Design)
- **Backend:** PHP (Native)
- **Database:** MySQL
- **Server:** Apache (XAMPP/WAMP)

---

## ⚙️ How to Run

1. **Clone the Repository**
   ```bash
   git clone [https://github.com/Aman-G-upta/CP-Project-Audit-logs.git](https://github.com/Aman-G-upta/CP-Project-Audit-logs.git)
