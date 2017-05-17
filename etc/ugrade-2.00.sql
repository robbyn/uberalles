
CREATE TABLE posts (
    id INT AUTO_INCREMENT PRIMARY KEY,

    pub_datetime DATETIME,
    title VARCHAR(255) NOT NULL,
    summary TEXT NOT NULL,
    content TEXT NOT NULL
) ENGINE=InnoDB;

CREATE INDEX posts_pub_datetime ON posts(pub_datetime);
