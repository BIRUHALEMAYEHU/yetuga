# 🚌 Yetuga - Urban Mobility & Resource Locator

[![PHP](https://img.shields.io/badge/PHP-8.0+-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://php.net/)
[![MySQL](https://img.shields.io/badge/MySQL-8.0+-4479A1?style=for-the-badge&logo=mysql&logoColor=white)](https://www.mysql.com/)
[![Security](https://img.shields.io/badge/Security-Enterprise%20Grade-brightgreen?style=for-the-badge)](https://github.com/BIRUHALEMAYEHU/yetuga)

**A professional web application that helps city residents find optimal transport routes and report transport issues in real-time.**

## 🌟 **Features**

- **🚌 Route Search**: Find optimal transport routes (bus, taxi, minibus, train)
- **💰 Fare Information**: Real-time pricing and travel time estimates
- **📝 Issue Reporting**: Report fare disputes, roadblocks, and driver behavior
- **👮‍♂️ Officer Dashboard**: Transport officials can manage routes and respond to reports
- **👑 Admin Panel**: Complete system management and oversight
- **🛡️ Enterprise Security**: CSRF protection, XSS prevention, SQL injection protection

## 🏗️ **Technology Stack**

- **Frontend**: HTML5, CSS3, JavaScript (Responsive Design)
- **Backend**: PHP 8.0+ with PDO
- **Database**: MySQL 8.0+
- **Security**: Enterprise-grade authentication and authorization
- **UI/UX**: Modern glassmorphism design with mobile-first approach

## 🚀 **Quick Start**

### **Prerequisites**
- PHP 8.0 or higher
- MySQL 8.0 or higher
- Apache/Nginx web server
- XAMPP/WAMP (for local development)

### **Installation**
1. Clone the repository
```bash
git clone https://github.com/BIRUHALEMAYEHU/yetuga.git
cd yetuga
```

2. Set up your web server to point to the `public/` directory

3. Import the database schema from `config/database_schema.sql`

4. Configure database connection in `config/database_config.php`

5. Access the application at `http://localhost/yetuga/public/`

## 📁 **Project Structure**

```
yetuga/
├── config/           # Security & database configurations
├── public/           # Web-accessible files (Document Root)
│   ├── user/        # User dashboard & pages
│   ├── officer/     # Officer dashboard & pages
│   ├── admin/       # Admin dashboard & pages
│   └── api/         # Secure API endpoints
├── assets/           # CSS, JavaScript, and images
├── temp/            # Rate limiting and cache files
└── docs/            # Documentation
```

## 🔐 **Security Features**

- **CSRF Protection**: All forms protected against cross-site request forgery
- **XSS Prevention**: Input/output sanitization and validation
- **SQL Injection Protection**: Prepared statements and parameterized queries
- **Session Security**: Secure session management with automatic timeout
- **Rate Limiting**: API abuse prevention
- **Role-Based Access Control**: Secure user role management

## 👥 **User Roles**

- **🚶‍♂️ Regular Users**: Search routes, submit reports, access urban services
- **👮‍♂️ Transport Officers**: Manage routes, respond to reports, update fares
- **👑 Administrators**: System oversight, user management, business approvals

## 🌍 **Target Market**

**Primary Focus**: Ethiopian cities (Addis Ababa, Adama)
**Problem Solved**: Urban mobility challenges with inconsistent taxi routes and unclear fare systems

## 📊 **Current Status**

- ✅ **Core Functionality**: Complete
- ✅ **Security**: Production-ready (LOW RISK - 2.5/10)
- ✅ **Database**: 14 tables, fully structured
- ✅ **Authentication**: Working with role-based access
- ✅ **UI/UX**: Modern, responsive design

## 🤝 **Contributing**

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

## 📄 **License**

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 📞 **Support**

For support and questions:
- Create an issue in this repository
- Contact the development team

---

**Built with ❤️ for better urban mobility in Ethiopia**

[![GitHub stars](https://img.shields.io/github/stars/BIRUHALEMAYEHU/yetuga?style=social)](https://github.com/BIRUHALEMAYEHU/yetuga/stargazers)
[![GitHub forks](https://img.shields.io/github/forks/BIRUHALEMAYEHU/yetuga?style=social)](https://github.com/BIRUHALEMAYEHU/yetuga/network)
[![GitHub issues](https://img.shields.io/github/issues/BIRUHALEMAYEHU/yetuga)](https://github.com/BIRUHALEMAYEHU/yetuga/issues)
