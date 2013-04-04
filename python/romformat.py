import unittest

gameboy_publishers = {
    "01": "Nintendo",
    "02": "Rocket Games",
    "08": "Capcom",
    "09": "Hot B Co.",
    "0A": "Jaleco",
    "0B": "Coconuts Japan",
    "0C": "Coconuts Japan/G.X.Media",
    "0H": "Starfish",
    "0L": "Warashi Inc.",
    "0N": "Nowpro",
    "0P": "Game Village",
    "13": "Electronic Arts Japan",
    "18": "Hudson Soft Japan",
    "19": "S.C.P.",
    "1A": "Yonoman",
    "1G": "SMDE",
    "1P": "Creatures Inc.",
    "1Q": "TDK Deep Impresion",
    "20": "Destination Software",
    "22": "VR 1 Japan",
    "25": "San-X",
    "28": "Kemco Japan",
    "29": "Seta",
    "2H": "Ubisoft Japan",
    "2K": "NEC InterChannel",
    "2L": "Tam",
    "2M": "Jordan",
    "2N": "Smilesoft",
    "2Q": "Mediakite",
    "36": "Codemasters",
    "37": "GAGA Communications",
    "38": "Laguna",
    "39": "Telstar Fun and Games",
    "41": "Ubi Soft Entertainment",
    "42": "Sunsoft",
    "47": "Spectrum Holobyte",
    "49": "IREM",
    "4D": "Malibu Games",
    "4F": "Eidos/U.S. Gold",
    "4J": "Fox Interactive",
    "4K": "Time Warner Interactive",
    "4Q": "Disney",
    "4S": "Black Pearl",
    "4X": "GT Interactive",
    "4Y": "RARE",
    "4Z": "Crave Entertainment",
    "50": "Absolute Entertainment",
    "51": "Acclaim",
    "52": "Activision",
    "53": "American Sammy Corp.",
    "54": "Take 2 Interactive",
    "55": "Hi Tech",
    "56": "LJN LTD.",
    "58": "Mattel",
    "5A": "Mindscape/Red Orb Ent.",
    "5C": "Taxan",
    "5D": "Midway",
    "5F": "American Softworks",
    "5G": "Majesco Sales Inc",
    "5H": "3DO",
    "5K": "Hasbro",
    "5L": "NewKidCo",
    "5M": "Telegames",
    "5N": "Metro3D",
    "5P": "Vatical Entertainment",
    "5Q": "LEGO Media",
    "5S": "Xicat Interactive",
    "5T": "Cryo Interactive",
    "5W": "Red Storm Ent./BKN Ent.",
    "5X": "Microids",
    "5Z": "Conspiracy Entertainment Corp.",
    "60": "Titus Interactive Studios",
    "61": "Virgin Interactive",
    "62": "Maxis",
    "64": "LucasArts Entertainment",
    "67": "Ocean",
    "69": "Electronic Arts",
    "6E": "Elite Systems Ltd.",
    "6F": "Electro Brain",
    "6G": "The Learning Company",
    "6H": "BBC",
    "6J": "Software 2000",
    "6L": "BAM! Entertainment",
    "6M": "Studio 3",
    "6Q": "Classified Games",
    "6S": "TDK Mediactive",
    "6U": "DreamCatcher",
    "6V": "JoWood Productions",
    "6W": "SEGA",
    "6X": "Wannado Edition",
    "6Y": "LSP",
    "6Z": "ITE Media",
    "70": "Infogrames",
    "71": "Interplay",
    "72": "JVC Musical Industries Inc",
    "73": "Parker Brothers",
    "75": "SCI",
    "78": "THQ",
    "79": "Accolade",
    "7A": "Triffix Ent. Inc.",
    "7C": "Microprose Software",
    "7D": "Universal Interactive Studios",
    "7F": "Kemco",
    "7G": "Rage Software",
    "7H": "Encore",
    "7J": "Zoo",
    "7K": "BVM",
    "7L": "Simon & Schuster Interactive",
    "7M": "Asmik Ace Entertainment Inc./AIA",
    "7N": "Empire Interactive",
    "7Q": "Jester Interactive",
    "7T": "Scholastic",
    "7U": "Ignition Entertainment",
    "7W": "Stadlbauer",
    "80": "Misawa",
    "83": "LOZC",
    "8B": "Bulletproof Software",
    "8C": "Vic Tokai Inc.",
    "8J": "General Entertainment",
    "8N": "Success",
    "8P": "SEGA Japan",
    "91": "Chun Soft",
    "92": "Video System",
    "93": "BEC",
    "96": "Yonezawa/S'pal",
    "97": "Kaneko",
    "99": "Victor Interactive Software",
    "9A": "Nichibutsu/Nihon Bussan",
    "9B": "Tecmo",
    "9C": "Imagineer",
    "9F": "Nova",
    "9H": "Bottom Up",
    "9L": "Hasbro Japan",
    "9N": "Marvelous Entertainment",
    "9P": "Keynet Inc.",
    "9Q": "Hands-On Entertainment",
    "A0": "Telenet",
    "A1": "Hori",
    "A4": "Konami",
    "A6": "Kawada",
    "A7": "Takara",
    "A9": "Technos Japan Corp.",
    "AA": "JVC",
    "AC": "Toei Animation",
    "AD": "Toho",
    "AF": "Namco",
    "AG": "Media Rings Corporation",
    "AH": "J-Wing",
    "AK": "KID",
    "AL": "MediaFactory",
    "AP": "Infogrames Hudson",
    "AQ": "Kiratto. Ludic Inc",
    "B0": "Acclaim Japan",
    "B1": "ASCII",
    "B2": "Bandai",
    "B4": "Enix",
    "B6": "HAL Laboratory",
    "B7": "SNK",
    "B9": "Pony Canyon Hanbai",
    "BA": "Culture Brain",
    "BB": "Sunsoft",
    "BD": "Sony Imagesoft",
    "BF": "Sammy",
    "BG": "Magical",
    "BJ": "Compile",
    "BL": "MTO Inc.",
    "BN": "Sunrise Interactive",
    "BP": "Global A Entertainment",
    "BQ": "Fuuki",
    "C0": "Taito",
    "C2": "Kemco",
    "C3": "Square Soft",
    "C5": "Data East",
    "C6": "Tonkin House",
    "C8": "Koei",
    "CA": "Konami/Palcom/Ultra",
    "CB": "Vapinc/NTVIC",
    "CC": "Use Co.,Ltd.",
    "CD": "Meldac",
    "CE": "FCI/Pony Canyon",
    "CF": "Angel",
    "CM": "Konami Computer Entertainment Osaka",
    "CP": "Enterbrain",
    "D1": "Sofel",
    "D2": "Quest",
    "D3": "Sigma Enterprises",
    "D4": "Ask Kodansa",
    "D6": "Naxat",
    "D7": "Copya System",
    "D9": "Banpresto",
    "DA": "TOMY",
    "DB": "LJN Japan",
    "DD": "NCS",
    "DF": "Altron Corporation",
    "DH": "Gaps Inc.",
    "DN": "ELF",
    "E2": "Yutaka",
    "E3": "Varie",
    "E5": "Epoch",
    "E7": "Athena",
    "E8": "Asmik Ace Entertainment Inc.",
    "E9": "Natsume",
    "EA": "King Records",
    "EB": "Atlus",
    "EC": "Epic/Sony Records",
    "EE": "IGS",
    "EL": "Spike",
    "EM": "Konami Computer Entertainment Tokyo",
    "EN": "Alphadream Corporation",
    "F0": "A Wave",
    "G1": "PCCW",
    "G4": "KiKi Co Ltd",
    "G5": "Open Sesame Inc.",
    "G6": "Sims",
    "G7": "Broccoli",
    "G8": "Avex",
    "G9": "D3 Publisher",
    "GB": "Konami Computer Entertainment Japan",
    "GD": "Square-Enix",
    "HY": "Sachen",
}

