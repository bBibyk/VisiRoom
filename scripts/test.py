import requests
from dotenv import load_dotenv
import os

load_dotenv()

API_KEY = os.getenv("MISTRAL_API_KEY")

API_URL = "https://api.mistral.ai/v1/chat/completions"

headers = {
    "Authorization": f"Bearer {API_KEY}",
    "Content-Type": "application/json"
}

data = {
    "model": "mistral-small",
    "messages": [
        {"role": "user", "content": "Peux-tu répondre à ce message ?"}
    ]
}

try:
    response = requests.post(API_URL, headers=headers, json=data)
    response.raise_for_status()  # Lève une erreur pour les statuts HTTP 4xx/5xx

    # Affichage de la réponse de l'API
    result = response.json()
    print("✅ Clé API valide. Réponse de l'IA :")
    print(result["choices"][0]["message"]["content"])

except requests.exceptions.HTTPError as err:
    print(f"❌ Erreur HTTP : {err}")
    print(f"Détails : {response.text}")
except Exception as e:
    print(f"❌ Une erreur est survenue : {e}")