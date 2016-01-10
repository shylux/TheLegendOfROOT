CREATE TABLE "users" 
("name" TEXT NOT NULL ,
"email" TEXT NOT NULL ,
"pass_hash" TEXT NOT NULL ,
"class" INTEGER NOT NULL ,
"att" INTEGER DEFAULT (0) ,
"def" INTEGER DEFAULT (0) ,
"hp" INTEGER DEFAULT (0) ,
"json_data" BLOB NOT NULL ,
"buddy_id" INTEGER PRIMARY KEY NOT NULL
);

CREATE TABLE games (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  name TEXT NOT NULL,
  username TEXT NOT NULL,
  json_data BLOB NOT NULL,
  FOREIGN KEY(username) REFERENCES users(name)
);
