import express from "express";
import UserController from "../../controllers/UserController";
import jwt, { JwtPayload } from "jsonwebtoken";
import { error } from "console";

let router = express.Router();
interface TokenPayload {
  _id: string;
  email: string;
  iat: number;
  exp: number;
  // Autres propriétés du payload
}
router.post("/", (req, res) => {
  const token = req.headers.authorization?.split(" ")[1];
  const isValid = jwt.verify(`${token}`, `${process.env.SECRET_KEY}`);
  if (!isValid) {
    res.status(402).json({ error: "TOKEN_ERROR" });
  }
  const decoded = jwt.decode(`${token}`);

  if (decoded && typeof decoded === "object" && !Array.isArray(decoded)) {
    const tokenPayload: TokenPayload = decoded as TokenPayload;

    // Maintenant, vous pouvez utiliser tokenPayload sans problème
    console.log(tokenPayload._id);

    UserController.addUserToStation(req, res, tokenPayload._id);
  } else {
    console.error("Erreur lors du décodage du token");
  }
});

export default router;
