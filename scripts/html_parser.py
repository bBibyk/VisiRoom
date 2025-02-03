import requests
from bs4 import BeautifulSoup
import tldextract
import re
from sitemap_parser import parse_sitemap

def get_sitemap_urls(base_url):
    sitemap_url = base_url.rstrip('/') + "/sitemap.xml"
    try:
        urls = parse_sitemap(sitemap_url)
        return urls
    except:
        return []

def fetch_html(url):
    try:
        response = requests.get(url, timeout=10, headers={'User-Agent': 'Mozilla/5.0'})
        if response.status_code == 200:
            return response.text
    except requests.exceptions.RequestException:
        return None
    return None

def analyze_html_structure(html):
    soup = BeautifulSoup(html, 'html.parser')
    
    title = soup.title.string if soup.title else "No Title Found"
    meta_desc = soup.find("meta", attrs={"name": "description"})
    meta_desc = meta_desc["content"] if meta_desc else "No Meta Description"
    
    headings = {f'h{i}': len(soup.find_all(f'h{i}')) for i in range(1, 7)}
    
    return {
        "Title": title,
        "Meta Description": meta_desc,
        "Headings Count": headings,
    }

def analyze_url_structure(url):
    extracted = tldextract.extract(url)
    domain = extracted.domain + '.' + extracted.suffix
    path = url.replace(f"https://{domain}", "").replace(f"http://{domain}", "")
    
    readable = bool(re.match(r'^[a-zA-Z0-9-_/]+$', path))
    
    return {
        "Domain": domain,
        "URL Path": path,
        "SEO Friendly": readable,
    }

def analyze_website(base_url):
    pages = get_sitemap_urls(base_url) or [base_url]
    results = {}
    
    for url in pages:
        print(f"Analyzing {url}...")
        html = fetch_html(url)
        if html:
            structure_data = analyze_html_structure(html)
            url_data = analyze_url_structure(url)
            results[url] = {**structure_data, **url_data}
        else:
            results[url] = {"Error": "Could not fetch HTML"}
    
    return results

if __name__ == "__main__":
    website = input("Enter website URL (e.g., https://example.com): ")
    seo_results = analyze_website(website)
    for url, data in seo_results.items():
        print(f"\nResults for {url}:")
        for key, value in data.items():
            print(f"{key}: {value}")
