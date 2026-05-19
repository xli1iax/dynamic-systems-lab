CREATE TABLE test_users (
                            id INT AUTO_INCREMENT PRIMARY KEY,
                            name VARCHAR(100) NOT NULL
);


CREATE TABLE IF NOT EXISTS logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    source VARCHAR(50) NOT NULL,
    result LONGTEXT NULL,
    error_message LONGTEXT NULL,
    command LONGTEXT NOT NULL,
    success BOOLEAN NOT NULL,
    ip_address VARCHAR(45) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS animation_usage (
                                               id INT AUTO_INCREMENT PRIMARY KEY,
                                               animation_name VARCHAR(100) NOT NULL,
    user_token VARCHAR(64) NOT NULL,
    ip_address VARCHAR(45) NULL,
    city VARCHAR(100) NULL,
    country VARCHAR(100) NULL,
    used_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );