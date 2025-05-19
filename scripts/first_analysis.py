import requests
import sys
import re
import json
import webcolors
from bs4 import BeautifulSoup
import textstat
from urllib.parse import urljoin, urlparse
from selenium import webdriver
from selenium.webdriver.chrome.options import Options
import time
import validators

def fetch_links_and_content(url, base_domain, t=0):
    def is_csr(content):
        soup = BeautifulSoup(content, 'html.parser')
        body = soup.body
        return not body or len(body.get_text(strip=True)) < 50

    try:
        response = requests.get(url, timeout=5)
        response.raise_for_status()
        page_content = response.text

        if is_csr(page_content):
            raise ValueError("Likely a CSR page")
        
        soup = BeautifulSoup(page_content, 'html.parser')
        return "SSR", soup
    except requests.HTTPError :
        return None, None
    except Exception:
        if t < 5:
            try:
                options = Options()
                options.add_argument("--headless")
                options.add_argument("--disable-gpu")
                options.add_argument("--no-sandbox")
                options.add_argument("--enable-unsafe-swiftshader")

                driver = webdriver.Chrome(options=options)
                driver.set_page_load_timeout(10)
                driver.get(url)
                time.sleep(3)

                page_source = driver.page_source
                driver.quit()
                soup = BeautifulSoup(page_source, 'html.parser')
                return "CSR", soup
            except Exception:
                if t < 5:
                    return fetch_links_and_content(url, base_domain, t=t+1)
                else:
                    return None, None
        else:
            return None, None
    

def text_analysis(soup):
    results = []

    # Extraire et nettoyer le texte de la page
    text = soup.get_text(separator=' ', strip=True)
    text = ' '.join(text.split())

    # Calcul des scores de lisibilité
    readability_scores = {
        "flesch_reading_ease": textstat.flesch_reading_ease(text),
        "flesch_kincaid_grade": textstat.flesch_kincaid_grade(text),
        "smog_index": textstat.smog_index(text),
        "automated_readability_index": textstat.automated_readability_index(text),
        "dale_chall_score": textstat.dale_chall_readability_score(text),
        "difficult_words": textstat.difficult_words(text),
        "linsear_write_formula": textstat.linsear_write_formula(text),
        "gunning_fog": textstat.gunning_fog(text),
        "text_standard": textstat.text_standard(text)
    }

    # Score global simplifié
    global_score = (
        readability_scores["flesch_reading_ease"]
        - readability_scores["gunning_fog"]
        - readability_scores["smog_index"]
    ) / 3

    results.append({
        "global_score": round(global_score, 2),
        "readability_scores": readability_scores
    })

    return results

def images_analysis(soup):
    errors = []
    images = soup.find_all("img")
    
    for img in images:
        src = img.get("src", "[SRC MISSING]")
        alt = img.get("alt", "")
        width = img.get("width")
        height = img.get("height")

        # Vérification de l'attribut alt
        if not alt:
            errors.append(f"Image '{src}' : L'attribut 'alt' est manquant.")
        elif len(alt) < 5:
            errors.append(f"Image '{src}' : L'attribut 'alt' est trop court ({len(alt)} caractères).")
        elif len(alt) > 100:
            errors.append(f"Image '{src}' : L'attribut 'alt' est trop long ({len(alt)} caractères).")
        
        # Vérification de la taille des images
        if not width or not height:
            errors.append(f"Image '{src}' : Taille non spécifiée (ajouter 'width' et 'height').")
        
        # Vérification du format d'image
        if not re.search(r'\.webp$|\.avif$', src, re.IGNORECASE):
            errors.append(f"Image '{src}' : Utiliser un format moderne comme WebP ou AVIF.")
        
        # Vérification du poids des images (exemple basique basé sur URL)
        if "large" in src.lower() or "uncompressed" in src.lower():
            errors.append(f"Image '{src}' : L'image semble volumineuse, pensez à la compresser.")
    
    return errors


