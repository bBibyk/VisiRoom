from sklearn.feature_extraction.text import TfidfVectorizer
from sklearn.metrics.pairwise import cosine_similarity

documents = [
    """Rejoignez une école traditionnelle de Kung Fu à Toulouse – 2 semaines d’essai gratuites
Vous recherchez des cours de Kung Fu à Toulouse ? Découvrez une pratique martiale authentique issue d’une tradition ancienne de plusieurs siècles. L’École Zhú Yī Quán Jiā vous propose d’apprendre le Kung Fu Tang Lang, le style ancestral de la Mante Religieuse, enseigné par des professeurs diplômés, dans une ambiance conviviale et respectueuse.

Pourquoi prendre des cours de Kung Fu ?
Le Kung Fu n’est pas seulement un art martial – c’est une voie d’apprentissage complète, mêlant discipline, précision et esprit combatif, enracinée dans une tradition millénaire.

Chaque entraînement est une invitation à se dépasser, à cultiver la rigueur du corps tout en forgeant la stabilité de l’esprit. La pratique de cet art martial unique développe bien plus que des techniques. Elle construit une posture intérieure, une force calme, et une conscience aiguisée.

Grande Muraille de Chine – symbole de tradition ancienne du Kung Fu
Ce que les cours de Kung Fu à Toulouse peuvent vous apporter :
Condition physique renforcée : agilité, coordination, endurance et puissance grâce à une pratique dynamique et complète.
Équilibre mental : gestion du stress, concentration et sérénité par l’ancrage dans le mouvement et le souffle.
Confiance et assurance : chaque progression renforce l’estime de soi et la présence face aux défis.
Techniques de self-défense efficaces : une approche réaliste et stratégique, inspirée des principes du style de la mante.
Esprit d’équipe et respect : l’entraide, la transmission et le partage sont au cœur de l’apprentissage.
Enseignant KFAT pratiquant le Kung Fu en Chine
Le Tang Lang Quan (螳螂拳) de KFAT
Style traditionnel chinois inspiré par le combat d’une mante religieuse observé par un moine Shaolin, Wang Lang, le Tang Lang Quan allie :

Frappes rapides
Déplacements vifs
Clés articulaires
Travail des armes traditionnelles
Formes (Tao Lu) spectaculaires
Développement de la souplesse et de la puissance
C’est un style complet, accessible à tous : progression physique, mentale, culture chinoise ou compétition.

Bâtiment traditionnel chinois – discipline et culture du Tang Lang Quan
Pourquoi choisir les cours de Kung Fu à Toulouse chez KFAT ?
Une école traditionnelle, vivante et respectueuse : Zhú Yī Quán Jiā transmet une tradition martiale millénaire adaptée à chacun.
Des enseignants expérimentés : plusieurs ceintures noires diplômées d’État, formées à Taïwan et en Chine.
Une progression personnalisée : loisir, bien-être, art martial ou compétition – chacun progresse à son rythme.
Des cours pour tous niveaux : débutants bienvenus ! Pas de condition physique requise. Des séances « ceintures noires » sont proposées les jeudis.
Un travail complet : formes, combat, armes, Tai Chi, Qi Gong – pour un équilibre corps-esprit.
Enseignants de Kung Fu qualifiés de l'école KFAT à Toulouse
FAQ – Vos questions fréquentes
Où nous trouver à Toulouse ?
Salle Fontaine Lestang – Fontaine Lestang ET Salle Équinoxe – Patte d’Oie.
Voir les horaires et lieux.
""",
    "cours de Kung Fu à Toulouse"
]

vectorizer = TfidfVectorizer()
tfidf_matrix = vectorizer.fit_transform(documents)
similarity = cosine_similarity(tfidf_matrix[0:1], tfidf_matrix[1:2])

print(f"Similarité cosinus : {similarity[0][0]:.4f}")