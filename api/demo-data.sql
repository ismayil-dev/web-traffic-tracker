-- Demo Data for Traffic Tracker
-- This script creates realistic demo data spanning 30 days with various browsers, devices, and visitor patterns

-- Clean existing data (optional - uncomment if needed)
-- DELETE FROM visits WHERE domain_id = 1;
-- DELETE FROM unique_visitors WHERE domain_id = 1;
-- DELETE FROM daily_stats WHERE domain_id = 1;

-- Demo data variables (30 days of data)
SET @domain_id = 1;
SET @start_date = DATE_SUB(CURDATE(), INTERVAL 30 DAY);

-- Create realistic demo visitors with different browsers, OS, and devices
INSERT INTO unique_visitors (domain_id, visitor_hash, device, os, browser, first_visit, last_visit, total_visits) VALUES
-- Desktop Chrome users (most common)
(@domain_id, SHA2('user1_chrome_desktop', 256), 'desktop', 'windows', 'chrome', DATE_SUB(NOW(), INTERVAL 28 DAY), DATE_SUB(NOW(), INTERVAL 1 DAY), 15),
(@domain_id, SHA2('user2_chrome_desktop', 256), 'desktop', 'windows', 'chrome', DATE_SUB(NOW(), INTERVAL 25 DAY), DATE_SUB(NOW(), INTERVAL 2 DAY), 8),
(@domain_id, SHA2('user3_chrome_desktop', 256), 'desktop', 'macos', 'chrome', DATE_SUB(NOW(), INTERVAL 22 DAY), NOW(), 12),
(@domain_id, SHA2('user4_chrome_desktop', 256), 'desktop', 'windows', 'chrome', DATE_SUB(NOW(), INTERVAL 20 DAY), DATE_SUB(NOW(), INTERVAL 3 DAY), 6),
(@domain_id, SHA2('user5_chrome_desktop', 256), 'desktop', 'macos', 'chrome', DATE_SUB(NOW(), INTERVAL 18 DAY), DATE_SUB(NOW(), INTERVAL 1 DAY), 9),

-- Firefox users
(@domain_id, SHA2('user6_firefox_desktop', 256), 'desktop', 'windows', 'firefox', DATE_SUB(NOW(), INTERVAL 26 DAY), DATE_SUB(NOW(), INTERVAL 4 DAY), 7),
(@domain_id, SHA2('user7_firefox_desktop', 256), 'desktop', 'linux', 'firefox', DATE_SUB(NOW(), INTERVAL 24 DAY), DATE_SUB(NOW(), INTERVAL 2 DAY), 5),
(@domain_id, SHA2('user8_firefox_desktop', 256), 'desktop', 'macos', 'firefox', DATE_SUB(NOW(), INTERVAL 15 DAY), NOW(), 4),

-- Safari users (Mac/iOS)
(@domain_id, SHA2('user9_safari_desktop', 256), 'desktop', 'macos', 'safari', DATE_SUB(NOW(), INTERVAL 19 DAY), DATE_SUB(NOW(), INTERVAL 1 DAY), 6),
(@domain_id, SHA2('user10_safari_mobile', 256), 'iphone', 'ios', 'safari', DATE_SUB(NOW(), INTERVAL 17 DAY), NOW(), 8),
(@domain_id, SHA2('user11_safari_mobile', 256), 'ipad', 'ios', 'safari', DATE_SUB(NOW(), INTERVAL 14 DAY), DATE_SUB(NOW(), INTERVAL 3 DAY), 3),

-- Mobile Chrome users
(@domain_id, SHA2('user12_chrome_mobile', 256), 'android_phone', 'android', 'chrome', DATE_SUB(NOW(), INTERVAL 21 DAY), DATE_SUB(NOW(), INTERVAL 1 DAY), 11),
(@domain_id, SHA2('user13_chrome_mobile', 256), 'android_phone', 'android', 'chrome', DATE_SUB(NOW(), INTERVAL 16 DAY), DATE_SUB(NOW(), INTERVAL 2 DAY), 7),
(@domain_id, SHA2('user14_chrome_mobile', 256), 'android_phone', 'android', 'chrome', DATE_SUB(NOW(), INTERVAL 12 DAY), NOW(), 5),

-- Edge users
(@domain_id, SHA2('user15_edge_desktop', 256), 'desktop', 'windows', 'microsoft_edge', DATE_SUB(NOW(), INTERVAL 23 DAY), DATE_SUB(NOW(), INTERVAL 5 DAY), 4),
(@domain_id, SHA2('user16_edge_desktop', 256), 'desktop', 'windows', 'microsoft_edge', DATE_SUB(NOW(), INTERVAL 13 DAY), DATE_SUB(NOW(), INTERVAL 1 DAY), 6),

