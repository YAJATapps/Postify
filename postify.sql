USE mysql;

CREATE TABLE IF NOT EXISTS ProjectPosts (
    Id INT AUTO_INCREMENT PRIMARY KEY,
    Username VARCHAR(255) NOT NULL,
    Title VARCHAR(512) NOT NULL,
    Description VARCHAR(15000) NOT NULL,
    Time TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS ProjectUsers (
    Id INT AUTO_INCREMENT PRIMARY KEY,
    Username VARCHAR(255) NOT NULL UNIQUE,
    Password VARCHAR(255) NOT NULL,
    Time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    Email VARCHAR(100) NOT NULL,
    Status VARCHAR(255) NOT NULL
);

CREATE TABLE IF NOT EXISTS ProjectComments (
    Id INT AUTO_INCREMENT PRIMARY KEY,
    Username VARCHAR(255) NOT NULL,
    Comment VARCHAR(2048) NOT NULL,
    PostId INT NOT NULL
);

CREATE TABLE IF NOT EXISTS ProjectLikes (
    Id INT AUTO_INCREMENT PRIMARY KEY,
    Username VARCHAR(255) NOT NULL,
    PostId INT NOT NULL
);

CREATE TABLE IF NOT EXISTS ProjectBlockList (
    Id INT AUTO_INCREMENT PRIMARY KEY,
    Username VARCHAR(255) NOT NULL,
    Blockname VARCHAR(255) NOT NULL
);
