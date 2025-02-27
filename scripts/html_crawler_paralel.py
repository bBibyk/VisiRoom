import requests
from bs4 import BeautifulSoup
from urllib.parse import urljoin, urlparse
import argparse
import concurrent.futures
import threading
import queue

visited_urls = set()
seo_issues = {}
url_queue = queue.Queue()
lock = threading.Lock()


def is_internal_link(base_url, link):
    base_netloc = urlparse(base_url).netloc
    link_netloc = urlparse(link).netloc
    return base_netloc == link_netloc or link_netloc == ''


def analyze_page(url, soup):
    issues = []

    # Vérifier la balise title
    title_tag = soup.find('title')
    if not title_tag or not title_tag.text.strip():
        issues.append("Balise <title> manquante ou vide.")

    # Vérifier la meta description
    meta_desc = soup.find('meta', attrs={'name': 'description'})
    if not meta_desc or not meta_desc.get('content', '').strip():
        issues.append("Balise <meta name='description'> manquante ou vide.")

    # Vérifier les balises h1 multiples
    h1_tags = soup.find_all('h1')
    if len(h1_tags) == 0:
        issues.append("Balise <h1> manquante.")
    elif len(h1_tags) > 1:
        issues.append("Balises <h1> multiples trouvées.")

    # Vérifier les images sans attribut alt
    images = soup.find_all('img')
    for img in images:
        if not img.get('alt'):
            issues.append(f"Image avec src '{img.get('src')}' sans attribut alt.")

    # Vérifier les styles en ligne
    inline_styles = soup.find_all(style=True)
    if inline_styles:
        issues.append(f"{len(inline_styles)} éléments avec des styles en ligne trouvés.")

    # Vérifier les balises div et span non sémantiques
    divs = soup.find_all('div')
    spans = soup.find_all('span')
    if divs:
        issues.append(f"{len(divs)} balises <div> trouvées.")
    if spans:
        issues.append(f"{len(spans)} balises <span> trouvées.")

    return issues


def crawl(url, base_url):
    with lock:
        if url in visited_urls:
            return
        visited_urls.add(url)
    
    try:
        response = requests.get(url, timeout=5)
        response.raise_for_status()
        soup = BeautifulSoup(response.text, 'html.parser')

        # Analyser la page actuelle
        issues = analyze_page(url, soup)
        if issues:
            with lock:
                seo_issues[url] = issues

        # Trouver et explorer les liens internes
        for link_tag in soup.find_all('a', href=True):
            link = link_tag['href']
            full_link = urljoin(base_url, link)
            if is_internal_link(base_url, full_link):
                with lock:
                    if full_link not in visited_urls:
                        url_queue.put(full_link)
    
    except Exception as e:
        print(f"Échec de la récupération de {url} : {e}")


def worker(base_url):
    while not url_queue.empty():
        url = url_queue.get()
        crawl(url, base_url)
        url_queue.task_done()


def main(start_url):
    url_queue.put(start_url)
    num_threads = 10  # Ajustez le nombre de threads en fonction de la taille du site

    with concurrent.futures.ThreadPoolExecutor(max_workers=num_threads) as executor:
        futures = [executor.submit(worker, start_url) for _ in range(num_threads)]
        concurrent.futures.wait(futures)

    if seo_issues:
        print("Problèmes SEO trouvés :")
        for page, issues in seo_issues.items():
            print(f"\nURL de la page : {page}")
            for issue in issues:
                print(f"  - {issue}")
        print("Total d'erreurs :", len(seo_issues))
    else:
        print("Aucun problème SEO trouvé.")


if __name__ == "__main__":
    parser = argparse.ArgumentParser(description="Analyseur SEO pour un site web.")
    parser.add_argument("url", help="URL du site web à analyser")
    args = parser.parse_args()
    main(args.url)
