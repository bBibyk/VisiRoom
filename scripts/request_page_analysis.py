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

def extract_semantic_text(url):
    try:
        response = requests.get(url, timeout=10)
        response.raise_for_status()
    except requests.RequestException as e:
        return f"Error fetching URL: {e}"

    soup = BeautifulSoup(response.text, 'html.parser')

    # Remove unwanted tags
    for tag in soup(['script', 'style', 'header', 'footer', 'nav', 'aside', 'form']):
        tag.decompose()

    # Remove elements likely to be links, emails, numbers, titles
    text_elements = soup.find_all(string=True)
    clean_texts = []

    for text in text_elements:
        stripped = text.strip()
        if not stripped:
            continue
        if any(keyword in text.lower() for keyword in ["@","mailto:", "http", "www"]):
            continue
        if re.search(r'\d{2,}', text):  # Filter out numbers/dates/phone numbers
            continue
        if re.match(r'^[A-Z\s]{5,}$', stripped):  # Avoid long uppercase headings
            continue
        if len(stripped.split()) < 4:  # Avoid tiny fragments
            continue

        clean_texts.append(stripped)
    return "\n".join(clean_texts)


def advise_content(link, query):
    response = {}
    first_result, current_position, referenced_page = google_search(query, extract_domain_base(link))
    response["current_position"] = current_position
    response["concurent"] = first_result
    response["referenced_page"] = referenced_page
    response["changes"] = get_advises(extract_semantic_text(first_result), extract_semantic_text(link), query)
    return response

def get_advises(reference_content, current_content, query):
    api_key = os.getenv("MISTRAL_API_KEY")
    model = "mistral-large-latest"

    prompt =f"""
Tu est un expert en SEO et copyrighting.
Compare deux textes extraits de contenus de sites web dans le contexte d'une requête de recherche web.
Analyse en détail les forces et faiblesses de chaque texte par rapport à cette requête, en mettant en évidence quels aspects sont mieux traités par chaque contenu.
Ensuite, propose des modifications précises et concrètes pour améliorer le contenu de la page cible, de façon à répondre plus pertinemment à la requête, surpassant ainsi la page concurrente.
Priorise la clarté, la pertinence et la richesse des informations fournies pour optimiser la satisfaction de l'intention de recherche.
Ton but est de fournir un contenu qui sera classé 1er dans la requête de recherche.

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
                "content": f"Voici le contenu de la page cible : '{current_content}'",
            },
            {
                "role": "user",
                "content": f"Voici le contenu de la page concurrente : '{reference_content}'",
            },
            {
                "role": "user",
                "content": f"Voici la requête de recherche : '{query}'",
            },
        ]
    )
    return chat_response.choices[0].message.content

def extract_domain_base(url):
    parsed = urlparse(url)
    base = f"{parsed.scheme}://{parsed.netloc}"
    return base

def google_search(query, domain):
    first_result = None
    current_position = None
    referenced_page = None
    
    try :
        results = search(query, num_results=100)

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

def is_valid_url(url):
    try:
        result = urlparse(url)
        if not all([result.scheme, result.netloc]):
            return False

        # Tente de faire une requête GET pour vérifier l'accessibilité
        response = requests.get(url, timeout=5)
        return response.status_code == 200 and response.text.strip() != ""

    except Exception:
        return False


def main(link, phrase):
    if not is_valid_url(link):
        return json.dumps({"erreur": "lien"})
    if (not phrase.strip()) or (len(phrase)>100):
        return json.dumps({"erreur": "phrase"})
    
    result = advise_content(link, phrase)
    return json.dumps({"advise": result})

if __name__ == "__main__":
    if len(sys.argv) != 3:
        print(json.dumps({"erreur": "arguments"}))
    else:
        load_dotenv()
        link = sys.argv[1]
        phrase = sys.argv[2]
        print(main(link, phrase))