def sanitize(title):
    """
    Turn all non-ASCII characters into spaces, and then return a stripped string.
    """
    result = ''
    for c in title:
        result += (c if (' ' <= c and c <= '~') else ' ')
    return result.strip()

gameboy_extensions = ["gb", "gbc", "cgb", "sgb"]
def parse_gameboy(filename):
    """
    Parse a Game Boy ROM. Valid extensions are gb, gbc, cgb, sgb.
    """
    data = open(filename, "rb").read(0x14b + 1)
    props = {}
    props["code"] = sanitize(data[0x134 : 0x134 + 15])
    if data[0x144 : 0x144 + 2] in gameboy_publishers:
        props["publisher"] = data[0x144 : 0x144 + 2]
    else:
        props["publisher"] = ""
    # Publisher at $14b takes precedence, if possible
    if data[0x14b] != 0x33:
        pub2 = "%02X" % ord(data[0x14b])
        if pub2 in gameboy_publishers:
            props["publisher"] = pub2
    return props

gameboy_advance_extensions = ["gba", "agb"]
def parse_gameboy_advance(filename):
    """
    Parse a Game Boy Advance ROM. Valid extensions are gba, agb.
    """
    data = open(filename, "rb").read(0xb0 + 2)
    props = {}
    props["title"] = sanitize(data[0xa0 : 0xa0 + 12])
    props["code"] = sanitize(data[0xac : 0xac + 4])
    if data[0xb0 : 0xb0 + 2] in gameboy_publishers:
        props["publisher"] = data[0xb0 : 0xb0 + 2]
    else:
        props["publisher"] = ""
    return props

def Parse(filename):
    if '.' not in filename:
        return {}

    ext = filename[filename.rindex('.') + 1 : ]
    if ext in gameboy_extensions:
        return parse_gameboy(filename)
    elif ext in gameboy_advance_extensions:
        return parse_gameboy_advance(filename)
    return {}

class TestParseFunctions(unittest.TestCase):
    def setUp(self):
        pass

    def test_gameboy(self):
        props = Parse("test/Tetris.gb")
        self.assertGreater(len(props), 0)
        self.assertEquals(props["code"], "TETRIS")
        self.assertEquals(props["publisher"], "01")

    def test_gameboy_advance(self):
        props = Parse("test/The Legend of Zelda - A Link to the Past & Four Swords.gba")
        self.assertGreater(len(props), 0)
        self.assertEquals(props["title"], "GBAZELDA")
        self.assertEquals(props["code"], "AZLE")
        self.assertEquals(props["publisher"], "01")

if __name__ == '__main__':
    unittest.main()