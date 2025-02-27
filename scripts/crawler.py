import requests
import re
from bs4 import BeautifulSoup
from urllib.parse import urljoin, urlparse
from concurrent.futures import ThreadPoolExecutor, as_completed

# Dictionnaire pour stocker le contenu des pages
visited_urls = {}
# Limite maximale de pages à analyser
MAX_PAGES = 100

def is_valid_url(url, base_domain):
    parsed_url = urlparse(url)
    return parsed_url.scheme in ("http", "https") and parsed_url.netloc == base_domain

def fetch_links_and_content(url, base_domain):
    urls_found = set()
    page_content = ""
    try:
        response = requests.get(url, timeout=5)
        response.raise_for_status()
        page_content = response.text
        soup = BeautifulSoup(page_content, 'html.parser')
        
        for link in soup.find_all('a', href=True):
            full_url = urljoin(url, link['href'])
            if is_valid_url(full_url, base_domain):
                urls_found.add(full_url)
    except requests.RequestException:
        pass  # Ignorer les erreurs de connexion ou de réponse
    
    return urls_found, page_content

def text_analysis(html_content):
    pass

def images_analysis(html_content):
    pass

def keywords_analysis(html_content):
    pass

def tags_analysis(html_code):
    soup = BeautifulSoup(html_code, 'html.parser')
    errors = []

    # 1. Vérification de la sémantique HTML (ex. utiliser header, footer, article plutôt que div ou span)
    semantic_tags = ['header', 'footer', 'article', 'section', 'nav', 'main', 'figure', 'aside']
    non_semantic_tags = ['div', 'span']
    
    for tag in non_semantic_tags:
        if soup.find_all(tag):
            errors.append(f"Utilisation de la balise non sémantique <{tag}>.")
    
    # 2. Vérification des balises meta manquantes
    meta_tags = ['description', 'robots', 'keywords']
    for meta in meta_tags:
        if not soup.find('meta', attrs={'name': meta}):
            errors.append(f"Balise <meta> {meta} manquante.")

    # 3. Vérification de la présence des balises <h1> et <title>
    if not soup.find('h1'):
        errors.append("Balise <h1> manquante.")
    
    if not soup.find('title'):
        errors.append("Balise <title> manquante.")
    
    # 4. Vérification des styles en ligne (inline styles)
    inline_styles = soup.find_all(style=True)
    if inline_styles:
        errors.append("Des styles en ligne ont été détectés (utilisation de l'attribut 'style').")

    print(errors)

