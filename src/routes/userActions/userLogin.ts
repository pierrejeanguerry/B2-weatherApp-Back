import express from "express";
import UserController from "../../controllers/UserController";
import { UserModel } from "../../models/UserModel";
import * as jwt from "jsonwebtoken";

let router = express.Router();
require("dotenv").config();

router.post("/", (req, res) => {
  UserController.getUserByEmail(req, res).then(({ email, password, _id }) => {
    const to_compare = new UserModel({
      email,
      password,
    })
      .isValidPassword(req.body.password)
      .then((isValid) => {
        if (!isValid) res.status(400).json({ error: "mot de passe incorrect" });
        const token = jwt.sign({ _id, email }, `${process.env.SECRET_KEY}`, {
          expiresIn: "1h",
        });
        console.log(token);
        res.setHeader("Content-Type", "application/json");
        res.setHeader("authorization", `Bearer ${token}`);
        console.log(res.getHeaders());
        res.status(200).send("Connection acceptée");
      })
      .catch((error) => console.log(error));
  });
});

export default router;
