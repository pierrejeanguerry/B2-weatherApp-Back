


## Pourquoi Node.js ?

1) Node.js est conçu pour gérer plusieurs opérations simultanément sans attendre que l'une soit terminée pour passer à la suivante. Cela permet une meilleure utilisation des ressources et une meilleure évolutivité pour les applications en temps réel.
2) Node.js est construit sur le moteur JavaScript V8, qui est connu pour sa rapidité. Il excelle particulièrement dans les applications gourmandes en entrées/sorties et en temps réel, telles que les applications de chat, les jeux en ligne, et les applications de diffusion en continu.
3) Node.js est particulièrement bien adapté pour les applications nécessitant une mise à jour en temps réel, telles que les applications de chat, les tableaux de bord en temps réel, les jeux en ligne, etc.
4) de.js a une communauté active qui crée et maintient un grand nombre de modules et de bibliothèques. Cela signifie qu'il est souvent plus facile de trouver des solutions aux problèmes, d'obtenir des conseils et de rester à jour avec les meilleures pratiques.

Conclusion : créer un serveur en Node.js peut être bénéfique pour des applications nécessitant une exécution rapide, une gestion efficace des connexions simultanées, et une intégration facile avec des bibliothèques tierces.

source : https://fr.wikipedia.org/wiki/Node.js
source : https://nodejs.org/en
# Faire un serveur Node.js étape par étape :
	 
### étape 1 : Initialisez le projet
1) Ouvrir le terminal et créer un nouveau dossier pour votre projet. 
2) Exécuter la commande 
pour initialiser un fichier ```npm init``` *package.json*
4) Répondre aux questions ou appuyer sur Entrée pour accepter les valeurs par défaut.

### étape 2  : Installez le module express

`Express` est un framework minimaliste pour construire des applications web avec Node.js.
1) Utiliser la commande suivante dans le terminal pour l'installer  : ``` npm install express```

### Étape 3 : Créer le fichier principal

1)  Créer un fichier appelé `app.js`  (ou `index.js`) dans le dossier de projet.
2)  mettre ce code 

```js
const express = require('express');
const app = express();
const port = 3000;


app.get('/', (req, res) => {
  res.send('Bonjour, ceci est votre premier serveur Node.js!');
});

app.listen(port, () => {
  console.log(`Le serveur écoute sur le port ${port}`);
});
```

### Étape 4 : Exécuter le serveur
1) Dans le terminal il faut executer le fichier créé avec la commande suivante : `node app.js
`
source : https://nodejs.org/en
source : https://hub.docker.com/search?q=nodejs
