<?php
/**
 * SmartHUB Database Setup & Seeder Script
 */

require_once __DIR__ . '/../app/config/config.php';
require_once __DIR__ . '/../app/core/Model.php';
require_once __DIR__ . '/../app/helpers/Encryption.php';

use App\Helpers\Encryption;

echo "--- Starting SmartHUB Database Setup ---\n";

try {
    // 1. Create database if not exists
    $pdo = new PDO("mysql:host=" . DB_HOST . ";port=" . DB_PORT, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `" . DB_NAME . "` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");
    echo "Database `" . DB_NAME . "` created or verified successfully.\n";

    // 2. Select DB and load Schema
    $pdo->exec("USE `" . DB_NAME . "`;");
    $schemaFile = __DIR__ . '/sql/schema.sql';
    if (!file_exists($schemaFile)) {
        throw new Exception("Schema file not found at: {$schemaFile}");
    }
    
    $sql = file_get_contents($schemaFile);
    $pdo->exec($sql);
    echo "DDL schema successfully imported.\n";

    // 3. Seed lookup tables
    echo "Seeding lookup tables...\n";
    
    // Years of Study
    $years = [
        ['name' => 'Year One', 'code' => 'Y1'],
        ['name' => 'Year Two', 'code' => 'Y2'],
        ['name' => 'Year Three', 'code' => 'Y3'],
    ];
    $stmt = $pdo->prepare("INSERT INTO years (name, code) VALUES (:name, :code)");
    foreach ($years as $y) {
        $stmt->execute($y);
    }
    echo "- Years seeded.\n";

    // Semesters
    $semesters = [
        ['name' => 'Semester One', 'code' => 'S1'],
        ['name' => 'Semester Two', 'code' => 'S2'],
    ];
    $stmt = $pdo->prepare("INSERT INTO semesters (name, code) VALUES (:name, :code)");
    foreach ($semesters as $s) {
        $stmt->execute($s);
    }
    echo "- Semesters seeded.\n";

    // Subscription Plans
    $plans = [
        [
            'name' => 'Basic Student',
            'price' => 0.00,
            'duration_months' => 12,
            'max_downloads' => 5,
            'description' => 'Standard free student plan. Max 5 downloads per day. Restrained to enrolled programme.'
        ],
        [
            'name' => 'Premium Student',
            'price' => 4.99,
            'duration_months' => 1,
            'max_downloads' => 50,
            'description' => 'Unlocks 50 downloads per day and allows choosing 1 external academic programme.'
        ],
        [
            'name' => 'Elite Scholar',
            'price' => 9.99,
            'duration_months' => 1,
            'max_downloads' => 0, // Unlimited
            'description' => 'Complete unlimited downloads and unrestricted access to all faculties and programmes.'
        ],
    ];
    $stmt = $pdo->prepare("INSERT INTO subscription_plans (name, price, duration_months, max_downloads, description) VALUES (:name, :price, :duration_months, :max_downloads, :description)");
    foreach ($plans as $p) {
        $stmt->execute($p);
    }
    echo "- Subscription plans seeded.\n";

    // 4. Seed Faculties and Programmes
    echo "Seeding Faculties & Programmes...\n";
    $faculties = [
        [
            'name' => 'Faculty of Business',
            'description' => 'Excellence in Finance, Procurement, Marketing and Accountancy.',
            'programmes' => [
                ['name' => 'Bachelor of Business Administration', 'code' => 'BBA', 'description' => 'General business administration.'],
                ['name' => 'Bachelor of Procurement & Logistics', 'code' => 'BPL', 'description' => 'Procurement logistics.'],
                ['name' => 'Bachelor of Commerce', 'code' => 'BCOM', 'description' => 'Finance & Accounting study.'],
            ]
        ],
        [
            'name' => 'Faculty of Science and Technology',
            'description' => 'Pioneering Computing, Information Technology, Engineering, and Digital Humanities.',
            'programmes' => [
                ['name' => 'Bachelor of Information Technology', 'code' => 'BIT', 'description' => 'Core BIT programme studying web, DB, and programming.'],
                ['name' => 'Bachelor of Computer Science', 'code' => 'BCS', 'description' => 'Advanced theoretical computer science.'],
            ]
        ]
    ];

    $facStmt = $pdo->prepare("INSERT INTO faculties (name, description) VALUES (:name, :description)");
    $progStmt = $pdo->prepare("INSERT INTO programmes (faculty_id, name, code, description) VALUES (:faculty_id, :name, :code, :description)");

    foreach ($faculties as $f) {
        $facStmt->execute([
            'name' => $f['name'],
            'description' => $f['description']
        ]);
        $facId = $pdo->lastInsertId();

        foreach ($f['programmes'] as $p) {
            $progStmt->execute([
                'faculty_id' => $facId,
                'name' => $p['name'],
                'code' => $p['code'],
                'description' => $p['description']
            ]);
        }
    }
    echo "- Faculties and Programmes seeded.\n";

    // 5. Seed Courses
    echo "Seeding Courses (BIT Curriculum example)...\n";
    // Get BIT programme ID
    $bitStmt = $pdo->query("SELECT id FROM programmes WHERE code = 'BIT'");
    $bitProgId = $bitStmt->fetchColumn();

    // Get Years and Semesters
    $y1 = $pdo->query("SELECT id FROM years WHERE code = 'Y1'")->fetchColumn();
    $y2 = $pdo->query("SELECT id FROM years WHERE code = 'Y2'")->fetchColumn();
    $s1 = $pdo->query("SELECT id FROM semesters WHERE code = 'S1'")->fetchColumn();
    $s2 = $pdo->query("SELECT id FROM semesters WHERE code = 'S2'")->fetchColumn();

    $courses = [
        [
            'programme_id' => $bitProgId,
            'year_id' => $y1,
            'semester_id' => $s1,
            'name' => 'Introduction to Programming',
            'code' => 'BIT-1101',
            'description' => 'Fundamentals of OOP and structured programming using PHP and Python.',
            'lecturer' => 'Dr. Robert Carter'
        ],
        [
            'programme_id' => $bitProgId,
            'year_id' => $y1,
            'semester_id' => $s1,
            'name' => 'Academic Writing and Communication',
            'code' => 'BIT-1102',
            'description' => 'Developing technical writing, business communications, and essay structures.',
            'lecturer' => 'Mrs. Grace Vance'
        ],
        [
            'programme_id' => $bitProgId,
            'year_id' => $y1,
            'semester_id' => $s2,
            'name' => 'Database Systems Design',
            'code' => 'BIT-1201',
            'description' => 'Database design, normalization to 3NF, ER diagrams, and SQL execution.',
            'lecturer' => 'Prof. Alan Turing'
        ],
        [
            'programme_id' => $bitProgId,
            'year_id' => $y2,
            'semester_id' => $s1,
            'name' => 'Internet and Web Development',
            'code' => 'BIT-2101',
            'description' => 'Advanced full-stack systems engineering without third-party frameworks.',
            'lecturer' => 'Dr. Tim Berners-Lee'
        ]
    ];

    $courseStmt = $pdo->prepare("INSERT INTO courses (programme_id, year_id, semester_id, name, code, description, lecturer) 
                                 VALUES (:programme_id, :year_id, :semester_id, :name, :code, :description, :lecturer)");
    foreach ($courses as $c) {
        $courseStmt->execute($c);
    }
    echo "- Courses seeded.\n";

    // 6. Seed Admin and Students
    echo "Seeding Users...\n";
    $adminPassword = password_hash('AdminPassword123', PASSWORD_DEFAULT);
    $studentPassword = password_hash('StudentPassword123', PASSWORD_DEFAULT);
    
    // Admin Phone
    $adminPhoneEnc = Encryption::encrypt('+256701234567');
    $studentPhoneEnc = Encryption::encrypt('+256702223344');

    $userStmt = $pdo->prepare("INSERT INTO users (programme_id, subscription_plan_id, role, name, email, encrypted_phone, password_hash, status) 
                               VALUES (:programme_id, :subscription_plan_id, :role, :name, :email, :encrypted_phone, :password_hash, :status)");
    
    // Seed Admin
    $userStmt->execute([
        'programme_id' => null,
        'subscription_plan_id' => null,
        'role' => 'admin',
        'name' => 'System Administrator',
        'email' => 'admin@smarthub.edu',
        'encrypted_phone' => $adminPhoneEnc,
        'password_hash' => $adminPassword,
        'status' => 'active'
    ]);
    
    // Seed Demo Student (under BIT)
    $basicPlanId = $pdo->query("SELECT id FROM subscription_plans WHERE name = 'Basic Student'")->fetchColumn();
    $userStmt->execute([
        'programme_id' => $bitProgId,
        'subscription_plan_id' => $basicPlanId,
        'role' => 'student',
        'name' => 'John Doe Student',
        'email' => 'student@smarthub.edu',
        'encrypted_phone' => $studentPhoneEnc,
        'password_hash' => $studentPassword,
        'status' => 'active'
    ]);

    echo "- Admin (admin@smarthub.edu / AdminPassword123) and Student (student@smarthub.edu / StudentPassword123) seeded.\n";

    // 7. Seed announcements
    $adminId = $pdo->query("SELECT id FROM users WHERE role = 'admin' LIMIT 1")->fetchColumn();
    $annStmt = $pdo->prepare("INSERT INTO announcements (title, content, creator_id) VALUES (:title, :content, :creator_id)");
    $annStmt->execute([
        'title' => 'Welcome to SmartHUB Academic Digital Library!',
        'content' => 'We are excited to launch the SmartHUB academic resource bank. Students can access lecture notes, reference books, past papers, practical guides, and class videos directly mapped to their registered programmes and semesters. Enjoy your studies!',
        'creator_id' => $adminId
    ]);
    echo "- System announcements seeded.\n";

    echo "--- Database Setup Completed Successfully ---\n";

} catch (Exception $e) {
    echo "CRITICAL DATABASE ERROR: " . $e->getMessage() . "\n";
    exit(1);
}
