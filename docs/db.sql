CREATE DATABASE coach_athlete_app;
USE coach_athlete_app;

-- Table for users (athletes and coaches)
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('athlete', 'coach') NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    first_name VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table for coach profiles
CREATE TABLE coach_profiles (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT UNIQUE NOT NULL,
    photo VARCHAR(255) DEFAULT NULL,
    biography TEXT,
    years_experience INT DEFAULT 0,
    certifications TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Table for sports disciplines
CREATE TABLE disciplines (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL UNIQUE
);

-- Junction table for coach-disciplines
CREATE TABLE coach_disciplines (
    id INT PRIMARY KEY AUTO_INCREMENT,
    coach_id INT NOT NULL,
    discipline_id INT NOT NULL,
    level ENUM('beginner', 'intermediate', 'advanced', 'professional') DEFAULT 'intermediate',
    FOREIGN KEY (coach_id) REFERENCES coach_profiles(id) ON DELETE CASCADE,
    FOREIGN KEY (discipline_id) REFERENCES disciplines(id) ON DELETE CASCADE,
    UNIQUE KEY unique_coach_discipline (coach_id, discipline_id)
);

-- Table for coach availability
CREATE TABLE availabilities (
    id INT PRIMARY KEY AUTO_INCREMENT,
    coach_id INT NOT NULL,
    date DATE NOT NULL,
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    is_available BOOLEAN DEFAULT TRUE,
    FOREIGN KEY (coach_id) REFERENCES coach_profiles(id) ON DELETE CASCADE,
    CONSTRAINT check_times CHECK (start_time < end_time)
);

-- Table for bookings
CREATE TABLE bookings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    athlete_id INT NOT NULL,
    coach_id INT NOT NULL,
    availability_id INT,
    booking_date DATE NOT NULL,
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    status ENUM('pending', 'accepted', 'rejected', 'cancelled') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (athlete_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (coach_id) REFERENCES coach_profiles(id) ON DELETE CASCADE,
    FOREIGN KEY (availability_id) REFERENCES availabilities(id) ON DELETE SET NULL
);

-- Table for reviews (Bonus)
CREATE TABLE reviews (
    id INT PRIMARY KEY AUTO_INCREMENT,
    booking_id INT UNIQUE NOT NULL,
    athlete_id INT NOT NULL,
    coach_id INT NOT NULL,
    rating INT NOT NULL CHECK (rating >= 1 AND rating <= 5),
    comment TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE CASCADE,
    FOREIGN KEY (athlete_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (coach_id) REFERENCES coach_profiles(id) ON DELETE CASCADE
);

-- Insert default sports disciplines
INSERT INTO disciplines (name) VALUES 
('Football'),
('Tennis'),
('Swimming'),
('Athletics'),
('Boxing'),
('MMA'),
('Karate'),
('Judo'),
('Bodybuilding'),
('Physical Training'),
('Basketball'),
('Volleyball'),
('Running'),
('Cycling'),
('Sports Yoga');

-- Insert users (athletes and coaches)
INSERT INTO users (email, password, role, last_name, first_name, phone) VALUES
-- Coaches
('john.smith@coach.com', '$2y$10$abcdefghijklmnopqrstuvwxyz1234567890', 'coach', 'Smith', 'John', '+212600111111'),
('sarah.johnson@coach.com', '$2y$10$abcdefghijklmnopqrstuvwxyz1234567890', 'coach', 'Johnson', 'Sarah', '+212600222222'),
('michael.brown@coach.com', '$2y$10$abcdefghijklmnopqrstuvwxyz1234567890', 'coach', 'Brown', 'Michael', '+212600333333'),
('emma.davis@coach.com', '$2y$10$abcdefghijklmnopqrstuvwxyz1234567890', 'coach', 'Davis', 'Emma', '+212600444444'),
('david.wilson@coach.com', '$2y$10$abcdefghijklmnopqrstuvwxyz1234567890', 'coach', 'Wilson', 'David', '+212600555555'),

-- Athletes
('alex.martin@athlete.com', '$2y$10$abcdefghijklmnopqrstuvwxyz1234567890', 'athlete', 'Martin', 'Alex', '+212600666666'),
('sophia.garcia@athlete.com', '$2y$10$abcdefghijklmnopqrstuvwxyz1234567890', 'athlete', 'Garcia', 'Sophia', '+212600777777'),
('james.rodriguez@athlete.com', '$2y$10$abcdefghijklmnopqrstuvwxyz1234567890', 'athlete', 'Rodriguez', 'James', '+212600888888'),
('olivia.martinez@athlete.com', '$2y$10$abcdefghijklmnopqrstuvwxyz1234567890', 'athlete', 'Martinez', 'Olivia', '+212600999999'),
('william.lee@athlete.com', '$2y$10$abcdefghijklmnopqrstuvwxyz1234567890', 'athlete', 'Lee', 'William', '+212601111111'),
('isabella.walker@athlete.com', '$2y$10$abcdefghijklmnopqrstuvwxyz1234567890', 'athlete', 'Walker', 'Isabella', '+212601222222'),
('noah.hall@athlete.com', '$2y$10$abcdefghijklmnopqrstuvwxyz1234567890', 'athlete', 'Hall', 'Noah', '+212601333333');

-- Insert coach profiles (user_id 1-5 are coaches)
INSERT INTO coach_profiles (user_id, photo, biography, years_experience, certifications) VALUES
(1, 'uploads/coaches/john_smith.jpg', 'Professional football coach with UEFA A License. Specialized in youth development and tactical training. Former professional player with 10 years of experience.', 12, 'UEFA A License, Sports Science Degree, First Aid Certification'),
(2, 'uploads/coaches/sarah_johnson.jpg', 'Elite swimming coach with Olympic experience. Specialized in competitive swimming and technique refinement. Trained multiple national champions.', 15, 'FINA Level 3 Coaching, Exercise Physiology MSc, Lifeguard Certification'),
(3, 'uploads/coaches/michael_brown.jpg', 'Expert boxing and MMA coach. Former professional fighter with black belt in multiple martial arts. Focus on discipline and mental strength.', 8, 'Boxing Coach License, BJJ Black Belt, Sports Psychology Certificate'),
(4, 'uploads/coaches/emma_davis.jpg', 'Certified tennis professional with ATP coaching experience. Specialized in junior development and tournament preparation.', 10, 'PTR Professional, Sports Nutrition Specialist, Mental Game Coach'),
(5, 'uploads/coaches/david_wilson.jpg', 'Strength and conditioning specialist. Expert in athletic performance and injury prevention. Works with professional athletes across multiple sports.', 14, 'CSCS, NSCA-CPT, Functional Movement Screen Level 2, Sports Nutrition');

-- Insert coach disciplines
INSERT INTO coach_disciplines (coach_id, discipline_id, level) VALUES
-- John Smith - Football specialist
(1, 1, 'professional'),
(1, 10, 'advanced'),

-- Sarah Johnson - Swimming specialist
(2, 3, 'professional'),
(2, 4, 'advanced'),
(2, 13, 'intermediate'),

-- Michael Brown - Combat sports
(3, 5, 'professional'),
(3, 6, 'professional'),
(3, 7, 'advanced'),
(3, 8, 'advanced'),

-- Emma Davis - Tennis
(4, 2, 'professional'),
(4, 10, 'intermediate'),

-- David Wilson - Strength training
(5, 9, 'professional'),
(5, 10, 'professional'),
(5, 4, 'advanced'),
(5, 14, 'advanced');

-- Insert availabilities for coaches (next 2 weeks)
INSERT INTO availabilities (coach_id, date, start_time, end_time, is_available) VALUES
-- John Smith availabilities
(1, '2025-12-17', '09:00:00', '11:00:00', TRUE),
(1, '2025-12-17', '14:00:00', '16:00:00', TRUE),
(1, '2025-12-18', '10:00:00', '12:00:00', TRUE),
(1, '2025-12-19', '09:00:00', '11:00:00', TRUE),
(1, '2025-12-20', '15:00:00', '17:00:00', TRUE),

-- Sarah Johnson availabilities
(2, '2025-12-17', '08:00:00', '10:00:00', TRUE),
(2, '2025-12-17', '11:00:00', '13:00:00', TRUE),
(2, '2025-12-18', '08:00:00', '10:00:00', TRUE),
(2, '2025-12-19', '14:00:00', '16:00:00', TRUE),
(2, '2025-12-20', '09:00:00', '11:00:00', TRUE),

-- Michael Brown availabilities
(3, '2025-12-17', '10:00:00', '12:00:00', TRUE),
(3, '2025-12-17', '16:00:00', '18:00:00', TRUE),
(3, '2025-12-18', '10:00:00', '12:00:00', TRUE),
(3, '2025-12-19', '16:00:00', '18:00:00', TRUE),
(3, '2025-12-20', '10:00:00', '12:00:00', TRUE),

-- Emma Davis availabilities
(4, '2025-12-17', '09:00:00', '11:00:00', TRUE),
(4, '2025-12-18', '09:00:00', '11:00:00', TRUE),
(4, '2025-12-18', '14:00:00', '16:00:00', TRUE),
(4, '2025-12-19', '10:00:00', '12:00:00', TRUE),
(4, '2025-12-20', '14:00:00', '16:00:00', TRUE),

-- David Wilson availabilities
(5, '2025-12-17', '07:00:00', '09:00:00', TRUE),
(5, '2025-12-17', '18:00:00', '20:00:00', TRUE),
(5, '2025-12-18', '07:00:00', '09:00:00', TRUE),
(5, '2025-12-19', '18:00:00', '20:00:00', TRUE),
(5, '2025-12-20', '07:00:00', '09:00:00', TRUE);

-- Insert bookings
INSERT INTO bookings (athlete_id, coach_id, availability_id, booking_date, start_time, end_time, status) VALUES
-- Accepted bookings
(6, 1, 1, '2025-12-17', '09:00:00', '10:00:00', 'accepted'),
(7, 2, 6, '2025-12-17', '08:00:00', '09:00:00', 'accepted'),
(8, 3, 11, '2025-12-17', '10:00:00', '11:00:00', 'accepted'),

-- Pending bookings
(9, 4, 16, '2025-12-17', '09:00:00', '10:00:00', 'pending'),
(10, 5, 21, '2025-12-17', '07:00:00', '08:00:00', 'pending'),
(11, 1, 3, '2025-12-18', '10:00:00', '11:00:00', 'pending'),

-- Past accepted bookings (for reviews)
(6, 2, NULL, '2025-12-15', '08:00:00', '09:00:00', 'accepted'),
(7, 3, NULL, '2025-12-14', '16:00:00', '17:00:00', 'accepted'),
(8, 1, NULL, '2025-12-13', '14:00:00', '15:00:00', 'accepted'),
(9, 5, NULL, '2025-12-12', '18:00:00', '19:00:00', 'accepted'),

-- Cancelled booking
(12, 4, NULL, '2025-12-16', '14:00:00', '15:00:00', 'cancelled'),

-- Rejected booking
(11, 2, NULL, '2025-12-16', '11:00:00', '12:00:00', 'rejected');

-- Insert reviews (for completed bookings)
INSERT INTO reviews (booking_id, athlete_id, coach_id, rating, comment) VALUES
(7, 6, 2, 5, 'Excellent coach! Sarah really helped me improve my swimming technique. Very professional and encouraging.'),
(8, 7, 3, 5, 'Michael is an amazing coach. His boxing training is intense but very effective. Highly recommend!'),
(9, 8, 1, 4, 'Great football coaching session. John knows his stuff and provides excellent tactical advice.'),
(10, 9, 5, 5, 'David is the best strength coach I have ever worked with. His program transformed my performance!');