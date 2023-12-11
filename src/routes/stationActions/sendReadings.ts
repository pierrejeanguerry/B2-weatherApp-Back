import express from "express";

let router = express.Router();
router.post("/", (req, res) => {
  console.log(req.body);
  res.json({ message: "Données récupérées avec succès depuis l'URL" });
});
export default router;
