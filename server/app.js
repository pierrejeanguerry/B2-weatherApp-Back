
const express = require('express');
const app = express();
const port = 3000;

app.get('/', (req, res) => {
  res.send('Bonjour, ceci est votre premier serveur Node.js!');
});

app.listen(port, () => {
  console.log(`Le serveur écoute sur le port ${port}`);
});
