import json 


d = {
    "url" : {
        "image" : ["div", "piv"],
        "access" : ["kiw", "siv"]
    }
}

d["url2"] = {}

print(json.dumps(d))