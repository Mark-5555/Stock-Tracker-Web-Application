CREATE TABLE time_series_data (
    id INT AUTO_INCREMENT PRIMARY KEY,
    stock_id INT NOT NULL,
    datetime DATETIME NOT NULL,
    open DECIMAL(10, 2),
    high DECIMAL(10, 2),
    low DECIMAL(10, 2),
    close DECIMAL(10, 2),
    volume BIGINT,
    FOREIGN KEY (stock_id) REFERENCES stocks(id)
);
