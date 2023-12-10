const express = require("express");
const router = express.Router();

/*
    Il faut vérifier:
    - si la requete contient le header attendu
    - si le header a une correspondance dans la base de donnée

    il faut faire:
    - enregistrement dans la base de donnée pour la bonne station
*/
router.post("/", (req, res) => {
  console.log(req.body);
  res.json({ message: "Données récupérées avec succès depuis l'URL" });
});

module.exports = router;
