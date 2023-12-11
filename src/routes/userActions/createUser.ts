import express from "express";
import UserController from "../../controllers/UserController";

let router = express.Router();
// const UserController = require("../../controllers/UserController");
const regexEmail = /^(?=[\s\S]{1,300}$)[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,4}$/;
const regexPassword =
  /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!?,.;/:*])[A-Za-z\d!?,.;/:*]{10,1024}$/;
router.post("/", (req, res) => {
  if (req.body.password !== req.body.repeatPassword) {
    res.status(400).json({ error: "PASSWORD_IS_NOT_EQUIVALENT" });
  } else if (
    !regexEmail.test(req.body.email) ||
    !regexPassword.test(req.body.password) ||
    (Object.keys(req.body.username).length < 5 &&
      Object.keys(req.body.username).length > 30) ||
    Object.keys(req.body.first_name).length < 2 ||
    Object.keys(req.body.last_name).length < 2
  ) {
    res.status(403).json({ error: "VALIDATION_ERROR" });
  } else {
    UserController.addUser(req, res);
  }
});

export default router;
