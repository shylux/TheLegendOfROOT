rm root.db.sqlite
sqlite3 root.db.sqlite < sql/init.sql
sqlite3 root.db.sqlite < sql/test_data.sql