def tags_analysis(soup):
    errors = []

    # 1. Vérification de la sémantique HTML (ex. utiliser header, footer, article plutôt que div ou span)
    # ['header', 'footer', 'article', 'section', 'nav', 'main', 'figure', 'aside']
    non_semantic_tags = ['div', 'span']
    
    for tag in non_semantic_tags:
        if soup.find_all(tag):
            errors.append(f"Utilisation de la balise non sémantique &lt;/{tag}&gt;/.")
    
    # 2. Vérification des balises meta manquantes
    meta_tags = ['description', 'robots', 'keywords']
    for meta in meta_tags:
        if not soup.find('meta', attrs={'name': meta}):
            errors.append(f"Balise &lt;/meta&gt;/ {meta} manquante.")

    # 3. Vérification de la présence des balises &lt;/h1&gt;/ et &lt;/title&gt;/
    if not soup.find('h1'):
        errors.append("Balise &lt;/h1&gt;/ manquante.")
    
    if not soup.find('title'):
        errors.append("Balise &lt;/title&gt;/ manquante.")
    
    # 4. Vérification des styles en ligne (inline styles)
    inline_styles = soup.find_all(style=True)
    if inline_styles:
        errors.append("Des styles en ligne ont été détectés (utilisation de l'attribut 'style').")

    return errors

