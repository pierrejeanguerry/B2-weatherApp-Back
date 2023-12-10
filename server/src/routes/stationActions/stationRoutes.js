const express = require("express");
const sendReanding = require("./sendReadings");

const router = express.Router();

router.use("/send-reading", sendReanding);

module.exports = router;
