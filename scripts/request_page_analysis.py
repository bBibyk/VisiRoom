import sys
import json
import os
from urllib.parse import urlparse
from googlesearch import search
from dotenv import load_dotenv
from mistralai import Mistral
import requests
from bs4 import BeautifulSoup
import re
import time
import validators
from selenium import webdriver
from selenium.webdriver.chrome.options import Options
import html2text

def extract_semantic_text(url, t=0):
    def is_csr(content):
        soup = BeautifulSoup(content, 'html.parser')
        body = soup.body
        return not body or len(body.get_text(strip=True)) < 50

    def get_soup_with_fallback(url, t=0):
        try:
            response = requests.get(url, timeout=10)
            response.raise_for_status()
            if is_csr(response.text):
                raise ValueError("Likely a CSR page")
            return BeautifulSoup(response.text, 'html.parser')
        except Exception:
            if t < 3:
                try:
                    options = Options()
                    options.add_argument("--headless")
                    options.add_argument("--disable-gpu")
                    options.add_argument("--no-sandbox")
                    options.add_argument("--enable-unsafe-swiftshader")
                    options.add_experimental_option("excludeSwitches", ["enable-logging"])
                    
                    driver = webdriver.Chrome(options=options)
                    driver.set_page_load_timeout(10)
                    driver.get(url)
                    time.sleep(3)
                    page_source = driver.page_source
                    driver.quit()
                    return BeautifulSoup(page_source, 'html.parser')
                except Exception:
                    return None
            else:
                return None

    soup = get_soup_with_fallback(url, t)
    if soup is None:
        return f"No content extracted"

    handler = html2text.HTML2Text()
    handler.ignore_links = True
    handler.ignore_images = True
    return handler.handle(str(soup))


def advise_content(link, query):
    response = {}
    first_result, current_position, referenced_page = google_search(query, extract_domain_base(link))
    response["current_position"] = current_position
    response["concurent"] = first_result
    response["referenced_page"] = referenced_page
    response["changes"] = get_advises(extract_semantic_text(first_result), extract_semantic_text(link), query)
    return response

def get_advises(reference_content, current_content, query):
    try:
        api_key = os.getenv("MISTRAL_API_KEY")
        model = "mistral-small-latest"

        prompt =f"""
    Tu est un expert en SEO et copyrighting. Parle à la 1ère personne du pluriel (nous).
    Compare deux textes extraits de contenus de sites web dans le contexte d'une requête de recherche web.
    Analyse en détail les forces et faiblesses de chaque texte par rapport à cette requête, en mettant en évidence quels aspects sont mieux traités par chaque contenu.
    Ensuite, propose des modifications précises et concrètes pour améliorer le contenu de la page cible, de façon à répondre plus pertinemment à la requête, surpassant ainsi la page concurrente.
    Priorise la clarté, la pertinence et la richesse des informations fournies pour optimiser la satisfaction de l'intention de recherche.
    Ton but est de fournir un contenu qui sera classé 1er dans la requête de recherche.
    Si au moins l'une des pages est vide, tu ne feras pas la comparaison, mais proposera seuelemnt un contenu pour cibler la requête de recherche et arriver en 1ère place du classement.
    """

        client = Mistral(api_key=api_key)
        chat_response = client.chat.complete(
            model= model,
            messages = [
                {
                    "role": "system",
                    "content": prompt,
                },
                {
                    "role": "user",
                    "content": f"Voici le contenu de la page cible (ignore les éléments HTML, comme ci s'était du texte pur) : '{current_content}'",
                },
                {
                    "role": "user",
                    "content": f"Voici le contenu de la page concurrente (ignore les éléments HTML, comme ci s'était du texte pur) : '{reference_content}'",
                },
                {
                    "role": "user",
                    "content": f"Voici la requête de recherche : '{query}'",
                },
            ]
        )
        return json.dumps({"advise": chat_response.choices[0].message.content})
    except :
        return json.dumps({"error:unavailable"})

def extract_domain_base(url):
    parsed = urlparse(url)
    base = f"{parsed.scheme}://{parsed.netloc}"
    return base

def google_search(query, domain):
    first_result = None
    current_position = None
    referenced_page = None
    
    try :
        results = search(query, num=100)

        for idx, result in enumerate(results, start=1):
            if idx == 1:
                first_result = result

            if domain in result:
                current_position = idx
                referenced_page = result
                break
            
    except Exception:
        pass

    return first_result, current_position, referenced_page

def main(link, phrase):
    if not validators.url(link):
        return json.dumps({"erreur": "lien"})
    if (not phrase.strip()) or (len(phrase)>100):
        return json.dumps({"erreur": "phrase"})
    
    result = advise_content(link, phrase)
    return result

if __name__ == "__main__":
    if len(sys.argv) != 3:
        print(json.dumps({"erreur": "arguments"}))
    else:
        load_dotenv()
        link = sys.argv[1]
        phrase = sys.argv[2]
        print(main(link, phrase))
