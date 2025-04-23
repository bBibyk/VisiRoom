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


def advise_content(link, phrase):
    # TODO
    return f"Analyse de '{phrase}' par rapport à {link}"

def extract_domain_base(url):
    parsed = urlparse(url)
    base = f"{parsed.scheme}://{parsed.netloc}"
    return base

def get_advises(reference_content, request):
    api_key = os.getenv("MISTRAL_API_KEY")
    model = "mistral-large-latest"

    # TODO
    prompt ="""
            """

    client = Mistral(api_key=api_key)
    chat_response = client.chat.complete(
        model= model,
        messages = [
            {
                "role": "user",
                "content": prompt,
            },
        ]
    )
    print(chat_response)

def google_search(query, domain):
    results = search(query, num_results=100)
    
    first_result = None
    target_position = None
    referenced_page = None

    for idx, result in enumerate(results, start=1):
        if idx == 1:
            first_result = result

        if domain in result:
            target_position = idx
            referenced_page = result
            break

    return first_result, target_position, referenced_page

def is_valid_url(url):
    try:
        result = urlparse(url)
        return all([result.scheme, result.netloc])
    except Exception:
        return False


def main(link, phrase):
    if not is_valid_url(link):
        return json.dumps({"erreur": "lien"})
    if not phrase.strip():
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
