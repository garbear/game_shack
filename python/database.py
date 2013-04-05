# Unofficial Windows binaries for Python packages can be found here:
# http://www.lfd.uci.edu/~gohlke/pythonlibs/
import MySQLdb
import unittest

db_name = 'game_shack'

def CreateTables():
    global cur
    tables = getTables()
    if "properties" not in tables:
        cur.execute(
            "CREATE TABLE properties (" + \
                "id INTEGER AUTO_INCREMENT PRIMARY KEY, " + \
                "extension VARCHAR(7), " + \
                "code VARCHAR(16), " + \
                "title VARCHAR(32), " + \
                "publisher VARCHAR(32), " + \
                "gamefile_count INTEGER DEFAULT 0, " + \
                "game_file_count INTEGER DEFAULT 0, " + \
                #"INDEX idx_properties_code (code), " + \
                "CONSTRAINT idx_properties_cross " + \
                    "UNIQUE INDEX (code, title, publisher)" + \
                # Should the unique constraint also include extension?
            ")"
        )
    if "gamefiles" not in tables:
        cur.execute(
            "CREATE TABLE gamefiles (" + \
                "id INTEGER AUTO_INCREMENT PRIMARY KEY, " + \
                "filename VARCHAR(128), " + \
                "site VARCHAR(10), " + \
                "platform VARCHAR(40), " + \
                "property_id INTEGER, " + \
                "created DATETIME DEFAULT NULL, " + \
                "CONSTRAINT idx_gamefiles_cross " + \
                    "UNIQUE INDEX (filename, site, platform, property_id), " + \
                "CONSTRAINT FK_gamefiles_properties " + \
                    "FOREIGN KEY (property_id) " + \
                    "REFERENCES properties (id) " + \
                    "ON DELETE SET NULL" + \
            ")"
        )
    if "usernames" not in tables:
        cur.execute(
            "CREATE TABLE usernames (" + \
                "id INTEGER AUTO_INCREMENT PRIMARY KEY, " + \
                "username VARCHAR(32), " + \
                "hoarded INTEGER DEFAULT 0, " + \
                "resolved INTEGER DEFAULT 0, " + \
                "created DATETIME DEFAULT NULL, " + \
                "modified DATETIME DEFAULT NULL" + \
            ")"
        )
    if "gamefileslinkusernames" not in tables:
        cur.execute(
            "CREATE TABLE gamefileslinkusernames (" + \
                "gamefile_id INTEGER, " + \
                "username_id INTEGER, " + \
                "INDEX idx_gamefileslinkusernames_gamefile_id (gamefile_id), " + \
                "INDEX idx_gamefileslinkusernames_username_id (username_id), " + \
                "CONSTRAINT idx_gamefileslinkusernames_cross1 " + \
                    "UNIQUE INDEX (gamefile_id, username_id), " + \
                "CONSTRAINT idx_gamefileslinkusernames_cross2 " + \
                    "UNIQUE INDEX (username_id, gamefile_id), " + \
                "CONSTRAINT FK_gamefileslinkusernames_gamefiles " + \
                    "FOREIGN KEY (gamefile_id) " + \
                    "REFERENCES gamefiles (id) " + \
                    "ON DELETE SET NULL, " + \
                "CONSTRAINT FK_gamefileslinkusernames_usernames " + \
                    "FOREIGN KEY (username_id) " + \
                    "REFERENCES usernames (id) " + \
                    "ON DELETE SET NULL" + \
            ")"
        )

def getTables():
    global cur
    cur.execute(
        "SELECT table_name " + \
        "FROM information_schema.TABLES " + \
        "WHERE table_schema='%s'" % db_name
    )
    return [table[0] for table in cur.fetchall()]

def getIndices():
    """
    Untested
    """
    global cur
    indices = list()
    for table in getTables():
        # DISTINCT, because MySQL considers a UNIQUE INDEX across two columns
        # to be two indices, whereas SQLite only considers it a single one
        cur.execute(
            "SELECT DISTINCT index_name " + \
            "FROM information_schema.STATISTICS " + \
            "WHERE table_schema='%s' and table_name='%s'" % (db_name, table)
        )
        indices.extend(index[0] for index in cur.fetchall() if index[0] != "PRIMARY")
    return indices

def main():
    global db, cur
    db = MySQLdb.connect(host="localhost", user="root", passwd="xbmc", db=db_name)
    cur = db.cursor()
    CreateTables()


class TestDatabase(unittest.TestCase):
    def setUp(self):
        global db, cur
        if db == None:
            db = MySQLdb.connect(host="localhost", user="root", passwd="xbmc", db=db_name)
        if cur == None:
            cur = db.cursor()

    def test_get_tables(self):
        tables = getTables()
        self.assertEqual(len(tables), 4)
        self.assertTrue("gamefiles" in tables)
        self.assertTrue("properties" in tables)
        self.assertTrue("usernames" in tables)
        self.assertTrue("gamefileslinkusernames" in tables)

    def test_get_indices(self):
        indices = getIndices()
        self.assertEqual(len(indices), 7)
        self.assertTrue("idx_gamefiles_cross" in indices)
        self.assertTrue("FK_gamefiles_properties" in indices)
        self.assertTrue("idx_properties_cross" in indices)
        self.assertTrue("idx_gamefileslinkusernames_gamefile_id" in indices)
        self.assertTrue("idx_gamefileslinkusernames_username_id" in indices)
        self.assertTrue("idx_gamefileslinkusernames_cross1" in indices)
        self.assertTrue("idx_gamefileslinkusernames_cross2" in indices)


db = None
cur = None

if __name__ == "__main__":
    main()
    unittest.main()