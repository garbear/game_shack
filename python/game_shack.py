import romformat
import tempfile
import unittest

import os
import sys
import json
import readline
import urllib2
import xml.etree.ElementTree as ET

def completer(text, state):
    global autocompletions
    options = [op for op in autocompletions if op.startswith(text)]
    try:
        return options[state]
    except IndexError:
        return None

def getPlatforms():
    platforms = list()
    cachedURI = os.path.join(tempfile.gettempdir(), "GetPlatformsList.xml")
    if os.path.exists(cachedURI):
        platformXml = open(cachedURI).read()
    else:
        platformXml = urllib2.urlopen("http://thegamesdb.net/api/GetPlatformsList.php").read()
        open(cachedURI, "w").write(platformXml)
    root = ET.fromstring(platformXml)
    for platform in root.findall("Platforms/Platform"):
        nametag = platform.find("name")
        if nametag != None and nametag.text != None:
            platforms.append(nametag.text)
    return platforms

def print_platforms(platforms):
    # Print platforms in two columns
    for i in range(len(platforms)):
        if i % 2 == 1:
            continue
        if i + 1 == len(platforms):
            sys.stdout.write("| %s%s  | %s  " % (platforms[i], ' ' * (35 - len(platforms[i])), ' ' * 37))
        else:
            sys.stdout.write("| %s%s  | %s%s" % (platforms[i], ' ' * (35 - len(platforms[i])), \
                                                 platforms[i + 1], ' ' * (39 - len(platforms[i + 1]))))

def getGameList(folder, mask):
    directory = list()
    if '.' in mask:
        mask = mask[mask.rindex('.') + 1 : ]
    for f in os.listdir(folder):
        if f.lower().endswith(mask):
            file_record = {
                "filename": f
            }
            props = romformat.Parse(os.path.join(folder, f))
            if props:
                file_record["properties"] = props
            directory.append(file_record)
    return directory

def main(folder=None):
    global autocompletions
    autocompletions = list()
    if folder == None:
        folder = os.getcwd()

    print("Downloading platform list...")
    autocompletions = platforms = getPlatforms()
    print_platforms(platforms)

    inp = raw_input("> ")
    platform = None
    for p in platforms:
        if p.lower().startswith(inp.lower()):
            platform = p
            break
    if not platform:
        print "No platform selected"
        #sys.exit(0)

    extensions = list()
    skip = ["py", "pyc", "pyo"]
    for f in os.listdir(folder):
        # Look for extension of at most 8 characters
        if '.' in f[len(f) - 8 : ] and f[len(f) - 1] != '.':
            ext = f[f.rindex('.') + 1 : ]
            if ext not in skip and ext not in extensions:
                extensions.append(ext)

    print "Using platform %s. Enter extension mask (options are %s):" % (platform,
              ', '.join(sorted(extensions)))
    autocompletions = extensions
    mask = raw_input("> ").lower()
    if not mask:
        sys.exit()

    print "Uploading game list..."

    game_list = {
        "site": "thegamesdb.org",
        "platform": platform,
        "directory": getGameList(folder, mask),
        "username": "testuser",
    }

    req = urllib2.Request("http://localhost/gamefiles/hoard", data=json.dumps(game_list), \
      headers={"Content-type": "application/json"})
    resp = urllib2.urlopen(req).read()

    try:
        result = json.loads(resp)
        if "result" in result:
            print "Success. Go to http://localhost/ to get started!"
        else:
            if "error" in result:
                print "Could not upload game list: %s" % result["error"]["message"]
            else:
                print "Could not upload game list: unknown error"
    except:
        print "Could not upload game list: error parsing response:"
        print resp


class TestGameShack(unittest.TestCase):
    def setUp(self):
        pass

    def test_get_platforms(self):
        platforms = getPlatforms()
        # Assume TheGamesDB doesn't drop any games in the future
        self.assertGreaterEqual(len(platforms), 49)
        cachedURI = os.path.join(tempfile.gettempdir(), "GetPlatformsList.xml")
        self.assertTrue(os.path.exists(cachedURI))

    def test_get_game_list(self):
        folder = os.path.join(os.getcwd(), "test")
        directory = getGameList(folder, ".gb")
        self.assertEqual(len(directory), 1)
        self.assertEqual(directory[0]["filename"], "Tetris.gb")
        self.assertEqual(directory[0]["properties"]["code"], "TETRIS")
        self.assertEqual(directory[0]["properties"]["publisher"], "01")


readline.set_completer(completer)
readline.parse_and_bind("tab: complete")
readline.set_completer_delims("|") # So that spaces aren't ignores

if __name__ == "__main__":
    main(sys.argv[1] if len(sys.argv) >= 2 else None)
    #unittest.main()