-- One-time visitors (bounce scenarios)
(@domain_id, SHA2('user17_chrome_bounce', 256), 'desktop', 'windows', 'chrome', DATE_SUB(NOW(), INTERVAL 10 DAY), DATE_SUB(NOW(), INTERVAL 10 DAY), 1),
(@domain_id, SHA2('user18_firefox_bounce', 256), 'desktop', 'macos', 'firefox', DATE_SUB(NOW(), INTERVAL 8 DAY), DATE_SUB(NOW(), INTERVAL 8 DAY), 1),
(@domain_id, SHA2('user19_safari_bounce', 256), 'iphone', 'ios', 'safari', DATE_SUB(NOW(), INTERVAL 6 DAY), DATE_SUB(NOW(), INTERVAL 6 DAY), 1),
(@domain_id, SHA2('user20_chrome_bounce', 256), 'android_phone', 'android', 'chrome', DATE_SUB(NOW(), INTERVAL 4 DAY), DATE_SUB(NOW(), INTERVAL 4 DAY), 1);

-- Create diverse page visits across different time periods
INSERT INTO visits (domain_id, url, base_url, page_title, visitor_ip, user_agent, browser, os, device, visitor_hash, referrer, timestamp) VALUES

-- Recent activity (last 7 days) - high volume
(@domain_id, 'http://localhost:8080/', 'localhost/', 'Home Page', '192.168.1.100', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36', 'chrome', 'windows', 'desktop', SHA2('user1_chrome_desktop', 256), 'https://google.com/search?q=example', NOW()),
(@domain_id, 'http://localhost:8080/about', 'localhost/about', 'About Us', '192.168.1.100', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36', 'chrome', 'windows', 'desktop', SHA2('user1_chrome_desktop', 256), 'http://localhost:8080/', DATE_SUB(NOW(), INTERVAL 1 HOUR)),
(@domain_id, 'http://localhost:8080/products', 'localhost/products', 'Our Products', '192.168.1.101', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36', 'chrome', 'macos', 'desktop', SHA2('user3_chrome_desktop', 256), 'https://google.com/search?q=products', DATE_SUB(NOW(), INTERVAL 2 HOUR)),
(@domain_id, 'http://localhost:8080/', 'localhost/', 'Home Page', '192.168.1.102', 'Mozilla/5.0 (iPhone; CPU iPhone OS 14_6 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/14.1.1 Mobile/15E148 Safari/604.1', 'safari', 'ios', 'iphone', SHA2('user10_safari_mobile', 256), 'https://twitter.com', DATE_SUB(NOW(), INTERVAL 3 HOUR)),
(@domain_id, 'http://localhost:8080/contact', 'localhost/contact', 'Contact Us', '192.168.1.103', 'Mozilla/5.0 (X11; Linux x86_64; rv:89.0) Gecko/20100101 Firefox/89.0', 'firefox', 'linux', 'desktop', SHA2('user7_firefox_desktop', 256), 'http://localhost:8080/about', DATE_SUB(NOW(), INTERVAL 4 HOUR)),

-- Medium activity (8-14 days ago)
(@domain_id, 'http://localhost:8080/', 'localhost/', 'Home Page', '192.168.1.104', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36', 'chrome', 'windows', 'desktop', SHA2('user2_chrome_desktop', 256), 'https://google.com/search?q=example', DATE_SUB(NOW(), INTERVAL 8 DAY)),
(@domain_id, 'http://localhost:8080/blog', 'localhost/blog', 'Blog', '192.168.1.104', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36', 'chrome', 'windows', 'desktop', SHA2('user2_chrome_desktop', 256), 'http://localhost:8080/', DATE_SUB(NOW(), INTERVAL 8 DAY)),
(@domain_id, 'http://localhost:8080/products/widget-a', 'localhost/products/widget-a', 'Widget A', '192.168.1.105', 'Mozilla/5.0 (Linux; Android 11; SM-G991B) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.120 Mobile Safari/537.36', 'chrome', 'android', 'android_phone', SHA2('user12_chrome_mobile', 256), 'https://google.com/search?q=widget', DATE_SUB(NOW(), INTERVAL 10 DAY)),
(@domain_id, 'http://localhost:8080/support', 'localhost/support', 'Support', '192.168.1.106', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/14.1.1 Safari/605.1.15', 'safari', 'macos', 'desktop', SHA2('user9_safari_desktop', 256), 'http://localhost:8080/contact', DATE_SUB(NOW(), INTERVAL 12 DAY)),

-- Older activity (15-30 days ago)
(@domain_id, 'http://localhost:8080/', 'localhost/', 'Home Page', '192.168.1.107', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:89.0) Gecko/20100101 Firefox/89.0', 'firefox', 'windows', 'desktop', SHA2('user6_firefox_desktop', 256), 'https://bing.com/search?q=example', DATE_SUB(NOW(), INTERVAL 20 DAY)),
(@domain_id, 'http://localhost:8080/about', 'localhost/about', 'About Us', '192.168.1.108', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36 Edg/91.0.864.59', 'microsoft_edge', 'windows', 'desktop', SHA2('user15_edge_desktop', 256), 'http://localhost:8080/', DATE_SUB(NOW(), INTERVAL 23 DAY)),
(@domain_id, 'http://localhost:8080/pricing', 'localhost/pricing', 'Pricing', '192.168.1.109', 'Mozilla/5.0 (iPad; CPU OS 14_6 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/14.1.1 Mobile/15E148 Safari/604.1', 'safari', 'ios', 'ipad', SHA2('user11_safari_mobile', 256), 'https://google.com/search?q=pricing', DATE_SUB(NOW(), INTERVAL 25 DAY)),

-- Add more visits for better distribution
(@domain_id, 'http://localhost:8080/blog/post-1', 'localhost/blog/post-1', 'How to Get Started', '192.168.1.110', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36', 'chrome', 'windows', 'desktop', SHA2('user4_chrome_desktop', 256), 'https://google.com/search?q=how+to+get+started', DATE_SUB(NOW(), INTERVAL 5 DAY)),
(@domain_id, 'http://localhost:8080/blog/post-2', 'localhost/blog/post-2', 'Best Practices Guide', '192.168.1.111', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36', 'chrome', 'macos', 'desktop', SHA2('user5_chrome_desktop', 256), 'http://localhost:8080/blog', DATE_SUB(NOW(), INTERVAL 7 DAY)),
(@domain_id, 'http://localhost:8080/products/widget-b', 'localhost/products/widget-b', 'Widget B', '192.168.1.112', 'Mozilla/5.0 (Linux; Android 11; Pixel 5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.120 Mobile Safari/537.36', 'chrome', 'android', 'android_phone', SHA2('user13_chrome_mobile', 256), 'http://localhost:8080/products', DATE_SUB(NOW(), INTERVAL 9 DAY)),
(@domain_id, 'http://localhost:8080/faq', 'localhost/faq', 'Frequently Asked Questions', '192.168.1.113', 'Mozilla/5.0 (iPhone; CPU iPhone OS 14_6 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/14.1.1 Mobile/15E148 Safari/604.1', 'safari', 'ios', 'iphone', SHA2('user10_safari_mobile', 256), 'http://localhost:8080/support', DATE_SUB(NOW(), INTERVAL 11 DAY)),

-- Some bounce visits (single page visits)
(@domain_id, 'http://localhost:8080/', 'localhost/', 'Home Page', '192.168.1.114', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36', 'chrome', 'windows', 'desktop', SHA2('user17_chrome_bounce', 256), 'https://google.com/search?q=example', DATE_SUB(NOW(), INTERVAL 10 DAY)),
(@domain_id, 'http://localhost:8080/products', 'localhost/products', 'Our Products', '192.168.1.115', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/14.1.1 Safari/605.1.15', 'safari', 'macos', 'desktop', SHA2('user18_firefox_bounce', 256), 'https://google.com/search?q=products', DATE_SUB(NOW(), INTERVAL 8 DAY)),
(@domain_id, 'http://localhost:8080/contact', 'localhost/contact', 'Contact Us', '192.168.1.116', 'Mozilla/5.0 (iPhone; CPU iPhone OS 14_6 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/14.1.1 Mobile/15E148 Safari/604.1', 'safari', 'ios', 'iphone', SHA2('user19_safari_bounce', 256), 'https://twitter.com', DATE_SUB(NOW(), INTERVAL 6 DAY)),
(@domain_id, 'http://localhost:8080/pricing', 'localhost/pricing', 'Pricing', '192.168.1.117', 'Mozilla/5.0 (Linux; Android 11; SM-G991B) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.120 Mobile Safari/537.36', 'chrome', 'android', 'android_phone', SHA2('user20_chrome_bounce', 256), 'https://google.com/search?q=pricing', DATE_SUB(NOW(), INTERVAL 4 DAY));

-- Generate daily stats (this would normally be done by your analytics aggregation job)
INSERT INTO daily_stats (domain_id, date, unique_visitors, total_visits, unique_pages) VALUES
(@domain_id, DATE_SUB(CURDATE(), INTERVAL 1 DAY), 8, 15, 6),
(@domain_id, DATE_SUB(CURDATE(), INTERVAL 2 DAY), 6, 12, 5),
(@domain_id, DATE_SUB(CURDATE(), INTERVAL 3 DAY), 7, 14, 7),
(@domain_id, DATE_SUB(CURDATE(), INTERVAL 4 DAY), 4, 8, 4),
(@domain_id, DATE_SUB(CURDATE(), INTERVAL 5 DAY), 5, 11, 5),
(@domain_id, DATE_SUB(CURDATE(), INTERVAL 6 DAY), 3, 6, 3),
(@domain_id, DATE_SUB(CURDATE(), INTERVAL 7 DAY), 9, 18, 8),
(@domain_id, DATE_SUB(CURDATE(), INTERVAL 8 DAY), 5, 10, 4),
(@domain_id, DATE_SUB(CURDATE(), INTERVAL 9 DAY), 6, 13, 6),
(@domain_id, DATE_SUB(CURDATE(), INTERVAL 10 DAY), 4, 9, 4),
(@domain_id, DATE_SUB(CURDATE(), INTERVAL 11 DAY), 3, 7, 3),
(@domain_id, DATE_SUB(CURDATE(), INTERVAL 12 DAY), 7, 15, 7),
(@domain_id, DATE_SUB(CURDATE(), INTERVAL 13 DAY), 5, 11, 5),
(@domain_id, DATE_SUB(CURDATE(), INTERVAL 14 DAY), 4, 8, 4),
(@domain_id, DATE_SUB(CURDATE(), INTERVAL 15 DAY), 6, 14, 6),
(@domain_id, DATE_SUB(CURDATE(), INTERVAL 16 DAY), 3, 6, 3),
(@domain_id, DATE_SUB(CURDATE(), INTERVAL 17 DAY), 8, 16, 7),
(@domain_id, DATE_SUB(CURDATE(), INTERVAL 18 DAY), 5, 12, 5),
(@domain_id, DATE_SUB(CURDATE(), INTERVAL 19 DAY), 4, 9, 4),
(@domain_id, DATE_SUB(CURDATE(), INTERVAL 20 DAY), 6, 13, 6),
(@domain_id, DATE_SUB(CURDATE(), INTERVAL 21 DAY), 7, 15, 7),
(@domain_id, DATE_SUB(CURDATE(), INTERVAL 22 DAY), 3, 7, 3),
(@domain_id, DATE_SUB(CURDATE(), INTERVAL 23 DAY), 5, 11, 5),
(@domain_id, DATE_SUB(CURDATE(), INTERVAL 24 DAY), 4, 8, 4),
(@domain_id, DATE_SUB(CURDATE(), INTERVAL 25 DAY), 6, 14, 6),
(@domain_id, DATE_SUB(CURDATE(), INTERVAL 26 DAY), 5, 10, 5),
(@domain_id, DATE_SUB(CURDATE(), INTERVAL 27 DAY), 3, 6, 3),
(@domain_id, DATE_SUB(CURDATE(), INTERVAL 28 DAY), 7, 16, 7),
(@domain_id, DATE_SUB(CURDATE(), INTERVAL 29 DAY), 4, 9, 4),
(@domain_id, DATE_SUB(CURDATE(), INTERVAL 30 DAY), 5, 12, 5);

-- Verification queries (uncomment to check the data)
-- SELECT 'Total Unique Visitors:' AS metric, COUNT(*) AS count FROM unique_visitors WHERE domain_id = @domain_id;
-- SELECT 'Total Visits:' AS metric, COUNT(*) AS count FROM visits WHERE domain_id = @domain_id;
-- SELECT 'Browser Distribution:' AS metric, browser, COUNT(*) AS count FROM unique_visitors WHERE domain_id = @domain_id GROUP BY browser ORDER BY count DESC;
-- SELECT 'Device Distribution:' AS metric, device, COUNT(*) AS count FROM unique_visitors WHERE domain_id = @domain_id GROUP BY device ORDER BY count DESC;
-- SELECT 'OS Distribution:' AS metric, os, COUNT(*) AS count FROM unique_visitors WHERE domain_id = @domain_id GROUP BY os ORDER BY count DESC;
-- SELECT 'Top Pages:' AS metric, base_url, COUNT(*) AS visits FROM visits WHERE domain_id = @domain_id GROUP BY base_url ORDER BY visits DESC LIMIT 10;

SELECT 'Demo data inserted successfully!' AS status;