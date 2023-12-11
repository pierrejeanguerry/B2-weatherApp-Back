import express from "express";
import UserController from "../../controllers/UserController";

let router = express.Router();
// const UserController = require("../../controllers/UserController");

router.post("/", (req, res) => {
  if (req.body.password !== req.body.repeatPassword) {
    res
      .status(400)
      .json({ error: "Password and Repeat password are different" });
  } else {
    UserController.addUser(req, res);
  }
});

export default router;
