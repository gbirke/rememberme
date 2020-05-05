CREATE TABLE "tokens" (
	"credential"	TEXT NOT NULL,
	"token"	TEXT NOT NULL,
	"persistent_token"	INTEGER NOT NULL,
	"expires"	TEXT NOT NULL,
	PRIMARY KEY("credential","persistent_token","expires")
) WITHOUT ROWID