def accessibility_analysis(soup):
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
            'link': ['a'],  # un lien doit être une balise &lt;/a&gt;/
            'dialog': ['div', 'section'],  # Un dialog est souvent une &lt;/div&gt;/ ou une &lt;/section&gt;/
            'checkbox': ['input'],  # Un checkbox doit être un &lt;/input&gt;/ de type checkbox
            'radio': ['input'],  # Un radio doit être un &lt;/input&gt;/ de type radio
            'textbox': ['input', 'textarea'],  # Un textbox peut être un &lt;/input&gt;/ ou un &lt;/textarea&gt;/
            'combobox': ['input', 'select'],  # Un combobox peut être un &lt;/input&gt;/ ou un &lt;/select&gt;/
            'menuitem': ['div', 'button'],  # Un menuitem peut être un &lt;/div&gt;/ ou un &lt;/button&gt;/
            'listbox': ['div', 'ul'],  # Un listbox peut être une &lt;/div&gt;/ ou une &lt;/ul&gt;/
            'progressbar': ['div', 'progress'],  # Un progressbar est souvent un &lt;/div&gt;/ ou un &lt;/progress&gt;/
            'slider': ['input'],  # Un slider doit être un &lt;/input&gt;/ de type range
            'alertdialog': ['div'],  # Un alertdialog est souvent une &lt;/div&gt;/
            'heading': ['h1', 'h2', 'h3', 'h4', 'h5', 'h6'],  # Un heading doit être un &lt;/h1&gt;/, &lt;/h2&gt;/, etc.
        }
        
        # Vérifier si l'élément a un rôle et si ce rôle est compatible avec l'élément HTML
        if role in role_compatibility:
            if element.name in role_compatibility[role]:
                return True
            else:
                return False
        return True
    
    errors = []

    # 1. Balises alt manquantes pour les images (déménage dans image_analyse)

    # 2. Vérification de "user-scalable='no'" dans les balises meta
    if soup.find('meta', attrs={'name': 'viewport', 'content': re.compile('.*user-scalable=no.*')}):
        errors.append("L'attribut 'user-scalable' est défini sur 'no'. Il est recommandé de permettre l'échelle de la page.")

    # 3. Vérification du maximum scale &lt;/ 5
    viewport_meta = soup.find('meta', attrs={'name': 'viewport'})
    if viewport_meta and 'maximum-scale' in viewport_meta.get('content', ''):
        if float(re.search(r"maximum-scale=([0-9.]+)", viewport_meta['content']).group(1)) < 5:
            errors.append("La valeur 'maximum-scale' dans la balise &lt;/meta&gt;/ est inférieure à 5. Il est recommandé de permettre un plus grand zoom.")

    # 4. Vérification du contraste entre le fond et le premier plan (simplifié)
    # Pour simplifier, on suppose qu'il y a un contraste adéquat si des couleurs claires et foncées sont définies dans les styles en ligne
    for element in soup.find_all(style=True):
        style = element['style']
        color_match = re.search(r'color:\s*(#[0-9a-fA-F]+|[a-zA-Z]+)', style)
        bg_color_match = re.search(r'background-color:\s*(#[0-9a-fA-F]+|[a-zA-Z]+)', style)
        
        if color_match and bg_color_match:
            color_value = color_match.group(1)
            bg_color_value = bg_color_match.group(1)
            
            try:
                color_rgb = webcolors.name_to_rgb(color_value) if color_value.isalpha() else webcolors.hex_to_rgb(color_value)
                bg_color_rgb = webcolors.name_to_rgb(bg_color_value) if bg_color_value.isalpha() else webcolors.hex_to_rgb(bg_color_value)
                
                contrast_ratio = sum(abs(c1 - c2) for c1, c2 in zip(color_rgb, bg_color_rgb))
                if contrast_ratio < 100:  # Seuil arbitraire pour faible contraste
                    errors.append(f"Contraste insuffisant entre la couleur {color_value} et le fond {bg_color_value}.")
            except :
                pass

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
                errors.append(f"L'élément &lt;/{element.name}&gt;/ avec le rôle '{role}' n'est pas compatible.")
            
            # Vérification des correspondances entre les attributs ARIA et leurs rôles
            for attr in element.attrs:
                if attr.startswith('aria-') and not is_aria_attribute_valid_for_role(attr, role):
                    errors.append(f"L'attribut {attr} n'est pas valide pour le rôle '{role}'.")

    # 7. Vérification des noms accessibles pour les boutons, liens et menuitems
    for element in soup.find_all(['button', 'a', 'menuitem']):
        if not element.get('aria-label') and not element.get('title') and not element.get_text(strip=True):
            errors.append(f"L'élément &lt;/{element.name}&gt;/ n'a pas de nom accessible.")

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
            errors.append(f"Le champ de formulaire &lt;/input&gt;/ n'a pas de label accessible.")

    # 11. Vérification des &lt;/frames&gt;/ et &lt;/iframes&gt;/ avec un titre
    for frame in soup.find_all(['frame', 'iframe']):
        if not frame.get('title'):
            errors.append(f"L'élément &lt;/{frame.name}&gt;/ n'a pas d'attribut 'title'.")

    # 12. Vérification de la validité de l'attribut [lang] dans le &lt;/html&gt;/
    html_tag = soup.find('html')
    if html_tag:
        lang_attr = html_tag.get('lang')
        if not lang_attr:
            errors.append("L'élément &lt;/html&gt;/ n'a pas d'attribut 'lang'.")

    return errors

def analyze_page(soup):
    result = {}
    result["text"] = text_analysis(soup)
    result["images"] = images_analysis(soup)
    result["tags"] = tags_analysis(soup)
    result["accessibility"] = accessibility_analysis(soup)
    return result

def crawl_website(start_url):
    if not validators.url(link):
        print(json.dumps({"erreur": "lien"}))
    else:
        parsed_start = urlparse(start_url)
        base_domain = parsed_start.netloc

        type, start_url_content = fetch_links_and_content(start_url, base_domain)
        if start_url_content is not None:
            result = {start_url : analyze_page(start_url_content),
                    "type" : type}
        else:
            result = {"error" : "unavailable"}
        print(json.dumps(result))


if __name__ == "__main__":
    if len(sys.argv) != 2:
        print(json.dumps({"erreur": "arguments"}))
    else:
        link = sys.argv[1]
        crawl_website(link)