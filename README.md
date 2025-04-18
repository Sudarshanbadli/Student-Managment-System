# 🎉 **Student Management System** 🎉

Welcome to the **Student Management System** – a fun, dynamic, and easy-to-use system built with PHP, MySQL, and a sprinkle of magic! ✨  

This project helps manage student records, grades, and attendance in a structured and efficient way. Perfect for schools, colleges, or anyone who loves organizing data!  

---

## 🚀 **Features**
✅ **Add, Edit, Delete Students**  
✅ **Manage Courses & Grades**  
✅ **Attendance Tracking**  
✅ **User-Friendly Dashboard**  
✅ **Responsive Design (Works on all devices!)**  
✅ **Secure & Efficient**  

---

## 🛠 **Installation Guide**  

### **Prerequisites**
- **XAMPP** (Apache + MySQL + PHP) – [Download Here](https://www.apachefriends.org/download.html)  
- A modern web browser (Chrome, Firefox, Edge)  
- A cup of coffee ☕ (optional but highly recommended)  

### **Step 1: Set Up XAMPP**
1. **Download & Install XAMPP**  
   - Visit [XAMPP Official Site](https://www.apachefriends.org/download.html)  
   - Install with default settings (Make sure **Apache** and **MySQL** are selected).  

2. **Start XAMPP Servers**  
   - Open **XAMPP Control Panel**  
   - Start **Apache** and **MySQL**  

   ![XAMPP Control Panel](https://i.imgur.com/JQh6FdS.png)  

3. **Verify Installation**  
   - Open browser and go to:  
     ```
     http://localhost
     ```
   - You should see the **XAMPP Dashboard**.  

---

### **Step 2: Set Up the Project**
1. **Clone/Download the Project**  
   - Download the ZIP or clone the repo into `htdocs` folder:  
     ```
     C:\xampp\htdocs\student-management-system
     ```  

2. **Import Database**  
   - Open **phpMyAdmin** (`http://localhost/phpmyadmin`)  
   - Create a new database: `student_management`  
   - Import the provided SQL file (`database/student_management.sql`)  

3. **Configure Database Connection**  
   - Open `config/db.php` and update credentials if needed:  
     ```php
     $host = "localhost";
     $user = "root";
     $password = "";
     $dbname = "student_management";
     ```

---

### **Step 3: Run the Project**
1. **Start the Application**  
   - Open your browser and navigate to:  
     ```
     http://localhost/student-management-system
     ```  

2. **Login**  
   - Default Admin Credentials:  
     ```
     Username: admin@school.com
     Password: password
     ```  

---

## 📂 **File Structure**
```bash
student-management-system/
│
├── index.php                # Login page
├── register.php             # User registration
├── forgot-password.php      # Password reset
│
├── dashboard/               # All dashboard pages
│   ├── home.php             # Dashboard homepage
│   ├── admin.php            # Admin dashboard
│   ├── teacher.php          # Teacher dashboard
│   ├── student.php          # Student dashboard
│   ├── attendance.php       # Attendance management
│   ├── timetable.php        # Timetable management
│   ├── multimedia.php       # Multimedia gallery
│   └── logout.php           # Logout handler
│
├── includes/                # PHP includes
│   ├── db.php               # Database connection
│   ├── functions.php        # Common functions
│   ├── auth.php             # Authentication functions
│   ├── admin_actions.php    # Admin action handlers
│   ├── teacher_actions.php  # Teacher action handlers
│   ├── student_actions.php  # Student action handlers
│   └── media_actions.php    # Media action handlers
│
├── assets/                  # Static assets
│   ├── css/
│   │   └── style.css        # Main stylesheet
│   ├── js/
│   │   └── script.js        # Main JavaScript file
│   ├── images/              # Image assets
│   └── uploads/             # File uploads directory
│
├── sql/
│   └── student_system.sql   # Database schema
│
└── README.md                # Project documentation
```

---

## 🎨 **Screenshots**
| Dashboard | Students List | Add New Student |
|-----------|--------------|----------------|
| ![Dashboard](https://iili.io/31VQxaI.png) | ![Students](https://iili.io/31VtzIn.png) | ![Add Student](https://iili.io/31VDRZg.png) | ![loginpage](https://iili.io/31VmckN.png) | ![Registrationpage](https://iili.io/31VphoF.png) |

---

## 💡 **Need Help?**
- **Report Bugs** 🐞 – [Open an Issue](#)  
- **Want a New Feature?** ✨ – Let me know!  
- **Star the Repo** ⭐ – If you like it!  

---

## 🎓 **Developed By**  
👨‍💻 **Your Sudarshan Badli**  
📧 **your.sudarshanbadli7@gmail.com**  
🌐 **https://beacons.ai/sudarshanbadli_7**  

---

### **Happy Coding!** 🚀  
```bash
  ______
 /      \
|  ^__^  |
|  (oo)  |
 \______/
```
*(This cow approves your setup! 🐄)*  

---

🎊 **Now go ahead and manage those students like a pro! and Always stay & support with Sudarshan Badli** 🎊
