CREATE TABLE IF NOT EXISTS Tracker (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    stock_id INT NOT NULL,
    created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_tracker_user FOREIGN KEY(user_id) REFERENCES Users(id) ON DELETE CASCADE,
    CONSTRAINT fk_tracker_stock FOREIGN KEY(stock_id) REFERENCES stocks(id) ON DELETE CASCADE,
    CONSTRAINT uc_user_stock UNIQUE(user_id, stock_id)
);