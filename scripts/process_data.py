import spacy
import logging
import json
import sys

logging.basicConfig(level=logging.DEBUG)

# Fonction pour traiter les données de Tesseract
def process_tesseract_data(tesseract_output):
    # Utilise spaCy pour traiter les données Tesseract
    nlp = spacy.load("fr_core_news_sm")
    doc = nlp(tesseract_output)

    # Initialise les variables pour stocker les données
    facture_found = False
    nom_found = False
    prenom_found = False
    contrat_found = False
    consommation_found = False
    client_found = False
    processed_data = {
        "facture": None,
        "nom": None,
        "prenom": None,
        "contrat": None,
        "consommation": None,
        "client": None
    }

    # Parcourt les phrases du texte
    for sentence in doc.sents:
        logging.debug(f"Analyzing sentence: {sentence.text}")

        # Vérifie la présence des mots spécifiés dans la phrase
        if "facture" in sentence.text.lower():
            facture_found = True
            processed_data["facture"] = sentence.text

        # Utilise spaCy pour extraire le nom et le prénom
        for ent in sentence.ents:
            if ent.label_ == "PER" and not nom_found:
                nom_found = True
                processed_data["nom"] = ent.text
            elif ent.label_ == "PER" and not prenom_found:
                prenom_found = True
                processed_data["prenom"] = ent.text

        if "contrat" in sentence.text.lower():
            contrat_found = True
            processed_data["contrat"] = extract_contract_number(sentence)

        if "consommation" in sentence.text.lower():
            consommation_found = True
            processed_data["consommation"] = sentence.text

        if "client" in sentence.text.lower():
            client_found = True
            processed_data["client"] = sentence.text

    return processed_data

def extract_contract_number(sentence):
    # Utilise les dépendances pour extraire le numéro de contrat
    for token in sentence:
        if token.text.isdigit() and "contrat" in [ancestor.text.lower() for ancestor in token.ancestors]:
            return token.text
    return None

if __name__ == "__main__":
    # Récupère les données Tesseract à partir des arguments de la ligne de commande
    tesseract_output = sys.argv[1] if len(sys.argv) > 1 else ""
    
    # Traite les données Tesseract avec spaCy
    result = process_tesseract_data(tesseract_output)

    # Convertit les résultats en JSON et imprime
    print(json.dumps(result))
