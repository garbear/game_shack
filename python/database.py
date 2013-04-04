# Unofficial Windows binaries for Python packages can be found here:
# http://www.lfd.uci.edu/~gohlke/pythonlibs/
import MySQLdb
import unittest

db_name = 'game_shack'

def CreateTables():
    global cur
    cur.execute(
        "CREATE TABLE gamefiles (" + \
            "id INTEGER UNSIGNED AUTO_INCREMENT PRIMARY KEY, " + \
            "filename VARCHAR(128), " + \
            "site VARCHAR(10), " + \
            "platform VARCHAR(40), " + \
            "idProperty INTEGER, " + \
            "created DATETIME DEFAULT NULL" + \
        ")"
    )
    cur.execute(
        "CREATE TABLE properties (" + \
            "id INTEGER UNSIGNED AUTO_INCREMENT PRIMARY KEY, " + \
            "code VARCHAR(16), " + \
            "title VARCHAR(32), " + \
            "publisher VARCHAR(32)" + \
        ")"
    )
    cur.execute(
        "CREATE TABLE usernames (" + \
            "id INTEGER UNSIGNED AUTO_INCREMENT PRIMARY KEY, " + \
            "username VARCHAR(32)" + \
        ")"
    )
    cur.execute(
        "CREATE TABLE gamefiles_link_usernames (" + \
            "id INTEGER UNSIGNED AUTO_INCREMENT PRIMARY KEY, " + \
            "username VARCHAR(32)" + \
        ")"
    )


def getTables():
    """
    Untested
    """
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
    if len(getTables()) == 0:
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
        self.assertTrue("gamefiles_link_usernames" in tables)

    def test_get_indices(self):
        indices = getIndices()
        self.assertEqual(len(indices), 0)


db = None
cur = None

if __name__ == "__main__":
    main()
    unittest.main()