def accessibility_analysis(html_code):
    def is_aria_attribute_valid_for_role(attribute, role):
        # Règles simples pour certains rôles ARIA et leurs attributs valides
        aria_roles_and_attributes = {
            'button': ['aria-label', 'aria-pressed', 'aria-expanded'],
            'link': ['aria-label', 'aria-labelledby'],
            'dialog': ['aria-labelledby', 'aria-describedby'],
            'alertdialog': ['aria-labelledby', 'aria-describedby'],
            'textbox': ['aria-label', 'aria-labelledby', 'aria-placeholder'],
            'checkbox': ['aria-checked', 'aria-labelledby', 'aria-label'],
            'radio': ['aria-checked', 'aria-labelledby', 'aria-label'],
            'combobox': ['aria-expanded', 'aria-placeholder', 'aria-labelledby', 'aria-label'],
            'menuitem': ['aria-checked', 'aria-label', 'aria-labelledby'],
            'listbox': ['aria-activedescendant', 'aria-labelledby'],
            'progressbar': ['aria-valuenow', 'aria-valuemin', 'aria-valuemax', 'aria-valuetext'],
            'slider': ['aria-valuenow', 'aria-valuemin', 'aria-valuemax'],
            'spinbutton': ['aria-valuenow', 'aria-valuemin', 'aria-valuemax'],
            'table': ['aria-sort', 'aria-labelledby'],
            'treeitem': ['aria-expanded', 'aria-selected'],
            'heading': ['aria-level'],
        }
        
        # Vérifie si l'attribut est valide pour ce rôle
        if role in aria_roles_and_attributes:
            valid_attributes = aria_roles_and_attributes[role]
            if attribute in valid_attributes:
                return True
        
        return False
    
    def is_role_compatible_with_element(element, role):
    # Vérifier les rôles communs et leur compatibilité avec les éléments HTML
        role_compatibility = {
            'button': ['button', 'a', 'div', 'span'],  # un bouton peut être un div, un span, ou un a (avec rôle)
            'link': ['a'],  # un lien doit être une balise <a>
            'dialog': ['div', 'section'],  # Un dialog est souvent une <div> ou une <section>
            'checkbox': ['input'],  # Un checkbox doit être un <input> de type checkbox
            'radio': ['input'],  # Un radio doit être un <input> de type radio
            'textbox': ['input', 'textarea'],  # Un textbox peut être un <input> ou un <textarea>
            'combobox': ['input', 'select'],  # Un combobox peut être un <input> ou un <select>
            'menuitem': ['div', 'button'],  # Un menuitem peut être un <div> ou un <button>
            'listbox': ['div', 'ul'],  # Un listbox peut être une <div> ou une <ul>
            'progressbar': ['div', 'progress'],  # Un progressbar est souvent un <div> ou un <progress>
            'slider': ['input'],  # Un slider doit être un <input> de type range
            'alertdialog': ['div'],  # Un alertdialog est souvent une <div>
            'heading': ['h1', 'h2', 'h3', 'h4', 'h5', 'h6'],  # Un heading doit être un <h1>, <h2>, etc.
        }
        
        # Vérifier si l'élément a un rôle et si ce rôle est compatible avec l'élément HTML
        if role in role_compatibility:
            if element.name in role_compatibility[role]:
                return True
            else:
                return False
        return True

    soup = BeautifulSoup(html_code, 'html.parser')
    errors = []

    # 1. Balises alt manquantes pour les images
    for img in soup.find_all('img'):
        if not img.get('alt'):
            errors.append("Balise <img> sans attribut 'alt'. Il est essentiel d'ajouter un texte alternatif pour les images.")

    # 2. Vérification de "user-scalable='no'" dans les balises meta
    if soup.find('meta', attrs={'name': 'viewport', 'content': re.compile('.*user-scalable=no.*')}):
        errors.append("L'attribut 'user-scalable' est défini sur 'no'. Il est recommandé de permettre l'échelle de la page.")

    # 3. Vérification du maximum scale < 5
    viewport_meta = soup.find('meta', attrs={'name': 'viewport'})
    if viewport_meta and 'maximum-scale' in viewport_meta.get('content', ''):
        if float(re.search(r"maximum-scale=([0-9.]+)", viewport_meta['content']).group(1)) < 5:
            errors.append("La valeur 'maximum-scale' dans la balise <meta> est inférieure à 5. Il est recommandé de permettre un plus grand zoom.")

    # 4. Vérification du contraste entre le fond et le premier plan (simplifié)
    # Pour simplifier, on suppose qu'il y a un contraste adéquat si des couleurs claires et foncées sont définies dans les styles en ligne
    for element in soup.find_all(style=True):
        style = element['style']
        if 'color' in style and 'background-color' in style:
            color = re.search(r'color:\s*(#[0-9a-fA-F]+|[a-zA-Z]+)', style)
            background_color = re.search(r'background-color:\s*(#[0-9a-fA-F]+|[a-zA-Z]+)', style)
            if color and background_color:
                # Vérification simplifiée des contrastes (méthode d'évaluation basique)
                color_value = color.group(1)
                bg_color_value = background_color.group(1)
                if color_value == bg_color_value:  # Contraste trop faible entre couleur et fond
                    errors.append(f"Contraste insuffisant entre la couleur {color_value} et le fond {bg_color_value}.")

    # 5. Vérification des attributs [accesskey] uniques
    accesskeys = []
    for element in soup.find_all(attrs={'accesskey': True}):
        key = element['accesskey']
        if key in accesskeys:
            errors.append(f"Attribut [accesskey] non unique trouvé: {key}.")
        else:
            accesskeys.append(key)

    # 6. Vérification des attributs ARIA et des rôles
    for element in soup.find_all(attrs={'aria-*': True}):
        role = element.get('role')
        if role:
            # Vérification des rôles ARIA valides pour l'élément
            if not is_role_compatible_with_element(element, role):
                errors.append(f"L'élément <{element.name}> avec le rôle '{role}' n'est pas compatible.")
            
            # Vérification des correspondances entre les attributs ARIA et leurs rôles
            for attr in element.attrs:
                if attr.startswith('aria-') and not is_aria_attribute_valid_for_role(attr, role):
                    errors.append(f"L'attribut {attr} n'est pas valide pour le rôle '{role}'.")

    # 7. Vérification des noms accessibles pour les boutons, liens et menuitems
    for element in soup.find_all(['button', 'a', 'menuitem']):
        if not element.get('aria-label') and not element.get('title') and not element.get_text(strip=True):
            errors.append(f"L'élément <{element.name}> n'a pas de nom accessible.")

    # 8. Vérification de la présence des titres sur les dialogues ou alertes
    for element in soup.find_all(attrs={'role': 'dialog'}):
        if not element.get('aria-labelledby') and not element.get_text(strip=True):
            errors.append("L'élément avec le rôle 'dialog' ou 'alertdialog' n'a pas de titre accessible.")

    # 9. Vérification que les éléments avec [aria-hidden='true'] ne contiennent pas de descendants focusables
    for element in soup.find_all(attrs={'aria-hidden': 'true'}):
        if any(child.get('tabindex') is not None for child in element.find_all(True)):  # Recherche d'éléments focusables
            errors.append("L'élément avec 'aria-hidden=true' contient des descendants focusables.")

    # 10. Vérification des éléments de formulaire avec des étiquettes associées
    for input_elem in soup.find_all('input'):
        if not input_elem.get('aria-label') and not input_elem.get('aria-labelledby') and not input_elem.find_parent('label'):
            errors.append(f"Le champ de formulaire <input> n'a pas de label accessible.")

    # 11. Vérification des <frames> et <iframes> avec un titre
    for frame in soup.find_all(['frame', 'iframe']):
        if not frame.get('title'):
            errors.append(f"L'élément <{frame.name}> n'a pas d'attribut 'title'.")

    # 12. Vérification de la validité de l'attribut [lang] dans le <html>
    html_tag = soup.find('html')
    if html_tag:
        lang_attr = html_tag.get('lang')
        if not lang_attr:
            errors.append("L'élément <html> n'a pas d'attribut 'lang'.")
        elif lang_attr not in ['fr', 'en', 'es']:  # Exemple de validation pour des langues spécifiques
            errors.append(f"L'attribut 'lang' dans <html> a une valeur invalide : {lang_attr}.")

    print(errors)

def analyze_page(url, html):
    text_analysis(html)
    images_analysis(html)
    keywords_analysis(html)
    tags_analysis(html)
    accessibility_analysis(html)

def crawl_website(start_url):
    parsed_start = urlparse(start_url)
    base_domain = parsed_start.netloc
    
    to_visit = set([start_url])
    
    with ThreadPoolExecutor(max_workers=1000) as executor:
        while to_visit:
            if len(visited_urls) >= MAX_PAGES:
                print(f"Le site contient trop de pages (> {MAX_PAGES}). Analyse interrompue.")
                return
            
            future_to_url = {
                executor.submit(fetch_links_and_content, url, base_domain): url
                for url in to_visit
            }
            to_visit = set()  # Réinitialiser pour les nouvelles URL trouvées
            
            for future in as_completed(future_to_url):
                url = future_to_url[future]
                if url not in visited_urls:
                    urls_found, page_content = future.result()
                    visited_urls[url] = page_content
                    analyze_page(url, page_content)
                    to_visit.update(urls_found - visited_urls.keys())

if __name__ == '__main__':
    import sys
    if len(sys.argv) != 2:
        print("Usage: python crawler.py <URL>")
        sys.exit(1)
    
    start_url = sys.argv[1]
    crawl_website(start_url)
