import romformat

import os
import sys
import json
import readline
import urllib2
import xml.etree.ElementTree as ET

def completer(text, state):
    global platforms
    options = [p for p in platforms if p.startswith(text)]
    try:
        return options[state]
    except IndexError:
        return None

def get_platforms():
    platforms = list()
    platformXml = urllib2.urlopen("http://thegamesdb.net/api/GetPlatformsList.php").read()
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

def main(folder):
    global platforms
    platforms = list()
    if folder == None:
        folder = os.getcwd()

    print("Downloading platform list...")
    #platforms = get_platforms()
    #print_platforms(platforms)

    inp = raw_input("> ")
    platform = None
    for p in platforms:
        if p.lower().startswith(inp.lower()):
            platform = p
    if not platform:
        print "No platform selected"
        #sys.exit(0)

    sys.stdout.write("Using platform %s. Enter extension mask: " % platform)
    sys.stdout.flush()
    mask = raw_input().lower()
    if not mask:
        sys.exit()
    if '.' in mask:
        mask = mask[mask.rindex('.') + 1 : ]

    print "Uploading game list..."
    directory = list()
    for f in os.listdir(folder):
        if f.lower().endswith(mask):
            file_record = {
                "file": f
            }
            props = romformat.Parse(f)
            if props:
                file_record["properties"] = props
            directory.append(file_record)

    game_list = {
        "site": "thegamesdb.org",
        "platform": platform,
        "directory": directory,
        "username": "testuser",
    }

    req = urllib2.Request("http://localhost/gameshacks/hoard", data=json.dumps(game_list), \
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
    

readline.set_completer(completer)
readline.parse_and_bind("tab: complete")
readline.set_completer_delims("|") # So that spaces aren't ignores

if __name__ == "__main__":
    main(sys.argv[1] if len(sys.argv) >= 2 else None)
