import express from "express";
import sendReading from "./sendReadings";

// const express = require("express");
let router = express.Router();

router.use("/send-reading", sendReading);
export default router;
