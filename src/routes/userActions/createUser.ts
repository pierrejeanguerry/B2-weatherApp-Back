import express from "express";
import UserController from "../../controllers/UserController";

let router = express.Router();
// const UserController = require("../../controllers/UserController");

router.post("/", (req, res) => {
  UserController.addUser(req.body, res);
  res.json({ message: "Données récupérées avec succès depuis l'URL" });
});

export default router;
