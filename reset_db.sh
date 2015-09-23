rm root.db
sqlite3 root.db < sql/init.sql
sqlite3 root.db < sql/test_data.sql
