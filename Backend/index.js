const express = require("express");
const bodyParser = require("body-parser");
const mongoose = require("mongoose");
const stationRoutes = require("./src/routes/stationActions/stationRoutes");
const userRoutes = require("./src/routes/userActions/userRoutes");
require("dotenv").config();

const app = express();
const PORT = process.env.SERVER_PORT;
const databaseUrl = `mongodb://${process.env.DB_HOST}:${process.env.DB_PORT}/${process.env.DB_NAME}`;

// Connexion à la base de données MongoDB
mongoose.connect(databaseUrl, {
  useNewUrlParser: true,
  useUnifiedTopology: true,
  useCreateIndex: true,
});

const db = mongoose.connection;

db.on(
  "error",
  console.error.bind(console, "Erreur de connexion à la base de données :")
);
db.once("open", () => {
  console.log("Connexion à la base de données établie avec succès!");
});

app.use(bodyParser.json());

// Differentes routes ici
app.use("/api/station", stationRoutes);
app.use("/api/user", userRoutes);

app.listen(PORT, () => {
  console.log(`Server is running on port ${PORT}`);
});
