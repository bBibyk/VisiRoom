import requests
from dotenv import load_dotenv
import os
from mistralai import Mistral

load_dotenv()

api_key = os.getenv("MISTRAL_API_KEY")

model = "mistral-small-latest"

client = Mistral(api_key=api_key)

chat_response = client.chat.complete(
    model= model,
    messages = [
        {
            "role": "user",
            "content": "What's the weather today ?",
        },
    ]
)
print(chat_response)