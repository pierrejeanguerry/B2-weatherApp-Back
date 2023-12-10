const express = require("express");
const addStation = require("./addStation");
const deleteStation = require("./deleteStation");

const router = express.Router();

router.use("/add-station", addStation);
router.use("/delete-station", deleteStation);

module.exports = router;
