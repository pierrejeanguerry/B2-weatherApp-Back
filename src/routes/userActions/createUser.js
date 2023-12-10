const express = require("express");
const router = express.Router();
const UserController = require("../../controllers/UserController");

router.post("/", (req, res) => {
  UserController.addUser(req.body);
  res.json({ message: "Données récupérées avec succès depuis l'URL" });
});

module.exports = router;
