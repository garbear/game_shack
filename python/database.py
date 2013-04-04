# Unofficial Windows binaries for Python packages can be found here:
# http://www.lfd.uci.edu/~gohlke/pythonlibs/
import MySQLdb

db_name = 'game_shack'

def CreateTables():
    pass

def GetTables():
    """
    Untested
    """
    global cur
    cur.execute(
        "SELECT table_name " +
        "FROM information_schema.TABLES " +
        "WHERE table_schema='%s'" % db_name
    )
    return cur.fetchall()

def GetIndices():
    """
    Untested
    """
    global cur
    indices = list()
    for table in GetTables():
        # DISTINCT, because MySQL considers a UNIQUE INDEX across two columns
        # to be two indices, whereas SQLite only considers it a single one
        cur.execute(
            "SELECT DISTINCT index_name " +
            "FROM information_schema.STATISTICS " +
            "WHERE table_schema='%s' and table_name='%s'" % (db_name, table)
        )
        indices.extend(index for index in cur.fetchall() if index != "PRIMARY")
    return indices

def main():
    global db, cur
    db = MySQLdb.connect(host="localhost", user="root", passwd="xbmc", db=db_name)
    cur = db.cursor()

    CreateTables()

db = None
cur = None

if __name__ == "__main__":
    main()