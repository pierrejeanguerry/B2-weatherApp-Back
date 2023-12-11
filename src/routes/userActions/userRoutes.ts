import express from "express";
import createUser from "./createUser";
import userLogin from "./userLogin";
import addStation from "./addStation";
// import deleteStation from "./deleteStation";

let router = express.Router();

router.use("/register", createUser);
router.use("/login", userLogin);
router.use("/add-station", addStation);
// router.use("/delete-station", deleteStation);

export default router;
